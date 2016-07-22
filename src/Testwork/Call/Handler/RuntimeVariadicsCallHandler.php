<?php

namespace FriendsOfBehat\PerformanceExtension\Testwork\Call\Handler;

use Behat\Testwork\Call\Call;
use Behat\Testwork\Call\CallResult;
use Behat\Testwork\Call\Exception\CallErrorException;
use Behat\Testwork\Call\Handler\CallHandler;
use Behat\Testwork\Call\Handler\RuntimeCallHandler;

/**
 * Replaces `call_user_func_array($callable, $arguments)` with `$callable(...$arguments)`.
 *
 * @see RuntimeCallHandler
 *
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class RuntimeVariadicsCallHandler implements CallHandler
{
    /**
     * @var int
     */
    private $errorReportingLevel;

    /**
     * @var bool
     */
    private $obStarted = false;

    /**
     * @param int $errorReportingLevel
     */
    public function __construct($errorReportingLevel = E_ALL)
    {
        $this->errorReportingLevel = $errorReportingLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsCall(Call $call)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handleCall(Call $call)
    {
        $this->startErrorAndOutputBuffering($call);

        $result = $this->executeCall($call);

        $this->stopErrorAndOutputBuffering();

        return $result;
    }

    /**
     * @param Call $call
     *
     * @return CallResult
     */
    private function executeCall(Call $call)
    {
        $callable = $call->getBoundCallable();
        $arguments = array_values($call->getArguments());

        $return = $exception = null;

        try {
            $return = $callable(...$arguments);
        } catch (\Exception $caught) {
            $exception = $caught;
        }

        $stdOut = $this->getBufferedStdOut();

        return new CallResult($call, $return, $exception, $stdOut);
    }

    /**
     * @return string|null
     */
    private function getBufferedStdOut()
    {
        return ob_get_length() ? ob_get_contents() : null;
    }

    /**
     * @param Call $call
     */
    private function startErrorAndOutputBuffering(Call $call)
    {
        $errorHandler = function ($level, $message, $file, $line) {
            if ($this->errorLevelIsNotReportable($level)) {
                return false;
            }

            throw new CallErrorException($level, $message, $file, $line);
        };
        $errorReporting = $call->getErrorReportingLevel() ?: $this->errorReportingLevel;

        set_error_handler($errorHandler, $errorReporting);

        $this->obStarted = ob_start();
    }

    private function stopErrorAndOutputBuffering()
    {
        restore_error_handler();

        if ($this->obStarted) {
            ob_end_clean();
        }
    }

    /**
     * @param int $level
     *
     * @return bool
     */
    private function errorLevelIsNotReportable($level)
    {
        return !(error_reporting() & $level);
    }
}
