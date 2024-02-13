<?php

namespace App\Nailit\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DefaultController extends AbstractController
{
	public $galleryDayFromDescription;
	public $gallerySeparateDays;
	public $mainPath;
	public function __construct(string $galleryDayFromDescription = 'true', string $gallerySeparateDays = 'false') {
		$this->galleryDayFromDescription = $galleryDayFromDescription;
		$this->gallerySeparateDays = $gallerySeparateDays;
	}
}
