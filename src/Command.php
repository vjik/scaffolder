<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use RuntimeException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function sprintf;

final readonly class Command
{
    /**
     * @param list<Change> $changes
     */
    public function __construct(
        private array $changes,
    ) {
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $cli = new Cli($input, $output);
        $context = $this->createContext($input, $cli);

        $appliers = array_filter(
            array_map(
                static fn(Change $change) => $change->decide($context),
                $this->changes,
            ),
        );

        if ($appliers === []) {
            $cli->success('No changes required.');
            return SymfonyCommand::SUCCESS;
        }

        foreach ($appliers as $applier) {
            $applier($cli);
        }

        $cli->success(
            sprintf(
                'Applied %d change%s.',
                count($appliers),
                count($appliers) === 1 ? '' : 's',
            ),
        );
        return SymfonyCommand::SUCCESS;
    }

    private function createContext(InputInterface $input, Cli $cli): Context
    {
        $dir = $input->getArgument('directory');
        if (!is_dir($dir)) {
            throw new RuntimeException(
                sprintf('Directory `%s` does not exist.', $dir)
            );
        }

        return new Context($dir, $cli);
    }
}
