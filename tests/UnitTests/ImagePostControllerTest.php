<?php

namespace Tests\UnitTests;

use App\Notification\ContactNotification;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ImagePostControllerTest extends KernelTestCase {
    
    public function testImageIsUploaded():void {
        $mailer = $this->createMock(MailerInterface::class);
        $fileUploader = new FileUploader(__DIR__.'/tests/UnitTests/uploads/images/', new AsciiSlugger(), new ContactNotification($mailer));
        
        $uploadedFile = new UploadedFile(
            __DIR__ . '/fixtures/207308098-real-estate-flat-design.jpg', 
            '207308098-real-estate-flat-design.jpg'
        );

        $filename = $fileUploader->upload($uploadedFile);

        $this->assertIsString($filename);
    }

}