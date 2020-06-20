<?php declare(strict_types=1);

namespace Mrself\Version;

use Cz\Git\GitRepository;

/**
 * @see NewVersion::getHelpMessage()
 */
class NewVersion
{
    /**
     * @var GitRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $newVersion;

    public function __construct(string $repositoryDir, string $newVersion)
    {
        $this->defineRepository($repositoryDir);
        $this->newVersion = $newVersion;
    }

    public static function getHelpMessage()
    {
        return "Creates a new version (tab) in a given repository.\n
        Can be a dotted version like 2.1.3 or a version update name: 'major', 'minor', 'patch.\n\n
        If a dotted version is passed, create a tag with just this exact name. If a named version is passed, the repository version is updated respectively";
    }

    public function new()
    {
        $this->repository->checkout('master');
        $this->repository->merge('develop');

        $this->repository->createTag($this->defineTag());

        $this->repository->push('origin', ['master']);
        $this->repository->push('origin', ['--tags']);

        $this->repository->checkout('develop');
        $this->repository->push('origin', ['develop']);
    }

    private function ensureRepositoryExists(string $repositoryDir)
    {
        if (!is_dir($repositoryDir . DIRECTORY_SEPARATOR . '.git')) {
            throw new \RuntimeException('The repository does not exist');
        }
    }

    private function defineRepository(string $repositoryDir)
    {
        $this->ensureRepositoryExists($repositoryDir);
        $this->repository = new GitRepository($repositoryDir);
    }

    private function prepareVersion(string $current): string
    {
        return Version::fromString($current)->toVersion($this->newVersion);
    }

    private function getCurrent(): string
    {
        $tags = $this->repository->getTags();
        if (!$tags) {
            return '1.0.0';
        }

        $lastTag = reset($tags);
        return str_replace('v', '', $lastTag);
    }

    private function defineTag(): string
    {
        $current = $this->getCurrent();
        return 'v' . $this->prepareVersion($current);
    }
}