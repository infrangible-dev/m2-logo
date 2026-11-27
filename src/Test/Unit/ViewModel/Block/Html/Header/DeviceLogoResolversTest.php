<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\Logo\Test\Unit\ViewModel\Block\Html\Header;

use Infrangible\Core\Helper\Stores;
use Infrangible\Logo\ViewModel\Block\Html\Header\DeviceLogoPathResolver;
use Infrangible\Logo\ViewModel\Block\Html\Header\DeviceLogoSizeResolver;
use PHPUnit\Framework\TestCase;

/**
 * Tests the path and the size of the logos of the devices from the design configuration.
 */
class DeviceLogoResolversTest extends TestCase
{
    /**
     * Returns the stores helper with the configuration values by path.
     *
     * @param array<string, mixed> $config
     *
     * @return Stores
     */
    private function createStoresHelper(array $config): Stores
    {
        $storesHelper = $this->createStub(Stores::class);
        $storesHelper->method('getStoreConfig')->willReturnCallback(
            fn(string $path): mixed => $config[ $path ] ?? null
        );

        return $storesHelper;
    }

    public function testPathOfUploadedLogo(): void
    {
        $storesHelper = $this->createStoresHelper(['design/header/tablet_logo_src' => 'stores/1/tablet.png']);

        $this->assertSame('logo/stores/1/tablet.png', (new DeviceLogoPathResolver($storesHelper, 'tablet'))->getPath());
        $this->assertNull((new DeviceLogoPathResolver($storesHelper, 'mobile'))->getPath());
    }

    public function testNoPathOfRemovedLogo(): void
    {
        $storesHelper = $this->createStoresHelper(['design/header/mobile_logo_src' => '']);

        $this->assertNull((new DeviceLogoPathResolver($storesHelper, 'mobile'))->getPath());
    }

    public function testSizeAsNumber(): void
    {
        $storesHelper = $this->createStoresHelper([
            'design/header/mobile_logo_width'  => '120',
            'design/header/mobile_logo_height' => '40'
        ]);

        $sizeResolver = new DeviceLogoSizeResolver($storesHelper, 'mobile');

        $this->assertSame(120, $sizeResolver->getWidth());
        $this->assertSame(40, $sizeResolver->getHeight());
    }

    public function testNoSizeWithoutValidValue(): void
    {
        $storesHelper = $this->createStoresHelper([
            'design/header/tablet_logo_width'  => '',
            'design/header/tablet_logo_height' => '0'
        ]);

        $sizeResolver = new DeviceLogoSizeResolver($storesHelper, 'tablet');

        $this->assertNull($sizeResolver->getWidth());
        $this->assertNull($sizeResolver->getHeight());
    }
}
