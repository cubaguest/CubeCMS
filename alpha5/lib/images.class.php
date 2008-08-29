<?php
/**
 * Třída pro práci s obrázky
 * umožňuje:	-ukládání
 * 				-resizing
 * 				-vytváření miniatur
 * 
 */
class Images {
	/**
	 * Proměná s název souboru, který se bude upravovat
	 * @var string
	 */
	private $imageFile = null;
	
	/**
	 * Proměná obsahuje jestli je soubor obrázek
	 * @var boolean
	 */
	private $isImage = false;
	
	/**
	 * Porměné obsahuje šířku původního obrázku
	 * @var int
	 */
	private $imageWidth = 0;

	/**
	 * Porměné obsahuje výšku původního obrázku
	 * @var int
	 */
	private $imageHeight = 0;
	
	/**
	 * Porměné obsahuje typ původního obrázku
	 * @var int (constant 'IMAGETYPE_XXX')
	 */
	private $imageType = null;

	/**
	 * Proměná obsahuje, jestli má být obrázku zachovány rozměry, nebo ořezán
	 * @var boolean
	 */
	private $cropNewImage = false;
	
	/**
	 * Proměná s šířkou nového obrázku
	 * @var integer
	 */
	private $newImageWidth = 0;
	
	/**
	 * Proměná s výškou nového obrázku
	 * @var integer
	 */
	private $newImageHeight = 0;
	
	/**
	 * Proměná obsahuje název nového obrázku
	 * @var string
	 */
	private $newImageName = null;
	
	/**
	 * Proměná obsahuje typ nového obrázku
	 * @var int (constant 'IMAGETYPE_XXX')
	 */
//	private $newImageType = null;
	
	/**
	 * proměná s nastavenou kvalitou pro výstup JPEG
	 * @var int
	 */
	private $jpegQuality = 85;

	/**
	 * Proměná s nastavenou kvalitou pro výstup PNG
	 * @var int
	 */
	private $pngQuality = 9;
	
	/**
	 * Objekt s chybovými zprávami
	 * @var Messages
	 */
	private $errors = null;
	
	/**
	 * Jestli mají bý hglášeny chyby
	 * @var boolean
	 */
	private $reportErrors = true;
	
	/**
	 * Konstruktor třídy
	 * 
	 * @param string -- název souboru
	 */
	function __construct(Messages $errors, $file, $reportErrors = true){
		$this->errors = $errors;
		$this->imageFile = $file;
		$this->reportErrors = $reportErrors;
		
		$this->checkIsImage();
	}
	
	/**
	 * Matedoa zjišťuje, jestli je daný soubor obrázek
	 * 
	 * @return boolean -- true pokud se jedná o obrázek se kterým umí pracovat
	 */
	public function isImage() {
		return $this->isImage;
	}
	
	/**
	 * Metoda načte informace o obrázku
	 */
	private function checkIsImage() {
//		Ověření existence obrázku
		if($this->getImageFile() != null AND file_exists($this->getImageFile())){

			//		zjištění vlastností obrázků
			$imageProperty = getimagesize($this->getImageFile());
			$this->imageWidth = $imageProperty[0];
			$this->imageHeight = $imageProperty[1];
			$this->imageType = $imageProperty[2];
			
			if($this->imageType == null){
				$this->isImage = false;
				
				if($this->reportErrors){
					$this->errors->addMessage(_('Zadaný soubor není podporovaný obrázek'));
				}
			} else {
				$this->isImage = true;
			}
			
		} else {
			if($this->reportErrors){
				new CoreException(_('Soubor se zadaným obrázkem neexistuje'), 1);
			}
		}
	}
	
	/**
	 * Metoda vrací název souboru s obrázkem
	 * 
	 * @return string -- název souboru
	 */
	private function getImageFile(){
		return $this->imageFile;
	}
	
	/**
	 * metoda nastaví rozměry obrázku
	 * 
	 * @param integer -- šířka v px
	 * @param integer -- výška v px
	 */
	public function setDimensions($width, $height){
		$this->newImageWidth = $width;
		$this->newImageHeight = $height;
	}
	
	/**
	 * Metoda zapíná/vypíná ořezání obrázku
	 * @param boolean -- true pro zapnutí ořezání
	 */
	public function setCrop($crop) {
		$this->cropNewImage = $crop;
	}
	
	
	/**
	 * Metoda nastaví jméno nového obrázku
	 * 
	 * @param string -- název nového obrázku
	 */
	public function setImageName($name) {
		$this->newImageName = $name;
	}
	
