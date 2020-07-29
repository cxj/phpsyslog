<?php
/**
 * @file LoggerTest.php
 */

namespace Cxj;

use phpmock\phpunit\PHPMock;
use Psr\Log\LogLevel;


/**
 * @property mixed isolator
 * @mixin Logger
 */
class LoggerTest extends \PHPUnit\Framework\TestCase
{
    use PHPMock;

    private $openlog;
    private $syslog;

    public function setUp(): void
    {
        $this->openlog = $this->getFunctionMock(__NAMESPACE__, "openlog");
        $this->openlog->expects($this->any())->willReturn(true);

        $this->syslog = $this->getFunctionMock(__NAMESPACE__, "syslog");

        $this->logger = new Logger(__FILE__, LogLevel::DEBUG);

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
        $this->syslog
            ->expects($this->once())
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
        $this->syslog->expects($this->never());

        $logger = new Logger(__FILE__, LogLevel::INFO);
        $logger->debug('This should not be logged.');
    }

    public function testLogOnlyInitsOnce(): void
    {
        $this->openlog->expects($this->once());

        $this->logger->info('one');
        $this->logger->info('two');
    }

    public function testEmergency(): void
    {
        $this->syslog
            ->expects($this->once())
            ->with(
                $this->equalTo($this->levels[LogLevel::EMERGENCY]),
                $this->equalTo('Test emergency message')
            );

        $this->logger->emergency('Test emergency message');
    }

    public function testAlert(): void
    {
        $this->syslog
            ->expects($this->once())
            ->with(
                $this->equalTo($this->levels[LogLevel::ALERT]),
                $this->equalTo('Test alert message')
            );

        $this->logger->alert('Test alert message');
    }

    public function testCritical(): void
    {
        $this->syslog
            ->expects($this->once())
            ->with(
                $this->equalTo($this->levels[LogLevel::CRITICAL]),
                $this->equalTo('Test critical message')
            );

        $this->logger->critical('Test critical message');
    }

    public function testError(): void
    {
        $this->syslog
            ->expects($this->once())
            ->with(
                $this->equalTo($this->levels[LogLevel::ERROR]),
                $this->equalTo('Test error message')
            );

        $this->logger->error('Test error message');
    }

    public function testWarning(): void
    {
        $this->syslog
            ->expects($this->once())
            ->with(
                $this->equalTo($this->levels[LogLevel::WARNING]),
                $this->equalTo('Test warning message')
            );

        $this->logger->warning('Test warning message');
    }

    public function testNotice(): void
    {
        $this->syslog
            ->expects($this->once())
            ->with(
                $this->equalTo($this->levels[LogLevel::NOTICE]),
                $this->equalTo('Test notice message')
            );

        $this->logger->notice('Test notice message');
    }

    public function testInfo(): void
    {
        $this->syslog
            ->expects($this->once())
            ->with(
                $this->equalTo($this->levels[LogLevel::INFO]),
                $this->equalTo('Test info message')
            );

        $this->logger->info('Test info message');
    }

    public function testDebug(): void
    {
        $this->syslog
            ->expects($this->once())
            ->with(
                $this->equalTo($this->levels[LogLevel::DEBUG]),
                $this->equalTo('Test debug message')
            );

        $this->logger->debug('Test debug message');
    }

    public function testFacility(): void
    {
        $this->syslog->expects($this->never());
        $this->logger->setFacility(LOG_USER);
        $this->assertEquals(LOG_USER, $this->logger->getFacility());
    }

    public function testIdent(): void
    {
        $this->syslog->expects($this->never());
        $this->logger->setIdent("ProgramName");
        $this->assertEquals("ProgramName", $this->logger->getIdent());
    }

    public function testMinimumLogLevel(): void
    {
        $this->syslog->expects($this->never());
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
        $this->syslog->expects($this->never());
        $this->logger->setOptions(LOG_PID | LOG_NDELAY | LOG_PERROR);
        $this->assertEquals(
            LOG_PID | LOG_NDELAY | LOG_PERROR,
            $this->logger->getOptions()
        );
    }
}
