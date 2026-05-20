<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

enum PackageAuthorsDefaultSource: string
{
    case User = 'user';
    case EmptyList = 'empty';
}
