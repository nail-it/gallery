<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class GalleryMainTest extends WebTestCase
{

    public function testMainPage($mainSlogan = ''): void
    {
        $client = static::createClient();
        $response = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', '/* Oto jesteśmy! */');

	    $crawler = new Crawler($response->html());
	    $this->assertGreaterThanOrEqual(1, $crawler->filter('div.item-box')->count());
    }

	public function testEvolutionPage(): void
	{
		$client = static::createClient();
		$response = $client->request('GET', '/evolution');

		$this->assertResponseIsSuccessful();
		$this->assertSelectorTextContains('h1', 'Moja ewolucja');

		$crawler = new Crawler($response->html());
		$this->assertGreaterThanOrEqual(1, $crawler->filter('div.item-box')->count());

	}

	public function testBestPage(): void
	{
		$client = static::createClient();
		$client->request('GET', '/best');

		$this->assertResponseIsSuccessful();
		$this->assertSelectorTextContains('h1', 'The best of');
	}

	public function testVideoPage(): void
	{
		$client = static::createClient();
		$client->request('GET', '/video');

		$this->assertResponseIsSuccessful();
		$this->assertSelectorTextContains('h1', 'Wideo');
	}

	public function testSecuredPage(): void
	{
		$client = static::createClient();
		$client->request('GET', '/secured');

		$this->assertResponseRedirects();

		$client->followRedirect();
		$this->assertSelectorTextContains('h1', 'Logowanie');

	}
}
