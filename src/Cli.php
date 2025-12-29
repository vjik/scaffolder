<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final readonly class Cli
{
    private SymfonyStyle $style;

    public function __construct(
        private InputInterface $input,
        OutputInterface $output,
    ) {
        $this->style = new SymfonyStyle($input, $output);
    }

    public function getOption(string $name): mixed
    {
        return $this->input->getOption($name);
    }

    /**
     * @param non-empty-string $name
     * @param callable(): mixed $step
     */
    public function step(string $name, callable $step): void
    {
        $this->style->write($name . '...');
        $step();
        $this->style->writeln(' Done.');
    }

    /**
     * @no-named-arguments
     */
    public function success(string ...$lines): void
    {
        $this->style->success($lines);
    }

    /**
     * @template T = string
     * @param non-empty-string $question
     * @param (callable(string): T)|null $normalizer
     * @return T
     */
    public function ask(string $question, ?string $default = null, ?callable $normalizer = null): mixed
    {
        /** @var string $answer */
        $answer = $this->style->ask($question, $default) ?? '';

        if ($normalizer === null) {
            return $answer;
        }

        try {
            return $normalizer($answer);
        } catch (NormalizeUserInputException $exception) {
            $this->style->error($exception->getMessage());
            return $this->ask($question, $default, $normalizer);
        }
    }
}
