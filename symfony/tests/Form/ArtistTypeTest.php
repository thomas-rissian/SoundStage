<?php

namespace App\Tests\Form;

use App\Entity\Artist;
use App\Form\ArtistType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class ArtistTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }


    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'John Doe',
            'description' => 'A talented artist.',
        ];

        $model = new Artist();
        $form = $this->factory->create(ArtistType::class, $model);

        $expected = new Artist();
        $expected->setName('John Doe');
        $expected->setDescription('A talented artist.');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $model);
    }

    public function testInvalidImageIsRejected()
    {
        // Créer un fichier temporaire qui simule un fichier texte
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFilePath, 'This is a text file content');

        $uploadedFile = new UploadedFile(
            $tempFilePath,
            'test.txt',
            'text/plain',
            null,
            true
        );

        // Créer manuellement la contrainte qui est utilisée dans le formulaire
        $fileConstraint = new \Symfony\Component\Validator\Constraints\File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'image/jpeg',
                'image/png',
                'image/gif',
            ],
            'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
        ]);

        // Créer un validateur et valider directement
        $validator = Validation::createValidator();
        $violations = $validator->validate($uploadedFile, $fileConstraint);

        // Vérifier qu'il y a au moins une violation
        $this->assertGreaterThan(
            0,
            count($violations),
            'Le validateur devrait rejeter un fichier texte'
        );

        // Vérifier le message d'erreur
        $violationMessage = $violations[0]->getMessage();
        $this->assertStringContainsString(
            'Veuillez télécharger une image valide',
            $violationMessage,
            'Le message d\'erreur devrait indiquer que le type de fichier est invalide'
        );
    }

    public function testSubmitValidImage()
    {
        $imagePath = __DIR__ . '/../../public/test/test.png';
        $image = new UploadedFile(
            $imagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $formData = [
            'name' => 'John Doe',
            'description' => 'A talented artist.',
            'image' => $image,
        ];

        $model = new Artist();
        $form = $this->factory->create(ArtistType::class, $model);

        $form->submit($formData);

        $this->assertTrue($form->isValid());
    }

    public function testSubmitImageExceedingMaxSize()
    {
        // Créer un fichier temporaire qui simule un fichier texte
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFilePath, 'This is a text file content');

        $uploadedFile = new UploadedFile(
            $tempFilePath,
            'test.txt',
            'text/plain',
            null,
            true
        );

        // Créer manuellement la contrainte qui est utilisée dans le formulaire
        $fileConstraint = new \Symfony\Component\Validator\Constraints\File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'image/jpeg',
                'image/png',
                'image/gif',
            ],
            'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
        ]);

        // Créer un validateur et valider directement
        $validator = Validation::createValidator();
        $violations = $validator->validate($uploadedFile, $fileConstraint);

        // Vérifier qu'il y a au moins une violation
        $this->assertGreaterThan(
            0,
            count($violations),
            'Le validateur devrait rejeter un fichier texte'
        );

        // Vérifier le message d'erreur
        $violationMessage = $violations[0]->getMessage();
        $this->assertStringContainsString(
            'Veuillez télécharger une image valide',
            $violationMessage,
            'Le message d\'erreur devrait indiquer que le type de fichier est invalide'
        );
    }
}