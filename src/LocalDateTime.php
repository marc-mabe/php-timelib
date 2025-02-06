<?php

namespace dt;

final class LocalDateTime implements Date, Time {
    public int $year { get => (int)$this->dt->format('Y'); }
    public Month $month { get => Month::from((int)$this->dt->format('m')); }
    public int $dayOfMonth { get => (int)$this->dt->format('d'); }
    public int $dayOfYear  { get => (int)$this->dt->format('z'); }
    public int $hour { get => (int)$this->dt->format('H'); }
    public int $minute  { get => (int)$this->dt->format('i'); }
    public int $second  { get => (int)$this->dt->format('s'); }
    public int $milliOfSecond  { get => (int)($this->dt->getMicrosecond() / 1000); }
    public int $microOfSecond  { get => $this->dt->getMicrosecond(); }
    public int $nanoOfSecond  { get => $this->dt->getMicrosecond() * 1000; }
    public LocalDate $date { get => LocalDate::fromYd($this->year, $this->dayOfYear); }
    public LocalTime $time { get => LocalTime::fromHms($this->hour, $this->minute, $this->second); }

    private function __construct(
        private readonly \DateTimeImmutable $dt,
    ) {}

    public function add(Duration $duration): self {
        return new self($this->dt->add($duration->toLegacyInterval()));
    }

    public function sub(Duration $duration): self {
        return new self($this->dt->sub($duration->toLegacyInterval()));
    }

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public function toUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float {
        $s = $this->dt->getTimestamp();
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
        $ts = $clock->takeUnixTimestamp(TimeUnit::Second, fractions: true);
        return new self(\DateTimeImmutable::createFromTimestamp($ts));
    }

    public static function fromUnixTimestamp(int|float $timestamp, TimeUnit $unit = TimeUnit::Second): self {
        return new self(\DateTimeImmutable::createFromTimestamp($timestamp));
    }

    public static function fromDateTime(Date $date, Time $time): self {
        $m = str_pad($time->minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($time->second, 2, '0', STR_PAD_LEFT);
        $u = str_pad($time->microOfSecond, 6, '0', STR_PAD_LEFT);
        return new self(\DateTimeImmutable::createFromFormat(
            'Y-n-j G:i:s.u',
            "{$date->year}-{$date->month->value}-{$date->dayOfMonth} {$time->hour}:{$m}:{$s}.{$u}",
            new \DateTimeZone('+00:00'),
        ));
    }

    public static function parse(DateTimeParser $parser): self {}
}
