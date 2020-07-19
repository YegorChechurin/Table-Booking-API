<?php

declare(strict_types=1);

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\ExistentTableId;
use App\Validator\Constraints\ExistentTableIdValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ExistentTableIdValidatorTest extends AbstractConstraintValidatorTest
{
    protected function provideConstraintClass(): string
    {
        return ExistentTableId::class;
    }

    protected function provideValidatorClass(): string
    {
        return ExistentTableIdValidator::class;
    }

    protected function getOptions(): ?array
    {
        return null;
    }

    public function testStringInputIsNotAccepted(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $validator = $this->getValidator();
        $validator->validate('string_input', $this->constraint);
    }

    public function testSFloatInputIsNotAccepted(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $validator = $this->getValidator();
        $validator->validate(1.58, $this->constraint);
    }

    /**
     * @dataProvider nonExistentTableIdsProvider
     */
    public function testNonExistentTableIdsRaiseViolations(int $nonExistentTableId): void
    {
        $validator = $this->getValidator();
        $validator->validate($nonExistentTableId, $this->constraint);

        $this->assertNotEmpty($this->context->getViolations());
    }

    public function nonExistentTableIdsProvider(): array
    {
        return [[-1], [-5], [6], [10]];
    }

    /**
     * @dataProvider existentTableIdsProvider
     */
    public function testExistentTableIdsPass(int $existentTableId): void
    {
        $validator = $this->getValidator();
        $validator->validate($existentTableId, $this->constraint);

        $this->assertEmpty($this->context->getViolations());
    }

    public function existentTableIdsProvider(): array
    {
        return [[1], [2], [3], [4], [5]];
    }
}
