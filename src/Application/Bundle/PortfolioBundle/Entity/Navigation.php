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
     * @ORM\Column(type="string", length=255)
     */
	protected $permalink;

    /**
     * @ORM\OneToMany(targetEntity="Navigation", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Navigation", inversedBy="children")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id")
     */
    protected $parent;
	
	/**
     * @ORM\Column(type="integer")
     */
	protected $toplevel;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getChildren()
    {
        return $this->children;
    }
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}

	public function getParent() {
		return $this->parent;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function setParent(Navigation $parent) {
		$this->parent = $parent;
	}
	
	public function getPermalink() {
		return $this->permalink;
	}
	
	public function setPermalink($permalink) {
		$this->permalink = $permalink;
	}
	
	public function getToplevel() {
		return $this->toplevel;
	}
	
	public function setToplevel($toplevel) {
		$this->toplevel = $toplevel;
	}
}
?>