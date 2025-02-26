<?php declare(strict_types=1);

namespace time;

final class LocalDateTime implements Date, Time {
    public int $year {
        get => (int)$this->legacySec->format('Y');
    }

    public Month $month {
        get => Month::from((int)$this->legacySec->format('m'));
    }

    public int $dayOfMonth {
        get => (int)$this->legacySec->format('d');
    }

    public int $dayOfYear  {
        get => ((int)$this->legacySec->format('z') + 1);
    }

    public DayOfWeek $dayOfWeek {
        get => DayOfWeek::from((int)$this->legacySec->format('N'));
    }

    public int $hour {
        get => (int)$this->legacySec->format('H');
    }

    public int $minute  {
        get => (int)$this->legacySec->format('i');
    }

    public int $second  {
        get => (int)$this->legacySec->format('s');
    }

    public int $milliOfSecond {
        get => \intdiv($this->nanoOfSecond, 1_000_000);
    }

    public int $microOfSecond {
        get => \intdiv($this->nanoOfSecond, 1_000);
    }

    public LocalDate $date {
        get => LocalDate::fromYd($this->year, $this->dayOfYear);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second);
    }

    /**
     * @param int<0, 999999999> $nanoOfSecond
     */
    private function __construct(
        private readonly \DateTimeImmutable $legacySec,
        public readonly int $nanoOfSecond,
    ) {}

    public function add(Period $period): self {
        // FIXME: handle fraction of second
        return new self($this->legacySec->add($period->toLegacyInterval()), 0);
    }

    public function sub(Period $period): self {
        // FIXME: handle fraction of second
        return new self($this->legacySec->sub($period->toLegacyInterval()), 0);
    }

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public static function fromDateTime(Date $date, Time $time): self {
        $z = $date->dayOfYear - 1;
        $n = \str_pad((string)$time->minute, 2, '0', STR_PAD_LEFT);
        $s = \str_pad((string)$time->second, 2, '0', STR_PAD_LEFT);
        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "{$date->year}-{$z} {$time->hour}:{$n}:{$s}",
            new \DateTimeZone('+00:00'),
        );
        assert($legacy !== false);
        return new self($legacy, $time->nanoOfSecond);
    }
}
