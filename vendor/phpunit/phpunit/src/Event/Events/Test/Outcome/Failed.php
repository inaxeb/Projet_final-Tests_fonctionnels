<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Test;

use const PHP_EOL;
use function sprintf;
use function trim;
use PHPUnit\Event\Code;
use PHPUnit\Event\Code\ComparisonFailure;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Failed implements Event
{
    private Telemetry\Info $telemetryInfo;
    private Code\Test $test;
    private Throwable $throwable;
    private ?ComparisonFailure $comparisonFailure;

    public function __construct(Telemetry\Info $telemetryInfo, Code\Test $test, Throwable $throwable, ?ComparisonFailure $comparisonFailure)
    {
        $this->telemetryInfo     = $telemetryInfo;
        $this->test              = $test;
        $this->throwable         = $throwable;
        $this->comparisonFailure = $comparisonFailure;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function test(): Code\Test
    {
        return $this->test;
    }

    public function throwable(): Throwable
    {
        return $this->throwable;
    }

    /**
     * @phpstan-assert-if-true !null $this->comparisonFailure
     */
    public function hasComparisonFailure(): bool
    {
        return $this->comparisonFailure !== null;
    }

    /**
     * @throws NoComparisonFailureException
     */
    public function comparisonFailure(): ComparisonFailure
    {
        if ($this->comparisonFailure === null) {
            throw new NoComparisonFailureException;
        }

        return $this->comparisonFailure;
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        $message = trim($this->throwable->message());

        if ($message !== '') {
            $message = PHP_EOL . $message;
        }

        return sprintf(
            'Test Failed (%s)%s',
            $this->test->id(),
            $message,
        );
    }
}
