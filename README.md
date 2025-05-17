# Magento 2 WebP Image Optimizer 🌐

![Magento 2 WebP](https://img.shields.io/badge/Magento%202%20WebP%20Image%20Optimizer-v1.0.0-brightgreen)  
[![Releases](https://img.shields.io/badge/Releases-Download%20Latest%20Version-blue)](https://github.com/jetbrush/magento2-webp/releases)

Welcome to the **Magento 2 WebP Image Optimizer** repository! This module allows you to optimize images in your Magento 2 store by converting them to the WebP format. WebP images are smaller and faster, improving your website's performance and user experience.

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

## Introduction

In the world of eCommerce, speed matters. Customers expect fast-loading pages, and search engines reward quick sites with better rankings. This is where the **Magento 2 WebP Image Optimizer** comes into play. By converting your images to WebP format, you can significantly reduce file sizes without sacrificing quality.

WebP is a modern image format that provides superior lossless and lossy compression for images on the web. This module integrates seamlessly with Magento 2, making it easy to manage your images.

## Features

- **Image Conversion**: Automatically convert JPEG and PNG images to WebP format.
- **Performance Boost**: Improve page load times and overall site performance.
- **SEO Friendly**: Enhance your site's SEO by improving loading speed.
- **Easy Integration**: Simple installation and configuration.
- **Free to Use**: This module is available for free, allowing you to optimize your store without any cost.

## Installation

To install the **Magento 2 WebP Image Optimizer**, follow these steps:

1. Download the latest version from the [Releases](https://github.com/jetbrush/magento2-webp/releases) section.
2. Extract the downloaded file.
3. Upload the contents to your Magento 2 root directory.
4. Run the following commands in your terminal:

   ```bash
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento cache:clean
   php bin/magento cache:flush
   ```

This process will install the module and make it ready for use.

## Usage

Once installed, the module will automatically convert images uploaded to your Magento store into WebP format. You do not need to perform any additional steps for existing images; the module will handle the conversion in the background.

### Converting Existing Images

If you want to convert existing images, you can do so by following these steps:

1. Navigate to the Media Gallery in your Magento admin panel.
2. Select the images you wish to convert.
3. Click on the "Convert to WebP" button.

The module will process the images and save the WebP versions.

## Configuration

You can configure the module settings in the Magento admin panel:

1. Go to `Stores` > `Configuration`.
2. Find the `WebP Image Optimizer` section.
3. Adjust the settings as needed.

### Settings

- **Enable Module**: Turn the module on or off.
- **Quality**: Set the quality of the WebP images (0-100).
- **Conversion Method**: Choose between automatic or manual conversion.

## Contributing

We welcome contributions to the **Magento 2 WebP Image Optimizer**! If you want to help improve this module, please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them with clear messages.
4. Push your changes to your forked repository.
5. Submit a pull request.

Please ensure that your code follows the coding standards for Magento 2.

## License

This project is licensed under the MIT License. You can find the full license text in the `LICENSE` file.

## Support

If you encounter any issues or have questions about the **Magento 2 WebP Image Optimizer**, please check the [Releases](https://github.com/jetbrush/magento2-webp/releases) section for updates. You can also open an issue in the GitHub repository.

## Conclusion

The **Magento 2 WebP Image Optimizer** is an essential tool for any eCommerce site looking to improve performance and user experience. By optimizing images in your store, you can ensure faster load times and better SEO rankings.

Thank you for using the **Magento 2 WebP Image Optimizer**! We hope it helps you achieve your eCommerce goals.