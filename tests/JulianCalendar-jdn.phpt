--TEST--
JulianCalendar: Julian day number conversion
--FILE--
<?php

include __DIR__ . '/include.php';

const JDN_OFFSET = 32083;

$cal = new time\JulianCalendar();

$ymd = $cal->getYmdByJdn(-365);
echo "-365 (Jan 1st 4714 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(-1);
echo "-1 (Dec 31th 4714 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(0);
echo "0 (Jan 1st 4713 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(1);
echo "1 (Jan 2nd 4713 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(1721423);
echo "1721423 (Dec 31th 0001 B.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(1721424);
echo "1721424 (Jan 1st 0001 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn(2460822);
echo "2460822 (May 13nd 2025 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

// min/max
$min = -JDN_OFFSET + 1;
$max = \intdiv(PHP_INT_MAX - JDN_OFFSET * 4 + 1, 4);

$ymd = $cal->getYmdByJdn($max);
echo "{$max} (MAX) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

$ymd = $cal->getYmdByJdn($min);
echo "{$min} (MIN) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

--EXPECTF--
-365 (Jan 1st 4714 B.C.) -> -4714-1-1
-4714-1-1 -> -365
-1 (Dec 31th 4714 B.C.) -> -4714-12-31
-4714-12-31 -> -1
0 (Jan 1st 4713 B.C.) -> -4713-1-1
-4713-1-1 -> 0
1 (Jan 2nd 4713 B.C.) -> -4713-1-2
-4713-1-2 -> 1
1721423 (Dec 31th 0001 B.C.) -> -1-12-31
-1-12-31 -> 1721423
1721424 (Jan 1st 0001 A.C.) -> 1-1-1
1-1-1 -> 1721424
2460822 (May 13nd 2025 A.C.) -> 2025-5-13
2025-5-13 -> 2460822
%d (MAX) -> %d-%d-%d
%d-%d-%d -> %d
-32082 (MIN) -> -4801-3-1
-4801-3-1 -> -32082
