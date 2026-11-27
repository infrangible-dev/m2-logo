# Infrangible Logo

Lets you upload separate logo images for tablets and smartphones in addition to the standard logo. The storefront shows
the logo that fits the screen of the visitor.

## Settings

Content > Design > Configuration, edit the theme of a website or store view, Other Settings > Header:

- **Tablet Logo Image** with **Tablet Logo Attribute Width** and **Tablet Logo Attribute Height**
- **Mobile Logo Image** with **Mobile Logo Attribute Width** and **Mobile Logo Attribute Height**

The images can be JPG, JPEG, GIF or PNG files, like the standard logo. Width and height are written into the image
tag; without them, the width and height set for the standard logo in the design configuration apply.

## Screens

- Smartphones (screens up to 767 pixels wide) show the mobile logo, else the tablet logo, else the standard logo.
- Tablets (768 to 1023 pixels) show the tablet logo, else the standard logo.
- Larger screens show the standard logo.

The breakpoints are those of Magento's Blank and Luma themes. The logos of the devices follow the standard logo in the
header as links with the classes `logo tablet` and `logo mobile`; the styles of the module show or hide them with
`:has()`, so themes with their own header can use the same classes.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.6 or newer
- PHP 8.1 or newer

## Installation

```bash
composer require infrangible/m2-logo
bin/magento module:enable Infrangible_Logo
bin/magento setup:upgrade
```

In production mode, also run `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy`.

## License

Infrangible Logo is licensed under the MIT License - see the LICENSE file for details.
