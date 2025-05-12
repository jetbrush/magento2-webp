<?php
namespace Kudja\Webp\Plugin;

use Kudja\Webp\Service\Config;
use Kudja\Webp\Service\ResponseProcessor;
use Magento\Framework\App\Response\Http as Response;

class HttpResponseProcessor
{
    public function __construct(
        private Config $config,
        private ResponseProcessor $responseProcessor,
    ) {}

    /**
     * Process the response before sending it to the client.
     *
     * @param Response $subject
     * @return void
     */
    public function beforeSend(Response $subject): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $contentTypeHeader = $subject->getHeader('Content-Type');
        $contentType = is_array($contentTypeHeader)
            ? $contentTypeHeader[0]->getFieldValue()
            : ($contentTypeHeader ? $contentTypeHeader->getFieldValue() : '');

        $html = $subject->getBody();
        if (!$contentType) {
            if (stripos($html, '<html') !== false) {
                $contentType = 'text/html';
            } elseif (
                (str_starts_with($html, '{') && str_ends_with($html, '}'))
                || (str_starts_with($html, '[') && str_ends_with($html, ']'))
            ) {
                $contentType = 'application/json';
            } else {
                return;
            }
        }

        if (str_starts_with($contentType, 'text/html')) {
            $html = $this->responseProcessor->processPageHtml($html);
        } elseif (
            str_starts_with($contentType, 'application/json')
            && preg_match('~\.(jpe?g|png)(?!\.webp)~i', $html)
        ) {
            $html = $this->responseProcessor->processJson($html);
        }

        $this->responseProcessor->flushBatch();

        $subject->setBody($html);
    }

}
