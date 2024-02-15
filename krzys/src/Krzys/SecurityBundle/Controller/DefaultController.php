<?php

namespace App\Krzys\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RequestStack;
//use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;


class DefaultController extends AbstractController
{

	public function __construct(RequestStack $requestStack)
	{
		$this->requestStack = $requestStack;
	}

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
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
//		$authenticationUtils = $this->security('security.authentication_utils');

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render(
			'@KrzysSecurity/Default/login.html.twig',
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
	public function loginCheckAction(LoggerInterface $logger)
	{
//			    $logger->info('this should not happen!!!!!');

		// this controller will not be executed,
		// as the route is handled by the Security system
	}
}
