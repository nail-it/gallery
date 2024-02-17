<?php 
namespace App\Entity;

class Media extends AbstractMedia {
	private $description;
	private $descriptionDayDau;
	private $descriptionDaySon;
	private $descriptionDayMom;
	private $descriptionDayDad;
	private $tag;
	private $type;
    private $evolution;
    private $dayCode;
    private $horizontal;
	
	public function __construct(
		$year = '',
		$month = '',
		$day = '',
		$name = '',
		$description = '',
		$descriptionDayDau = '',
		$descriptionDaySon = '',
		$descriptionDayMom = '',
		$descriptionDayDad = '',
		$tag = '',
		$type = '',
		$horizontal = null
	) {
		$this->description = $description;
		$this->descriptionDayDau = $descriptionDayDau;
		$this->descriptionDaySon = $descriptionDaySon;
		$this->descriptionDayMom = $descriptionDayMom;
		$this->descriptionDayDad = $descriptionDayDad;
		$this->tag = $tag;
		$this->type = $type;
		$this->horizontal = $horizontal;
		
	    $code = new Code('krzysiu');
	    $this->dayCode = $code->encode($day, $month, $year);
	    
	    parent::__construct($year, $month, $day, $name);
	}

//	public function __set(string $name, mixed $value): void {
//
//	}

	public function getDescription() {
		return stripcslashes($this->description);
	}	

	public function getDescriptionDayDau() {
	    return stripcslashes($this->descriptionDayDau);
	}
	
	public function getDescriptionDaySon() {
		return stripcslashes($this->descriptionDaySon);
	}

	public function getDescriptionDayMom() {
		return stripcslashes($this->descriptionDayMom);
	}
	
	public function getDescriptionDayDad() {
		return stripcslashes($this->descriptionDayDad);
	}
	
	public function getTag() {
		return $this->tag;
	}	
		
	public function getType() {
		return $this->type;
	}

	public function setEvolution($bool) {
		$this->evolution = $bool;
	}
	
	public function getEvolution() {
		return $this->evolution;
	}

	public function getDayCode() {
		return $this->dayCode;
	}
	
	public function isHorizontal() {
		if($this->horizontal == true)
			return true;
		else 
			return false;
	}
	
}

?>