<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\Filesystem;
use App\Entity\Media;
use App\Entity\MyTimeNavigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

//use Symfony\Component\HttpFoundation\Request;


class DefaultController extends AbstractController
{
	public $filesystem;
	public $mainSlogan;
	public $sortDays;
	public $pageTitle;
    public $pageSubTitle;
	public $mainPath;
	public $requestStack;


	public function __construct(
		Filesystem $filesystem,
		string $mainSlogan,
		string $sortDays,
		string $pageTitle,
        string $pageSubTitle,
		string $mainPath,
		RequestStack $requestStack
	)
	{
		$this->filesystem = $filesystem;
		$this->mainSlogan = $mainSlogan;
		$this->pageTitle = $pageTitle;
		$this->pageSubTitle = $pageSubTitle;
		$this->mainPath = $mainPath;
		$this->requestStack = $requestStack;
	}
	
	private static function getRealIpAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
/*
	public function log($pageName) {
		$logger = $this->get('logger');
		$ip = self::getRealIpAddr();
		//$ipDetail = $this->countryCityFromIP($ip);
		$logger->info("Page visitor, ip: ".$ip.", agent: ".$_SERVER['HTTP_USER_AGENT'].", watching: ".$pageName);
	}
*/
	public function oneDayAction($parameter) {
        $code = new Code($this->getParameter('code.masterkey'));

        try {
            $code->decode($parameter);

//            $this->getParameter('code.masterkey')->decode($parameter);
		} catch(\Exception $e) {
			throw $this->createNotFoundException('Page does not exist');
		}

		$day 	= $code->getDay();
		$month 	= $code->getMonth();
		$year 	= $code->getYear();

        $timeHandler = new MyTimeNavigation($year, $month);
        $timeHandler->getNavigation();

		$files = array();
		$files = $this->filesystem->setNoLinks(true)->readImagesForOneDayFromFilesystem($files, $this->getParameter('dir.years'), $year, $month, $day);
		return $this->render(
		    'page/index-no-links.html.twig',
            array(
                'files' => $files,
                'amountDays'     => $timeHandler->getAmountDaysFromMonthBegin(),
                'amountDays2'     => $timeHandler->getAmountDays2FromMonthBegin(),
                'page_title' => $this->pageTitle,
                'page_subtitle' => $this->pageSubTitle,
            )
        );
	}
	
    public function indexAction($year = null, $month = null, Filesystem $filesystem = null)
    {
    	$dateStart		= $this->getParameter('date.start');
    	 
    	$year 	= (is_null($year)||$month=='secured')?date('Y'):$year;
    	$month 	= (is_null($month)||$month=='secured')?date('n'):$month;
    	
    	if($year == date('Y') && $month > date('n')) 
    		throw $this->createNotFoundException('Page does not exist');
    	
    	$timeHandler = new MyTimeNavigation($year, $month);
    	$timeHandler->getNavigation();

//    	$this->logAction('main page '.$year.' '.$month);
    	$files = $filesystem->readImagesFromFilesystem(
			$this->getParameter('dir.years'),
			$year,
			$month,
			null,
			$this->getParameter('nailit_gallery_separate_days') === true?true:null
		);

        return $this->render(
            'page/index.html.twig',
            array(
                'files'          => $files, 
                'selectedYear'   => $year,
                'selectedMonth'  => $month, 
            	'dateStart'      => $dateStart,
                'nowYear'        => date('Y'),
                'nowMonth'       => date('n'), 
                'prevYear'       => $timeHandler->getPrevYear(),
                'prevMonth'      => $timeHandler->getPrevMonth(),
                'nextYear'       => $timeHandler->getNextYear(),
                'nextMonth'      => $timeHandler->getNextMonth(),
                'birthYear'      => $timeHandler->getBirthYear(),
                'birthMonth'     => $timeHandler->getBirthMonth(),
            	'amountDays'     => $timeHandler->getAmountDaysFromMonthBegin(),               
            	'amountDays2'     => $timeHandler->getAmountDays2FromMonthBegin(),
	            'sort_days'      => $this->sortDays,
	            'day_description' => 'a',
	            'separate_days'   => 'b',
	            'main_slogan' => $this->mainSlogan,
	            'page_title' => $this->pageTitle,
	            'page_subtitle' => $this->pageSubTitle,
            )
        );
    }

