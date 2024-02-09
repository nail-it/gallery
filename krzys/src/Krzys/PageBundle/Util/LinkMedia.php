<?php 
namespace App\Krzys\PageBundle\Util;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nailit\GalleryBundle\Entity\AbstractMedia;

class LinkMedia extends AbstractMedia {
    private $file;
	private $sourceName;
	private $tag;	
	
	public function __construct($file, $name) {
		//die($name);
		$stack = explode('/',$name);
		$this->file = array_pop($stack);
		array_pop($stack); //thumb
		$day = array_pop($stack);
		$month = array_pop($stack);		
		$year = array_pop($stack);
		$this->sourceName = preg_replace('/\/thumb\//', '/', $name);
		$this->name = $name;
		/* backward compatibility, for no tag */
		$this->tag = '';
		if(substr($file, -2, 1) == '-')
			$this->tag = substr($file, -1);

		parent::__construct($year, $month, $day, $name);
	}
	

	public function getFile() {
		return $this->file;
	}	
	
	public function getSourceName() {
		return $this->sourceName;
	}
		
	public function getTag() {
		return $this->tag;
	}	
	
}

?>