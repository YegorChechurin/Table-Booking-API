<?php

declare(strict_types=1);

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\FromDateIsSameAsDate;
use App\Validator\Constraints\FromDateIsSameAsDateValidator;

class FromDateIsSameAsDateValidatorTest extends AbstractConstraintValidatorTest
{
    protected function provideConstraintClass(): string
    {
        return FromDateIsSameAsDate::class;
    }

    protected function provideValidatorClass(): string
    {
        return FromDateIsSameAsDateValidator::class;
    }

    protected function getOptions(): ?array
    {
        return ['value' => 'stub'];
    }

    /**
     * @dataProvider differentDatesProvider
     */
    public function testDifferentDatesRaiseViolations(string $date, string $dateTime): void
    {
        $validator = $this->getValidator();
        $this->constraint->value = $date;
        $validator->validate($dateTime, $this->constraint);

        $this->assertNotEmpty($this->context->getViolations());
    }

    public function differentDatesProvider(): array
    {
        return [
            ['2020-01-08', '2020-01-18 17:45'],
            ['2020-05-08', '2020-11-08 01:00'],
        ];
    }

    /**
     * @dataProvider sameDatesProvider
     */
    public function testSameDatesPass(string $date, string $dateTime): void
    {
        $validator = $this->getValidator();
        $this->constraint->value = $date;
        $validator->validate($dateTime, $this->constraint);

        $this->assertEmpty($this->context->getViolations());
    }

    public function sameDatesProvider(): array
    {
        return [
            ['2020-01-08', '2020-01-08 17:45'],
            ['2020-05-08', '2020-05-08 01:00'],
        ];
    }
}
