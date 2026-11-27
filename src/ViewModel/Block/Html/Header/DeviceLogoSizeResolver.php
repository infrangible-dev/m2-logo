<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\Logo\ViewModel\Block\Html\Header;

use Infrangible\Core\Helper\Stores;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Theme\ViewModel\Block\Html\Header\LogoSizeResolverInterface;

/**
 * The width and height of the logo of a device, as set in the design configuration.
 */
class DeviceLogoSizeResolver implements LogoSizeResolverInterface, ArgumentInterface
{
    /**
     * @param Stores $storesHelper
     * @param string $device "tablet" or "mobile"
     */
    public function __construct(protected Stores $storesHelper, protected string $device)
    {
    }

    /**
     * Returns the width of the logo of the device in pixels, null if none is set.
     *
     * @param int|null $storeId
     *
     * @return int|null
     */
    public function getWidth(?int $storeId = null): ?int
    {
        return $this->getSize('width', $storeId);
    }

    /**
     * Returns the height of the logo of the device in pixels, null if none is set.
     *
     * @param int|null $storeId
     *
     * @return int|null
     */
    public function getHeight(?int $storeId = null): ?int
    {
        return $this->getSize('height', $storeId);
    }

    /**
     * Returns the width or height of the logo of the device, null if none is set.
     *
     * @param string $dimension "width" or "height"
     * @param int|null $storeId
     *
     * @return int|null
     */
    private function getSize(string $dimension, ?int $storeId): ?int
    {
        $size = $this->storesHelper->getStoreConfig(
            sprintf('design/header/%s_logo_%s', $this->device, $dimension),
            null,
            false,
            $storeId
        );

        return is_numeric($size) && (int)$size > 0 ? (int)$size : null;
    }
}
