<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Value;

use function array_slice;

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

    /**
     * @return list<self>
     */
    public static function range(self $from, self $to): array
    {
        $versions = array_filter(
            self::cases(),
            static fn(self $version): bool => $version !== self::UNKNOWN,
        );

        $fromIndex = array_search($from, $versions, true);
        $toIndex = array_search($to, $versions, true);

        if ($fromIndex === false || $toIndex === false || $fromIndex > $toIndex) {
            return [];
        }

        return array_slice($versions, $fromIndex, $toIndex - $fromIndex + 1);
    }
}
