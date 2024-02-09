<?php
namespace App\Nailit\GalleryBundle\Entity;

use App\Nailit\GalleryBundle\Entity\MyTime;

class MyTimeNavigation extends MyTime {
	private $selectedTime;
	private $prevYear;
	private $prevMonth;
	private $nextYear;
	private $nextMonth;
	private $birthYear;
	private $birthMonth;

	public function __construct($selectedYear, $selectedMonth) {
		parent::__construct();
		$this->selectedTime = mktime(0, 0, 0, $selectedMonth, 1, $selectedYear);
	}

	public function getNavigation() {
		$this->prevMonth = date('n',strtotime('- 1 month', $this->selectedTime));
		if($this->prevMonth == '12') {
			$this->prevYear = date('Y',strtotime('- 1 year', $this->selectedTime));
		} else {
			$this->prevYear = date('Y',$this->selectedTime);
		}
		$this->nextMonth = date('n',strtotime('+ 1 month', $this->selectedTime));
		if($this->nextMonth == '1') {
			$this->nextYear = date('Y',strtotime('+ 1 year', $this->selectedTime));
		} else {
			$this->nextYear = date('Y',$this->selectedTime);
		}
		$this->birthYear = date('Y', $this->begin);
		$this->birthMonth = date('n', $this->begin);
	}

	public function getPrevYear() {
		return $this->prevYear;
	}
	public function getPrevMonth() {
		return $this->prevMonth;
	}
	public function getNextYear() {
		return $this->nextYear;
	}
	public function getNextMonth() {
		return $this->nextMonth;
	}
	public function getBirthYear() {
		return $this->birthYear;
	}
	public function getBirthMonth() {
		return $this->birthMonth;
	}
	public function getSelectedTime() {
		return $this->selectedTime;
	}
	public function setSelectedTime($selectedTime) {
		$this->selectedTime = $selectedTime;
		return $this;
	}

	public function getAmountDaysFromMonthBegin() {
		return $this->getAmountDays($this->getSelectedTime());
	}

	public function getAmountDays2FromMonthBegin() {
		return $this->getAmountDays2($this->getSelectedTime());
	}

}
?>