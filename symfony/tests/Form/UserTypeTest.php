<?php

namespace App\Tests\Form;

use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class UserTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();
        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'email' => 'test@example.com',
            'password' => [
                'first' => 'Password123!',
                'second' => 'Password123!',
            ],
        ];

        $form = $this->factory->create(UserType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit être synchronisé.');
        $this->assertTrue($form->isValid(), 'Le formulaire doit être valide.');

        $this->assertSame('test@example.com', $form->get('email')->getData());
        $this->assertSame('Password123!', $form->get('password')->get('first')->getData());
        $this->assertSame('Password123!', $form->get('password')->get('second')->getData());
    }

    public function testInvalidEmail(): void
    {
        $formData = [
            'email' => 'invalid-email',
            'password' => [
                'first' => 'Password123!',
                'second' => 'Password123!',
            ],
        ];

        $form = $this->factory->create(UserType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid(), 'Le formulaire doit être invalide avec un email incorrect.');
    }

    public function testMismatchedPasswords(): void
    {
        $formData = [
            'email' => 'test@example.com',
            'password' => [
                'first' => 'Password123!',
                'second' => 'WrongPassword!',
            ],
        ];

        $form = $this->factory->create(UserType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid(), 'Le formulaire doit être invalide si les mots de passe ne correspondent pas.');
    }
}
