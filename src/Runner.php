<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

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
        Fact\NamespaceX::class,
        Fact\PackageDescription::class,
        Fact\PackageLicense::class,
        Fact\PackageType::class,
        Fact\PhpConstraint::class,
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
        $params = new Params($this->params);

        $app = new SingleCommandApplication()
            ->addArgument('directory', default: getcwd());

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
}
