<?php
namespace Krzys\SecurityBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlSuccessfull
     */
    public function testPageIsSuccessful($url)
    {
    	
    	$client = self::createClient();
    	
        $crawler = $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());        
/*        
        if (!$client->getResponse()->isSuccessful()) {
        	$block = $crawler->filter('div.text-exception > h1');
        	if ($block->count()) {
        		$error = $block->text();
        	}
        }        
        print_r($error);
*/
    }

    /**
     * @dataProvider urlRedirect
     */
    public function testPageIsRedirect($url)
    {
    	 
    	$client = self::createClient();
    	 
    	$crawler = $client->request('GET', $url);
    
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    }    
    
    public function urlSuccessfull()
    {
        return array(
            array('/login'),
        	array('/evolution'),
        	array('/best'),
        	array('/gallery'),
        	array('/gallery2'),
        	array('/drw'),
        	array('/spr'),
        );
    }

    public function urlRedirect()
    {
    	return array(
    		array('/gallery/secured')
    	);
    }
}