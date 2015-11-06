<?php
/**
 * @file LoggerTest.php
 *
 * Created: 10/27/15 8:40 AM
 * $Id$
 */

namespace Cxj;

use Psr\Log\LogLevel;



/**
 * @property mixed isolator
 * @mixin \Icecave\Isolator\Isolator
 * @mixin \Cxj\Logger
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
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

    public function testLogWithPlaceholderValues()
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

    public function testLogIgnoresLowLogLevel()
    {
        $this->isolator
            ->expects($this->never())
            ->method('syslog');

        $this->logger = new Logger(__FILE__, LogLevel::INFO);

        $this->logger->setIsolator($this->isolator);
        $this->logger->debug('This should not be logged.');
    }

    public function testLogOnlyInitsOnce()
    {
        $this->isolator
            ->expects($this->once())
            ->method('openlog');

        $this->logger->info('one');
        $this->logger->info('two');
    }

    public function testEmergency()
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

    public function testAlert()
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

    public function testCritical()
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

    public function testError()
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

    public function testWarning()
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

    public function testNotice()
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

    public function testInfo()
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

    public function testDebug()
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
}
