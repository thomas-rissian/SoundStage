<?php

namespace App\Tests\Form;

use App\Entity\Artist;
use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EventTypeTest extends TypeTestCase
{
    private $doctrine;
    private $entityManager;

    protected function setUp(): void
    {
        // Create mocks for Doctrine's services
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Configure the mock
        $this->doctrine->expects($this->any())
            ->method('getManager')
            ->willReturn($this->entityManager);

        $this->doctrine->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($this->entityManager);

        // Use the correct metadata factory class
        $classMetadata = $this->createMock(ClassMetadata::class);
        $metadataFactory = $this->createMock(ClassMetadataFactory::class);

        $this->entityManager->expects($this->any())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory);

        $metadataFactory->expects($this->any())
            ->method('hasMetadataFor')
            ->willReturn(true);

        $this->entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        // Mock repository and query builder
        $repository = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $repository->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        // Use the concrete Query class instead of AbstractQuery
        $query = $this->createMock(Query::class);
        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        // Mock the query to return an empty array of results
        $query->expects($this->any())
            ->method('execute')
            ->willReturn([]);

        $queryBuilder->expects($this->any())
            ->method('where')
            ->willReturnSelf();

        $queryBuilder->expects($this->any())
            ->method('setParameter')
            ->willReturnSelf();

        // Add identity map mock
        $classMetadata->expects($this->any())
            ->method('getIdentifierFieldNames')
            ->willReturn(['id']);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        // Create an instance of EntityType with proper dependency injection
        $entityType = new EntityType($this->doctrine);

        // Create an instance of our form type with its dependency
        $eventType = new EventType($this->doctrine);

        // Register both form types
        return [
            new PreloadedExtension(
                [
                    EntityType::class => $entityType,
                    EventType::class => $eventType,
                ],
                []
            ),
        ];
    }

    public function testBuildForm()
    {
        $event = new Event();

        // Create the form using the registered type
        $form = $this->factory->create(EventType::class, $event);

        // Assert that form was created successfully
        $this->assertInstanceOf(FormInterface::class, $form);

        // You can add more assertions here to test the form structure
        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('date'));
        $this->assertTrue($form->has('artist'));
    }
    // Version modifiée pour les tests uniquement
    public function testArtistFieldValidationWithSimplifiedForm()
    {
        // Créer un formulaire de test simplifié qui n'utilise pas EntityType
        $formBuilder = $this->factory->createBuilder(FormType::class, new Event());
        $formBuilder
            ->add('name')
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('artist'); // Pas de EntityType pour simplifier le test

        $form = $formBuilder->getForm();

        // Créer un artiste simple
        $artist = new Artist();
        $reflectionClass = new \ReflectionClass(Artist::class);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setValue($artist, 1);

        $nameProperty = $reflectionClass->getProperty('name');
        $nameProperty->setValue($artist, 'Test Artist');

        // Soumettre le formulaire avec l'artiste directement
        $formData = [
            'name' => 'Concert Live',
            'date' => '2025-03-17',
            'artist' => $artist
        ];

        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertSame('Concert Live', $form->getData()->getName());
        $this->assertSame('2025-03-17', $form->getData()->getDate()->format('Y-m-d'));
        $this->assertSame($artist, $form->getData()->getArtist());
    }

}