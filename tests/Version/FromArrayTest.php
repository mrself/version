<?php declare(strict_types=1);

namespace Mrself\Version\Tests\Version;

use Mrself\Version\Version;
use Mrself\Version\EInvalidArrayVersion;
use PHPUnit\Framework\TestCase;

class FromArrayTest extends TestCase
{
    public function testExceptionIsThrownIfArrayHasInvalidCount()
    {
        $this->expectException(EInvalidArrayVersion::class);
        Version::fromArray([
            'major' => 1,
            'minor' => 2
        ]);
    }

    public function testExceptionIsThrownIfArrayHasInvalidNames()
    {
        $this->expectException(EInvalidArrayVersion::class);
        Version::fromArray([
            'major' => 1,
            'minor' => 2,
            'pathWithAMistake' => 3
        ]);
    }
}