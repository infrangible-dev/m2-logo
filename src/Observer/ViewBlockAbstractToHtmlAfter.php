<?php

declare(strict_types=1);

namespace Infrangible\Logo\Observer;

use Infrangible\Core\Helper\Stores;
use Infrangible\Logo\ViewModel\Block\Html\Header\MobileLogoPathResolver;
use Infrangible\Logo\ViewModel\Block\Html\Header\MobileLogoSizeResolver;
use Infrangible\Logo\ViewModel\Block\Html\Header\TabletLogoPathResolver;
use Infrangible\Logo\ViewModel\Block\Html\Header\TabletLogoSizeResolver;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Theme\Block\Html\Header\Logo;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ViewBlockAbstractToHtmlAfter implements ObserverInterface
{
    /** @var Stores */
    protected $storesHelper;

    /** @var TabletLogoPathResolver */
    protected $tabletLogoPathResolver;

    /** @var TabletLogoSizeResolver */
    protected $tabletLogoSizeResolver;

    /** @var MobileLogoPathResolver */
    protected $mobileLogoPathResolver;

    /** @var MobileLogoSizeResolver */
    protected $mobileLogoSizeResolver;

    public function __construct(
        Stores $storesHelper,
        TabletLogoPathResolver $tabletPathResolver,
        TabletLogoSizeResolver $tabletSizeResolver,
        MobileLogoPathResolver $logoPathResolver,
        MobileLogoSizeResolver $logoSizeResolver
    ) {
        $this->storesHelper = $storesHelper;

        $this->tabletLogoPathResolver = $tabletPathResolver;
        $this->tabletLogoSizeResolver = $tabletSizeResolver;
        $this->mobileLogoPathResolver = $logoPathResolver;
        $this->mobileLogoSizeResolver = $logoSizeResolver;
    }

    /**
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var Logo $block */
        $block = $observer->getEvent()->getData('block');

        if ($block instanceof Logo && $block->getNameInLayout() === 'logo') {
            if ($this->storesHelper->getStoreConfig('design/header/tablet_logo_src')) {
                /** @var Logo $logoBlock */
                $logoBlock = $block->getLayout()->createBlock(Logo::class);

                $logoBlock->setTemplate('Infrangible_Logo::html/header/tablet_logo.phtml');
                $logoBlock->setData(
                    'logoPathResolver',
                    $this->tabletLogoPathResolver
                );
                $logoBlock->setData(
                    'logo_size_resolver',
                    $this->tabletLogoSizeResolver
                );

                /** @var DataObject $transport */
                $transport = $observer->getEvent()->getData('transport');

                $html = $transport->getData('html');

                $html .= $logoBlock->toHtml();

                $transport->setData(
                    'html',
                    $html
                );
            }

            if ($this->storesHelper->getStoreConfig('design/header/mobile_logo_src')) {
                /** @var Logo $logoBlock */
                $logoBlock = $block->getLayout()->createBlock(Logo::class);

                $logoBlock->setTemplate('Infrangible_Logo::html/header/mobile_logo.phtml');
                $logoBlock->setData(
                    'logoPathResolver',
                    $this->mobileLogoPathResolver
                );
                $logoBlock->setData(
                    'logo_size_resolver',
                    $this->mobileLogoSizeResolver
                );

                /** @var DataObject $transport */
                $transport = $observer->getEvent()->getData('transport');

                $html = $transport->getData('html');

                $html .= $logoBlock->toHtml();

                $transport->setData(
                    'html',
                    $html
                );
            }
        }
    }
}
