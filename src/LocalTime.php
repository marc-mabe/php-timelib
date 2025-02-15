<?php declare(strict_types=1);

namespace time;

final class LocalTime implements Time {
    public int $hour {
        get => (int)$this->legacySec->format('G');
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

    private function __construct(
        private \DateTimeImmutable $legacySec,
        public readonly int $nanoOfSecond,
    ) {}

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public static function fromHms(
        int $hour,
        int $minute,
        int $second,
        int $nanoOfSecond = 0
    ): self {
        $i = str_pad((string)$minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad((string)$second, 2, '0', STR_PAD_LEFT);
        return new self(\DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "1970-0 $hour:$i:$s",
            new \DateTimeZone('UTC'),
        ), $nanoOfSecond);
    }

    public static function fromTimeUnit(int|float $value, TimeUnit $unit): self {}
}
