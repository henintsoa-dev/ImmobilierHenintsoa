<?php

namespace Tests\UnitTests;

use App\Entity\Contact;
use App\Entity\Property;
use App\Notification\ContactNotification;
use App\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ContactNotificationTest extends TestCase
{
    public function testContactIfsNotified():void
    {
        $mailer = $this->getMockBuilder(MailerInterface::class)
            ->onlyMethods(['send'])
            ->getMock();

        $contactNotification = new ContactNotification($mailer);

        $property = (new Property)
            ->setTitle('Mon bien')
            ->setDescription('Ma description')
            ->setSurface(100)
            ->setBedrooms(2)
            ->setFloor(1)
            ->setPrice(250000)
            ->setHeat(1)
            ->setCity('Paris')
            ->setAddress('7 rue des patriarches')
            ->setPostalCode(75005)
            ->setSold(false);

        $contact = (new Contact())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setPhone('3315141312')
            ->setEmail('john@doe.com')
            ->setMessage('interested')
            ->setProperty($property);
        
        $mailer->expects($this->once())->method('send');

        $contactNotification->notify($contact);
    }

    public function testExceptionNotification()
    {
        $exception = new FileException("Your file could not be uploaded correctly", 500);
        $mailer = $this->getMockBuilder(MailerInterface::class)
            ->onlyMethods(['send'])
            ->getMock();
        $contactNotification = new ContactNotification($mailer);

        $mailer->expects($this->once())->method('send');

        $contactNotification->notifyException($exception);
    }
}