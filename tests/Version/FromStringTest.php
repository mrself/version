<?php declare(strict_types=1);

namespace Mrself\Version\Tests\Version;

use Mrself\Version\EInvalidArrayVersion;
use Mrself\Version\Version;
use PHPUnit\Framework\TestCase;

class FromStringTest extends TestCase
{
    public function testExceptionIsThrownIfSomeNameIsMissing()
    {
        $this->expectException(EInvalidArrayVersion::class);
        Version::fromString('1.2');
    }

    public function testExceptionIsThrownIfSomeValueIsNotANumber()
    {
        $this->expectException(EInvalidArrayVersion::class);
        Version::fromString('1.nonANumber');
    }

    public function testExceptionIsThrownIfVersionIsEmptyString()
    {
        $this->expectException(EInvalidArrayVersion::class);
        Version::fromString('');
    }

    public function testInstanceContainsAllVersionNames()
    {
        $version = Version::fromString('1.2.3');

        $versionMap = [
            'major' => 1,
            'minor' => 2,
            'patch' => 3
        ];
        foreach ($versionMap as $name => $numericPart) {
            $this->assertEquals($numericPart, $version->$name);
        }
    }
}