	/**
	 * Metoda vrací název uloženého obrázku
	 * 
	 * @return string -- název obrázku
	 */
	public function getNewImageName() {
		return $this->newImageName;
	}
	
	
	/**
	 * Metoda uloží obrázek ve stejném formátu, v jakém byl zadán
	 * 
	 * @param string -- cílový adresář, kam se obrázek ukládá
	 * @param int -- šířka výsledného obrázku
	 * @param int -- výška výsledného obrázku
	 * @param string -- nový název obrázku
	 * @param int -- typ výsledného obrázku (constant IMAGETYPE_XXX)
	 */
	public function saveImage($dstDir, $width = null, $heigh = null, $newName = null, $imageType = null) {
		$tmpImage = $this->createTempImage();
		
		if($width == null){
			$width = $this->newImageWidth;
		}
		
		if($heigh == null){
			$heigh = $this->newImageHeight;
		}

		if($newName == null){
			$newName = $this->newImageName;
		}
		
//		test jestli je zpracováván obrázek
		if($this->isImage()){
			//		Test názvu souboru
			$newName = $this->checkNewImageName($dstDir, $newName);

			$newImage = $this->resampleImage($tmpImage,$width,$heigh);
			//ImageDestroy($tmpImage);

			if($imageType == null){
				$imageType = $this->imageType;
			}
			
			$this->saveNewImage($newImage, $imageType, $dstDir.$newName);
			imagedestroy($newImage);
		}
	}	
	
	/**
	 * Metoda uloží JPEG obrázek do zadané cesty
	 * 
	 * @param string -- cílový adresář, kam se obrázek ukládá
	 * @param int -- šířka výsledného obrázku
	 * @param int -- výška výsledného obrázku
	 * @param string -- nový název obrázku
	 */
	public function saveJpegImage($dstDir, $width = null, $heigh = null, $newName = null) {
		$this->saveImage($dstDir, $width, $heigh, $newName, IMAGETYPE_JPEG);
	}

	/**
	 * Metoda uloží PNG obrázek do zadané cesty
	 * 
	 * @param string -- cílový adresář, kam se obrázek ukládá
	 * @param int -- šířka výsledného obrázku
	 * @param int -- výška výsledného obrázku
	 * @param string -- nový název obrázku
	 */
	public function savePngImage($dstDir, $width = null, $heigh = null, $newName = null) {
		$this->saveImage($dstDir, $width, $heigh, $newName, IMAGETYPE_GIF);
	}
	
	/**
	 * Metoda ověřuje, zda-li nový obrázek již není uložen, popřípadě vytváří nový název
	 * 
	 * @param string -- adresář s obrázkem
	 * @param string -- název obrázku
	 */
	private function checkNewImageName($dir, $file) {
		$files = new Files();
		
//		kontrola adresáře
		$files->checkDir($dir);
		
//		kontrola unikátnosti jména
		$this->newImageName = $files->createNewFileName($file, $dir);
		unset($files);
		return $this->newImageName;
	}
	
	
	/**
	 * Metoda rozhoduje ze kterého typu obrázku se bude načítat obrázku 
	 * 
	 * @return image -- vrací objekt obrázku z původního obrázku
	 */
	private function createTempImage() {
		//		Zjištění druhu obrázku a vytvoření pracovního obrázk
		switch ($this->imageType) {
			case IMAGETYPE_GIF:
				$tempImage = imagecreatefromgif($this->imageFile);
				break;
			case IMAGETYPE_JPEG:
				$tempImage = imagecreatefromjpeg($this->imageFile);
				break;
			case IMAGETYPE_PNG:
				$tempImage = imagecreatefrompng($this->imageFile);
				break;
			case IMAGETYPE_WBMP:
				$tempImage = imagecreatefromwbmp($this->imageFile);
				break;
			case IMAGETYPE_JPEG2000:
				$tempImage = imagecreatefromjpeg($this->imageFile);
				break;
			default:
				$tempImage = false;
		};
		return $tempImage;
	}
	
	/**
	 * Metoda přesampluje zadaný obrázek na nový obrázek
	 * 
	 * @param image -- vytvořený obrázek z původního
	 * @param int -- šířka nového obrázku
	 * @param int -- výška nového obrázku
	 */
	private function resampleImage($tempImage, $width, $height) {
		if($this->cropNewImage == false){
			//				obrázek je na šířku
			if($this->imageWidth > $this->imageHeight){
				$imageRate = $this->imageHeight/$this->imageWidth;
				$height = $imageRate*$width;
			}
			//				obrázek je na výšku
			else {
				$imageRate = $this->imageWidth/$this->imageHeight;
				$width = $imageRate*$height;
			}

			$newImage = imagecreatetruecolor($width, $height);
			ImageCopyResampled($newImage, $tempImage, 0,0,0,0, $width, $height, $this->imageWidth, $this->imageHeight);

		} else {
			//			Ořezání obrázku do jedné velikosti
			$newImage = imagecreatetruecolor($width, $height);

			$scale = (($width / $this->imageWidth) > ($height / $this->imageHeight)) ? ($width / $this->imageWidth) : ($height / $this->imageHeight); // vyber vetsi pomer a zkus to nejak dopasovat...
			$newW = $width/$scale;    // jak by mel byt zdroj velky (pro poradek :)
			$newH = $height/$scale;

			// ktera strana precuhuje vic (kvuli chybe v zaokrouhleni)
			if (($this->imageWidth - $newW) > ($this->imageHeight - $newH)) {
				$imageX = floor(($this->imageWidth - $newW)/2);
				$imageY = 0;
				$imageWidth = floor($newW);
				$imageHeight = $this->imageHeight;

				//					$src = array(floor(($imageProperty[0] - $newW)/2), 0, floor($newW), $imageProperty[1]);
			}
			else {
				$imageX = 0;
				$imageY = floor(($this->imageHeight - $newH)/2);
				$imageWidth = $this->imageHeight;
				$imageHeight = floor($newH);

				//					$src = array(0, floor(($imageProperty[1] - $newH)/2), $imageProperty[0], floor($newH));
			}

			ImageCopyResampled($newImage, $tempImage, 0,0, $imageX, $imageY, $width, $height, $imageWidth,$imageHeight);	
		}
//		ImageDestroy($tempImage); //není potřeba
		return $newImage;
	}
	
