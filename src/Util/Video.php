<?php
namespace App\Util;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Video {
    private $name;

    public function __construct($name = '') {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

}

?>