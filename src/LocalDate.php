<?php declare(strict_types=1);

namespace time;

final class LocalDate implements Date
{
    public int $year {
        get => GregorianCalendar::getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[0];
    }

    public Month $month {
        get => GregorianCalendar::getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[1];
    }

    public int $dayOfMonth {
        get => GregorianCalendar::getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[2];
    }

    public int $dayOfYear {
        get {
            $date = GregorianCalendar::getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch);
            return GregorianCalendar::getDayOfYearByYmd($date[0], $date[1], $date[2]);
        }
    }

    public DayOfWeek $dayOfWeek {
        get => GregorianCalendar::getDayOfWeekByDaysSinceUnixEpoch($this->daysSinceEpoch);
    }

    private function __construct(
        private readonly int $daysSinceEpoch
    ) {}

    public function format(DateTimeFormatter|string $format): string
    {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public static function fromYmd(int $year, Month|int $month, int $dayOfMonth): self
    {
        $n = $month instanceof Month ? $month->value : $month;
        $legacy = \DateTimeImmutable::createFromFormat(
            '|Y-n-j',
            "{$year}-{$n}-{$dayOfMonth}",
            new \DateTimeZone('+00:00'),
        );
        assert($legacy !== false);
        $daysSinceEpoch = \intdiv($legacy->getTimestamp(), 24 * 3600);
        return new self($daysSinceEpoch);
    }

    public static function fromYd(int $year, int $dayOfYear): self
    {
        $z = $dayOfYear - 1;
        $y = ($year < 0 ? '-' : '+') . str_pad((string)abs($year), 4, '0', STR_PAD_LEFT);
        $legacy = \DateTimeImmutable::createFromFormat(
            '|X-z',
            "{$y}-{$z}",
            new \DateTimeZone('+00:00'),
        );
        assert($legacy !== false);
        $daysSinceEpoch = \intdiv($legacy->getTimestamp(), 24 * 3600);
        return new self($daysSinceEpoch);
    }
}
