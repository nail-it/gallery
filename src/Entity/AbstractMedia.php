<?php 
namespace App\Entity;

abstract class AbstractMedia {
	protected $name;
	protected $year;
	protected $month;
	protected $day;
	protected $amountDays;
	protected $amountWeeks;
	protected $amountDays2;
	protected $amountWeeks2;

	protected $yearForAmountDays;
	protected $yearForAmountDays2;
	public $monthForAmountDays;
	protected $monthForAmountDays2;
	
	public function __construct($year, $month, $day, $name) {
		$this->name = $name;
		$this->day = $day;
		$this->month = $month;
		$this->year = $year;
		
		$time = new MyTime();
		$this->amountDays = $time->getAmountDays(mktime(0, 0, 0, $this->month, $this->day, $this->year));
		$this->amountWeeks = ceil($this->amountDays/7);
		$this->amountDays2 = $time->getAmountDays2(mktime(0, 0, 0, $this->month, $this->day, $this->year));
		$this->amountWeeks2 = ceil($this->amountDays2/7);

		$this->monthForAmountDays = $time->getMonthFromBeginForAmountDays2($this->amountDays);
		$this->yearForAmountDays = $time->getYearFromBeginForAmountDays2($this->amountDays);
		
		$this->monthForAmountDays2 = $time->getMonthFromBeginForAmountDays($this->amountDays2);
		$this->yearForAmountDays2 = $time->getYearFromBeginForAmountDays($this->amountDays2);
	}
	
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	public function getYear() {
		return $this->year;
	}
	public function setYear($year) {
		$this->year = $year;
		return $this;
	}
	public function getMonth() {
		return $this->month;
	}
	public function setMonth($month) {
		$this->month = $month;
		return $this;
	}
	public function getDay() {
		return $this->day;
	}
	public function setDay($day) {
		$this->day = $day;
		return $this;
	}
	public function getAmountDays() {
		return $this->amountDays;
	}
	public function setAmountDays($amountDays) {
		$this->amountDays = $amountDays;
		return $this;
	}
	public function getAmountWeeks() {
		return $this->amountWeeks;
	}
	public function setAmountWeeks($amountWeeks) {
		$this->amountWeeks = $amountWeeks;
		return $this;
	}
	public function getAmountDays2() {
		return $this->amountDays2;
	}
	public function setAmountDays2($amountDays2) {
		$this->amountDays2 = $amountDays2;
		return $this;
	}
	public function getAmountWeeks2() {
		return $this->amountWeeks2;
	}
	public function setAmountWeeks2($amountWeeks2) {
		$this->amountWeeks2 = $amountWeeks2;
		return $this;
	}
	public function getYearForAmountDays() {
		return $this->yearForAmountDays;
	}
	public function setYearForAmountDays($yearForAmountDays) {
		$this->yearForAmountDays = $yearForAmountDays;
		return $this;
	}
	public function getYearForAmountDays2() {
		return $this->yearForAmountDays2;
	}
	public function setYearForAmountDays2($yearForAmountDays2) {
		$this->yearForAmountDays2 = $yearForAmountDays2;
		return $this;
	}
	public function getMonthForAmountDays2() {
		return $this->monthForAmountDays2;
	}
	public function setMonthForAmountDays2($monthForAmountDays2) {
		$this->monthForAmountDays2 = $monthForAmountDays2;
		return $this;
	}
}
	
?>