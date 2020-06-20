<?php declare(strict_types=1);

namespace Mrself\Version;

class EInvalidVersion extends \Exception
{
    public static function emptyValue()
    {
        return new static('The passed version is empty');
    }

    public static function notString()
    {
        return new static('The passed array version is not a string');
    }

    public static function notNumericParts()
    {
        return new static('The parts of dotted version should be numeric');
    }

    public static function invalidNames()
    {
        return new static('The names (keys in array) should match semver names');
    }
}