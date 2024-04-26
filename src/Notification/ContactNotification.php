<?php
    namespace App\Notification;

use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ContactNotification {

        /**
         * @var MailerInterface
         */
        private $mailer;

        public function __construct(MailerInterface $mailer)
        {
            $this->mailer = $mailer;
        }

        public function notify(Contact $contact)
        {
            $message = (new TemplatedEmail())
                ->from('noreply@server.com')
                ->to('contact@agence.fr')
                ->subject('Agence : ' . $contact->getProperty()->getTitle())
                ->replyTo($contact->getEmail())
                ->htmlTemplate('emails/contact.html.twig',)
                ->context(['contact' => $contact]);
            
            $this->mailer->send($message);
        }

    }