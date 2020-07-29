<?php
/**
 * @file LoggerTest.php
 */

namespace Cxj;

use Psr\Log\LogLevel;



/**
 * @property mixed isolator
 * @mixin \Icecave\Isolator\Isolator
 * @mixin \Cxj\Logger
 */
class LoggerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->isolator = $this->getMockBuilder('Icecave\Isolator\Isolator')
            ->setMethods(array('openlog', 'syslog'))
            ->getMock();

        $this->isolator->method('openlog')->willReturn(true);
        $this->isolator->method('syslog')->willReturn($this->returnArgument(1));

        $this->logger = new Logger(__FILE__, LogLevel::DEBUG);

        $this->logger->setIsolator($this->isolator);

        $this->levels = [
            LogLevel::EMERGENCY => 0,
            LogLevel::ALERT     => 1,
            LogLevel::CRITICAL  => 2,
            LogLevel::ERROR     => 3,
            LogLevel::WARNING   => 4,
            LogLevel::NOTICE    => 5,
            LogLevel::INFO      => 6,
            LogLevel::DEBUG     => 7,
        ];
    }

    public function testLogWithPlaceholderValues(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::INFO]),
                $this->equalTo('Foo: FOO, F: F, Missing: {missing}')
            );

        $this->logger->log(
            LogLevel::INFO,
            'Foo: {foo}, F: {f}, Missing: {missing}',
            [
                'foo' => 'FOO',
                'f'   => 'F',
            ]
        );
    }

    public function testLogIgnoresLowLogLevel(): void
    {
        $this->isolator
            ->expects($this->never())
            ->method('syslog');

        $this->logger = new Logger(__FILE__, LogLevel::INFO);

        $this->logger->setIsolator($this->isolator);
        $this->logger->debug('This should not be logged.');
    }

    public function testLogOnlyInitsOnce(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('openlog');

        $this->logger->info('one');
        $this->logger->info('two');
    }

    public function testEmergency(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::EMERGENCY]),
                $this->equalTo('Test emergency message')
            );

        $this->logger->emergency('Test emergency message');
    }

    public function testAlert(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::ALERT]),
                $this->equalTo('Test alert message')
            );

        $this->logger->alert('Test alert message');
    }

    public function testCritical(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::CRITICAL]),
                $this->equalTo('Test critical message')
            );

        $this->logger->critical('Test critical message');
    }

    public function testError(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::ERROR]),
                $this->equalTo('Test error message')
            );

        $this->logger->error('Test error message');
    }

    public function testWarning(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::WARNING]),
                $this->equalTo('Test warning message')
            );

        $this->logger->warning('Test warning message');
    }

    public function testNotice(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::NOTICE]),
                $this->equalTo('Test notice message')
            );

        $this->logger->notice('Test notice message');
    }

    public function testInfo(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::INFO]),
                $this->equalTo('Test info message')
            );

        $this->logger->info('Test info message');
    }

    public function testDebug(): void
    {
        $this->isolator
            ->expects($this->once())
            ->method('syslog')
            ->with(
                $this->equalTo($this->levels[LogLevel::DEBUG]),
                $this->equalTo('Test debug message')
            );

        $this->logger->debug('Test debug message');
    }

    public function testFacility(): void
    {
        $this->logger->setFacility(LOG_USER);
        $this->assertEquals(LOG_USER, $this->logger->getFacility());
    }

    public function testIdent(): void
    {
        $this->logger->setIdent("ProgramName");
        $this->assertEquals("ProgramName", $this->logger->getIdent());
    }

    public function testMinimumLogLevel(): void
    {
        $this->logger->setMinimumLogLevel("alert");

        $this->assertEquals(
            $this->levels["alert"],
            $this->logger->getMinimumLogLevel()
        );

        $this->assertEquals(
            "alert",
            $this->logger->getMinimumLogLevelString()
        );
    }

    public function testOptions(): void
    {
        $this->logger->setOptions(LOG_PID|LOG_NDELAY|LOG_PERROR);
        $this->assertEquals(
            LOG_PID|LOG_NDELAY|LOG_PERROR,
            $this->logger->getOptions()
        );
    }
}
