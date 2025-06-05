--TEST--
JulianCalendar: Julian day number conversion
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = time\JulianCalendar::getInstance();

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

// 31^2-1
$ymd = $cal->getYmdByJdn(2147483647);
echo "2147483647 (Oct 17th 5874777 A.C.) -> {$ymd[0]}-{$ymd[1]}-{$ymd[2]}\n";
echo "{$ymd[0]}-{$ymd[1]}-{$ymd[2]} -> {$cal->getJdnByYmd(...$ymd)}\n";

--EXPECT--
0 (Jan 1st 4713 B.C.) -> -4712-1-1
-4712-1-1 -> 0
1 (Jan 2nd 4713 B.C.) -> -4712-1-2
-4712-1-2 -> 1
1721423 (Dec 31th 0001 B.C.) -> 0-12-31
0-12-31 -> 1721423
1721424 (Jan 1st 0001 A.C.) -> 1-1-1
1-1-1 -> 1721424
2460822 (May 13nd 2025 A.C.) -> 2025-5-13
2025-5-13 -> 2460822
2147483647 (Oct 17th 5874777 A.C.) -> 5874777-10-17
5874777-10-17 -> 2147483647
