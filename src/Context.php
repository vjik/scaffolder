<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use Stringable;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

use function array_key_exists;

final class Context
{
    private readonly Filesystem $filesystem;

    /**
     * @var array<class-string<Fact>, mixed>
     */
    private array $facts = [];

    /**
     * @param array<string, string> $defaults
     */
    public function __construct(
        private readonly string $directory,
        private readonly Cli $cli,
        private readonly array $defaults,
    ) {
        $this->filesystem = new Filesystem();
    }

    public function fileExists(string $file): bool
    {
        return $this->filesystem->exists(
            $this->locateFile($file),
        );
    }

    /**
     * @throws IOException If the file cannot be read.
     */
    public function readFile(string $file): string
    {
        return $this->filesystem->readFile(
            $this->locateFile($file),
        );
    }

    public function tryReadFile(string $file): ?string
    {
        try {
            return $this->readFile($file);
        } catch (IOException) {
            return null;
        }
    }

    public function writeFile(string $file, string|Stringable $content = ''): void
    {
        $this->filesystem->dumpFile(
            $this->locateFile($file),
            $content,
        );
    }

    public function copyFile(string $origin, string $target): void
    {
        $this->filesystem->copy(
            originFile: $this->locateFile($origin),
            targetFile: $this->locateFile($target),
            overwriteNewerFiles: true,
        );
    }

    /**
     * @template T
     * @param class-string<Fact<T>> $factClass
     * @return T
     */
    public function getFact(string $factClass): mixed
    {
        if (array_key_exists($factClass, $this->facts)) {
            return $this->facts[$factClass];
        }

        return $this->facts[$factClass] = $factClass::resolve($this->cli, $this);
    }

    public function hasFact(string $factClass): bool
    {
        return array_key_exists($factClass, $this->facts);
    }

    public function getDefault(string $key): ?string
    {
        return $this->defaults[$key] ?? null;
    }

    /**
     * @param non-empty-string $command
     */
    public function execute(string $command, bool $throwExceptionOnError = true): ExecuteResult
    {
        exec("cd $this->directory && $command", $lines, $code);
        $result = new ExecuteResult($command, $lines, $code);
        if ($throwExceptionOnError && !$result->isSuccess()) {
            $result->throwRuntimeException();
        }
        return $result;
    }

    private function locateFile(string $path): string
    {
        if ($this->filesystem->isAbsolutePath($path)) {
            return $path;
        }

        return $this->directory . '/' . $path;
    }
}
