<?php

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

    private function __construct(
        private readonly \DateTimeImmutable $legacySec,
        public readonly int $nanoOfSecond,
    ) {}

    public function add(Duration $duration): self {
        // FIXME: handle fraction of second
        return new self($this->legacySec->add($duration->toLegacyInterval()), 0);
    }

    public function sub(Duration $duration): self {
        // FIXME: handle fraction of second
        return new self($this->legacySec->sub($duration->toLegacyInterval()), 0);
    }

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public function toUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float {
        $s = $this->legacySec->getTimestamp();
        $ns = $this->nanoOfSecond;
        $value = match ($unit) {
            TimeUnit::Second => $s,
            TimeUnit::Minute => $s / 60,
            TimeUnit::Hour   => $s / 3600,
            TimeUnit::Millisecond => $s * 1000,
            TimeUnit::Microsecond => $s * 1000000,
            TimeUnit::Nanosecond => $s * 1000000000,
        };
        return $fractions ? (float)match ($unit) {
            TimeUnit::Second => $value + ($ns / 1000000000),
            TimeUnit::Minute => $value + (($ns / 1000000000) * 60),
            TimeUnit::Hour   => $value + (($ns / 1000000000) * 3600),
            TimeUnit::Millisecond => $value + ($ns / 1000),
            TimeUnit::Microsecond => $value + ($ns / 1000000),
            TimeUnit::Nanosecond => $value + $ns,
        } : (int)$value;
    }

    public static function fromNow(Clock $clock = new Clock()): self {
        [$ts, $ns] = $clock->takeUnixTimestampTuple();
        return new self(\DateTimeImmutable::createFromTimestamp($ts), $ns);
    }

    public static function fromDateTime(Date $date, Time $time): self {
        $z = $date->dayOfYear - 1;
        $n = str_pad($time->minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($time->second, 2, '0', STR_PAD_LEFT);
        return new self(\DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "{$date->year}-{$z} {$time->hour}:{$n}:{$s}",
            new \DateTimeZone('+00:00'),
        ), $time->nanoOfSecond);
    }

    public static function parse(DateTimeParser $parser): self {}
}
