<?php

declare(strict_types=1);

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\CurrentDateOrGreater;
use App\Validator\Constraints\CurrentDateOrGreaterValidator;

class CurrentDateOrGreaterValidatorTest extends AbstractConstraintValidatorTest
{
    protected function provideConstraintClass(): string
    {
        return CurrentDateOrGreater::class;
    }

    protected function provideValidatorClass(): string
    {
        return CurrentDateOrGreaterValidator::class;
    }

    protected function getOptions(): ?array
    {
        return null;
    }

    /**
     * @dataProvider timeIntervalsProvider
     */
    public function testDatesEarlierThanTodayProduceViolations(string $timeInterval): void
    {
        $validator = new CurrentDateOrGreaterValidator();
        $validator->initialize($this->context);

        $now = new \DateTimeImmutable();
        $today = new \DateTimeImmutable($now->format('Y-m-d'));
        $earlierThanTodayByInterval = $today->sub(\DateInterval::createFromDateString($timeInterval));

        $validator->validate($earlierThanTodayByInterval->format('Y-m-d'), $this->constraint);
        $this->assertNotEmpty($this->context->getViolations());
    }

    /**
     * @dataProvider timeIntervalsProvider
     */
    public function testDatesGreaterThanTodayPassValidation(string $timeInterval): void
    {
        $validator = new CurrentDateOrGreaterValidator();
        $validator->initialize($this->context);

        $now = new \DateTimeImmutable();
        $today = new \DateTimeImmutable($now->format('Y-m-d'));
        $greaterThanTodayByInterval = $today->add(\DateInterval::createFromDateString($timeInterval));

        $validator->validate($greaterThanTodayByInterval->format('Y-m-d'), $this->constraint);
        $this->assertEmpty($this->context->getViolations());
    }

    public function timeIntervalsProvider(): array
    {
        return [
            ['1 minute'],
            ['1 hour'],
            ['1 day'],
            ['1 month'],
            ['1 week'],
            ['1 year']
        ];
    }
}
