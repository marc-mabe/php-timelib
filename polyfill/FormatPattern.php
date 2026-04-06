<?php declare(strict_types=1);

namespace time;

/**
 * Parsed CLDR-style date/time format pattern (literals, field symbols, optional sections).
 *
 * @phpstan-type FormatPatternToken array{
 *     type: 'symbol',
 *     value: FormatPatternSymbol,
 *     count: int,
 * }|array{
 *     type: 'literal',
 *     value: string,
 *  }|array{
 *     type: 'optional',
 *     value: list<mixed>
 *  }
 */
final class FormatPattern
{
    /**
     * @param list<FormatPatternToken> $tokens
     */
    public function __construct(
        public readonly string $pattern,
        public readonly array $tokens,
    ) {
    }

    /**
     * @throws InvalidFormatError
     */
    public static function parse(string $pattern): self
    {
        return new self($pattern, self::tokenize($pattern, $pattern));
    }

    /**
     * @return list<FormatPatternToken>
     * @throws InvalidFormatError
     */
    private static function tokenize(string $pattern, string $fullPatternForErrors): array
    {
        /** @var list<FormatPatternToken> $tokens */
        $tokens = [];
        $len = \strlen($pattern);
        $i = 0;

        while ($i < $len) {
            $char = $pattern[$i];

            if ($char === "'" || $char === '"') {
                $quote = $char;
                $literal = '';
                $i++;

                if ($i < $len && $pattern[$i] === $quote) {
                    $literal = $quote;
                    $i++;
                } else {
                    while ($i < $len && $pattern[$i] !== $quote) {
                        $literal .= $pattern[$i];
                        $i++;
                    }

                    if ($i >= $len) {
                        throw new InvalidFormatError("Unterminated quote in pattern: {$fullPatternForErrors}");
                    }
                    $i++;
                }

                $tokens[] = ['type' => 'literal', 'value' => $literal];
                continue;
            }

            if ($char === '[') {
                $i++;
                $depth = 1;
                $optionalPattern = '';

                // @phpstan-ignore greater.alwaysTrue (depth is 1+ until optional section closes)
                while ($i < $len && $depth > 0) {
                    if ($pattern[$i] === '[') {
                        $depth++;
                    } elseif ($pattern[$i] === ']') {
                        $depth--;
                        if ($depth === 0) {
                            break;
                        }
                    }
                    $optionalPattern .= $pattern[$i];
                    $i++;
                }

                if ($depth > 0) {
                    throw new InvalidFormatError("Unterminated optional section in pattern: {$fullPatternForErrors}");
                }

                $i++;
                $tokens[] = [
                    'type'  => 'optional',
                    'value' => self::tokenize($optionalPattern, $fullPatternForErrors),
                ];
                continue;
            }

            if ($char === ']') {
                throw new InvalidFormatError("Unexpected ']' in pattern: {$fullPatternForErrors}");
            }

            $symbol = FormatPatternSymbol::tryFrom($char);

            if ($symbol !== null) {
                $count = 1;
                $i++;
                while ($i < $len && $pattern[$i] === $char) {
                    $count++;
                    $i++;
                }

                self::validateSymbolCount($symbol, $count, $fullPatternForErrors);

                $tokens[] = ['type' => 'symbol', 'value' => $symbol, 'count' => $count];
                continue;
            }

            throw new InvalidFormatError("Unquoted literal '{$char}' in pattern: {$fullPatternForErrors}. Literals must be quoted with single or double quotes.");
        }

        return $tokens;
    }

    private static function validateSymbolCount(FormatPatternSymbol $symbol, int $count, string $pattern): void
    {
        $error = match ($symbol) {
            FormatPatternSymbol::ZONE_ID => $count !== 2
                ? "Pattern letter 'V' must appear exactly twice"
                : null,
            FormatPatternSymbol::ZONE_NAME => $count > 4
                ? "Pattern letter 'z' cannot appear more than 4 times"
                : null,
            FormatPatternSymbol::ZONE_GENERIC => $count !== 1 && $count !== 4
                ? "Pattern letter 'v' must appear exactly 1 or 4 times"
                : null,
            FormatPatternSymbol::ZONE_OFFSET_GMT => $count !== 1 && $count !== 4
                ? "Pattern letter 'O' must appear exactly 1 or 4 times"
                : null,
            FormatPatternSymbol::ZONE_OFFSET_ISO_BASIC,
            FormatPatternSymbol::ZONE_OFFSET_ISO_BASIC_LOCAL => $count > 5
                ? "Pattern letter '{$symbol->value}' cannot appear more than 5 times"
                : null,
            FormatPatternSymbol::ZONE_OFFSET_ISO_EXTENDED => $count > 5
                ? "Pattern letter 'Z' cannot appear more than 5 times"
                : null,
            FormatPatternSymbol::LOCAL_DAY_OF_WEEK_STANDALONE => null,
            FormatPatternSymbol::DAY_OF_WEEK_IN_MONTH => $count > 1
                ? "Pattern letter 'F' can only appear once"
                : null,
            FormatPatternSymbol::DAY_OF_MONTH,
            FormatPatternSymbol::HOUR_23,
            FormatPatternSymbol::HOUR_12,
            FormatPatternSymbol::HOUR_11,
            FormatPatternSymbol::HOUR_24,
            FormatPatternSymbol::MINUTE,
            FormatPatternSymbol::SECOND => $count > 2
                ? "Pattern letter '{$symbol->value}' cannot appear more than twice"
                : null,
            FormatPatternSymbol::DAY_OF_YEAR => $count > 3
                ? "Pattern letter 'D' cannot appear more than 3 times"
                : null,
            FormatPatternSymbol::FRACTIONAL_SECOND => $count > 9
                ? "Pattern letter 'S' cannot appear more than 9 times"
                : null,
            default => null,
        };

        if ($error !== null) {
            throw new InvalidFormatError("{$error} in pattern: {$pattern}");
        }
    }
}
