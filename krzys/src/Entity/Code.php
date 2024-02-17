<?php 
namespace App\Entity;

class Code {
	
	private $key;
	
	private $day;
	private $month;
	private $year;
	
	public function __construct($key = '') {
		$this->key = $key;
	}
			
	public function encode($day, $month, $year) {
		$this->day = $day;
		$this->month = $month;
		$this->year = $year;
		$string = $day.'/'.$month.'/'.$year;
		$j = 0;
		$hash = '';
		
		$key = sha1($this->key);
		$strLen = strlen($string);
		$keyLen = strlen($key);
		for ($i = 0; $i < $strLen; $i++) {
			$ordStr = ord(substr($string,$i,1));
			if ($j == $keyLen) {
				$j = 0;
			}
			$ordKey = ord(substr($key,$j,1));
			$j++;
			$hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
		}
		return $hash;
	}
	
	public function decode($string) {
		$j = 0;
		$hash = '';
		$key = sha1($this->key);
		$strLen = strlen($string);
		$keyLen = strlen($key);
		for ($i = 0; $i < $strLen; $i+=2) {
			$ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
			if ($j == $keyLen) {
				$j = 0;
			}
			$ordKey = ord(substr($key,$j,1));
			$j++;
			$hash .= chr($ordStr - $ordKey);
		}
		list($this->day, $this->month, $this->year) = explode('/', $hash);
		
		if(!ctype_digit($this->day) || !ctype_digit($this->month) || !ctype_digit($this->year)) {
			throw new \Exception('Decode unsuccessfull');		
		}
		
		return;
	}
	
	public function getDay() {
		return $this->day;
	}
	
	public function getMonth() {
		return $this->month;
	}
	
	public function getYear() {
		return $this->year;
	}	
	
}

?>