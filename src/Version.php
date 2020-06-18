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
            throw EInvalidArrayVersion::notString();
        }

        if (!$source) {
            throw EInvalidArrayVersion::emptyValue();
        }

        $parts = explode(static::DOTTED_VERSION_DELIMITER, $source);
        $instance->ensureValidCount($parts);

        $version = array_combine(static::ALLOWED_NAMED_VALUES, $parts);
        $instance->major = (int) $version['major'];
        $instance->minor = (int) $version['minor'];
        $instance->patch = (int) $version['patch'];

        return $instance;
    }

    public static function fromArray(array $versionArray)
    {
        $instance = new static();

        $instance->ensureValidCount($versionArray);
        $instance->ensureValidNames($versionArray);

        return $instance;
    }

    public function toDotted()
    {
        return $this->major . '.' . $this->minor . '.' .$this->patch;
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
            throw new EInvalidArrayVersion($error->__toString());
        }
    }

    /**
     * Checks if all values in $parts are numbers
     * @param array $parts
     * @throws EInvalidArrayVersion
     */
    private function ensureValidCount(array $parts)
    {
        $filtered = array_filter($parts, 'is_numeric');
        if (count($filtered) !== 3) {
            throw EInvalidArrayVersion::notNumericParts();
        }
    }

    /**
     * Checks if the keys of $versionArray are valid version names.
     * @see Version::ALLOWED_NAMED_VALUES
     * @param array $versionArray
     * @throws EInvalidArrayVersion
     */
    private function ensureValidNames(array $versionArray)
    {
        $keys = array_keys($versionArray);
        if (array_diff($keys, static::ALLOWED_NAMED_VALUES)) {
            throw EInvalidArrayVersion::invalidNames();
        }
    }
}