<?php
namespace Application\Bundle\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pages")
 */
class Pages
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
     * @ORM\Column(type="integer")
     */
    protected $nav_id;
	
	/**
     * @ORM\Column(type="text")
     */
    protected $text;
	
	/**
     * @ORM\Column(type="string", length=255)
     */
    protected $img;

	public function getId()
	{
		return $this->id;
	}

	public function getNav_id()
	{
		return $this->nav_id;
	}
	
	public function setNav_id($nav_id)
	{
		$this->nav_id = $nav_id;
	}
	
	public function getText()
	{
		return $this->text;
	}
	
	public function setText($text)
	{
		$this->text = $text;
	}
	
	public function getImg()
	{
		return $this->img;
	}
	
	public function setImg($img)
	{
		$this->img = $img;
	}
}
?>