<?php declare(strict_types=1);

namespace time;

final class LocalDate implements Date {
    public int $year { get => (int)$this->legacy->format('Y'); }
    public Month $month { get => Month::from((int)$this->legacy->format('m')); }
    public int $dayOfMonth { get => (int)$this->legacy->format('d'); }
    public int $dayOfYear  { get => ((int)$this->legacy->format('z') + 1); }

    public DayOfWeek $dayOfWeek {
        get => DayOfWeek::from((int)$this->legacy->format('N'));
    }

    private function __construct(
        private readonly \DateTimeImmutable $legacy,
    ) {}

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public static function fromYmd(int $year, Month|int $month, int $dayOfMonth): self {
        $n = $month instanceof Month ? $month->value : $month;
        return new self(\DateTimeImmutable::createFromFormat(
            'Y-n-j',
            "{$year}-{$n}-{$dayOfMonth}",
            new \DateTimeZone('+00:00'),
        ));
    }

    public static function fromYd(int $year, int $dayOfYear): self {
        $z = $dayOfYear - 1;
        return new self(\DateTimeImmutable::createFromFormat(
            'Y-z',
            "{$year}-{$z}",
            new \DateTimeZone('+00:00'),
        ));
    }
}
