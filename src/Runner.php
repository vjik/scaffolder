<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\SingleCommandApplication;

final readonly class Runner
{
    /**
     * @var list<class-string<Fact<*>>>
     */
    private const array BUILT_IN_FACT_CLASSES = [
        Fact\ComposerJson::class,
        Fact\PackageAuthors::class,
        Fact\CopyrightHolder::class,
        Fact\CopyrightYear::class,
        Fact\PackageVendor::class,
        Fact\PackageProject::class,
        Fact\PackageName::class,
        Fact\LowestMinorPhpVersion::class,
        Fact\HighestMinorPhpVersion::class,
        Fact\NamespaceX::class,
        Fact\PackageDescription::class,
        Fact\PackageLicense::class,
        Fact\PackageType::class,
        Fact\PhpConstraint::class,
        Fact\PhpConstraintName::class,
        Fact\PrepareComposerAutoload::class,
        Fact\PrepareComposerAutoloadDev::class,
        Fact\SourceDirectory::class,
        Fact\TestsDirectory::class,
        Fact\Title::class,
        Fact\UserName::class,
        Fact\UserEmail::class,
    ];

    /**
     * @param list<Change> $changes
     * @param list<class-string<Fact<*>>> $factClasses
     * @param array<string, mixed> $params
     */
    public function __construct(
        private array $changes = [],
        private array $factClasses = [],
        private array $params = [],
    ) {}

    public function run(): void
    {
        $params = $this->createParams();

        $app = new SingleCommandApplication()
            ->addOption('directory', mode: InputOption::VALUE_OPTIONAL, default: getcwd())
            ->addOption('scaffolder-file', mode: InputOption::VALUE_OPTIONAL, default: 'scaffolder.php');

        $factClasses = [
            ...$this->factClasses,
            ...self::BUILT_IN_FACT_CLASSES,
        ];
        foreach ($factClasses as $factClass) {
            $factClass::configureCommand($app, $params);
        }

        $command = new Command($this->changes, $params);

        $app
            ->setCode($command)
            ->run();
    }

    private function createParams(): Params
    {
        $input = new ArgvInput(
            definition: new InputDefinition([
                new InputOption('directory', mode: InputOption::VALUE_OPTIONAL, default: getcwd()),
                new InputOption('scaffolder-file', mode: InputOption::VALUE_OPTIONAL, default: 'scaffolder.php'),
            ]),
        );

        /** @var string $directory */
        $directory = $input->getOption('directory');

        /** @var string $fileName */
        $fileName = $input->getOption('scaffolder-file');

        $scaffolderFile = $directory . '/' . $fileName;

        if (file_exists($scaffolderFile)) {
            /** @var array<string, mixed> $paramsFromFile */
            $paramsFromFile = require $scaffolderFile;
            return new Params(array_merge($this->params, $paramsFromFile));
        }

        return new Params($this->params);
    }
}
