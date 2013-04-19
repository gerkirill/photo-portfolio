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
    	$repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Image');
		$images = $repository->findAll();
		$menu = $this->getMenu();
		return array('all_menu' => $menu, 'images' => $images);
    }
	
	function getMenu()
	{
		$repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Navigation');
		$menu = $repository->findAll();
		$child_menu = $parent = array();
		foreach ($menu as $m) {
			if($m->getParentId() == 0) {
				$main_menu[] = $m->getName();
			} else {
				$parent_name = $repository->find($m->getParentId())->getName();
				$child_menu[] = array('parent' => $parent_name, 'child' => $m->getName());
				$parent[$m->getParentId()] = $parent_name;
			}
		}
		return array('main_menu' => $main_menu, 'child_menu' => $child_menu, 'parent' => $parent);
	}
}
