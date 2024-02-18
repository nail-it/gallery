<?php 
namespace App\Entity;

use App\Util\LinkMedia;
use App\Util\Video;

class Filesystem {
	
	private $noLinks;
	
	public function setNoLinks() {
		$this->noLinks = true;
		return $this;
	}

    private function rotateImage($source) {
        $image = new \Imagick($source);
        $orientation = $image->getImageOrientation();
        if(isset($orientation) && $orientation != 1) {
            switch ($orientation) {
                case \imagick::ORIENTATION_BOTTOMRIGHT:
                    $image->rotateimage("#000", 180); // rotate 180 degrees
                    break;
                case \imagick::ORIENTATION_RIGHTTOP:
                    $image->rotateimage("#000", 90); // rotate 90 degrees CW
                    break;
                case \imagick::ORIENTATION_LEFTBOTTOM:
                    $image->rotateimage("#000", -90); // rotate 90 degrees CCW
                    break;
            }
            $image->setImageOrientation(\imagick::ORIENTATION_TOPLEFT);
			$newpath = $source;
			unlink($source);
			$image->writeImage($newpath);
        }
    }

	private function sortByDataFromIncoming($incoming, $dirYears, $name, $year = null, $month = null, $day = null) {
		$fp = fopen($incoming, "r");
		if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
		
			if(is_readable($incoming) === false) {
				die('Wygląda na to, że właśnie wrzucamy nowe zdjęcia :D Odśwież stronę żeby sprawdzić czy już są!');
			}
			
			//$exif = exif_read_data($incoming, 'IFD0');
			//echo $exif===false ? "No header data found.<br />\n" : "Image contains headers<br />\n";
			$exif = exif_read_data($incoming, 0, true);
		
			if(is_null($year) || is_null($month) || is_null($day)) {
				if(array_key_exists('EXIF', $exif)) {
					$dateOriginal = $exif['EXIF']['DateTimeOriginal'];
					list($date, $time) = explode(' ',$dateOriginal);
					list($year, $month, $day) = explode(':', $date);
				} else {
					$year = date('Y',$exif['FILE']['FileDateTime']);
					$month = date('m',$exif['FILE']['FileDateTime']);
					$day = date('d',$exif['FILE']['FileDateTime']);
				}
			}
		
			$this->createFolders($dirYears, $year, $month, $day);
		
			// Get new sizes
			list($width, $height) = getimagesize($incoming);
			if($width > $height) {
				$newwidth = 200;
				$newheight = ($height * $newwidth) / $width;
			} else {
				$newheight = 200;
				$newwidth = ($width * $newheight) / $height;
			}
		
			// Load
			$thumb = imagecreatetruecolor($newwidth, $newheight);
			$source = imagecreatefromjpeg($incoming);
		
			$aName = explode('.', $name);
			$ending = end($aName);
			
			if($ending == 'jpg' || $ending == "jpeg") {
				$ending = 'JPG';
			}
			
			$name = prev($aName).'-'.time().rand(1000, 9999).'.'.$ending;

			// resize for thumb
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			// save thumb
            imagejpeg($thumb, $dirYears.'/'.$year.'/'.$month.'/'.$day.'/thumb/'.$name, 100);
			if(!rename($incoming, $dirYears.'/'.$year.'/'.$month.'/'.$day.'/'.$name)) {
				imagejpeg($source, $dirYears.'/'.$year.'/'.$month.'/'.$day.'/'.$name, 100);
			} else {
                $this->rotateImage($dirYears.'/'.$year.'/'.$month.'/'.$day.'/'.$name);
            }
			fflush($fp);            // flush output before releasing the lock
			flock($fp, LOCK_UN);    // release the lock
		}
		
