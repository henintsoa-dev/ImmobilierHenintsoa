<?php

namespace App\Tests\EndToEndTests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Panther\PantherTestCase;

class PropertyFormTest extends PantherTestCase
{
  public function testPropertyFormSent(): void
  {
    $client = static::createPantherClient();
    $crawler = $client->request(Request::METHOD_GET, '/login');

    $this->assertSelectorTextContains(
      'h1',
      'Please sign in'
    );

    $form = $crawler->selectButton('Sign in')->form([
      'email' => 'john@doe.com',
      'password' => 'demo'
    ]);

    $client->submit($form);

    $this->assertSelectorTextContains(
      'h1',
      'Gérer les biens'
    );

    $crawler = $client->request(Request::METHOD_GET, '/admin/property/new');

    $this->assertSelectorTextContains(
      'h1',
      'Créer un nouveau bien'
    );

    $client->wait(3);

    $propertyName = uniqid();
    $form = $crawler->selectButton('Enregistrer')->form([
      'property[title]' => $propertyName,
      'property[surface]' => 100,
      'property[price]' => 100000,
      'property[rooms]' => 3,
      'property[bedrooms]' => 1,
      'property[floor]' => 3,
      'property[heat]' => 1,
      'property[address]' => '7 r des patriarches',
      'property[city]' => 'Paris',
      'property[postal_code]' => '75005',
      'property[description]' => 'Lorem ipsum',
      'property[sold]' => 0,
    ]);
    $client->wait(3);
    $client->submit($form);
    $client->wait(10);
    $crawler = $client->request(Request::METHOD_GET, '/admin/property/');

    $this->assertSelectorTextContains(
      'h1',
      'Gérer les biens'
    );

    $this->assertSelectorTextContains(
      'td',
      $propertyName
    );
  }
}