	public function indexOneDayAction($year, $month, $day)
	{
		$dateStart		= $this->getParameter('date.start');

		$year 	= (is_null($year)||$month=='secured')?date('Y'):$year;
		$month 	= (is_null($month)||$month=='secured')?date('n'):$month;

		if($year == date('Y') && $month > date('n'))
			throw $this->createNotFoundException('Page does not exist');

		$timeHandler = new MyTimeNavigation($year, $month);
		$timeHandler->getNavigation();

//		$this->log('main page one day '.$year . ' ' . $month . ' ' . $day);
		$files = $this->filesystem->readImagesFromFilesystem(
			$this->getParameter('dir.years'),
			$year,
			$month,
			$day,
			$this->getParameter('nailit_gallery_separate_days') === true?true:null
		);

		return $this->render(
			'page/index.html.twig',
			array(
				'files'          => $files,
				'selectedYear'   => $year,
				'selectedMonth'  => $month,
				'dateStart'      => $dateStart,
				'nowYear'        => date('Y'),
				'nowMonth'       => date('n'),
				'prevYear'       => $timeHandler->getPrevYear(),
				'prevMonth'      => $timeHandler->getPrevMonth(),
				'nextYear'       => $timeHandler->getNextYear(),
				'nextMonth'      => $timeHandler->getNextMonth(),
				'birthYear'      => $timeHandler->getBirthYear(),
				'birthMonth'     => $timeHandler->getBirthMonth(),
				'amountDays'     => $timeHandler->getAmountDaysFromMonthBegin(),
				'amountDays2'     => $timeHandler->getAmountDays2FromMonthBegin(),
                'page_title' => $this->pageTitle,
                'page_subtitle' => $this->pageSubTitle,
			)
		);
	}

