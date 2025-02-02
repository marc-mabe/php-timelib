<?php

namespace dt;

final class LocalTime implements Time {
    public int $hour { get => (int)$this->dt->format('H'); }
    public int $minute  { get => (int)$this->dt->format('i'); }
    public int $second  { get => (int)$this->dt->format('s'); }
    public int $milliOfSecond { get => (int)($this->dt->getMicrosecond() / 1000); }
    public int $microOfSecond { get => $this->dt->getMicrosecond(); }
    public int $nanoOfSecond  { get => $this->dt->getMicrosecond() * 1000; }

    private function __construct(
        private \DateTimeImmutable $dt,
    ) {}

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public static function fromHms(
        int|float $hour,
        null|int|float $minute = null,
        null|int|float $second = null,
        null|int|float $nanoOfSecond = null
    ): self {
        $m = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($second, 2, '0', STR_PAD_LEFT);
        $u = str_pad((int)($nanoOfSecond / 1000), 6, '0', STR_PAD_LEFT);
        return new self(\DateTimeImmutable::createFromFormat(
            'Y-n-j G:i:s.u',
            "1970-1-1 $hour:$m:$s.$u",
            new \DateTimeZone('UTC'),
        ));
    }

    public static function fromTimeUnit(int|float $value, TimeUnit $unit): self {}
}
