<?php 
namespace App\Krzys\PageBundle\Util;

class DescriptionDay {
	
	private $text;
	private $who;
	
	public function __construct($text, $who) {
		$this->text = $text;
		$this->who = $who;
	}
	
	public function getText() {
		return $this->text;
	}
	
	public function getWho() {
		return $this->who;
	}
}
?>