	public function incomingAction() {
		$dirIncoming 	= $this->getParameter('dir.incoming');
		$dirImages 		= $this->getParameter('dir.images');
		$dirYears 		= $this->getParameter('dir.years');

        $isAjax = $this->requestStack->getCurrentRequest()->isXmlHttpRequest();
		$this->filesystem->handleIncoming($dirIncoming, $dirImages, $dirYears);

		if($isAjax) {
			$result = array();
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
	}

	public function incomingAmountAction()
	{
		$dirIncoming = $this->getParameter('dir.incoming');
		$dirImages = $this->getParameter('dir.images');
		$dirYears = $this->getParameter('dir.years');

		$isAjax = $this->requestStack->getCurrentRequest()->isXmlHttpRequest();
		$amount = $this->filesystem->handleIncomingAmount($dirImages);

		if ($isAjax) {
			$result = array('amount'=>$amount);
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
	}
    
    public function bestNoLinksAction() {
//    	$this->log('best page');
    	$files = $this->filesystem->readBestFromFilesystem($this->getParameter('dir.images'));
    	return $this->render('page/best-no-links.html.twig', array('files'=>$files, 'page_title' => $this->pageTitle, 'page_subtitle' => $this->pageSubTitle));
    }
    
    public function bestAction() {
//    	$this->log('best page');
    	$files = $this->filesystem->readBestFromFilesystem($this->getParameter('dir.images'));
    	return $this->render('page/best.html.twig', array('files'=>$files, 'page_title' => $this->pageTitle, 'page_subtitle' => $this->pageSubTitle));
    }
    
    public function bestAddAction($year, $month, $day, $file) {
    	$dirImages = $this->getParameter('dir.images');
    	symlink($year.'/'.$month.'/'.$day.'/thumb/'.$file, $dirImages.'/best/'.$year.$month.$day.$file);
    	if($this->requestStack->getCurrentRequest()->isXmlHttpRequest()) {
    		$result = array();
    		$result['image'] = $this->renderView('page/single-image.html.twig', array('pic'=>new Media($year, $month, $day, $file, '', '', '', '', '', '', 'image')));
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');
    		return $response;
    	} 
    }

    public function videoAction() {
    	$files = $this->filesystem->readVideoFromFilesystem($this->getParameter('dir.videos'));
    	return $this->render('page/video.html.twig', array('files'=>$files, 'page_title' => $this->pageTitle, 'page_subtitle' => $this->pageSubTitle));
    }
    
    
    public function evolutionAddAction($year, $month, $day, $file, $tag, $week) {
    	$dirImages = $this->getParameter('dir.images');
    	$path = $dirImages.'/evolution/'.$week.'-'.$tag;
    	if(is_link($path)) { 
    	    unlink($path);
    	}
    	symlink($year.'/'.$month.'/'.$day.'/thumb/'.$file, $path);
    	if($this->requestStack->getCurrentRequest()->isXmlHttpRequest()) {
    		$result = array();
    		$result['image'] = $this->renderView('page/single-image.html.twig', array('pic'=>new Media($year, $month, $day, $file, '', '', '', '', '', '', 'image')));
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');
    		return $response;
    	}
    }
    
    public function evolutionNoLinksAction() {
//    	$this->log('evolution no links page');
    	$files = $this->filesystem->readEvolutionFromFilesystem($this->getParameter('dir.images'));
    	return $this->render('page/evolution-no-links.html.twig', array('files'=>$files, 'page_title' => $this->pageTitle, 'page_subtitle' => $this->pageSubTitle));
    }
    
    public function evolutionAction() {
//    	$this->log('evolution page');
    	$files = $this->filesystem->readEvolutionFromFilesystem($this->getParameter('dir.images'));
    	return $this->render('page/evolution.html.twig', array('files'=>$files, 'page_title' => $this->pageTitle, 'page_subtitle' => $this->pageSubTitle));
    }
    
    private function applyEvolutionToManager($files, $year, $month) {
    	$evolutionFiles = $this->filesystem->readEvolutionFromFilesystem($this->getParameter('dir.images'));
    	foreach($evolutionFiles as $ef) {    		
    		if($ef->getYear() == $year && $ef->getMonth() == $month) {
	    		$medias = $files[$ef->getYear()][$ef->getMonth()][$ef->getDay()];
	    		foreach($medias as $media) {
	    			if(is_object($media)) {
	    				if($media->getName() == $ef->getFile()) {
	    					$media->setEvolution(true);
	    				    $files[$ef->getYear()][$ef->getMonth()][$ef->getDay()][$ef->getName()] = $media;
	    				}
	    			}
	    		}
    		}
    	}
    	return $files;
    }
    
    public function adminAction($year = null, $month = null)
    {
    	$year = (is_null($year)||$month=='secured')?date('Y'):$year;
    	$month = is_null($month)?date('n'):$month;
    	
    	//$this->filesystem->handleIncoming($this->>getParameter('dir.incoming'), $this->>getParameter('dir.images'), $this->>getParameter('dir.years'));
    	$files = $this->filesystem->readImagesFromFilesystem(
			$this->getParameter('dir.years'),
			$year,
			$month
		);

        $files = $this->applyEvolutionToManager($files, $year, $month);
        
        $dateStart		= $this->getParameter('date.start');
    	
    	return $this->render('page/admin.html.twig',
		    array(
				'files'=>$files,
				'selectedYear'=>$year,
				'selectedMonth'=>$month,
				'dateStart'=>$dateStart,
			    'main_path'=>$this->mainPath,
                'page_title' => $this->pageTitle,
                'page_subtitle' => $this->pageSubTitle,
            )
	    );
    }
    
    public function logAction()
    {
		$lines = $this->filesystem->readLog($this->getParameter('dir.log'));
    	return $this->render('page/log.html.twig', array('lines'=>$lines));
    }    
    
    public function hideAction($year, $month, $day, $file) {
    	$dirYears = $this->getParameter('dir.years');
    	$picture = $dirYears.'/'.$year.'/'.$month.'/'.$day.'/thumb/'.$file;
    	unlink($picture);
    	$isAjax = $this->requestStack->getCurrentRequest()->isXmlHttpRequest();
    	if($isAjax) {
    		$result = array();
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');
    		return $response;
    	}
    }

    public function galleryAction($year, $month, $day, $file) {
    	$screenWidth = $this->requestStack->getCurrentRequest()->query->get('width');
    	$screenHeight = $this->requestStack->getCurrentRequest()->query->get('height');
    	
    	$image = $newpath = $this->getParameter('dir.years').'/'.$year.'/'.$month.'/'.$day.'/'.$file;
//    	$request = Request::createFromGlobals();
    	$isAjax = $this->requestStack->getCurrentRequest()->isXmlHttpRequest();
//		$pathToCachedFile = 'http://localhost/krzys-phpstorm/web/bundles/gallery/images/cache/' . $screenWidth . '-' . $screenHeight . '-'. $file;
//		$localPathToCachedFile = $this->getParameter('dir.images') . '/cache/' . $screenWidth . '-' . $screenHeight . '-'. $file;

		$response = new Response($this->filesystem->handleImage($image, $isAjax, $screenWidth, $screenHeight));
       	$response->headers->set('Content-Type', 'text/html');
       	return $response;
    }
    
    public function rotateAction($year, $month, $day, $file, $degrees) {
    	// thumb
        $incoming = $newpath = $this->getParameter('dir.years').'/'.$year.'/'.$month.'/'.$day.'/thumb/'.$file;
    	$source = imagecreatefromjpeg($incoming);
    	$rotate = imagerotate($source, $degrees, 0);
    	unlink($incoming);
    	imagejpeg($rotate, $newpath, 100);
    	
    	// original
    	$incoming = $newpath = $this->getParameter('dir.years').'/'.$year.'/'.$month.'/'.$day.'/'.$file;
    	$source = imagecreatefromjpeg($incoming);
    	$rotate = imagerotate($source, $degrees, 0);
    	unlink($incoming);
    	imagejpeg($rotate, $newpath, 100);
    	
//    	$request = Request::createFromGlobals();
    	$isAjax = $this->requestStack->getCurrentRequest()->isXmlHttpRequest();
    	if($isAjax) {
    		$result = array();
    		$result['image'] = $this->renderView('page/single-image.html.twig', array('pic'=>new Media($year, $month, $day, $file, '', '', '', '', '', '', 'image')));
    		$response = new Response(json_encode($result));
    	    $response->headers->set('Content-Type', 'application/json');
    	    return $response;    	
    	}
    }
    
    public function tagAction($year, $month, $day, $file) {
    	$action = $this->requestStack->getCurrentRequest()->query->get('action');
    	$select = $this->requestStack->getCurrentRequest()->query->get('select');
    	$dirYears = $this->getParameter('dir.years');
		$path = $dirYears.'/'.$year.'/'.$month.'/'.$day.'/tag';

		$this->filesystem->tagImage($action, $select, $path, $file);

    	$isAjax = $this->requestStack->getCurrentRequest()->isXmlHttpRequest();
    	if($isAjax) {
    		$result = array();
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');
    		return $response;
    	}
    }

	public function tagAllAction() {
		$dirYears = $this->getParameter('dir.years');
		$select = $this->requestStack->getCurrentRequest()->query->get('select');
		$limit = $this->requestStack->getCurrentRequest()->query->get('limit');

		$this->filesystem->tagAll($dirYears, $select, $limit);

		$isAjax = $this->requestStack->getCurrentRequest()->isXmlHttpRequest();
		if($isAjax) {
			$result = array();
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
	}

	public function redoThumbsAction() {
		$dirYears = $this->getParameter('dir.years');
		$limit = $this->requestStack->getCurrentRequest()->query->get('limit');

		$this->filesystem->redoThumbs($dirYears, $limit);

		$isAjax = $this->requestStack->getCurrentRequest()->isXmlHttpRequest();
		if($isAjax) {
			$result = array();
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
	}

	public function descriptionAction($year, $month, $day, $file) {
    	$dirYears = $this->getParameter('dir.years');
    	$description = $this->requestStack->getCurrentRequest()->request->get('description');
		if(!file_exists($dirYears.'/'.$year.'/'.$month.'/'.$day.'/desc')) {
			mkdir($dirYears.'/'.$year.'/'.$month.'/'.$day.'/desc', 0777);
		}
    	$filename = $dirYears.'/'.$year.'/'.$month.'/'.$day.'/desc/'.$file.'.txt';
    	$handle = fopen($filename, "w");
    	$contents = fwrite($handle, $description);
    	fclose($handle);
    	return new Response(json_encode(array()));
    }
    
    public function sessionAction() {
    	$tag = $this->requestStack->getCurrentRequest()->query->get('tag');
    	$session = $this->get('session');
    	$session->set('tag', $tag);    	
    	
    	if($this->requestStack->getCurrentRequest()->query->has('r')) {
    		$params = $this->get('router')->match('/'.$this->requestStack->getCurrentRequest()->query->get('r'));
    		return $this->redirect($this->generateUrl($params['_route']), 301);
    	} else	 
    		return new Response(json_encode(array()));
    }    
    
}
