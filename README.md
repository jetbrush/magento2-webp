# Magento 2 WebP Images Optimizer

Simple light WebP images converter for Magento 2 with almost no overhead in response time.
Parses html/json before sending it to client and replaces jpg|png images with webp format. 
Not yet converted images are added to queue for conversion and converted by cron.
For some older browsers that [doesn't support webp](https://caniuse.com/webp), fallback js is used to load original images.

- process html
- process x-magento-init blocks (images gallery, swatches, etc.)
- process json responses (like cart, etc)
- fallback for old browsers
- works well with fotorama image gallery, swatches, ajax cart etc. as it is extensions independent

## Installation

```bash
composer require kudja/magento2-webp
bin/magento module:enable Kudja_Webp
bin/magento setup:upgrade
```

The only thing you need to install on your server is `cwebp`. You can find it in the [webp](https://developers.google.com/speed/webp/download) package.

Ubuntu/Debian:

```bash
apt-get install webp
```

## Remove generated images

```bash
find -L pub/media -type f \( -iname "*.jpg.webp" -o -iname "*.jpeg.webp" -o -iname "*.png.webp" \) -delete
```
