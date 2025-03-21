<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class RegistrationFormTypeTest extends TypeTestCase
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
            'pseudo' => 'TestUser',
            'agreeTerms' => true,
            'plainPassword' => 'SecurePass123!',
        ];

        $form = $this->factory->create(RegistrationFormType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit être synchronisé.');
        $this->assertTrue($form->isValid(), 'Le formulaire doit être valide.');

        $user = $form->getData();
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('TestUser', $user->getPseudo());
    }

    public function testInvalidEmail(): void
    {
        $formData = [
            'email' => 'invalid-email',
            'pseudo' => 'TestUser',
            'agreeTerms' => true,
            'plainPassword' => 'SecurePass123!',
        ];

        $form = $this->factory->create(RegistrationFormType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid(), 'Le formulaire doit être invalide avec un email incorrect.');
    }

    public function testMismatchedTermsAgreement(): void
    {
        $formData = [
            'email' => 'test@example.com',
            'pseudo' => 'TestUser',
            'agreeTerms' => false,
            'plainPassword' => 'SecurePass123!',
        ];

        $form = $this->factory->create(RegistrationFormType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid(), 'Le formulaire doit être invalide si les termes ne sont pas acceptés.');
    }

    public function testInvalidPasswordLength(): void
    {
        $formData = [
            'email' => 'test@example.com',
            'pseudo' => 'TestUser',
            'agreeTerms' => true,
            'plainPassword' => '123',
        ];

        $form = $this->factory->create(RegistrationFormType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid(), 'Le formulaire doit être invalide si le mot de passe est trop court.');
    }
}
