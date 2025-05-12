# Magento 2 WebP Image Optimizer

A lightweight Magento 2 module that automatically replaces `.jpg` and `.png` images with `.webp` equivalents in HTML and JSON responses.  
Conversion happens in the background, ensuring fast page loads and compatibility with all modern and legacy browsers.

## Highlights

- **Seamless Integration**: Replaces image URLs in page output using efficient HTML/JSON parsing. No template overrides or theme changes required.
- **Performance-Oriented**: Designed to operate with negligible impact on response times, ensuring a seamless user experience.
- **Asynchronous WebP Conversion**: Original images are queued for background conversion via cron jobs — no delay for users.
- **Frontend Compatibility**: Works with Magento core features like fotorama, swatches, and ajax cart, sections etc.
- **Third-Party Extensions**: Designed to work independently, minimizing conflicts with other modules.
- **Browser Support**: Automatically serves original images to browsers lacking WebP support via a JavaScript fallback.

## Installation

```bash
composer require kudja/magento2-webp
bin/magento module:enable Kudja_Webp
bin/magento setup:upgrade
```

### Install `cwebp` 

Ubuntu/Debian:
```bash
sudo apt install webp
```


