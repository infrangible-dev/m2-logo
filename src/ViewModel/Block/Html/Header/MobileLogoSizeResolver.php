<?php

declare(strict_types=1);

namespace Infrangible\Logo\ViewModel\Block\Html\Header;

use Infrangible\Core\Helper\Stores;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Theme\ViewModel\Block\Html\Header\LogoSizeResolverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class MobileLogoSizeResolver implements LogoSizeResolverInterface, ArgumentInterface
{
    /** @var Stores */
    protected $storesHelper;

    public function __construct(Stores $storesHelper)
    {
        $this->storesHelper = $storesHelper;
    }

    public function getWidth(?int $storeId = null): ?int
    {
        return $this->storesHelper->getStoreConfig('design/header/mobile_logo_width');
    }

    public function getHeight(?int $storeId = null): ?int
    {
        return $this->storesHelper->getStoreConfig('design/header/mobile_logo_height');
    }
}
