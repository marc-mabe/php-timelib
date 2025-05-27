--TEST--
GregorianCalendar: Julian day number conversion
--FILE--
<?php

include __DIR__ . '/include.php';

$gcal = time\GregorianCalendar::getInstance();

$ymd = $gcal->getYmdFromJdn(0);
echo "0 (Nov 24th 4714 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$gcal->getJdnFromYmd(...$ymd)}\n";

$ymd = $gcal->getYmdFromJdn(1);
echo "1 (Nov 25th 4714 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$gcal->getJdnFromYmd(...$ymd)}\n";

$ymd = $gcal->getYmdFromJdn(1721425);
echo "1721425 (Dec 31st 0001 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$gcal->getJdnFromYmd(...$ymd)}\n";

$ymd = $gcal->getYmdFromJdn(1721426);
echo "1721426 (Jan 1st 0001 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$gcal->getJdnFromYmd(...$ymd)}\n";

$ymd = $gcal->getYmdFromJdn(2460822);
echo "2460822 (May 26st 2025 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$gcal->getJdnFromYmd(...$ymd)}\n";

// 31^2-1
$ymd = $gcal->getYmdFromJdn(2147483647);
echo "2147483647 (Jun 3rd 5874898 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$gcal->getJdnFromYmd(...$ymd)}\n";

--EXPECT--
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
2147483647 (Jun 3rd 5874898 A.C.) -> 5874898-6-3
5874898-6-3 -> 2147483647
