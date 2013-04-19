<?php

namespace Application\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


class GalleryController extends Controller
{

    /**
     * @Route("/")
	 * @Template("ApplicationPortfolioBundle:Gallery:index.html.twig")
     */
    public function indexAction()
    {
    	$repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Navigation');
		$menu = $repository->findAll();
		
		//return new Response('Gallery index page');
		return array('menus' => $menu);
    }
}
