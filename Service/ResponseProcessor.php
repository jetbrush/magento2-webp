<?php

namespace Kudja\Webp\Service;

use Kudja\Webp\Logger\Logger;

class ResponseProcessor
{

    protected string $baseDir;
    protected string $baseUrl;
    protected string $baseMediaUrl;
    protected string $baseStaticUrl;
    protected string $staticVersion;

    protected string $scheme;
    protected array  $fileExistsCache = [];
    protected array $batchPaths      = [];

    protected string $tagsPattern = "/<(?P<tag>{tags}[^>]*)[^>]+\.(jpe?g|png)(?!\.webp)[^>]*>/i";

    protected string $attributesPattern = "/({attributes})\s*=\s*(['\"])([^\\2]+?)\\2/i";

    /**
     * ResponseProcessor constructor.
     *
     * @param Config $config
     * @param Queue  $queueService
     * @param Logger $logger
     */
    public function __construct(
        private Config $config,
        private Queue  $queueService,
        private Logger $logger
    ) {
        $this->baseDir = BP . DIRECTORY_SEPARATOR . 'pub' . DIRECTORY_SEPARATOR;
        $this->baseUrl = $this->config->getBaseUrl();
        $this->scheme  = parse_url($this->config->getBaseUrl(), PHP_URL_SCHEME);

        $this->baseMediaUrl = $this->config->getBaseMediaUrl();
        $this->baseStaticUrl = $this->config->getBaseStaticUrl();
        $this->staticVersion = explode('/', $this->baseStaticUrl)[4] ?? '';

        $tags = $config->getAllowedTags();
        $tags = implode('|', $tags);
        $this->tagsPattern = str_replace('{tags}', $tags, $this->tagsPattern);

        $attributes = $config->getAllowedAttributes();
        $attributes = implode('|', $attributes);
        $this->attributesPattern = str_replace('{attributes}', $attributes, $this->attributesPattern);
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function processPageHtml(string $html): string
    {
        $html = $this->processHtml($html);
        return $this->processXMagentoInit($html);
    }

    /**
     * @param string $json
     *
     * @return string
     */
    public function processJson(string $json): string
    {
        $result = $json;
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            $data = $this->processArray($data);

            $result = json_encode($data, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->error('JSON processing error: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error('General error: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function processHtml(string $html): string
    {
        return preg_replace_callback($this->tagsPattern, function ($m) {
            $tag = $m[0];
            $modified = $tag;

            preg_match_all($this->attributesPattern, $tag, $attrMatches, PREG_SET_ORDER);
            foreach ($attrMatches as $attr) {
                [$attrHtml, $attrName, $attrQuote, $attrValue] = $attr;

                $isSrcset = stripos($attrName, 'srcset') !== false;
                $parts = $isSrcset ? preg_split('/\s*,\s*/', $attrValue) : [$attrValue];
                $newParts = [];

                foreach ($parts as $part) {
                    if (preg_match('/(?P<url>[^\s]+\.(jpe?g|png))(?!\.webp)(?P<rest>.*)/i', $part, $pm)) {
                        $url     = $pm['url'];
                        $fullUrl = $this->resolveFullUrl($url);
                        $rest    = $pm['rest'];

                        $path = parse_url($url, PHP_URL_PATH);
                        if (!$path || !$this->isAllowedPathUrl($fullUrl)) {
                            $newParts[] = $part;
                            continue;
                        }

                        $fullPath = $this->getLocalFilePath($path);
                        $webpPath = $fullPath . '.webp';
                        $webpUrl  = $url . '.webp';

                        if ($this->fileExistsCached($webpPath)) {
                            $newParts[] = $webpUrl . $rest;
                        } else {
                            $this->batchPaths[$path] = str_replace('/' . $this->staticVersion, '', $path);
                            $newParts[]              = $part;
                        }
                    } else {
                        $newParts[] = $part;
                    }
                }

                $newValue = implode($isSrcset ? ', ' : '', $newParts);
                $modified = str_replace($attrHtml, $attrName . '=' . $attrQuote . $newValue . $attrQuote, $modified);
            }

            return $modified;
        }, $html);
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function processXMagentoInit(string $html): string
    {
        return preg_replace_callback(
            '#(<script\s+type=["\']text/x-magento-init["\'][^>]*>)(.*?)</script>#is',
            function ($m) {
                if (!preg_match('/\.(jpe?g|png)(?!\.webp)/i', $m[2])) {
                    return $m[0];
                }

                $json = $m[2];
                return $m[1] . $this->processJson($json) . '</script>';
            },
            $html
        );
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function processArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->processArray($value);
            } elseif (is_string($value)) {
                if (preg_match('/<[^>]+>/', $value) === 1) {
                    $data[$key] = $this->processHtml($value);
                    continue;
                }

                if (preg_match('~\.(jpe?g|png)$~i', $value)) {
                    $url = $value;
                    $path = parse_url($url, PHP_URL_PATH);
                    if (!$path || !$this->isAllowedPathUrl($url)) {
                        continue;
                    }

                    $fullPath = $this->getLocalFilePath($path);
                    $webpPath = $fullPath . '.webp';
                    $webpUrl  = $url . '.webp';

                    if ($this->fileExistsCached($webpPath)) {
                        $data[$key] = $webpUrl;
                        continue;
                    }

                    $this->batchPaths[$path] = str_replace('/' . $this->staticVersion, '', $path);
                }
            }
        }

        return $data;
    }

    /**
     * Converts protocol-relative URL to full URL based on current scheme
     *
     * @param string $url
     *
     * @return string
     */
    protected function resolveFullUrl(string $url): string
    {
        $url = str_starts_with($url, '//') ? $this->scheme . ':' . $url : $url;
        return !str_starts_with($url, 'http') ? $this->baseUrl . ltrim($url, '/') : $url;
    }

    /**
     * Checks whether a URL is internal (belongs to the current base URL)
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isInternalUrl(string $url): bool
    {
        return !str_starts_with($url, 'http') || str_starts_with($url, $this->baseUrl);
    }

    protected function isAllowedPathUrl(string $url): bool
    {
        return $this->isInternalUrl($url)
            && (str_starts_with($url, $this->baseMediaUrl) || str_starts_with($url, $this->baseStaticUrl));
    }

    /**
     * Resolves local filesystem path from relative URL path
     *
     * @param string $path
     *
     * @return string
     */
    protected function getLocalFilePath(string $path): string
    {
        if ($this->staticVersion) {
            $path = str_replace('/' . $this->staticVersion, '', $path);
        }

        return $this->baseDir . ltrim($path, '/');
    }

    /**
     * Cached wrapper for file_exists to reduce repeated I/O
     *
     * @param string $path
     *
     * @return bool
     */
    protected function fileExistsCached(string $path): bool
    {
        return $this->fileExistsCache[$path] ??= file_exists($path);
    }

    /**
     * Flushes all collected image paths to the conversion queue
     *
     * @return void
     */
    public function flushBatch(): void
    {
        if (!empty($this->batchPaths)) {
            $this->queueService->batchAddImages(array_values($this->batchPaths));
            $this->batchPaths = [];
        }
    }

}
