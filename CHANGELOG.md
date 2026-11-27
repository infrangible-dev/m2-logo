# Changelog

## 2.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.1 or newer. Older versions of Magento and PHP 7.1 to 8.0 are no longer
  supported; use version 1.x for these.
- Requires `infrangible/m2-core` 5.0 and `infrangible/m2-foundation` 3.0.
- One path resolver and one size resolver for both devices, `DeviceLogoPathResolver` and `DeviceLogoSizeResolver` with
  the device as constructor argument, instead of `TabletLogoPathResolver`, `TabletLogoSizeResolver`,
  `MobileLogoPathResolver` and `MobileLogoSizeResolver`.
- One template `html/header/device_logo.phtml` for both devices instead of `tablet_logo.phtml` and `mobile_logo.phtml`.
- The observer creates the resolvers with their factories; it has native types and uses constructor property
  promotion.

### Fixed

- A width or height set for the tablet or mobile logo broke the page: the setting was returned as text where a number
  was declared.
- The styles picked the logos by their position in the header, so with only a tablet logo, smartphones showed the
  tablet logo and tablets the standard logo. The styles now pick the logos by their classes `tablet` and `mobile`:
  tablets show the tablet logo, smartphones the mobile logo and, if there is none, the tablet logo.
- Screens of 768 pixels showed the mobile logo, although Magento's themes show the header of larger screens from 768
  pixels on. Tablets now start at 768 pixels, like the breakpoint of the themes.

### Added

- Translations into 24 languages.
- Unit tests.
