<?php declare(strict_types=1);

namespace Mrself\Version\Tests;

use Mrself\Version\VersionBuilder;
use PHPUnit\Framework\TestCase;

class VersionBuilderTest extends TestCase
{
    public function testItCreatesNewVersionFromDottedByDotted()
    {
        $newVersion = VersionBuilder::fromDotted('1.2.3')
            ->toVersion('1.2.4');

        $this->assertEquals('1.2.4', $newVersion);
    }

    /**
     * @dataProvider getVersionMap
     * @param string $namedVersion
     * @param string $newVersion
     */
    public function testItCreatesNewVersionFromDottedByNamedVersion(
        string $namedVersion,
        string $newVersion
    ) {
        $result = VersionBuilder::fromDotted('1.2.3')
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