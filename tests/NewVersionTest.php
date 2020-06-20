<?php declare(strict_types=1);

namespace Mrself\Version\Tests;

use Cz\Git\GitRepository;
use Mrself\Version\NewVersion;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class NewVersionTest extends TestCase
{
    /**
     * @var string
     */
    private $currentRepositoryDir;

    /**
     * @var string
     */
    private $originRepositoryDir;

    public function testItCreatesCorrectDottedTag()
    {
        $newVersion = new NewVersion($this->currentRepositoryDir, '1.0.0');
        $newVersion->new();

        $currentRepository = new GitRepository($this->currentRepositoryDir);
        $tags = $currentRepository->getTags();
        $this->assertEquals('v1.0.0', $tags[0]);
    }

    public function testItCreatesCorrectTagByNameIfRepositoryDoesNotHaveTags()
    {
        $newVersion = new NewVersion($this->currentRepositoryDir, 'major');
        $newVersion->new();

        $currentRepository = new GitRepository($this->currentRepositoryDir);
        $tags = $currentRepository->getTags();
        $this->assertEquals('v2.0.0', $tags[0]);
    }

    public function testItCreatesCorrectTagByNameIfRepositoryHasTag()
    {
        $repository = new GitRepository($this->currentRepositoryDir);
        $repository->createTag('v1.2.3');

        $newVersion = new NewVersion($this->currentRepositoryDir, 'minor');
        $newVersion->new();

        $currentRepository = new GitRepository($this->currentRepositoryDir);
        $tags = $currentRepository->getTags();
        $this->assertEquals('v1.3.0', $tags[1]);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->initRepository('origin');
        $this->initRepository('current');
    }

    private function initRepository(string $type)
    {
        $repositoryProperty = $type . 'RepositoryDir';
        $this->$repositoryProperty = Bootstrap::getTmpDir() . '/git/repository/' . $type;

        $this->ensureDirectoryExists($this->$repositoryProperty);
        $this->prepareRepository($this->$repositoryProperty);
    }

    private function ensureDirectoryExists($dir)
    {
        (new Filesystem())->remove($dir . '/.git');
        (new Filesystem())->remove($dir);
        mkdir($dir, 0755, true);
        file_put_contents($dir . '/init_file', '');
    }

    private function prepareRepository($directory)
    {
        $repository = GitRepository::init($directory);
        $repository->addAllChanges();
        $repository->commit('init');
        $repository->createBranch('develop');
        $repository->addRemote('origin', $this->originRepositoryDir);
    }
}