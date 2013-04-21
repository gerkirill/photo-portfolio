<?php

namespace Application\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class DefaultController
 * @Route("/design")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

	/**
	 * @Route("/menu", name="menu")
	 * @Template()
	 */
	public function menuAction()
	{
		return array();
	}

	/**
	 * @Route("/slider", name="slider")
	 * @Template()
	 */
	public function sliderAction()
	{
		return array();
	}

}
