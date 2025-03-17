<?php

namespace App\Tests\Form;

use App\Entity\Artist;
use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query; // Use the specific Query class
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
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
    public function testArtistFieldValidationWithMock()
    {
        $artist = $this->entityManager->getRepository(Artist::class)->find(1);
        dump($artist);

        // Créer un événement et le formulaire
        $event = new Event();
        $form = $this->factory->create(EventType::class, $event);

        // Soumettre le formulaire avec l'artiste mocké
        $form->submit([
            'name' => 'Concert Live',
            'date' => '2025-03-17',
            'artist' => $artist,
        ]);
        dump($form->getData());
        $this->assertTrue($form->isValid());
        $this->assertSame('Concert Live', $event->getName());
        $this->assertSame('2025-03-17', $event->getDate()->format('Y-m-d'));
        $this->assertSame($artist, $event->getArtist());
    }

}