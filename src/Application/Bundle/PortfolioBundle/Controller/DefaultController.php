<?php

namespace Application\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Application\Bundle\PortfolioBundle\Entity;

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
			array('nav_id' => 0),
			array('sort' => 'ASC')
		);
		return array('images' => $images);
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
		$repository = $this->getDoctrine()->getRepository('ApplicationPortfolioBundle:Image');
		$images = $repository->findAll();
		return array('images' => $images);
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
	
	/**
	 * @Route("/upload", name="upload")
	 */
	public function uploadAction()
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
			$this->get('image.handling')->open('uploads/'.$fileName)->zoomCrop(72,72)->save('images/72x72/'.$fileName);
			$this->get('image.handling')->open('uploads/'.$fileName)->zoomCrop(930,620)->save('images/930x620/'.$fileName);
			$this->get('image.handling')->open('uploads/'.$fileName)->zoomCrop(705,470)->save('images/705x470/'.$fileName);
			$this->get('image.handling')->open('uploads/'.$fileName)->zoomCrop(1150,770)->save('images/1150x770/'.$fileName);
			
			if ($objs = @glob($targetDir."/*")) {
			   foreach($objs as $obj) {
				@unlink($obj);
			   }
			}
			@rmdir($targetDir);

			$image = new Entity\Image;
			$image->setName($fileName);
			$image->setUrl($fileName);
			$image->setNav_id(0);
			$image->setSort(0);
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($image);
			$em->flush();
		}

		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
		
		return array();
	}
	
	/**
	 * @Route("/sortable", name="sortable")
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
			$em->flush();
		}
		$data = json_encode(array('result' => 'ok'));
		$headers = array( 'Content-type' => 'application-json; charset=utf8' );
		$responce = new Response( $data, 200, $headers );
		return $responce;
	}

}
