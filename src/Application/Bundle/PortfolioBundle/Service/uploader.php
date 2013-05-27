<?php
namespace Application\Bundle\PortfolioBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;

/**
 * @Service("portfolio.uploader", public=true)
 */
class Uploader
{
	protected $targetDir = 'uploads';
	protected $cleanupTargetDir = true; // Remove old files
	protected $maxFileAge = 5*3600; // Temp file age in seconds
	protected $time_limit = 5*60; // 5 minutes execution time
	
	protected $chunk;
	protected $chunks;
	protected $fileName;
	
	function __construct($request)
	{
		$this->fileName = $request['file'];
		
		if (!file_exists($this->targetDir))
			@mkdir($this->targetDir);
	}
	
	public function setSettings($settings)
	{
		$this->targetDir = $settings['targetDir'];
		$this->cleanupTargetDir = $settings['cleanupTargetDir'];
		$this->maxFileAge = $settings['maxFileAge'];
		$this->time_limit = $settings['time_limit'];
	}
	
	public function savePhoto($id)
	{
		$image = new Entity\Image;
		$image->setName($this->fileName);
		$image->setUrl($this->fileName);
		$image->setNav_id($id);
		$image->setSort(0);
		$em = $this->getDoctrine()->getEntityManager();
		$em->persist($image);
		$em->flush();
	}
	
	public function saveImg($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('ApplicationPortfolioBundle:Pages');
		$page = $repository->find($id);
		$page->setImg($this->fileName);
		$em->persist($page);
		$em->flush();
	}
}
?>