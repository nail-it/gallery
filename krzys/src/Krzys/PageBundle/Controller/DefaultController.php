<?php

namespace App\Krzys\PageBundle\Controller;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Nailit\GalleryBundle\Entity\Media;
use App\Nailit\GalleryBundle\Entity\MyTimeNavigation;
use Symfony\Component\HttpFoundation\Request;

use App\Nailit\GalleryBundle\Entity\Filesystem;

class DefaultController extends AbstractController
{
	public $filesystem;
	public $mainSlogan;
	public $sortDays;
	public $pageTitle;
	public $mainPath;


	public function __construct(
		Filesystem $filesystem,
		string $mainSlogan = '',
		string $sortDays = 'desc',
		string $pageTitle = '',
		string $mainPath = ''
	)
	{
		$this->filesystem = $filesystem;
		$this->mainSlogan = $mainSlogan;
		$this->pageTitle = $pageTitle;
		$this->mainPath = $mainPath;
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
		try {
			$this->get('nailit_code')->decode($parameter);
		} catch(\Exception $e) {
			throw $this->createNotFoundException('Page does not exist');
		}
		$day 	= $this->get('nailit_code')->getDay();
		$month 	= $this->get('nailit_code')->getMonth();
		$year 	= $this->get('nailit_code')->getYear();

        $timeHandler = new MyTimeNavigation($year, $month);
        $timeHandler->getNavigation();
				
		$files = array();
		$files = $this->filesystem->setNoLinks(true)->readImagesForOneDayFromFilesystem($files, $this->getParameter('dir.years'), $year, $month, $day);
		return $this->render(
		    'KrzysPageBundle:Default:index-no-links.html.twig',
            array(
                'files' => $files,
                'amountDays'     => $timeHandler->getAmountDaysFromMonthBegin(),
                'amountDays2'     => $timeHandler->getAmountDays2FromMonthBegin()
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

    	$this->logAction('main page '.$year.' '.$month);
    	$files = $filesystem->readImagesFromFilesystem(
			$this->getParameter('dir.years'),
			$year,
			$month,
			null,
			$this->getParameter('nailit_gallery_separate_days') === true?true:null
		);

        return $this->render(
            '@KrzysPage/Default/index.html.twig',
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
	            'page_title' => $this->pageTitle
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
			'KrzysPageBundle:Default:index.html.twig',
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
			)
		);
	}

	public function incomingAction() {
		$dirIncoming 	= $this->getParameter('dir.incoming');
		$dirImages 		= $this->getParameter('dir.images');
		$dirYears 		= $this->getParameter('dir.years');

		$request = Request::createFromGlobals();
		$isAjax = $request->isXmlHttpRequest();
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

		$request = Request::createFromGlobals();
		$isAjax = $request->isXmlHttpRequest();
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
    	return $this->render('KrzysPageBundle:Default:best-no-links.html.twig', array('files'=>$files));
    }
    
    public function bestAction() {
//    	$this->log('best page');
    	$files = $this->filesystem->readBestFromFilesystem($this->getParameter('dir.images'));
    	return $this->render('@KrzysPage/Default/best.html.twig', array('files'=>$files));
    }
    
    public function bestAddAction($year, $month, $day, $file) {
    	$dirImages = $this->getParameter('dir.images');
    	symlink($year.'/'.$month.'/'.$day.'/thumb/'.$file, $dirImages.'/best/'.$year.$month.$day.$file);
    	$request = Request::createFromGlobals();
    	if($request->isXmlHttpRequest()) {
    		$result = array();
    		$result['image'] = $this->renderView('KrzysPageBundle:Default:single-image.html.twig', array('pic'=>new Media($year, $month, $day, $file, '', '', '', '', '', '', 'image')));
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');
    		return $response;
    	} 
    }

    public function videoAction() {
//    	$this->log('video page');
    	$files = $this->filesystem->readVideoFromFilesystem($this->getParameter('dir.videos'));
    	return $this->render('@KrzysPage/Default/video.html.twig', array('files'=>$files));
    }
    
    
    public function evolutionAddAction($year, $month, $day, $file, $tag, $week) {
    	$dirImages = $this->getParameter('dir.images');
    	$path = $dirImages.'/evolution/'.$week.'-'.$tag;
    	if(is_link($path)) { 
    	    unlink($path);
    	}
    	symlink($year.'/'.$month.'/'.$day.'/thumb/'.$file, $path);
    	$request = Request::createFromGlobals();
    	if($request->isXmlHttpRequest()) {
    		$result = array();
    		$result['image'] = $this->renderView('KrzysPageBundle:Default:single-image.html.twig', array('pic'=>new Media($year, $month, $day, $file, '', '', '', '', '', '', 'image')));
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');
    		return $response;
    	}
    }
    
    public function evolutionNoLinksAction() {
//    	$this->log('evolution no links page');
    	$files = $this->filesystem->readEvolutionFromFilesystem($this->getParameter('dir.images'));
    	return $this->render('KrzysPageBundle:Default:evolution-no-links.html.twig', array('files'=>$files));
    }
    
    public function evolutionAction() {
//    	$this->log('evolution page');
    	$files = $this->filesystem->readEvolutionFromFilesystem($this->getParameter('dir.images'));
    	return $this->render('@KrzysPage/Default/evolution.html.twig', array('files'=>$files));
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
    	
    	return $this->render('@KrzysPage/Default/admin.html.twig',
		    array(
				'files'=>$files,
				'selectedYear'=>$year,
				'selectedMonth'=>$month,
				'dateStart'=>$dateStart,
			    'main_path'=>$this->mainPath
		    )
	    );
    }
    
    public function logAction()
    {
		$lines = $this->filesystem->readLog($this->getParameter('dir.log'));
    	return $this->render('@KrzysPage/Default/log.html.twig', array('lines'=>$lines));
    }    
    
    public function hideAction($year, $month, $day, $file) {
    	$dirYears = $this->getParameter('dir.years');
    	$picture = $dirYears.'/'.$year.'/'.$month.'/'.$day.'/thumb/'.$file;
    	unlink($picture);
    	$request = Request::createFromGlobals();
    	$isAjax = $request->isXmlHttpRequest();
    	if($isAjax) {
    		$result = array();
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');
    		return $response;
    	}
    }

    public function galleryAction($year, $month, $day, $file) {
    	$request = Request::createFromGlobals();

    	$screenWidth = $request->query->get('width');
    	$screenHeight = $request->query->get('height');
    	
    	$image = $newpath = $this->getParameter('dir.years').'/'.$year.'/'.$month.'/'.$day.'/'.$file;
    	$request = Request::createFromGlobals();
    	$isAjax = $request->isXmlHttpRequest();
		$pathToCachedFile = 'http://localhost/krzys-phpstorm/web/bundles/gallery/images/cache/' . $screenWidth . '-' . $screenHeight . '-'. $file;
		$localPathToCachedFile = $this->getParameter('dir.images') . '/cache/' . $screenWidth . '-' . $screenHeight . '-'. $file;

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
    	
    	$request = Request::createFromGlobals();
    	$isAjax = $request->isXmlHttpRequest();
    	if($isAjax) {
    		$result = array();
    		$result['image'] = $this->renderView('KrzysPageBundle:Default:single-image.html.twig', array('pic'=>new Media($year, $month, $day, $file, '', '', '', '', '', '', 'image')));
    		$response = new Response(json_encode($result));
    	    $response->headers->set('Content-Type', 'application/json');
    	    return $response;    	
    	}
    }
    
    public function tagAction($year, $month, $day, $file) {
    	$request = Request::createFromGlobals();
    	$action = $request->query->get('action');
    	$select = $request->query->get('select');
    	$dirYears = $this->getParameter('dir.years');
		$path = $dirYears.'/'.$year.'/'.$month.'/'.$day.'/tag';

		$this->filesystem->tagImage($action, $select, $path, $file);

    	$request = Request::createFromGlobals();
    	$isAjax = $request->isXmlHttpRequest();
    	if($isAjax) {
    		$result = array();
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');
    		return $response;
    	}
    }

	public function tagAllAction() {
		$request = Request::createFromGlobals();
		$dirYears = $this->getParameter('dir.years');
		$select = $request->query->get('select');
		$limit = $request->query->get('limit');

		$this->filesystem->tagAll($dirYears, $select, $limit);

		$request = Request::createFromGlobals();
		$isAjax = $request->isXmlHttpRequest();
		if($isAjax) {
			$result = array();
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
	}

	public function redoThumbsAction() {
		$request = Request::createFromGlobals();
		$dirYears = $this->getParameter('dir.years');
		$limit = $request->query->get('limit');

		$this->filesystem->redoThumbs($dirYears, $limit);

		$request = Request::createFromGlobals();
		$isAjax = $request->isXmlHttpRequest();
		if($isAjax) {
			$result = array();
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
	}

	public function descriptionAction($year, $month, $day, $file) {
    	$dirYears = $this->getParameter('dir.years');
    	$request = Request::createFromGlobals();
    	$description = $request->request->get('description');
		if(!file_exists($dirYears.'/'.$year.'/'.$month.'/'.$day.'/desc')) {
			mkdir($dirYears.'/'.$year.'/'.$month.'/'.$day.'/desc', 0777);
		}
    	$filename = $dirYears.'/'.$year.'/'.$month.'/'.$day.'/desc/'.$file.'.txt';
    	$handle = fopen($filename, "w");
    	$contents = fwrite($handle, $description);
    	fclose($handle);
    	return new Response(json_encode(array()));;
    }
    
    public function sessionAction() {
    	$request = Request::createFromGlobals();
    	$tag = $request->query->get('tag');    	
    	$session = $this->get('session');
    	$session->set('tag', $tag);    	
    	
    	if($request->query->has('r')) {
    		$params = $this->get('router')->match('/'.$request->query->get('r'));
    		return $this->redirect($this->generateUrl($params['_route']), 301);
    	} else	 
    		return new Response(json_encode(array()));;
    }    
    
}
