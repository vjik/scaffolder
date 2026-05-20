# PHP Scaffolder Change Log

## 0.2.0 under development

- New #4: Add `package-authors-default-source` setting to `PackageAuthors` fact that controls the default author source
  when authors are not provided explicitly and absent in `composer.json`: take from user (`user`, default) or use an
  empty list (`empty`).
- Bug #6: Don't add empty `authors` section to `composer.json` in `PrepareComposerJson` change.

## 0.1.0 January 04, 2026

- Initial release.
