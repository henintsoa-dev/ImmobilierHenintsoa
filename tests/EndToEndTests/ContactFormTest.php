<?php

namespace App\Tests\EndToEndTests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Panther\PantherTestCase;

class ContactFormTest extends PantherTestCase
{
    public function testContactFormSent(): void
    {
        $client = static::createPantherClient();
        $client->request(Request::METHOD_GET, '/biens/lorem-ipsum-1');

        $this->assertSelectorExists('h2', 'Caractéristiques');

        $client->clickLink('Contacter l\'agence');
        $client->wait(3);

        $crawler = $client->waitForVisibility('#contactForm');
        $form = $crawler->selectButton('Envoyer')->form([
            'contact[firstName]' => 'Nhoj',
            'contact[lastName]' => 'EOD',
            'contact[phone]' => '0615141312',
            'contact[email]' => 'nhoj@eod.com',
            'contact[message]' => 'lorem ipsum',
        ]);

        $client->submit($form);
        $client->wait(3);

        $this->assertSelectorExists('.alert.alert-success', 'Votre email a bien été envoyé');
    }
}
