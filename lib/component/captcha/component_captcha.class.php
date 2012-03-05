<?php
/** 
 * Třída Komponenty pro tvorbu obrázku pro captchu
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty pro tvorbu pdf souborů
 */

class Component_Captcha extends Component {

   protected $config = array(
      'rand' => null,
      'salt' => null,
      'width' => 100,
      'height' => 25,
      'baseimage' => null,
      'session' => 'captcha',
   );

      /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false)
   {
      $this->componentName = str_ireplace(__CLASS__.'_', '', get_class($this));
      $this->setConfig('baseimage', AppCore::getAppLibDir().'images'.DIRECTORY_SEPARATOR.'captcha.jpg');
   }

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init() {}

   /**
    * Metoda provede validaci captchy
    * @param string $str -- řetězec
    * @return bool
    */
   public function validate($str)
   {
      if(isset ($_SESSION[$this->getConfig('session')])){
         return $_SESSION[$this->getConfig('session')] == $str;
      }
      return false;
   }

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart() {}

   /**
    * Spuštění komponenty (hlavní kontroler)
    */
   public function mainController() {}
   
   /**
    * Metoda provede vykreslení komponenty
    */
   public function mainView() {}
   
   private function generateRandom($length=6) 
   { 
    $_rand_src = array( 
        array(48,57) //digits 
        , array(97,122) //lowercase chars 
//        , array(65,90) //uppercase chars 
    ); 
    srand ((double) microtime() * 1000000); 
    $random_string = ""; 
    for($i=0;$i<$length;$i++){ 
        $i1=rand(0,sizeof($_rand_src)-1); 
        $random_string .= chr(rand($_rand_src[$i1][0],$_rand_src[$i1][1])); 
    } 
    return $random_string; 
   } 
   
   /**
    * Spuštění komponenty (hlavní kontroler)
    */
   public function imageJpgController()
   {
      $this->setConfig('rand', $this->generateRandom(3));
      $this->setConfig('salt', $this->generateRandom(3));
//      $_SESSION[$this->getConfig('session')][Category::getSelectedCategory()->getId()] = $this->getConfig('rand'); // pokud jich bude na stránce více
      $_SESSION[$this->getConfig('session')] = $this->getConfig('rand');
   }
   
   public function imageJpgView()
   {
      $image = @imagecreatefromjpeg($this->getConfig('baseimage'));  
      
      $rand = $this->getConfig('salt'); 
      imagestring($image, 5, 2, 2, $rand[0]." ".$rand[1]." ".$rand[2]." ", imagecolorallocate($image, 0, 0, 0)); 
      $rand = $this->getConfig('rand'); 
      imagestring($image, 5, 2, 2, " ".$rand[0]." ".$rand[1]." ".$rand[2], imagecolorallocate($image, 255, 0, 0)); 
      Template_Output::setOutputType('jpeg');
      Template_Output::sendHeaders();
      imagejpeg($image);
      imagedestroy($image);
      die();
   }

   /**
    * Metoda vrací adresu k obrázku pro captchu
    * @return type 
    */
   public static function getImage()
   {
      $link = new Url_Link_Component('captcha');
      return $link->onlyAction('image', 'jpg');
   }
   
}
?>