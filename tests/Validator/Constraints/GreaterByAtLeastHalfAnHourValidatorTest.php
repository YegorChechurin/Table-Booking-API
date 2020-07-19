<?php

declare(strict_types=1);

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\GreaterByAtLeastHalfAnHour;
use App\Validator\Constraints\GreaterByAtLeastHalfAnHourValidator;

class GreaterByAtLeastHalfAnHourValidatorTest extends AbstractConstraintValidatorTest
{
    protected function provideConstraintClass(): string
    {
        return GreaterByAtLeastHalfAnHour::class;
    }

    protected function provideValidatorClass(): string
    {
        return GreaterByAtLeastHalfAnHourValidator::class;
    }

    protected function getOptions(): ?array
    {
        return ['value' => 'stub'];
    }

    /**
     * @dataProvider intervalsLessThanHalfAnHourProvider
     */
    public function testIntervalsLessThanHalfAnHourRaiseViolations(string $startTime, string $endTime): void
    {
        $validator = $this->getValidator();
        $this->constraint->value = $startTime;
        $validator->validate($endTime, $this->constraint);

        $this->assertNotEmpty($this->context->getViolations());
    }

    public function intervalsLessThanHalfAnHourProvider(): array
    {
        return [
            ['2020-01-08 17:30', '2020-01-08 17:45'],
            ['2020-05-08 00:50', '2020-05-08 01:00'],
            ['2020-05-08 14:00', '2020-05-08 14:29'],
        ];
    }

    /**
     * @dataProvider intervalsEqualOrGreaterThanHalfAnHourProvider
     */
    public function testIntervalsEqualOrGreaterThanHalfAnHourPass(string $startTime, string $endTime): void
    {
        $validator = $this->getValidator();
        $this->constraint->value = $startTime;
        $validator->validate($endTime, $this->constraint);

        $this->assertEmpty($this->context->getViolations());
    }

    public function intervalsEqualOrGreaterThanHalfAnHourProvider(): array
    {
        return [
            ['2020-01-08 17:30', '2020-01-08 18:00'],
            ['2020-05-08 00:50', '2020-05-08 01:40'],
            ['2020-05-08 14:00', '2020-05-08 18:29'],
            ['2020-05-08 23:00', '2020-05-09 01:00'],
        ];
    }
}
