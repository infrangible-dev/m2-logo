<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\Logo\Test\Unit\Observer;

use Infrangible\Logo\Observer\ViewBlockAbstractToHtmlAfter;
use Infrangible\Logo\ViewModel\Block\Html\Header\DeviceLogoPathResolver;
use Infrangible\Logo\ViewModel\Block\Html\Header\DeviceLogoPathResolverFactory;
use Infrangible\Logo\ViewModel\Block\Html\Header\DeviceLogoSizeResolver;
use Infrangible\Logo\ViewModel\Block\Html\Header\DeviceLogoSizeResolverFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Theme\Block\Html\Header\Logo;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests adding the logos of the devices after the standard logo.
 */
#[AllowMockObjectsWithoutExpectations]
class ViewBlockAbstractToHtmlAfterTest extends TestCase
{
    /** @var list<Logo> the created logo blocks */
    private array $createdBlocks = [];

    /**
     * Returns the observer with logos uploaded for the devices.
     *
     * @param list<string> $devices
     *
     * @return ViewBlockAbstractToHtmlAfter
     */
    private function createObserver(array $devices): ViewBlockAbstractToHtmlAfter
    {
        $pathResolverFactory = $this->createStub(DeviceLogoPathResolverFactory::class);
        $pathResolverFactory->method('create')->willReturnCallback(
            fn(array $data): DeviceLogoPathResolver => $this->createPathResolver(
                in_array($data[ 'device' ], $devices, true) ? 'logo/' . $data[ 'device' ] . '.png' : null
            )
        );

        $sizeResolver = $this->createStub(DeviceLogoSizeResolver::class);
        $sizeResolverFactory = $this->createStub(DeviceLogoSizeResolverFactory::class);
        $sizeResolverFactory->method('create')->willReturn($sizeResolver);

        return new ViewBlockAbstractToHtmlAfter($pathResolverFactory, $sizeResolverFactory);
    }

    /**
     * Returns a path resolver with the path of the logo.
     *
     * @param string|null $path
     *
     * @return DeviceLogoPathResolver
     */
    private function createPathResolver(?string $path): DeviceLogoPathResolver
    {
        $pathResolver = $this->createStub(DeviceLogoPathResolver::class);
        $pathResolver->method('getPath')->willReturn($path);

        return $pathResolver;
    }

    /**
     * Returns a logo block whose layout creates logo blocks that render their device.
     *
     * @param string $name
     *
     * @return Logo
     */
    private function createLogoBlock(string $name): Logo
    {
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('createBlock')->willReturnCallback(fn(): Logo => $this->createDeviceLogoBlock());

        $block = $this->getMockBuilder(Logo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getNameInLayout', 'getLayout'])
            ->getMock();
        $block->method('getNameInLayout')->willReturn($name);
        $block->method('getLayout')->willReturn($layout);

        return $block;
    }

    /**
     * Returns a logo block that renders its device and adds it to the list of created blocks.
     *
     * @return Logo
     */
    private function createDeviceLogoBlock(): Logo
    {
        $block = $this->getMockBuilder(Logo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toHtml'])
            ->getMock();
        $block->method('toHtml')->willReturnCallback(
            function () use ($block): string {
                $device = $block->getData('device');

                return '<a class="logo ' . (is_string($device) ? $device : '') . '"></a>';
            }
        );

        $this->createdBlocks[] = $block;

        return $block;
    }

    /**
     * Runs the observer for the block and returns the HTML of the transport.
     *
     * @param ViewBlockAbstractToHtmlAfter $observer
     * @param Template $block
     *
     * @return mixed
     * @throws LocalizedException
     */
    private function renderLogo(ViewBlockAbstractToHtmlAfter $observer, Template $block): mixed
    {
        $transport = new DataObject(['html' => '<a class="logo"></a>']);

        $observer->execute(new Observer(['event' => new Event(['block' => $block, 'transport' => $transport])]));

        return $transport->getData('html');
    }

    /**
     * @throws LocalizedException
     */
    public function testLogosOfDevicesAfterStandardLogo(): void
    {
        $html = $this->renderLogo($this->createObserver(['tablet', 'mobile']), $this->createLogoBlock('logo'));

        $this->assertSame('<a class="logo"></a><a class="logo tablet"></a><a class="logo mobile"></a>', $html);
        $this->assertCount(2, $this->createdBlocks);

        foreach ($this->createdBlocks as $block) {
            $this->assertSame(ViewBlockAbstractToHtmlAfter::TEMPLATE, $block->getTemplate());
            $this->assertInstanceOf(DeviceLogoPathResolver::class, $block->getData('logoPathResolver'));
            $this->assertInstanceOf(DeviceLogoSizeResolver::class, $block->getData('logo_size_resolver'));
        }
    }

    /**
     * @throws LocalizedException
     */
    public function testOnlyUploadedLogos(): void
    {
        $html = $this->renderLogo($this->createObserver(['mobile']), $this->createLogoBlock('logo'));

        $this->assertSame('<a class="logo"></a><a class="logo mobile"></a>', $html);
    }

    /**
     * @throws LocalizedException
     */
    public function testOtherBlocksUnchanged(): void
    {
        $observer = $this->createObserver(['tablet', 'mobile']);

        $this->assertSame('<a class="logo"></a>', $this->renderLogo($observer, $this->createLogoBlock('other.logo')));
        $this->assertSame('<a class="logo"></a>', $this->renderLogo($observer, $this->createStub(Template::class)));
        $this->assertSame([], $this->createdBlocks);
    }
}