		fclose($fp);
	}
	
	public function handleIncoming($dirIncoming, $dirImages, $dirYears) {
		$this->createFolders($dirYears, date('Y'), date('m'), date('d'));
		/* check if there is something in incoming folder */
		if ($handle = opendir($dirImages.'/incoming')) {
			$i = 0;
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file != ".svn") {
					if(is_file($dirIncoming.$file)) {
						// rotate if needed
						$this->rotateImage($dirIncoming . $file);
						$this->sortByDataFromIncoming($dirIncoming . $file, $dirYears, $file);
                    } else {
						list($year, $month, $day) = explode('-', $file);
						if ($handleDay = opendir($dirImages.'/incoming'.'/'.$file)) {
							while (false !== ($fileFromDay = readdir($handleDay))) {
								if ($fileFromDay != "." && $fileFromDay != ".." && $fileFromDay != ".svn") {
									$this->rotateImage($dirIncoming.$file.'/'.$fileFromDay);
									$this->sortByDataFromIncoming($dirIncoming.$file.'/'.$fileFromDay, $dirYears, $fileFromDay, $year, $month, $day);
								}
							}
						}
						closedir($handleDay);
					}
					if(++$i > 0) {
						break;
					}
				}
			}
		}
		closedir($handle);
	}

	public function handleIncomingAmount($dirImages) {
		/* check if there is something in incoming folder */
        $i = 0;
		if ($handle = opendir($dirImages.'/incoming')) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file != ".svn") {
					$i++;
				}
			}
		}
		closedir($handle);
		return $i;
	}

	public function createFolders($dirYears, $year, $month = null, $day = null) {
		if(!file_exists($dirYears.'/'.$year)) {
			if(!file_exists($dirYears)) {
				die('Ups... Wrong configuration.');
			}
			mkdir($dirYears.'/'.$year, 0777);
		}

        $yearMonthPath = $dirYears.'/'.$year.'/'.$month;

        if(!is_null($month)) {

			if(!file_exists($yearMonthPath)) {
				mkdir($yearMonthPath, 0777);
			}
		}
		
		if(!is_null($day)) {
			if(!file_exists($yearMonthPath.'/'.$day)) {
				mkdir($yearMonthPath.'/'.$day, 0777);
			}
			if(!file_exists($yearMonthPath.'/'.$day.'/thumb')) {
				mkdir($yearMonthPath.'/'.$day.'/thumb', 0777);
			}
		}
	}
	
	public function readEvolutionFromFilesystem($dirImages) {
		$files = array();
		$path = $dirImages.'/evolution';
		/* read all files */
		if(!file_exists($path)) {
			mkdir($path, 0777);
		}		
		if ($handleEvolution = opendir($path)) {
			/* for each year */
			while (false !== ($file = readdir($handleEvolution))) {
				if ($file != "." && $file != "..") {
					$files[$file] = new LinkMedia($file, readlink($path.'/'.$file));
				}
			}
		}
		closedir($handleEvolution);
		krsort($files);
		return $files;
	}
	
	public function readBestFromFilesystem($dirImages) {
		/* read all files */
		$files = array();
		$path = $dirImages.'/best';
		if ($handleBest = opendir($dirImages.'/best')) {
			/* for each year */
			while (false !== ($file = readdir($handleBest))) {
				if ($file != "." && $file != "..") {
					$files[] = new LinkMedia($file, readlink($path.'/'.$file));
				}
			}
		}
		closedir($handleBest);
		krsort($files);
	
		return $files;
	}	

	public function readVideoFromFilesystem($dirVideos) {
		/* read all files */
		$files = array();
		if ($handleVideos = opendir($dirVideos)) {
			/* for each year */
			while (false !== ($file = readdir($handleVideos))) {
				if ($file != "." && $file != "..") {
					$files[] = new Video($file);
				}
			}
		}
		closedir($handleVideos);
		krsort($files);
	
		return $files;
	}
	
	private function readDescription($dirYears, $year, $month, $day, $file) {
        $yearMonthDayPath = $dirYears.'/'.$year.'/'.$month.'/'.$day;

		if(!file_exists($yearMonthDayPath.'/desc/'.$file.'.txt')) {
			return '';
		} else {
			$filename = $yearMonthDayPath.'/desc/'.$file.'.txt';
			if(filesize($filename) > 0 ) {
				$handle = fopen($filename, "r");
				$contents = fread($handle, filesize($filename));
				if($this->noLinks) {
					$pattern = $replace = array();
					$pattern[0] = '/<a.*>/';
					$pattern[1] = '/<\/a>/';
					$replace[0] = '';
					$replace[1] = '';
					$contents = preg_replace($pattern, $replace, $contents);
				}
				
				fclose($handle);
				return $contents;
			}
			return '';
		}
	}	
	
	private function readTag($dirYears, $year, $month, $day, $file) {
        $yearMonthDayPath = $dirYears.'/'.$year.'/'.$month.'/'.$day;
		if(file_exists($yearMonthDayPath.'/tag/1/'.$file.'.txt') === true) {
			return '1';
		} else {
			if(file_exists($yearMonthDayPath.'/tag/2/'.$file.'.txt') === true) {
		  		return '2';
		  	}
		} 
		return null;
	}
	
	public function readImagesForOneDayFromFilesystem($files, $dirYears, $year, $month, $day, $limit = null) {
		/* for each day */
        $yearMonthDayPath = $dirYears.'/'.$year.'/'.$month.'/'.$day;
		if ($handleDay = opendir($yearMonthDayPath.'/thumb')) {
		    $descriptionDayDau = $this->readDescription($dirYears, $year, $month, $day, 'text3');
		    $descriptionDaySon = $this->readDescription($dirYears, $year, $month, $day, 'text');
			$descriptionDayMom = $this->readDescription($dirYears, $year, $month, $day, 'text1');
			$descriptionDayDad = $this->readDescription($dirYears, $year, $month, $day, 'text2');

			$i = 0;
			while (false !== ($file = readdir($handleDay))) {
				if ($file != "." && $file != "..") {
					$tag = $this->readTag($dirYears, $year, $month, $day, $file);
					list($width, $height) = getimagesize($yearMonthDayPath.'/thumb/'.$file);
					if(isset($width) && !empty($width) && $width > 285) {
						$type = 'big-image';
					} else {
						$type = 'image';
					}
					$description = $this->readDescription($dirYears, $year, $month, $day, $file);
					$files[$year][$month][$day][] = new Media($year, $month, $day, $file, $description, $descriptionDayDau, $descriptionDaySon, $descriptionDayMom, $descriptionDayDad, $tag, $type, ($width > $height ? true : false));
					if(!is_null($limit) && ++$i >= 1)
						return $files;
				}
			}
			// if it is today and today no pictures were uploaded, create one empty (to make descriptions available)
			if($day == date('d') && $month == date('m') && $year == date('Y') && empty($files[$year][$month][$day])) {
				$files[$year][$month][$day][] = new Media($year, $month, $day, '', '', $descriptionDayDau, $descriptionDaySon, $descriptionDayMom, $descriptionDayDad, '', 'image');
			}
			
			if(!is_null($files) && isset($files[$year][$month][$day]) && is_array($files[$year][$month][$day]))
			    uasort($files[$year][$month][$day], function($a, $b) { return strcmp($a->getName(), $b->getName()); } );
		}
		// movie
		if (file_exists($yearMonthDayPath.'/movie') && $handleDay = opendir($yearMonthDayPath.'/movie')) {
			while (false !== ($file = readdir($handleDay))) {
				if ($file != "." && $file != "..") {
					if(substr($file, -3, 3) == 'mp4') {
						$type = 'movie-html5';
					} else {
						$type = 'movie';
					}
					$description = $this->readDescription($dirYears, $year, $month, $day, $file);
					$files[$year][$month][$day][] = new Media($year, $month, $day, $file, $description, '', '', '', '', '', $type);					
				}
			}
		}
		closedir($handleDay);
		return $files;	
	}

	public function readImagesForOneMonthFromFilesystem($dirYears, $year, $month, $d, $limit) {
		$files = array();
		if ($handleMonth = opendir($dirYears.'/'.$year.'/'.$month)) {
			/* for each day */
			while (false !== ($day = readdir($handleMonth))) {
				if ($day != "." && $day != "..") {
					if(!is_null($d)) {
						if($day == $d) {
							$files = $this->readImagesForOneDayFromFilesystem($files, $dirYears, $year, $month, $day, null);
						}
					} else {
						$files = $this->readImagesForOneDayFromFilesystem($files, $dirYears, $year, $month, $day, $limit);
					}
				}
			}
		}
		closedir($handleMonth);
		return $files;
	}

	public function readImagesFromFilesystem($dirYears, $y, $m, $d = null, $limit = null) {
        $files = array();

		if (is_dir($dirYears . '/' . $y . '/' . sprintf('%02d', $m))) {

			$this->createFolders($dirYears, $y, $m);
			$files = $this->readImagesForOneMonthFromFilesystem($dirYears, $y, sprintf('%02d', $m), $d, $limit);
			if (!empty($files[$y][$m]))
				krsort($files[$y][$m]);

			if(array_key_exists($y, $files)) {
				krsort($files[$y]);
			}
		}

		return $files;
	
	}
	
	private static function stringToColorCode($str) {
		$code = dechex(crc32($str));
		$code = substr($code, 0, 6);
		return $code;
	}	
	
	public function readLog($dirLog) {
		$lines = $files = array();
		$i = 0;
		foreach (new \DirectoryIterator($dirLog.'visited/') as $fileInfo) {
			if($fileInfo->isDot()) continue;
			
			$files[] = $dirLog.'visited/'.$fileInfo->getFilename();
		}
		rsort($files);
		
		$match = array(
				'wilga' 	=> '89.70.139.158',
				'localhost' => '127.0.0.1',
				'hania' 	=> '91.223.175.1',
				'hania2' 	=>	'91.224.192.23',
		);
		
		foreach($files as $file) { 
			$fileo = new \SplFileObject($file, "r");
			while (!$fileo->eof()) {
				$found = false;
				foreach($match as $who => $m) {
					if (preg_match("/".$m."/i", $fileo->current())) {
						if($who != 'wilga') {
							if($i > 0) {
								if(strstr(substr($fileo->current(), 49), ',', true) === strstr(substr(($lines[$i-1]), 49), ',', true)) {
									$lines[$i-1] .= '<br />' . $fileo->current();
								} else {
									$lines[$i-1] .= '<h2 style="background-color: #'.self::stringToColorCode($who).'; color: #FFFFFF;">' . $who . '</h2> ';
									$lines[$i++] = $fileo->current();
								}
							} else {
								$lines[$i++] = $fileo->current();
							}
						}
						$found = true;
						break;
					}
				}
				if($found == false) {
					if($i > 0) {
						if(strstr(substr($fileo->current(), 49), ',', true) === strstr(substr(($lines[$i-1]), 49), ',', true)) {
							$lines[$i-1] .= '<br />' . $fileo->current();
						} else {
							$lines[$i-1] .= '<h2>unknown</h2> ';
							$lines[$i++] = $fileo->current();
						}
					} else {
						$lines[$i++] = $fileo->current();
					}
					//echo strstr(substr($fileo->current(), 49), ',', true);
				}
				
				$fileo->next();
			}
		}
		rsort($lines);
		return $lines;
	}	
	
	public function handleImage($image, $isAjax, $screenWidth, $screenHeight) {

		// Get new sizes
    	list($width, $height) = getimagesize($image);
/*
        if($this->isRotate($image)) {
            $tempHeight = $height;
            $height = $width;
            $width = $tempHeight;
        }
*/
		// Load
		if($isAjax) {

			if($width > $height) {
				$newwidth = $screenWidth;
				$newheight = ($height * $newwidth) / $width;
				if($newheight > $screenHeight) {
					$newheight = $screenHeight;
					$newwidth = ($width * $newheight) / $height;
				}
			} else {
				$newheight = $screenHeight;
				$newwidth = ($width * $newheight) / $height;
			}

			if($newwidth > $width || $newheight > $height) {
				$newwidth = $width;
				$newheight = $height;
			}

			$medium = imagecreatetruecolor($newwidth, $newheight);

    		$source = imagecreatefromjpeg($image);

    		// Resize
			imagecopyresampled($medium, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

//			imagejpeg($medium, $localPathToCachedFile, 100);
//			return '<img width="'.round($newwidth).'" src="' . $pathToCachedFile . '" />';
            ob_start();
            imagejpeg($medium);
            $i = ob_get_clean();
            return '<img width="'.round($newwidth).'" src="data:image/jpg;base64,'.base64_encode($i).'" />';
       	} else {
			$source = imagecreatefromjpeg($image);

			if(isset($screenWidth) && !empty($screenWidth)) {
				ob_start();
				imagejpeg($source);
				$i = ob_get_clean();
				return '<img width="'.round($screenWidth).'" src="data:image/jpg;base64,'.base64_encode($i).'" />';
			} else {
				ob_start();
				imagejpeg($source);
				$i = ob_get_clean();
				return '<img width="'.round($width).'" src="data:image/jpg;base64,'.base64_encode($i).'" />';
			}
       	}
	}

	public function tagAll($dirYears, $select, $limit) {
		$i = 0;

		$files = array();
		/* read all files */
		if ($handleYears = opendir($dirYears)) {
			/* for each year */
			while (false !== ($year = readdir($handleYears))) {
				if ($year != "." && $year != "..") {

					if ($handleYear = opendir($dirYears.'/'.$year)) {
						/* for each year */
						while (false !== ($month = readdir($handleYear))) {
							if ($month != "." && $month != "..") {

								/* for each month */
								if ($handleMonth = opendir($dirYears.'/'.$year.'/'.$month)) {
									while (false !== ($day = readdir($handleMonth))) {
										if ($day != "." && $day != "..") {
											$path = $dirYears.'/'.$year.'/'.$month.'/'.$day;
											if ($handleDay = opendir($path.'/thumb')) {
												while (false !== ($file = readdir($handleDay))) {
													if ($file != "." && $file != "..") {
														if(!$this->tagImage('a', $select, $path . '/tag', $file, true)) {
															if ($i++ > $limit) {
																break;
															}
														}
													}
												}

											}
										}
									}
								}
								closedir($handleMonth);
								if(!empty($files[$year][$month]))
									krsort($files[$year][$month]);
							}
						}
						closedir($handleYear);
					}
				}
			}
		}
		closedir($handleYears);
	}

	public function tagImage($action, $select, $path, $file, $force = null) {
		if($action == 'a' && ($select == 1 || $select == 2)) {
			if(!file_exists($path)) {
				mkdir($path, 0777);
			}
			if(!file_exists($path.'/'.$select)) {
				mkdir($path.'/'.$select, 0777);
			}
		}

		switch($action) {
			case 'a':
				switch($select) {
					case '1':
					case '2':
						if ($force === true && (file_exists($path.'/1/'.$file.'.txt') || file_exists($path.'/2/'.$file.'.txt'))) {
							return false;
						}
						$filename = $path.'/'.$select.'/'.$file.'.txt';
						$handle = fopen($filename, "w");
						fclose($handle);
						if($select == '1') {
							if (file_exists($path.'/2/'.$file.'.txt') &&
								is_writable($path.'/2/'.$file.'.txt')) {
								unlink($path.'/2/'.$file.'.txt');
							}
						}
						if($select == '2') {
							if (file_exists($path.'/1/'.$file.'.txt') &&
								is_writable($path.'/1/'.$file.'.txt')) {
								unlink($path.'/1/'.$file.'.txt');
							}
						}
						break;
					default:
						break;
				}
				break;
			case 'r':
				if (file_exists($path.'/'.$select.'/'.$file.'.txt') &&
					is_writable($path.'/'.$select.'/'.$file.'.txt')) {
					switch($select) {
						case '1':
						case '2':
							unlink($path.'/'.$select.'/'.$file.'.txt');
							break;
						default:
							break;
					}
				} else {
					if (file_exists($path.'/1/'.$file.'.txt') &&
						is_writable($path.'/1/'.$file.'.txt')) {
						unlink($path.'/1/'.$file.'.txt');
					}
					if (file_exists($path.'/2/'.$file.'.txt') &&
						is_writable($path.'/2/'.$file.'.txt')) {
						unlink($path.'/2/'.$file.'.txt');
					}
				}
				break;
		}
	}

	public function redoThumbs($dirYears, $limit) {
		$i = 0;

		$files = array();
		/* read all files */
		if ($handleYears = opendir($dirYears)) {
			/* for each year */
			while (false !== ($year = readdir($handleYears))) {
				if ($year != "." && $year != "..") {

					if ($handleYear = opendir($dirYears.'/'.$year)) {
						/* for each year */
						while (false !== ($month = readdir($handleYear))) {
							if ($month != "." && $month != "..") {

								/* for each month */
								if ($handleMonth = opendir($dirYears.'/'.$year.'/'.$month)) {
									while (false !== ($day = readdir($handleMonth))) {
										if ($day != "." && $day != "..") {
											$path = $dirYears.'/'.$year.'/'.$month.'/'.$day;
											if ($handleDay = opendir($path.'/thumb')) {
												while (false !== ($file = readdir($handleDay))) {
													if ($file != "." && $file != ".." && $file != 'desc' && $file != 'thumb' && $file != 'wideo' && $file != 'video') {

														// Get new sizes
														list($width, $height) = getimagesize($path.'/'.$file);
														if($width > $height) {
															$newwidth = 200;
															$newheight = ($height * $newwidth) / $width;
														} else {
															$newheight = 200;
															$newwidth = ($width * $newheight) / $height;
														}

														// Load
														$thumb = imagecreatetruecolor($newwidth, $newheight);
														$source = imagecreatefromjpeg($path.'/'.$file);

														// resize for thumb
														imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
														// save thumb
														imagejpeg($thumb, $dirYears.'/'.$year.'/'.$month.'/'.$day.'/thumb/'.$file, 100);

														if ($i++ > $limit) {
																break;
														}

													}
												}

											}
										}
									}
								}
								closedir($handleMonth);
								if(!empty($files[$year][$month]))
									krsort($files[$year][$month]);
							}
						}
						closedir($handleYear);
					}
				}
			}
		}
		closedir($handleYears);
	}


}

?>