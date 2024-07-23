<?php

namespace App\Tests\ApplicationTests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testDisplayLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertSelectorTextContains('h1', 'Please sign in');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials(): void
    {
        $client = static::createClient();
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadAliceFixture([
            __DIR__ . '/users.yaml'
        ]);

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'john@doe.fr',
            'password' => 'fakepassword'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'john@doe.fr',
            'password' => '0000'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/admin/property/');
    }
}
