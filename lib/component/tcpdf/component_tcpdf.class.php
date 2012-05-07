<?php
/** 
 * Třída Komponenty pro tvorbu pdf souborů - je postavena na knihovně TCPDF
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty pro tvorbu pdf souborů
 */

class Component_Tcpdf extends Component {
   protected $config = array('orientation' => null,
           'unit' => null,
           'format' => null,
           'unicode' => true,
           'encoding' => 'UTF-8',
           'disccache' => false);

   /**
    * Objek TCPDF
    * @var TCPDF
    */
   private $tcPDFObj = null;

   /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false) {
      $this->componentName = str_ireplace(__CLASS__.'_', '', get_class($this));

      // jazykové nastavení
      require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
                      .'nonvve'.DIRECTORY_SEPARATOR."tcpdf".DIRECTORY_SEPARATOR."lang"
                      .DIRECTORY_SEPARATOR.Locales::getLang().".php";

      // nastavení pro šablony
//      require_once Template::faceDir().'config'.DIRECTORY_SEPARATOR."tcpdf.conf.php";

      /*
       * Konfigurace tcpdf
       * příklad lze nalézt v adresáři s knihovnou /lib/nonvve/tcpdf/
      */

      define("K_TCPDF_EXTERNAL_CONFIG", true);

      /**
       * header title
       */
      define ('VVE_PDF_HEADER_TITLE', VVE_WEB_NAME);

      /**
       * header description string
       */
      define ('VVE_PDF_HEADER_STRING', (string)new Url_Link());

      // donastavení do konfigu
      /**
       * Installation path (/var/www/tcpdf/).
       * By default it is automatically calculated but you can also set it as a fixed string to improve performances.
       */
//      define ('K_PATH_MAIN', AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
//              .'nonvve'.DIRECTORY_SEPARATOR."tcpdf".DIRECTORY_SEPARATOR);
      define ('K_PATH_MAIN', AppCore::getAppWebDir());

      /**
       * URL path to tcpdf installation folder (http://localhost/tcpdf/).
       * By default it is automatically calculated but you can also set it as a fixed string to improve performances.
       */
      define ('K_PATH_URL', Url_Link::getMainWebDir());

      /**
       * path for PDF fonts
       * use K_PATH_MAIN.'fonts/old/' for old non-UTF8 fonts
       */
      define ('K_PATH_FONTS', AppCore::getAppLibDir() . 'fonts'.DIRECTORY_SEPARATOR.'tcpdf'.DIRECTORY_SEPARATOR);

      /**
       * cache directory for temporary files (full path)
       */
      define ('K_PATH_CACHE', AppCore::getAppWebDir().'cache'.DIRECTORY_SEPARATOR);

      /**
       * cache directory for temporary files (url path)
       */
      define ('K_PATH_URL_CACHE', K_PATH_URL.'cache'.DIRECTORY_SEPARATOR);

      /**
       *images directory
       */
      define ('K_PATH_IMAGES', Template::faceDir().'images'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR);

      /**
       * blank image
       */
      define ('K_BLANK_IMAGE', K_PATH_IMAGES.'_blank.png');

      /**
       * height of cell repect font height
       */
      define('K_CELL_HEIGHT_RATIO', 1.25);

      /**
       * title magnification respect main font size
       */
      define('K_TITLE_MAGNIFICATION', 1.3);

      /**
       * reduction factor for small font
       */
      define('K_SMALL_RATIO', 2/3);
      

      // jádro
      require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
                      .'nonvve'.DIRECTORY_SEPARATOR."tcpdf".DIRECTORY_SEPARATOR."tcpdf.php";
      $this->setConfig('orientation', VVE_PDF_PAGE_ORIENTATION);
      $this->setConfig('unit', VVE_PDF_UNIT);
      $this->setConfig('format', VVE_PDF_PAGE_FORMAT);
   }

   /**
    * Metoda vytváří pdf objekt a vrací jej
    * @return TCPDF -- objekt pdf
    */
   public function createPdfObj() {
      $this->tcPDFObj = new TCPDF($this->getConfig("orientation"), $this->getConfig("unit"),
              $this->getConfig("format"), $this->getConfig("unicode"), $this->getConfig("encoding"),
              $this->getConfig("diskcache"));

      $this->tcPDFObj->SetCreator(VVE_PDF_CREATOR);

      $tcpdfLangSet = Array();

      // PAGE META DESCRIPTORS --------------------------------------
      $tcpdfLangSet['a_meta_charset'] = 'UTF-8';
      $tcpdfLangSet['w_page'] = $this->tr('strana');
      switch (Locales::getLang()){
         case 'cs':
            $tcpdfLangSet['a_meta_language'] = 'cs';
            $tcpdfLangSet['a_meta_dir'] = 'ltr';
            break;
         default:
            $tcpdfLangSet['a_meta_language'] = 'en';
            $tcpdfLangSet['a_meta_dir'] = 'ltr';
            break;
      }
      $this->tcPDFObj->setLanguageArray($tcpdfLangSet);
      
      // load aditional Fonts
//      $f1 = $this->tcPDFObj->addTTFfont(AppCore::getAppLibDir()."fonts/tcpdf/Arial.ttf", 'TrueTypeUnicode', "", 32);
//      $f2 = $this->tcPDFObj->addTTFfont(AppCore::getAppLibDir()."fonts/tcpdf/ArialBold.ttf", 'TrueTypeUnicode', "", 32);
//      $f3 = $this->tcPDFObj->addTTFfont(AppCore::getAppLibDir()."fonts/tcpdf/ArialBoldItalic.ttf", 'TrueTypeUnicode', "", 32);
//      $f4 = $this->tcPDFObj->addTTFfont(AppCore::getAppLibDir()."fonts/tcpdf/ArialItalic.ttf", 'TrueTypeUnicode', "", 32);
//      var_dump($f1, $f2, $f3, $f4);
      
      /**
       * @todo TOHLE dodělat !! nebo jestli to stačí
       */
      // set default monospaced font
      $this->tcPDFObj->SetDefaultMonospacedFont(VVE_PDF_FONT_MONOSPACED);
      //set margins
      $this->tcPDFObj->SetMargins(VVE_PDF_MARGIN_LEFT, VVE_PDF_MARGIN_TOP, VVE_PDF_MARGIN_RIGHT);
      //set auto page breaks
      $this->tcPDFObj->SetAutoPageBreak(true, VVE_PDF_MARGIN_BOTTOM);
      //set image scale factor
      $this->tcPDFObj->setImageScale(VVE_PDF_IMAGE_SCALE_RATIO);

      $this->tcPDFObj->setHeaderFont(Array(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN));
      $this->tcPDFObj->SetHeaderMargin(VVE_PDF_MARGIN_HEADER);
      $this->tcPDFObj->SetFont(VVE_PDF_FONT_NAME_MAIN);

      return $this->tcPDFObj;
   }

   /**
    * Metoda vrací pdf objekt
    * @return TCPDF -- objekt pdf
    */
   public function pdf() {
      if($this->tcPDFObj === null) {
         $this->createPdfObj();
      }
      return $this->tcPDFObj;
   }

   /**
    * Send the document to a given destination: string, local file or browser.
    * In the last case, the plug-in may be used (if present) or a download ("Save as" dialog box) may be forced.<br />
    * The method first calls Close() if necessary to terminate the document.
    * Alias for function Output
    * @param string $name The name of the file when saved. Note that special characters are removed and blanks characters are replaced with the underscore character.
    * @param string $dest Destination where to send the document. It can take one of the following values:<ul><li>I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.</li><li>D: send to the browser and force a file download with the name given by name.</li><li>F: save to a local file with the name given by name.</li><li>S: return the document as a string. name is ignored.</li></ul>
    * @access public
    * @since 1.0
    * @see Close()
    */
   public function flush($file = 'doc.pdf', $dest='I') {
      $this->pdf()->Output($file, $dest);
      exit();
   }

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init() {

   }

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart() {

   }

   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   public function mainController() {

   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {

   }
}
?>