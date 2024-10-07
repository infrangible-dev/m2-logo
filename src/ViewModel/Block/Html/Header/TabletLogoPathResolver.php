<?php

declare(strict_types=1);

namespace Infrangible\Logo\ViewModel\Block\Html\Header;

use Infrangible\Core\Helper\Stores;
use Magento\Config\Model\Config\Backend\Image\Logo;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Theme\ViewModel\Block\Html\Header\LogoPathResolverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class TabletLogoPathResolver implements LogoPathResolverInterface, ArgumentInterface
{
    /** @var Stores */
    protected $storesHelper;

    public function __construct(Stores $storesHelper)
    {
        $this->storesHelper = $storesHelper;
    }

    public function getPath(): ?string
    {
        $path = null;

        $storeLogoPath = $this->storesHelper->getStoreConfig('design/header/tablet_logo_src');

        if ($storeLogoPath !== null) {
            $path = Logo::UPLOAD_DIR . '/' . $storeLogoPath;
        }

        return $path;
    }
}
