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
use App\Utils\Form\FormHelper;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin/property')]
class AdminPropertyController extends AbstractController
{
    /**
     * @var FileUploader $fileUploader
     * 
     */
    private $fileUploader;

    private $targetDirectory;

    const CURRENT_MENU = "admin_property";

    public function __construct(FileUploader $fileUploader, string $targetDirectory)
    {
        $this->fileUploader = $fileUploader;
        $this->targetDirectory = $targetDirectory;
    }

    #[Route('/', name: 'app_admin_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('admin_property/index.html.twig', [
            'current_menu' => self::CURRENT_MENU,
            'properties' => $propertyRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_admin_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $photoMax    = 4;

        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $images = $form->get('images')->getData();

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
                
                $redirectParams = [];
                $redirectRoute = 'app_admin_property_index';
                
                if ($request->isXmlHttpRequest()) {
                    $url = $this->generateUrl($redirectRoute, $redirectParams);

                    $this->addFlash('success', 'Bien modifié avec succès');
                    
                    return new JsonResponse([
                        'success' => true,
                        'url' => $url
                    ]);
                } else {
                    return $this->redirectToRoute($redirectRoute, $redirectParams);
                }
            }
            // If it's an AJAX request...
            elseif ($request->isXmlHttpRequest()) {
                $env = $this->getParameter('kernel.environment');

                $response = new JsonResponse([
                    'type' => 'validation_error',
                    'errors' => FormHelper::getFormErrors($form)
                ], 400);

                if ($env === 'dev') {
                    $response->headers->set('Symfony-Debug-Toolbar-Replace', 1);
                }

                return $response;
            }
        }

        return $this->render('admin_property/new.html.twig', [
            'current_menu' => self::CURRENT_MENU,
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
    public function edit(Request $request, Property $property, EntityManagerInterface $entityManager, CacheManager $cacheManager): Response
    {
        $photoMax    = 4;
        $initialImages = [];
        $finalImages = [];

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($property->getImages() as $image) {
            $initialImages[]  = $image->getName();
        }

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
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
                
                $redirectParams = [ 'id' => $property->getId() ];
                $redirectRoute = 'app_admin_property_edit';
                
                if ($request->isXmlHttpRequest()) {
                    $url = $this->generateUrl($redirectRoute, $redirectParams);

                    $this->addFlash('success', 'Bien modifié avec succès');
                    
                    return new JsonResponse([
                        'success' => true,
                        'url' => $url
                    ]);
                } else {
                    return $this->redirectToRoute($redirectRoute, $redirectParams);
                }
            } 
            // If it's an AJAX request...
            elseif ($request->isXmlHttpRequest()) {
                $env = $this->getParameter('kernel.environment');

                $response = new JsonResponse([
                    'type' => 'validation_error',
                    'errors' => FormHelper::getFormErrors($form)
                ], 400);

                if ($env === 'dev') {
                    $response->headers->set('Symfony-Debug-Toolbar-Replace', 1);
                }

                return $response;
            }
            
        }

        return $this->render('admin_property/edit.html.twig', [
            'current_menu' => self::CURRENT_MENU,
            'property' => $property,
            'form' => $form,
            'photoMax'  => $photoMax,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, EntityManagerInterface $entityManager, CacheManager $cacheManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->getPayload()->get('_token'))) {
            $imageArray = [];
            $images = $property->getImages();
            
            foreach ($images as $image) {
                $imageArray[] = $image->getName();
            }

            $entityManager->remove($property);
            $entityManager->flush();

            foreach ($imageArray as $key => $singleImage) {
                $imagePath = $this->targetDirectory . $singleImage;
                unlink($imagePath);
            }

            $this->addFlash('success', 'Bien supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_property_index', [], Response::HTTP_SEE_OTHER);
    }
}
