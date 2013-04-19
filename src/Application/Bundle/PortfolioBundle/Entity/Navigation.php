<?php
namespace Application\Bundle\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="navigations")
 */
class Navigation
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
     * @ORM\Column(type="integer")
     */
	protected $parentId;
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getParentId() {
		return $this->parentId;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function setParentId($parentId) {
		$this->parentId = $parentId;
	}
}
?>