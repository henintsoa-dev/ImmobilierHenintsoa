<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Image;

#[Route('/admin/property')]
class AdminPropertyController extends AbstractController
{
    /**
     * @var FileUploader $fileUploader
     * 
     */
    private $fileUploader;

    private $targetDirectory;

    public function __construct(FileUploader $fileUploader, string $targetDirectory)
    {
        $this->fileUploader = $fileUploader;
        $this->targetDirectory = $targetDirectory;
    }

    #[Route('/', name: 'app_admin_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('admin_property/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $photoMax    = 4;

        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            // dd($images);

            /** @var Image $image **/
            foreach ($images as $image) {
                if ($image->getFile()) {
                    $fileName = $this->fileUploader->upload($image->getFile());
                    $image->setName($fileName);

                    $property->addImage($image);
                }
            }
            
            $entityManager->persist($property);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_property/new.html.twig', [
            'property' => $property,
            'form' => $form,
            'photoMax'  => $photoMax
        ]);
    }

    #[Route('/{id}', name: 'app_admin_property_show', methods: ['GET'])]
    public function show(Property $property): Response
    {
        return $this->render('admin_property/show.html.twig', [
            'property' => $property,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_property_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        $photoMax    = 4;
        $initialImages = [];
        $finalImages = [];

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($property->getImages() as $image) {
            $initialImages[]  = $image->getName();
        }

        $form = $this->createForm(PropertyType::class, $property, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            /** @var Image $image **/
            foreach ($images as $image) {
                if ($image->getFile()) {
                    $fileName = $this->fileUploader->upload($image->getFile());
                    $image->setName($fileName);

                    $property->addImage($image);
                }
            }
            
            $entityManager->flush();
            
            foreach($property->getImages() as $finalImage) {
                $finalImages[] = $finalImage->getName();
            }

            foreach ($initialImages as $initialImage) {
                if (!in_array($initialImage, $finalImages)) {
                    $fileExist = file_exists($this->targetDirectory . $initialImage);

                    if ($fileExist) {
                        unlink($this->targetDirectory . $initialImage);
                    }
                }
            }
            
            $this->addFlash('success', 'Bien modifié avec succès');
            // return $this->redirectToRoute('app_admin_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_property/edit.html.twig', [
            'property' => $property,
            'form' => $form,
            'photoMax'  => $photoMax,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->getPayload()->get('_token'))) {
            $imageArray = [];
            $imagesAnnonce = $property->getImages();

            foreach ($imagesAnnonce as $imageAnnonce) {
                $imageArray[] = $imageAnnonce->getName();
            }

            $entityManager->remove($property);
            $entityManager->flush();

            foreach ($imageArray as $singleImage) {
                $imagePath = $this->targetDirectory . $singleImage;
                unlink($imagePath);
            }

            $this->addFlash('success', 'Bien supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_property_index', [], Response::HTTP_SEE_OTHER);
    }
}
