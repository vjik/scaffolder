<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Value;

enum MinorPhpVersion: string
{
    case PHP74 = '7.4';
    case PHP80 = '8.0';
    case PHP81 = '8.1';
    case PHP82 = '8.2';
    case PHP83 = '8.3';
    case PHP84 = '8.4';
    case PHP85 = '8.5';
    case UNKNOWN = 'unknown';
}
