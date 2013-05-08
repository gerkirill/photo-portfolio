<?php
namespace Application\Bundle\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="images")
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;
	
	/**
     * @ORM\Column(type="string", length=255)
     */
    protected $url;
	
	/**
     * @ORM\Column(type="integer")
     */
    protected $nav_id;
	
	/**
     * @ORM\Column(type="integer")
     */
    protected $sort;

	public function getID()
	{
		return $this->id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getUrl()
	{
		return $this->url;
	}
	
	public function getNav_id()
	{
		return $this->nav_id;
	}
	
	public function getSort()
	{
		return $this->sort;
	}
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	public function setNav_id($nav_id)
	{
		$this->nav_id = $nav_id;
	}
	
	public function setSort($sort)
	{
		$this->sort = $sort;
	}
}
?>