<?php

namespace App\Krzys\PageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
	
    public function testIndex($year = null, $month = null)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/gallery/2aaa/6');
        $this->assertFalse($client->getResponse()->isSuccessful());
        
        $address = '/gallery/'.date("Y", strtotime("+1 month")).'/'.date("m", strtotime("+1 month"));
        $crawler = $client->request('GET', $address);
        $this->assertFalse($client->getResponse()->isSuccessful());
    }
    
	
	
}
