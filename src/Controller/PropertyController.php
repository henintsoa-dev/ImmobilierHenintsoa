<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PropertyController extends AbstractController
{
    /** 
     * @var EntityManagerInterface $em 
     */
    private $em;

    /** 
     * @var PropertyRepository $repository 
     */
    private $repository;

    public function __construct(EntityManagerInterface $em, PropertyRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    #[Route('/biens', name: 'properties.index')]
    public function index(): Response
    {
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties',
        ]);
    }

    #[Route('/biens/{slug}-{id}', name: 'property.show', requirements: [
        'slug' => '[a-z0-9\-]*',
    ])]
    public function show(Property $property, string $slug): Response
    {
        if ($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301);
        }

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => 'properties',
        ]);
    }
}
