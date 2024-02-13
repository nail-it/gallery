<?php

namespace App\Krzys\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;


class DefaultController extends AbstractController
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

	/**
	 * @Route("/login", name="login_route")
	 */
    public function loginAction(Request $request)
    {
		$authenticationUtils = $this->get('security.authentication_utils');

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render(
			'KrzysSecurityBundle:Default:login.html.twig',
			array(
				// last username entered by the user
				'last_username' => $lastUsername,
				'error'         => $error,
			)
		);
    }

	/**
	 * @Route("/login_check", name="login_check")
	 */
	public function loginCheckAction()
	{
		// this controller will not be executed,
		// as the route is handled by the Security system
	}
}
