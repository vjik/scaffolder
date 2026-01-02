<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use Symfony\Component\Console\SingleCommandApplication;

final readonly class Runner
{
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
            ...[
                Fact\ComposerJson::class,
                Fact\CopyrightHolder::class,
                Fact\CopyrightYear::class,
                Fact\PhpConstraint::class,
                Fact\UserName::class,
            ],
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
