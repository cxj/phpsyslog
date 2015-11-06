<?php
/**
 * @file Logger.php
 *
 * A simple PSR-3 logger implementation that outputs to syslog.
 *
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 *
 * Created: 10/26/15 9:51 PM
 * $Id$
 */

namespace Cxj;

use Icecave\Isolator\IsolatorTrait;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;


/**
 * A very simple PSR-3 logger implementation that outputs to syslog.
 */
class Logger extends AbstractLogger
{
    use IsolatorTrait;

    const LOG_OPTIONS = LOG_NDELAY | LOG_PID;   // todo paramterize
    const LOG_FACILITY = LOG_LOCAL7;            // todo paramterize

    private static $levels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7,
    ];

    // syslog() controls:
    private $options; // initialized in constructor.
    private $facility; // initialized in constructor.
    private $minimumLogLevel; // initialized in constructor params.
    private $ident;

    // miscellaneous properties:
    private $isInit;


    /**
     * CONSTRUCTOR
     *
     * @param string $ident - usually program name, defaults to null.
     * @param string $minimumLogLevel
     */
    public function __construct(
        $ident = null,
        $minimumLogLevel = LogLevel::DEBUG
    )
    {
        $this->options  = self::LOG_OPTIONS;
        $this->facility = self::LOG_FACILITY;

        $this->ident           = $ident;
        $this->minimumLogLevel = self::$levels[$minimumLogLevel];
    }

    /**
     * Log a message.
     *
     * @param mixed $level The log level.
     * @param string $message The message to log.
     * @param array $context Additional contextual information.
     *
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        if (self::$levels[$level] > $this->minimumLogLevel) {
            return;
        }
        $iso = $this->isolator();
        $this->init();

        $iso->syslog(
            self::$levels[$level],
            $this->substitutePlaceholders($message, $context)
        );
    }

    /**
     * Sets the identification string, logging options and facility.
     *
     * @return boolean true success | false failure
     */
    private function init()
    {
        if (!$this->isInit) {
            $this->isInit = true;

            $iso = $this->isolator();

            return $iso->openlog($this->ident, $this->options, $this->facility);
        }
        return true;
    }

    /**
     * Substitute PSR-3 style placeholders in a message.
     *
     * @param string $message The message template.
     * @param array <string, mixed> $context The placeholder values.
     *
     * @return string The message template with placeholder values substituted.
     */
    private function substitutePlaceholders($message, array $context)
    {
        if (false === strpos($message, '{')) {
            return $message;
        }
        $replacements = [];
        foreach ($context as $key => $value) {
            $replacements['{' . $key . '}'] = $value;
        }
        return strtr($message, $replacements);
    }
}
