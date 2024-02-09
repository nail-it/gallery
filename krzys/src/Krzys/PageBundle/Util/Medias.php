<?php 
namespace App\Krzys\PageBundle\Util;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Medias {
	private $medias;
	
	public function add($media) {
	    $this->medias[] = $media;
	}
	
	public function getAll() {
		return $this->medias;
	}
}
?>