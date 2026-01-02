## Project Overview

This is a PHP scaffolding library that provides a framework for making automated changes to project files. The library
uses a decision-based architecture where `Change` objects decide what modifications to apply, and `Fact` objects provide
contextual information.

## Commands

### Code Quality

```bash
# Run PHPStan static analysis (level 10 with bleeding edge)
composer phpstan

# Fix code style (PER-CS3.0 standard)
composer cs-fix
```

### Testing

There is currently no test suite configured in this project.

## Architecture

### Core Execution Flow

1. **Runner** (`src/Runner.php`) - Entry point that:
    - Accepts a list of `Change` objects, optional `Fact` classes, and params
    - Creates a Symfony Console `SingleCommandApplication`
    - Registers all fact classes (custom + built-in) with the command
    - Executes the `Command`

2. **Command** (`src/Command.php`) - Orchestrates the scaffolding process:
    - Creates a `Context` with the target directory
    - Calls `decide()` on each `Change` to get "applier" callables
    - Executes all appliers in sequence
    - Reports success with count of applied changes

3. **Context** (`src/Context.php`) - Provides:
    - File operations (`readFile`, `writeFile`, `writeTextFile`, `copyFile`, `fileExists`)
    - Fact resolution with memoization (`getFact`, `hasFact`)
    - Param access (`getParam`, `hasParam`)
    - Command execution (`execute`)

4. **Cli** (`src/Cli.php`) - Wrapper around Symfony Console I/O:
    - `step()` - Execute operation with "Step name... Done." output
    - `ask()` - Interactive prompts with optional normalizer/validator
    - `success()` - Display success messages
    - `getOption()` - Access command options

### Key Abstractions

**Change Interface**
- Implements `decide(Context $context): callable|array|null`
- Returns applier callable(s) to execute, or null for no-op
- Applier signature: `callable(Cli): void`
- Examples: `WriteFile`, `CopyFile`, `PrepareComposerJson`, `NormalizeComposerJson`

**Fact Abstract Class**
- Template class `Fact<T>` for resolving contextual information
- `resolve(Cli $cli, Context $context): T` - Compute/prompt for the fact value
- `configureCommand()` - Optional: add CLI options for this fact
- Facts are lazily resolved and cached in Context
- Built-in facts: `ComposerJson`, `CopyrightHolder`, `CopyrightYear`, `PhpConstraint`, `UserName`

**Params Class**
- Readonly immutable container for arbitrary parameters
- Passed to Runner, accessible via Context
- Used to pass configuration to Change/Fact objects

## Key Patterns

**Decision-Based Changes**: Changes inspect Context and decide whether to apply. This allows idempotent operations - 
re-running applies only what's needed.

**Fact Resolution**: Facts are resolved on-demand and cached. This allows interactive prompts only when actually needed,
and facts can depend on other facts or file content.

**Applier Callables**: Changes return callables rather than executing directly. This separates decision logic from 
execution, allowing the Command to collect all planned changes before applying.

## Configuration

**PHP Version**: Locked to PHP 8.4.*

**PHPStan**: Level 10 with bleeding edge, runs on `src/` directory

**PHP-CS-Fixer**: PER-CS3.0 ruleset with `no_unused_imports`, runs on `src/` directory

**Composer Normalize**: Uses bundled phar at root level

## Development Guidelines

### File Writing

- **Use `writeTextFile()`** for text files (LICENSE, README.md, composer.json, .php files, etc.). This method ensures a trailing newline is added if missing, which is the standard convention for text files.
- **Use `writeFile()`** only for binary files or when exact control over file content is required (no automatic newline).

Examples:
```php
// Text files - use writeTextFile
$context->writeTextFile('LICENSE', $licenseText);
$context->writeTextFile('composer.json', $json);
$context->writeTextFile('README.md', $readme);

// Binary or exact content - use writeFile
$context->writeFile('image.png', $binaryData);
```
