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
	
	function __construct()
	{
		
	}
	
	public function setSettings($settings)
	{
		$this->targetDir = $settings['targetDir'];
		$this->cleanupTargetDir = $settings['cleanupTargetDir'];
		$this->maxFileAge = $settings['maxFileAge'];
		$this->time_limit = $settings['time_limit'];
	}
}
?>