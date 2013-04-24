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
	
	/**
	 * @Route("/test", name="test")
	 * @Template()
	 */
	public function testAction()
	{
		$images = $this->get('image.handling')->open('http://www.shtern.ru/storage/photos/21/0_ekatirina-stern-d913ba37d4d1b2b1c115a625a41d854e.jpg');
		$images->zoomCrop(100,100);
		$images->save('test.jpg');
		return array('img' => 'test.jpg');
	}

}
