# phpsyslog
A very simple
[PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
logger implementation that logs to a
[POSIX syslog](http://pubs.opengroup.org/onlinepubs/9699919799/functions/closelog.html).

## Installation

This class requires PHP 5.4 or later, but we recommend using the latest available version of PHP as a matter of principle.  It has two dependencies, the [FIG] PSR-3 interface, linked above, and [IcecaveStudios/isolater] which is used to isolate global functions to make them more easily tested.

It is installable and autoloadable via Composer as [cxj/phpsyslog] from [Packagist](https://packagist.org/).

Alternatively, [download a release](https://github.com/cxj/phpsylog/release) from GitHub, or clone this repository.  Then require or include its _autoload.php_ file.

## Quality

This class attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

This package is unit tested using [PHPUnit](https://phpunit.de).

## Usage

In the current version, you may want to edit the class constants (or derive your own class and override them) which define the log options and the syslog facility.

Example usage:
```php
<?php

$ident = basename($argv[0]);

$logger = new Cxj\Logger($ident, Psr\Log\LogLevel::WARNING);

// Will be logged.
$logger->alert("This is an alert level message.");    

// Not logged because lower level than default WARNING level set in constructor.
$logger->debug("This is a debug level message.");     

?>
```
