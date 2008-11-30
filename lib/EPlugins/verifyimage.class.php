<?php
/**
 * Description of verifyimageclass
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: verifyimageclass.class.php 419 2008-11-28 23:21:19Z jakub $ VVE3.3.0 $Revision: 419 $
 * @author			$Author: jakub $ $Date: 2008-11-28 23:21:19 +0000 (Pá, 28 lis 2008) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2008-11-28 23:21:19 +0000 (Pá, 28 lis 2008) $
 * @abstract		text
 */
class VerifyImageEplugin extends Eplugin {
	/**
	 * Adresář s fonty
	 * @var string
	 */
	const FONTS_DIR = 'fonts';

	/**
	 * Název použitého fontu pro vypisování písma
	 * @var string
	 */
	const FONT_NAME = 'Timeless-Bold.ttf';

	/**
	 * Název session s heslem
	 * @var string
	 */
	const SESSION_NAME = 'control_chars';

	/**
	 * Název obrázku s ověřením
	 * @var string
	 */
	const IMAGE_NAME = 'epluginVerifyImage.jpg';

	/**
	 * Objekt se session
	 * @var Sessions
	 */
	private $session = null;

	private $previousCode = null;

	/**
	 * Metoda inicializace, je spuštěna pří vytvoření objektu
	 *
	 */
	protected function init(){
		$this->session = new Sessions();
		if(!$this->isRunOnly()){
			$this->generateVerifyChars();
		}
	}

	/**
	 * Funkce ověřuje správné zadání kontrloního obrázk
	 * //TODO není třeba
	 * @param string -- název postu s odesýlaným textem
	 * @return boolean -- true pokud se data shodují
	 */
	public function verifyImage($post_name)
	{
		$post = htmlspecialchars($_POST[$post_name]);
		if($post == $this->previousCode){
			return true;
		} else {
			$this->errMsg()->addMessage(_('Kontrolní obrázek nebyl správně opsán'));
			return false;
		}
	}

	/**
	 * funkce vygeneruje náhodné znaky pro ověření obrázkem
	 * a uloží je do session
	 */
	private function generateVerifyChars()
	{
		$this->previousCode = $this->session->get(self::SESSION_NAME);
		$chars = "ABCDEFHJKLMNPRSTUVWXYZ1234567890";
		$passwd = null;
		for ($i=0; $i<5; $i++){
			$passwd .= $chars{rand(0, strlen($chars)-1)};
		}
		$this->session->add(self::SESSION_NAME, $passwd);
	}

	/**
	 * Metoda je spuštěna při načítání souborů epluginu
	 * Zde je odeslán soubor s obrázkem
	 * @todo -- dodělat zjitování pozadí obrázku podle zvolené faces
	 */
	public function runOnlyEplugin() {
		$fontDir = './'.self::FONTS_DIR.'/'.self::FONT_NAME;

		$chars = $this->session->get(self::SESSION_NAME);
		$backlayer = ImageCreateFromPNG('./'.AppCore::TEMPLATES_IMAGES_DIR."/control_chars_back.png");
		$frontlayer = imagecreatefromPNG('./'.AppCore::TEMPLATES_IMAGES_DIR."/control_chars_front.png");
		$color = ImageColorAllocate($backlayer, 0, 0, rand(0,200));
		ImageTTFText($backlayer, rand(25,30), rand(-40,40), 20, 40, $color, $fontDir, $chars[0]);
		$color = ImageColorAllocate($backlayer, 0, 0, rand(0,200));
		ImageTTFText($backlayer, rand(25,30), rand(-40,40), 60, 40, $color, $fontDir, $chars[1]);
		$color = ImageColorAllocate($backlayer, 0, 0, rand(0,200));
		ImageTTFText($backlayer, rand(25,30), rand(-40,40), 100, 40, $color, $fontDir, $chars[2]);
		$color = ImageColorAllocate($backlayer, 0, 0, rand(0,200));
		ImageTTFText($backlayer, rand(25,30), rand(-40,40), 140, 40, $color, $fontDir, $chars[3]);
		$color = ImageColorAllocate($backlayer, 0, 0, rand(0,200));
		ImageTTFText($backlayer, rand(25,30), rand(-40,40), 180, 40, $color, $fontDir, $chars[4]);
		imagecopy($backlayer, $frontlayer, 0, 0, 0, 0, 220, 51 );
		header("Content-type: image/png");
		ImagePNG($backlayer);
		ImageDestroy($backlayer);
		ImageDestroy($fronlayer);
	}

	/**
	 * Metoda vrací cestku ke kontrolnímu obrázku
	 * @return string -- cestak k obrázku
	 */
	public function getImageName() {
		$link = new Links(true, true, true);
		return $link.self::IMAGE_NAME;
	}
}
?>
