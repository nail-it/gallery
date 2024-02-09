<?php 
namespace App\Nailit\GalleryBundle\Entity;

class MyTime {
    protected $now;
    protected $begin;
    protected $begin2;
    
    const BIRTHDAY = '4 October 2011';
    const BIRTHDAY2 = '1 January 2015';
    
    
	public function __construct() {
    	$this->now = time();
    	 
    	$this->begin = strtotime(self::BIRTHDAY);
    	$this->begin2 = strtotime(self::BIRTHDAY2);
    }
    
    public function getAmountDays($to) {
    	return round(abs($to - $this->begin) / 60 / 60 / 24);
    }

    public function getAmountDays2($to) {
        if($to - $this->begin2 < 0) {
            return 0;
        } else {
            return round(abs($to - $this->begin2) / 60 / 60 / 24);
        }
    }
    
    public function getMonthFromBeginForAmountDays($amountDays) {
    	return date("n",strtotime("+$amountDays days",strtotime(self::BIRTHDAY)));
    }

    public function getYearFromBeginForAmountDays($amountDays) {
    	return date("Y",strtotime("+$amountDays days",strtotime(self::BIRTHDAY)));
    }

    public function getMonthFromBeginForAmountDays2($amountDays) {
    	if(time() - strtotime("+$amountDays days",strtotime(self::BIRTHDAY2)) < 0)
    		return null;
    	else 
    		return date("n",strtotime("+$amountDays days",strtotime(self::BIRTHDAY2)));
    }
    
    public function getYearFromBeginForAmountDays2($amountDays) {
    	if(time() - strtotime("+$amountDays days",strtotime(self::BIRTHDAY2)) < 0)
    		return null;
    	else
    		return date("Y",strtotime("+$amountDays days",strtotime(self::BIRTHDAY2)));
    }
    
}
?>