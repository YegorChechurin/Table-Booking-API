<?php

declare(strict_types=1);

namespace App\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractConstraintValidatorTest extends TestCase
{
    protected ExecutionContext $context;

    protected Constraint $constraint;

    protected string $constraintClass;

    protected string $validatorClass;

    abstract protected function provideConstraintClass(): string;

    abstract protected function provideValidatorClass(): string;

    abstract protected function getOptions(): ?array;

    public function setUp(): void
    {
        $this->assignConstraintAndValidatorClasses();
        $this->context = new ExecutionContext(
            $this->createMock(ValidatorInterface::class),
            'test',
            $this->createMock(TranslatorInterface::class)
        );
        $this->constraint = new $this->constraintClass($this->getOptions());
        $this->context->setConstraint($this->constraint);
    }

    protected function getValidator(): ConstraintValidator
    {
        $validator = new $this->validatorClass();
        $validator->initialize($this->context);

        return $validator;
    }

    private function assignConstraintAndValidatorClasses(): void
    {
        if (empty($this->constraintClass)) {
            $this->constraintClass = $this->provideConstraintClass();
        }

        if (empty($this->validatorClass)) {
            $this->validatorClass = $this->provideValidatorClass();
        }
    }
}