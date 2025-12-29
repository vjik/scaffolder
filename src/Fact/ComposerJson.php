<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use JsonException;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;

/**
 * @phpstan-type Autoload = array{"psr-4"?: array<non-empty-string, non-empty-string>}
 * @phpstan-type Author = array{name?: string, email?: string, homepage?: string, role?: string}
 * @phpstan-type Type = array{
 *     name?: non-empty-string,
 *     description?: string,
 *     type?: non-empty-string,
 *     license?: string|list<string>,
 *     require?: array<non-empty-string, non-empty-string>,
 *     "require-dev"?: array<non-empty-string, non-empty-string>,
 *     autoload?: Autoload,
 *     "autoload-dev"?: Autoload,
 *     config?: array{
 *         "sort-packages"?: bool,
 *         lock?: bool,
 *         "allow-plugins"?: array<non-empty-string, bool>,
 *         "platform"?: array<non-empty-string, non-empty-string>,
 *         "bump-after-update"?: bool|"dev"|"no-dev",
 *         ...
 *     },
 *     authors?: list<Author>,
 *     extra?: array<mixed>,
 *     scripts?: array<non-empty-string, non-empty-string|list<non-empty-string>>,
 *     ...
 * }
 * @extends Fact<Type>
 */
final class ComposerJson extends Fact
{
    public static function resolve(Cli $cli, Context $context): mixed
    {
        $json = $context->tryReadFile('composer.json');
        if ($json === null) {
            return [];
        }

        try {
            /** @var Type */
            return json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }
}

