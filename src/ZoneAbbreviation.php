<?php declare(strict_types=1);

namespace time;

final class ZoneAbbreviation
{
    /**
     * @var null|array<string, non-empty-list<array{offset: int, dst: bool, zoneId: null|string}>>
     */
    private static ?array $registry = null;

    /**
     * @var null|array<string, array<int, string>>
     */
    private static ?array $registryByZoneIdAndOffset = null;

    private function __construct() {}

    public static function findAbbreviation(Zone $zone, ZoneOffset $offset): ?string
    {
        $registryByZoneIdOffset = self::registryByZoneIdAndOffset();
        return $registryByZoneIdOffset[$zone->identifier][$offset->totalSeconds] ?? null;
    }

    /**
     * @return array<string, non-empty-list<array{offset: int, dst: bool, zoneId: null|string}>>
     */
    private static function registry(): array
    {
        if (self::$registry !== null) {
            return self::$registry;
        }

        $registry = [];
        foreach (\DateTimeZone::listAbbreviations() as $abbrId => $abbrInfos) {
            if (!$abbrInfos) {
                continue;
            }

            $abbrId = \strtoupper($abbrId);
            $registry[$abbrId] ??= [];
            foreach ($abbrInfos as $abbrInfo) {
                $abbrInfo['zoneId'] = $abbrInfo['timezone_id'];
                unset($abbrInfo['timezone_id']);
                $registry[$abbrId][] = $abbrInfo;
            }
        }

        return self::$registry = $registry;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private static function registryByZoneIdAndOffset(): array
    {
        if (self::$registryByZoneIdAndOffset !== null) {
            return self::$registryByZoneIdAndOffset;
        }

        $registryByZoneIdAndOffset = [];
        foreach (self::registry() as $abbrId => $abbrInfos) {
            foreach ($abbrInfos as $abbrInfo) {
                if ($abbrInfo['zoneId'] === null) {
                    continue;
                }

                $registryByZoneIdAndOffset[$abbrInfo['zoneId']][$abbrInfo['offset']] = $abbrId;
            }
        }

        return self::$registryByZoneIdAndOffset = $registryByZoneIdAndOffset;
    }
}
