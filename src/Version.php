<?php declare(strict_types=1);

namespace Mrself\Version;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;

class Version
{
    /**
     * The number of parts in a version like: "2.4.1"
     */
    public const ARRAY_PARTS_COUNT = 3;

    public const DOTTED_VERSION_DELIMITER = '.';

    /**
     * @var int
     */
    public $major;

    /**
     * @var int
     */
    public $minor;

    /**
     * @var int
     */
    public $patch;

    private const ALLOWED_NAMED_VALUES = ['major', 'minor', 'patch'];

    protected function __construct()
    {
    }

    public static function fromString($source)
    {
        $instance = new static();

        if (!is_string($source)) {
            throw EInvalidVersion::notString();
        }

        if (!$source) {
            throw EInvalidVersion::emptyValue();
        }

        $parts = explode(static::DOTTED_VERSION_DELIMITER, $source);
        $instance->ensureValidCount($parts);

        $version = array_combine(static::ALLOWED_NAMED_VALUES, $parts);
        $instance->major = (int) $version['major'];
        $instance->minor = (int) $version['minor'];
        $instance->patch = (int) $version['patch'];

        return $instance;
    }

    /**
     * Makes an instance from a named version array.
     * The array should have keys as Version::ALLOWED_NAMED_VALUES
     * and numeric values for each name (key)
     * @param array $versionArray
     * @return static
     * @throws EInvalidVersion
     *@see Version::ALLOWED_NAMED_VALUES
     */
    public static function fromArray(array $versionArray)
    {
        $instance = new static();

        $instance->ensureValidCount($versionArray);
        $instance->ensureValidNames($versionArray);

        return $instance;
    }

    public function isNamedVersion(string $version): bool
    {
        return in_array($version, static::ALLOWED_NAMED_VALUES);
    }

    /**
     * Makes a regular semver version like "1.2.3"
     * @return string
     */
    public function toDotted()
    {
        return $this->major . static::DOTTED_VERSION_DELIMITER
            . $this->minor . static::DOTTED_VERSION_DELIMITER
            . $this->patch;
    }

    private function validate($source)
    {
        $validator = VersionContainer::getInstance()->validator;
        $singleAssert = new Assert\All([
            new Assert\NotBlank(),
            new Assert\PositiveOrZero(),
        ]);
        $errors = $validator->validate($source, new Assert\Collection([
            'major' => $singleAssert,
            'minor' => $singleAssert,
            'patch' => $singleAssert
        ]));

        foreach ($errors as $error) {
            /** @var ConstraintViolation $error */
            throw new EInvalidVersion($error->__toString());
        }
    }

    /**
     * Checks if all values in $parts are numbers
     * @param array $parts
     * @throws EInvalidVersion
     */
    private function ensureValidCount(array $parts)
    {
        $filtered = array_filter($parts, 'is_numeric');
        if (count($filtered) !== 3) {
            throw EInvalidVersion::notNumericParts();
        }
    }

    /**
     * Checks if the keys of $versionArray are valid version names.
     * @param array $versionArray
     * @throws EInvalidVersion
     *@see Version::ALLOWED_NAMED_VALUES
     */
    private function ensureValidNames(array $versionArray)
    {
        $keys = array_keys($versionArray);
        if (array_diff($keys, static::ALLOWED_NAMED_VALUES)) {
            throw EInvalidVersion::invalidNames();
        }
    }

    public function updateByName(string $name)
    {
        $this->$name++;
        $index = array_search($name, static::ALLOWED_NAMED_VALUES);

        $nextVersions = array_slice(static::ALLOWED_NAMED_VALUES, $index + 1);
        foreach ($nextVersions as $version) {
            $this->$version = 0;
        }

        return $this;
    }

    public function toVersion($newVersion): string
    {
        if ($this->isNamedVersion($newVersion)) {
            return $this->updateByName($newVersion)->toDotted();
        }

        return $newVersion;
    }
}