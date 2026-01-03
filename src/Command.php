<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use RuntimeException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function in_array;
use function is_array;
use function sprintf;

/**
 * @phpstan-import-type ApplierCallable from Change
 */
final readonly class Command
{
    /**
     * @param array<Change|list<Change>> $changes
     */
    public function __construct(
        private array $changes,
        private Params $params,
    ) {}

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $cli = new Cli($input, $output);
        $context = $this->createContext($input, $cli);

        $appliers = [];
        $changes = $this->prepareChanges($context);
        foreach ($changes as $change) {
            $applier = $change->decide($context);
            if ($applier === null) {
                continue;
            }
            if (is_array($applier)) {
                /** @var list<ApplierCallable> $applier */
                $appliers = array_merge($appliers, $applier);
            } else {
                $appliers[] = $applier;
            }
        }

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
        /** @var string $directory */
        $directory = $input->getOption('directory');
        if (!is_dir($directory)) {
            throw new RuntimeException(
                sprintf('Directory `%s` does not exist.', $directory),
            );
        }

        return new Context($directory, $cli, $this->params);
    }

    /**
     * @return list<Change>
     */
    private function prepareChanges(Context $context): array
    {
        $changes = [];

        /** @var list<string> $disabledChanges */
        $disabledChanges = $context->getParam('disable', []);

        foreach ($this->changes as $name => $change) {
            if (in_array($name, $disabledChanges, true)) {
                continue;
            }
            if (is_array($change)) {
                $changes = array_merge($changes, $change);
            } else {
                $changes[] = $change;
            }
        }

        return $changes;
    }
}
