<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\Logo\ViewModel\Block\Html\Header;

use Infrangible\Core\Helper\Stores;
use Magento\Config\Model\Config\Backend\Image\Logo;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Theme\ViewModel\Block\Html\Header\LogoPathResolverInterface;

/**
 * The path of the uploaded logo of a device in the media directory, like Magento's resolver of the standard logo.
 */
class DeviceLogoPathResolver implements LogoPathResolverInterface, ArgumentInterface
{
    /**
     * @param Stores $storesHelper
     * @param string $device "tablet" or "mobile"
     */
    public function __construct(protected Stores $storesHelper, protected string $device)
    {
    }

    /**
     * Returns the path of the logo below the media directory, null if no logo is uploaded for the device.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        $logoPath = $this->storesHelper->getStoreConfig(sprintf('design/header/%s_logo_src', $this->device));

        return is_string($logoPath) && $logoPath !== '' ? Logo::UPLOAD_DIR . '/' . $logoPath : null;
    }
}
