--TEST--
GregorianCalendar: Julian day number conversion
--FILE--
<?php

include __DIR__ . '/include.php';

const JDN_OFFSET = 32045;

$cal = new time\GregorianCalendar();

$ymd = $cal->getYmdByJdn(-365);
echo "-365 (Nov 24th 4715 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(-1);
echo "-1 (Nov 23th 4714 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(0);
echo "0 (Nov 24th 4714 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(1);
echo "1 (Nov 25th 4714 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(1721425);
echo "1721425 (Dec 31st 0001 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(1721426);
echo "1721426 (Jan 1st 0001 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(2460822);
echo "2460822 (May 26st 2025 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

// max 32bit
$ymd = $cal->getYmdByJdn(536838866);
echo "536838866 (17th Oct 1465102 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

// min/max
$max = intdiv(PHP_INT_MAX - 4 * JDN_OFFSET, 4);
$min = -JDN_OFFSET + 1;

$ymd = $cal->getYmdByJdn($max);
echo "{$max} (MAX) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn($min);
echo "{$min} (MIN) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

try {
    echo ($max + 1) . "\n";
    $cal->getYmdByJdn($max + 1);
} catch (Throwable $e) {
    echo $e::class . ": {$e->getMessage()}\n";
}

try {
    echo ($min - 1) . "\n";
    $cal->getYmdByJdn($min - 1);
} catch (Throwable $e) {
    echo $e::class . ": {$e->getMessage()}\n";
}

--EXPECTF--
-365 (Nov 24th 4715 B.C.) -> -4714-11-24
-4714-11-24 -> -365
-1 (Nov 23th 4714 B.C.) -> -4713-11-23
-4713-11-23 -> -1
0 (Nov 24th 4714 B.C.) -> -4713-11-24
-4713-11-24 -> 0
1 (Nov 25th 4714 B.C.) -> -4713-11-25
-4713-11-25 -> 1
1721425 (Dec 31st 0001 B.C.) -> 0-12-31
0-12-31 -> 1721425
1721426 (Jan 1st 0001 A.C.) -> 1-1-1
1-1-1 -> 1721426
2460822 (May 26st 2025 A.C.) -> 2025-5-26
2025-5-26 -> 2460822
536838866 (17th Oct 1465102 A.C.) -> 1465102-10-17
1465102-10-17 -> 536838866
%d (MAX) -> %d-%d-%d
%d-%d-%d -> %d
-32044 (MIN) -> -4800-3-1
-4800-3-1 -> -32044
%d
time\RangeError: Julian day number must be between -32044 and %d
-32045
time\RangeError: Julian day number must be between -32044 and %d