	/**
	 * Metoda uloží obrázek do souboru
	 * 
	 */
	private function saveNewImage($newImage, $type, $destFile) {
		switch ($type) {
			case IMAGETYPE_GIF:
				imagegif($newImage, $destFile);
				break;
			case IMAGETYPE_JPEG:
				imagejpeg($newImage, $destFile, $this->jpegQuality);
				break;
			case IMAGETYPE_PNG:
				imagepng($newImage, $destFile, $this->pngQuality);
				break;
			case IMAGETYPE_WBMP:
				imagewbmp($newImage, $destFile);
				break;
			case IMAGETYPE_JPEG2000:
				imagejpeg($newImage, $destFile, $this->jpegQuality); //jen výstup do jpegu
				break;
			default:
				$newImageType = false;
		};
	}
	
	
	
	/**
	 * Funkce vytvoří obrázek ze zadaného obrázku a uloží jej do specifikovaného souboru
	 *
	 * @param string -- zdrojový soubor ($_FILE['file']['tmp_file'])
	 * @param string -- cesta a cílový soubor
	 * @param integer -- max. šířka výsledného obrázku
	 * @param integer -- max. výška výsledného obrázku
	 * @param boolean -- true pro ořezání obrázku na zadané velikosti, false pouze pro změnu velikosti
	 * @param integer -- výsledná kvalita jpg komprese (default 85)
	 * @param integer -- výsledná kvalit png komprese (default 9
	 * @return boolean -- true, jestliže byl obrázek uspěšně vytvořen a ulože
	 */
	public function createImage($srcFile, $destFile, $width, $height, $cropSize = false, $jpegQuality = 85, $pngQuality = 9){
			

			//		Výpočet nové velikosti
			$imageProperty = getimagesize($srcFile);
			if($newImageType != false){
				if($cropSize == false){
					//				obrázek je na šířku
					if($imageProperty[0] > $imageProperty[1]){
						$imageRate = $imageProperty[1]/$imageProperty[0];
						$height = $imageRate*$width;
					}
					//				obrázek je na výšku
					else {
						$imageRate = $imageProperty[0]/$imageProperty[1];
						$width = $imageRate*$height;
					}

					$newImage = imagecreatetruecolor($width, $height);
					imagecopyresampled($newImage, $tempImage, 0,0,0,0, $width, $height, $imageProperty[0], $imageProperty[1]);

				} else {
					//			Ořezání obrázku do jedné velikosti
					$newImage = imagecreatetruecolor($width, $height);

					$scale = (($width / $imageProperty[0]) > ($height / $imageProperty[1])) ? ($width / $imageProperty[0]) : ($height / $imageProperty[1]); // vyber vetsi pomer a zkus to nejak dopasovat...
					$newW = $width/$scale;    // jak by mel byt zdroj velky (pro poradek :)
					$newH = $height/$scale;

					// ktera strana precuhuje vic (kvuli chybe v zaokrouhleni)
					if (($imageProperty[0] - $newW) > ($imageProperty[1] - $newH)) {
						$imageX = floor(($imageProperty[0] - $newW)/2);
						$imageY = 0;
						$imageWidth = floor($newW);
						$imageHeight = $imageProperty[1];

						//					$src = array(floor(($imageProperty[0] - $newW)/2), 0, floor($newW), $imageProperty[1]);
					}
					else {
						$imageX = 0;
						$imageY = floor(($imageProperty[1] - $newH)/2);
						$imageWidth = $imageProperty[0];
						$imageHeight = floor($newH);

						//					$src = array(0, floor(($imageProperty[1] - $newH)/2), $imageProperty[0], floor($newH));
					}

					ImageCopyResampled($newImage, $tempImage, 0,0, $imageX, $imageY, $width, $height, $imageWidth,$imageHeight);
					
				}

				//			uložení obrázku
				if ($newImageType == IMAGETYPE_JPEG){
					$newImageFunction($newImage, $destFile, $jpegQuality);
				} else if($newImageType == IMAGETYPE_JPEG){
					$newImageFunction($newImage, $destFile, $pngQuality);
				} else {
					$newImageFunction($newImage, $destFile);
				}

				ImageDestroy($newImage);
				ImageDestroy($tempImage);

				return true;
			}
		return false;
	}
	
}

?>