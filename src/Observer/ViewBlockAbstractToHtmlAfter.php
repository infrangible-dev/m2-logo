<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\Logo\Observer;

use Infrangible\Logo\ViewModel\Block\Html\Header\DeviceLogoPathResolverFactory;
use Infrangible\Logo\ViewModel\Block\Html\Header\DeviceLogoSizeResolverFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Theme\Block\Html\Header\Logo;

/**
 * Adds the logos of tablets and smartphones after the standard logo of the header, if they are uploaded. The styles of
 * the module show each logo only on the screens of its device.
 */
class ViewBlockAbstractToHtmlAfter implements ObserverInterface
{
    /** The devices with an own logo, in the order of their logos after the standard logo. */
    public const DEVICES = ['tablet', 'mobile'];

    /** The template of the logos of the devices. */
    public const TEMPLATE = 'Infrangible_Logo::html/header/device_logo.phtml';

    /**
     * @param DeviceLogoPathResolverFactory $pathResolverFactory
     * @param DeviceLogoSizeResolverFactory $sizeResolverFactory
     */
    public function __construct(
        protected DeviceLogoPathResolverFactory $pathResolverFactory,
        protected DeviceLogoSizeResolverFactory $sizeResolverFactory
    ) {
    }

    /**
     * Adds the logos of the devices to the HTML of the standard logo.
     *
     * @param Observer $observer
     *
     * @return void
     * @throws LocalizedException if the logo block has no layout
     */
    public function execute(Observer $observer): void
    {
        $event = $observer->getEvent();
        $block = $event->getData('block');
        $transport = $event->getData('transport');

        if (! $block instanceof Logo || $block->getNameInLayout() !== 'logo' || ! $transport instanceof DataObject) {
            return;
        }

        $html = $transport->getData('html');
        $html = is_string($html) ? $html : '';

        foreach (self::DEVICES as $device) {
            $pathResolver = $this->pathResolverFactory->create(['device' => $device]);

            if ($pathResolver->getPath() === null) {
                continue;
            }

            $logoBlock = $block->getLayout()->createBlock(Logo::class);

            if (! $logoBlock instanceof Logo) {
                continue;
            }

            $logoBlock->setTemplate(self::TEMPLATE);
            $logoBlock->setData('device', $device);
            $logoBlock->setData('logoPathResolver', $pathResolver);
            $logoBlock->setData('logo_size_resolver', $this->sizeResolverFactory->create(['device' => $device]));

            $html .= $logoBlock->toHtml();
        }

        $transport->setData('html', $html);
    }
}
