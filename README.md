# PHP Scaffolder Library

A PHP library for making automated changes to project files through a decision-based architecture.

## Quick Start with Template

A ready-made scaffolder tool built on top of this library is available at https://github.com/vjik/scaffolder-template.

This template provides a complete scaffolder framework that you can clone and customize for your projects.

## Projects Using Scaffolder

Real-world examples of a scaffolder tool built using this library:

- [PHPTG Scaffolder](https://github.com/phptg/scaffolder)

## General Usage

The scaffolder allows you to automate project file modifications through a declarative approach. Create a PHP
script that defines the changes you want to apply:

```php
use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Runner;

require_once __DIR__ . '/vendor/autoload.php';

new Runner(
    changes: [
        new Change\WriteFile('README.md', 'Hello World'),
        new Change\PrepareComposerJson(),
        new Change\CopyFile(
            from: __DIR__ . '/templates/LICENSE',
            to: 'LICENSE',
        ),
    ],
)->run();
```

Run your script:

```bash
php my-scaffolder.php
```

The scaffolder will execute each change in sequence, showing progress:

```
Write `README.md`... Done.
Write `composer.json`... Done.
Normalize `composer.json`... Done.
Copy LICENSE... Done.

Success! 4 changes applied.
```

## Architecture

### Changes

Changes implement the `Change` interface and define what modifications to apply. Each change:

- Implements a `decide(Context $context): callable|array|null` method
- Returns an "applier" callable to execute, or `null` for no-op
- Can inspect the current state and decide whether to apply

**Built-in Changes:**

- `WriteFile` - Write content to a file
- `WriteFileIfNotExists` - Write file only if it doesn't exist
- `CopyFile` - Copy a file
- `CopyFileIfNotExists` - Copy file only if it doesn't exist
- `PrepareComposerJson` - Update composer.json with package information
- `EnsureDirectoryWithGitkeep` - Ensure directory exists with .gitkeep
- `EnsureFact` - Ensure a fact is resolved (useful for prompting user input)
- `ChangeIf` - Conditionally apply changes based on a condition

### Facts

Facts are template classes that resolve contextual information on-demand. They can:

- Read from files (e.g., existing `composer.json`)
- Prompt the user for input
- Derive values from other facts
- Add CLI options

**Example:**

```php
use Vjik\Scaffolder\Context;

$context->getFact(PackageName::class); // Returns "vendor/package"
$context->getFact(NamespaceX::class);  // Returns "Vendor\\Package"
```

Facts are lazily resolved and cached, so expensive operations only happen when needed.

**Built-in Facts:**

- `ComposerJson` - Read and parse existing composer.json
- `PackageName` - Package name (e.g., "vendor/package")
- `PackageVendor` - Vendor name from package name
- `PackageProject` - Project name from package name
- `PackageDescription` - Package description
- `PackageLicense` - Package license
- `PackageType` - Package type (library, project, etc.)
- `PackageAuthors` - Array of package authors
- `NamespaceX` - Root namespace (e.g., "Vendor\\Package")
- `SourceDirectory` - Source code directory (default: "src")
- `TestsDirectory` - Tests directory (default: "tests")
- `PhpConstraint` - PHP version constraint
- `PhpConstraintName` - PHP constraint name (default: "php")
- `LowestMinorPhpVersion` - Lowest minor PHP version
- `HighestMinorPhpVersion` - Highest minor PHP version
- `MinorPhpVersionRange` - PHP version range
- `PrepareComposerAutoload` - Whether to prepare autoload section
- `PrepareComposerAutoloadDev` - Whether to prepare autoload-dev section
- `CopyrightHolder` - Copyright holder name
- `CopyrightYear` - Copyright year
- `Title` - Project title
- `UserName` - User/author name
- `UserEmail` - User/author email

### Parameters

The scaffolder supports a flexible parameter system that allows configuration through multiple sources with a clear
priority order:

1. Command line options - facts can add their own CLI options
2. `scaffolder.php` file - project-specific configuration
3. Runner constructor - default values

**Setting parameters via scaffolder.php file:**

Create a `scaffolder.php` file in your project directory:

```php
<?php

return [
    'namespace' => 'MyVendor\\MyProject',
    'license' => 'MIT',
];
```

**Setting default parameters via Runner:**

```php
new Runner(
    // ...
    params: [
        'namespace' => 'DefaultVendor\\DefaultProject',
        'license' => 'BSD-3-Clause',
    ],
)->run();
```

Parameters from `scaffolder.php` will override runner defaults.

**Command Line Options:**

- `--directory` - specify the target directory where changes will be applied (default: current working directory)
- `--scaffolder-file` - Specify custom name for the configuration file (default: `scaffolder.php`)

```bash
php my-scaffolder.php --directory=/path/to/project --scaffolder-file=my-config.php
```

## Documentation

If you have any questions or problems with this package, use [author telegram chat](https://t.me/predvoditelev_chat) for communication.

## License

The `vjik/scaffolder` is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE) for more information.
