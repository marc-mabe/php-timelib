--TEST--
Month->getPrevious & Month->getNext
--FILE--
<?php

include __DIR__ . '/include.php';

foreach (time\Month::cases() as $month) {
    echo $month->name . ': ' . $month->getPrevious()->name . ' - ' . $month->getNext()->name . "\n";
}

--EXPECT--
January: December - February
February: January - March
March: February - April
April: March - May
May: April - June
June: May - July
July: June - August
August: July - September
September: August - October
October: September - November
November: October - December
December: November - January
