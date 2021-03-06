<?php

namespace Application\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Application\Bundle\PortfolioBundle\Entity;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Core\SecurityContext;

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
        $repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Image');
		$images = $repository->findBy(
			array('nav_id' => 1),
			array('sort' => 'ASC', 'id' => 'DESC')
		);
		return array('images' => $images, 'nav_id' => 1);
    }
	
	/**
     * @Route("/about", name="about")
     * @Template()
     */
    public function aboutAction()
    {
		return array('name' => 'about');
    }
	
	/**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction()
    {
		$request = $this->getRequest();
		$session = $request->getSession();
		
		// получить ошибки логина, если таковые имеются
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }
		
		return array(
            // имя, введённое пользователем в последний раз
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }
	
	/**
     * @Route("/page/{name}", name="page")
     * @Template()
     */
    public function pageAction($name)
    {
		$nav = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Navigation')->findOneBy(array('permalink' => '/design/'.$name));
		$repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Pages');
		$page = $repository->findOneBy(array('nav_id' => $nav->getId()));
		return array('page' => $page);
    }
	
	/**
     * @Route("/pageSave", name="pageSave")
     */
    public function pageSaveAction()
    {
		$request = $this->getRequest()->request->all();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('ApplicationPortfolioBundle:Pages');
		
		$id = $request['id'];
		$text = $request['text'];
		$page = $repository->find($id);
		$page->setText($text);
		$em->persist($page);
		$em->flush();
		
		$data = json_encode(array('result' => 'ok'));
		$headers = array( 'Content-type' => 'application-json; charset=utf8' );
		$responce = new Response( $data, 200, $headers );
		return $responce;
    }

	/**
     * @Route("/gallery", name="gallery")
     * @Template()
     */
    public function galleryAction()
    {
        $repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Image');
		$image = $repository->findOneBy(array('sort' => 0));
		return array('image' => $image);
    }
	
	/**
     * @Route("/portfolio/{gallery}", name="portfolio")
     * @Template()
     */
    public function portfolioAction($gallery)
    {
        $repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Image');
		$nav = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Navigation')->findOneBy(array('permalink' => $gallery));
		$image = $repository->findOneBy(array('sort' => 0, 'nav_id' => $nav->getId()));
		$images = $repository->findBy(
			array('nav_id' => $nav->getId()),
			array('sort' => 'ASC', 'id' => 'DESC')
		);
		return array('image' => $image, 'permalink' => $gallery, 'images' => $images, 'nav_id' => $nav->getId());
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
	 * @Route("/listMenu", name="listMenu")
	 * @Template()
	 */
	public function listMenuAction()
	{
		$repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Navigation');
		$nav = $repository->findBy(array('toplevel' => 1));
		return array('nav' => $nav);
	}

	/**
	 * @Route("/slider/{gallery}", name="slider")
	 * @Template()
	 */
	public function sliderAction($gallery)
	{
		$repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Image');
		$nav = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Navigation')->findOneBy(array('permalink' => $gallery));
		$images = $repository->findBy(array('nav_id' => $nav->getId()));
		return array('images' => $images);
	}
	
	/**
	 * @Route("/photosEdit/{nav_id}", name="photosEdit")
	 * @Template()
	 */
	public function photosEditAction($nav_id)
	{
		$repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Image');
		$images = $repository->findBy(
			array('nav_id' => $nav_id),
			array('sort' => 'ASC', 'id' => 'DESC')
		);
		return array('images' => $images, 'nav_id' => $nav_id);
	}
	
	/**
	 * @Route("/test", name="test")
	 * @Template()
	 */
	public function testAction()
	{
//		$images = $this->get('image.handling')->open('http://www.shtern.ru/storage/photos/21/0_ekatirina-stern-d913ba37d4d1b2b1c115a625a41d854e.jpg');
//		$images->zoomCrop(100,100);
//		$images->save('test.jpg');
		return array('img' => 'test.jpg');
	}
	
	/**
	 * @Route("/upload/{id}", name="upload")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function uploadAction($id)
	{
		$targetDir = 'uploads';
		
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds

		// 5 minutes execution time
		@set_time_limit(5 * 60);

		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		
		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
		
		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . '/' . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir . '/' . $fileName_a . '_' . $count . $fileName_b))
				$count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}
		$fileNameNew = 'image_'.date('dmYHis');
		$filePath = $targetDir . '/' . $fileName;
		
		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir);
			
		// Remove old temp files	
		if ($cleanupTargetDir) {
			if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
				while (($file = readdir($dir)) !== false) {
					$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

					// Remove temp file if it is older than the max age and is not the current file
					if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
						@unlink($tmpfilePath);
					}
				}
				closedir($dir);
			} else {
				die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			}
		}
		
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];

		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = @fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					@fclose($in);
					@fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = @fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

				@fclose($in);
				@fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off 
			rename("{$filePath}.part", $filePath);
			$this->get('image.handling')->open('uploads/'.$fileName)->zoomCrop(72,72)->save('images/72x72/'.$fileNameNew);
			$this->get('image.handling')->open('uploads/'.$fileName)->zoomCrop(930,620)->save('images/930x620/'.$fileNameNew);
			$this->get('image.handling')->open('uploads/'.$fileName)->zoomCrop(705,470)->save('images/705x470/'.$fileNameNew);
			$this->get('image.handling')->open('uploads/'.$fileName)->zoomCrop(1150,770)->save('images/1150x770/'.$fileNameNew);
			
			if ($objs = @glob($targetDir."/*")) {
			   foreach($objs as $obj) {
				@unlink($obj);
			   }
			}
			@rmdir($targetDir);

			$image = new Entity\Image;
			$image->setName($fileNameNew);
			$image->setUrl($fileNameNew);
			$image->setNav_id($id);
			$image->setSort(0);
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($image);
			$em->flush();
		}

		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
		
		return array();
	}
	
	/**
	 * @Route("/uploadImg", name="uploadImg")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function uploadImgAction()
	{
		$request = $this->getRequest()->request->all();
		var_dump($request);
		
		$data = json_encode(array('result' => 'ok'));
		$headers = array( 'Content-type' => 'application-json; charset=utf8' );
		$responce = new Response( $data, 200, $headers );
		return $responce;
	}
	
	/**
	 * @Route("/sortable", name="sortable")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function sortableAction()
	{
		$request = $this->getRequest()->request->all();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('ApplicationPortfolioBundle:Image');
		
		foreach($request['items'] as $key=>$item){
			$id = substr(strstr($item, '_'), 1);
			$image = $repository->find($id);
			$image->setSort($key);
			$em->persist($image);
		}
		$em->flush();
		
		$data = json_encode(array('result' => 'ok'));
		$headers = array( 'Content-type' => 'application-json; charset=utf8' );
		$responce = new Response( $data, 200, $headers );
		return $responce;
	}
	
	/**
	 * @Route("/photo-edit", name="photoEdit")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function photoEditAction()
	{
		$request = $this->getRequest()->request->all();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('ApplicationPortfolioBundle:Image');
		
		$key = $request['key'];
		$img_id = $request['img_id'];
		$image = $repository->find($img_id);
		$mes = 'error';
		
		if($key == 'Delete'){
			$em->remove($image);
			$mes = "delete";
		}else{
			$pos = strpos($key, '-');
			$action = substr($key, 0, $pos);
			$page = substr($key, $pos+1);
			if($action == "move"){
				$mes = "move";
			}
			if($action == "copy"){
				$mes = "copy";
			}
		}
		$em->flush();
		
		$data = json_encode(array('result' => $mes, 'img_id' => $img_id));
		$headers = array( 'Content-type' => 'application-json; charset=utf8' );
		$responce = new Response( $data, 200, $headers );
		return $responce;
	}
	
	/**
	 * @Route("/menu-edit", name="menuEdit")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function menuEditAction()
	{
		$request = $this->getRequest()->request->all();
		$em = $this->getDoctrine()->getEntityManager();
		$repositoryNav = $em->getRepository('ApplicationPortfolioBundle:Navigation');
		
		$title = $request['title'];
		$id = $request['id'];
		$status = 'ok';
		$menu = $repositoryNav->find($id);
		$menu->setName($title);
		$menu->setPermalink($this->translit($title));
		$em->persist($menu);
		$em->flush();
		
		$data = json_encode(array('id' => $id, 'status' => $status));
		$headers = array( 'Content-type' => 'application-json; charset=utf8' );
		$responce = new Response( $data, 200, $headers );
		return $responce;
	}
	
	/**
	 * @Route("/menu-add", name="menuAdd")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function menuAddAction()
	{
		$request = $this->getRequest()->request->all();
		$em = $this->getDoctrine()->getEntityManager();
		$repositoryNav = $em->getRepository('ApplicationPortfolioBundle:Navigation');
		
		$title = $request['title'];
		$id = $request['id'];
		
		$nav = new Entity\Navigation;
		$nav->setName($title);
		if($id == 0){
			$nav->setPermalink($this->translit('/design/'.$title));
			$nav->setToplevel(1);
		}else{
			$nav->setPermalink($this->translit($title));
			$menu = $repositoryNav->find($id);
			$nav->setParent($menu);
			$nav->setToplevel(0);
		}
		$em->persist($nav);
		$em->flush();
		$id = $nav->getId();
		$status = 'ok';

		$data = json_encode(array('id' => $id, 'status' => $status));
		$headers = array( 'Content-type' => 'application-json; charset=utf8' );
		$responce = new Response( $data, 200, $headers );
		return $responce;
	}
	
	/**
	 * @Route("/menu-delete", name="menuDelete")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function menuDeleteAction()
	{
		$request = $this->getRequest()->request->all();
		$em = $this->getDoctrine()->getEntityManager();
		$repositoryNav = $em->getRepository('ApplicationPortfolioBundle:Navigation');
		
		$id = $request['id'];
		$menu = $repositoryNav->find($id);
		if(count($menu->getChildren()) > 0){
			foreach($menu->getChildren() as $nav){
				$em->remove($nav);
			}
		}
		$em->remove($menu);
		$em->flush();
		$status = 'ok';

		$data = json_encode(array('id' => $id, 'status' => $status));
		$headers = array( 'Content-type' => 'application-json; charset=utf8' );
		$responce = new Response( $data, 200, $headers );
		return $responce;
	}
	
	private function translit($str)
	{
		$toreplace = array("А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С",
			"Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ь", "Ы", "Ъ", "Э", "Ю", "Я",
			"а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с",
			"т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ь", "ы", "ъ", "э", "ю", "я", " "
		);
		$replacement = array("a", "b", "v", "g", "d", "ye", "yo", "zh", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s",
			"t", "u", "f", "kh", "ts", "ch", "sh", "shch", "", "y", "", "e", "yu", "ya",
			"a", "b", "v", "g", "d", "ye", "yo", "zh", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s",
			"t", "u", "f", "kh", "ts", "ch", "sh", "shch", "", "y", "", "e", "yu", "ya", "_"
		);
		return str_replace($toreplace, $replacement, $str);
	}

}
