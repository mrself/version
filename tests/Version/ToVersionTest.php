<?php declare(strict_types=1);

namespace Mrself\Version\Tests\Version;

use Mrself\Version\Version;
use PHPUnit\Framework\TestCase;

class ToVersionTest extends TestCase
{
    public function testItCreatesNewVersionFromDottedByDotted()
    {
        $newVersion = Version::fromString('1.2.3')
            ->toVersion('1.2.4');

        $this->assertEquals('1.2.4', $newVersion);
    }

    /**
     * @dataProvider getVersionMap
     * @param string $namedVersion
     * @param string $newVersion
     * @throws \Mrself\Version\EInvalidVersion
     */
    public function testItCreatesNewVersionFromDottedByNamedVersion(
        string $namedVersion,
        string $newVersion
    ) {
        $result = Version::fromString('1.2.3')
            ->toVersion($namedVersion);

        $this->assertEquals($newVersion, $result);
    }

    public function getVersionMap()
    {
        return [
            ['major', '2.0.0'],
            ['minor', '1.3.0'],
            ['patch', '1.2.4']
        ];
    }
}