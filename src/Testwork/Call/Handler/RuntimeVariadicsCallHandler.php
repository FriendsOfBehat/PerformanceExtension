<?php

declare(strict_types=1);

/*
 * This file is part of the PerformanceExtension package.
 *
 * (c) Kamil Kokot <kamil@kokot.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function supportsCall(Call $call): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handleCall(Call $call): CallResult
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
    private function executeCall(Call $call): CallResult
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
    private function getBufferedStdOut(): ?string
    {
        return ob_get_length() ? ob_get_contents() : null;
    }

    /**
     * @param Call $call
     */
    private function startErrorAndOutputBuffering(Call $call): void
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

    private function stopErrorAndOutputBuffering(): void
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
    private function errorLevelIsNotReportable(int $level): bool
    {
        return !(error_reporting() & $level);
    }
}
