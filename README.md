# php-timelib

Playground for new date and time standard library for PHP.

## State

**Very much early state!**

## TODOs

* Completely remove use of legacy functions / classes
* Timezone DB
* Comparing different objects of the same type
* Different calendar systems
  * Maybe a civil calendar automatically switching systems.
  * Like switching between Julian and Gregorian at a specific date.
* and much more

## Overview

### Moment

A class that specifies a current point in time.
It's based on unix timestamp with nanosecond adjustment,
supports the `Date` and `Time` interface based on UTC,
but otherwise does not provide any time zone information. 

### Local[Date|Time|DateTime]

Classes that represent a local date, time or date+time
without time zone information.

It does **not** represent a point in time
as the used information might be ambiguous.

#### Examples
* **New-Year's-Eve**: is at a specific date+time
  but happens on multiple moments on the earth
  -> `LocalDateTime`
* **Day of birth**: A specific date in your passport but on the edge
  you might have a different age on different locations on the earth
  -> `LocalDate`
* **Midnight**: A specific time without any date information,
  which also happens on different moments on the earth
  -> `LocalTime`

### ZonedDateTime

Represents a specific moment at a specific time zone.

### Zone

A class that represents a specific time zone via a timezone identifier.

The identifier is one of:
- `±HH:MM[:SS]`
- `UTC` / `GMT`
- Regional identifier like `Europe/Berlin` from timezone DB

### ZoneOffset (extends Zone)

A specialized zone representing a fixed time offset only.

It's used in
* `ZonedDateTime->offset`
* `Zone->fixedOffset`
* `ZoneInfo->fixedOffset`
* `ZoneTransition->offset`

### ZoneInfo

A representation of a time zone or offset with information of all zone transitions.

How these zone transitions are stored internally (like via database or rules)
is not defined by this class. 

### ZoneTransition

A representation of a single zone transition with the exact moment
when the transition happened/happens and the zone offset that has been / will be applied. 

### Duration

A relative duration of time with fixed definition
and does not need to handle time zone on applying. 

### Period

A relative time including dates
that needs to handle time zone on applying.

### DateUnit / TimeUnit / Month / DayOfWeek (enum)

Represents a specific predefined unit of a date or time. 

### DateTimeFormatter

For formatting (and parsing) dates and times.

### WallClock

An abstraction of the system wall clock.

Represents the current moment synchronized with external clock.

### MonotonicClock 

An abstraction of the system monotonic timer. 

By default it's initialized with the wall clock.

### GregorianCalendar

Singleton implementation of the gregorian calendar system.

Currently, this is the default calendar system used if not provided.

### Stopwatch

For time measurement using `MonotonicClock`
without wall clock initializer by default.

### Interfaces

The following interfaces are defined:
* `Momented`: Something that represents a specific point-in-time
  via a `moment` property providing a `Moment` object.
* `Date`: An object that represents a date
* `Time`: An object that represents a time
* `Zoned`: An object that is adjusted by time zone
* `Clock`: A clock
* `Calendar`: An object that represents a calendar system
