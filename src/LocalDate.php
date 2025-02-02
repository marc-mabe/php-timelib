<?php

namespace dt;

final class LocalDate implements Date {
    public int $year { get => (int)$this->dt->format('Y'); }
    public Month $month { get => Month::from((int)$this->dt->format('m')); }
    public int $dayOfMonth { get => (int)$this->dt->format('d'); }
    public int $dayOfYear  { get => (int)$this->dt->format('z'); }

    private function __construct(
        private readonly \DateTimeImmutable $dt,
    ) {}

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public static function fromYmd(int $year, Month|int $month, int $dayOfMonth): self {
        $m = $month instanceof Month ? $month->value : $month;
        return new self(new \DateTimeImmutable("{$year}-{$m}-{$dayOfMonth}", new \DateTimeZone('+00:00')));
    }

    public static function fromYd(int $year, int $dayOfYear): self {
        return new self(\DateTimeImmutable::createFromFormat('Y-z', "{$year}-{$dayOfYear}", new \DateTimeZone('+00:00')));
    }
}
