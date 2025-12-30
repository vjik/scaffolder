<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use Symfony\Component\Console\SingleCommandApplication;

final readonly class Runner
{
    /**
     * @param list<Change> $changes
     * @param list<class-string<Fact<*>>> $factClasses
     * @param array<string, string> $defaults
     */
    public function __construct(
        private array $changes = [],
        private array $factClasses = [],
        private array $defaults = [],
    ) {
    }

    public function run(): void
    {
        $app = new SingleCommandApplication()
            ->addArgument('directory', default: getcwd());

        $factClasses = [
            ...$this->factClasses,
            ...[
                Fact\ComposerJson::class,
                Fact\CopyrightHolder::class,
                Fact\CopyrightYear::class,
                Fact\PhpConstraint::class,
                Fact\UserName::class,
            ],
        ];
        foreach ($factClasses as $factClass) {
            $factClass::configureCommand($app, $this->defaults);
        }

        $command = new Command($this->changes, $this->defaults);

        $app
            ->setCode($command)
            ->run();
    }
}
