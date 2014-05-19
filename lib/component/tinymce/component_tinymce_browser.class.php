<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of component_tinymce_rowser
 * @author cuba
 */
class Component_TinyMCE_Browser extends Component_TinyMCE {
   const DIR_HOME = 'home';
   const DIR_PUBLIC = 'public';
   
   const REQ_PATH = 'path';
   const REQ_NAME = 'name';

   const REQ_FILE = 'file';
   const REQ_NEWNAME = 'newname';
   const REQ_LIST_TYPE = 'type';

   const TYPE_DIR = 'dir';
   const TYPE_DOT = 'dot';
   const TYPE_FILE = 'file';
   const TYPE_IMAGE = 'flash';
   
   const LIST_TYPE_IMAGES = 'image';
   const LIST_TYPE_FILES = 'file';
   const LIST_TYPE_MEDIA = 'media';

   const ACL_FILE = 'acl';

   private static $mediaExtensions = array('swf','swc','qt','avi','mpg','mpeg','mp2','mp3','ogg','wav');

   private $allowdDirs = array();

   public function __construct($runOnly = false)
   {
      parent::__construct($runOnly);
      if(isset($_GET['allowDirs']) && is_array($_GET['allowDirs'])){
         $this->allowdDirs = $_GET['allowDirs'];
      }
   }
   
   public function browserController()
   {
      // inicializace adresářů
      /* home */
      $dir = substr(AppCore::getAppDataDir(), 0,-1).$this->getDir();
      $dir = new Filesystem_Dir($dir);
      $dir->checkDir();
      /* public */
      $dir = AppCore::getAppDataDir().self::DIR_PUBLIC;
      $dir = new Filesystem_Dir($dir);
      $dir->checkDir();
      unset ($dir);
   }
   
   public function browserView()
   {
      $this->template = new Template_Core();
      $this->template()->uploadLink = $this->link()->onlyAction('upload', 'php');
      
      $this->template()->linkC = $this->link();
      
      $this->template()->addTplFile('filebrowser/filebrowser.phtml');
      $this->template()->uploadForm = $this->createUploadForm();
      echo $this->template();flush();
   }

   public static function getUploaderFunction(Component_TinyMCE_Settings_Advanced $settings)
   {
      $browserUrl = Url_Request::getBaseWebDir().'/component/tinymce_browser/{CATID}/browser.php?t="+type+"'
      .'&allowDirs[]=' . implode('&allowDirs[]=', array_map('urlencode', $settings->getAllowedDirs())).
      '&forcedir='.urlencode($settings->getForceDir());
      
      $func = 'function vveTinyMCEFileBrowser (field_name, url, type, win) {
      var cmsURL = location.toString();    // script URL - use an absolute path!
      tinyMCE.activeEditor.windowManager.open({
      file : "'.$browserUrl.'",
      title : "Cube File Browser", width : 810, height : 505, resizable : "yes", inline : "yes",  close_previous : "no"
      }, {
      window : win,
      input : field_name,
      listType : type,
      cat : tinyMCE.activeEditor.getParam(\'cid\'),
      url:url });
      return false;
      }';
      return $func;
   }
   
   /*
    * Metody pro obsluhu filebrowseru
    */

   /**
    * Metoda vrátí adresáře ve formátu JSON
    */
   public function getItemsController()
   {
      $this->checkRights();

      $reqDir = $this->getDir();
      $this->template()->request = $reqDir;
      $dataDir = substr(AppCore::getAppDataDir(),0,-1);
      $currDirRealPath = $dataDir . str_replace(URL_SEPARATOR, DIRECTORY_SEPARATOR, $reqDir);

      if(!file_exists($currDirRealPath) || !is_dir($currDirRealPath)){
         throw new UnexpectedValueException(sprintf($this->tr('Adresář %s neexistuje'), '/'.VVE_DATA_DIR.$reqDir));
      }
      $dirIter = new DirectoryIterator($currDirRealPath);

      $items = array();
      foreach ($dirIter as $item) {
         if($item->getFilename() == '.' OR $item->isLink() // odkaz, kořen, aktulání dir, acl file
            OR ($item->getFilename() == '..' AND $reqDir == URL_SEPARATOR)
            OR $item->getFilename() == self::ACL_FILE)
         {
            continue;
         }
         // kontroly typů souborů podle listu


         // item default struct
         $it = array(
            'name' => null,
            'path' => null,
            'realpath' => null,
            'type' => 'file', // typ položky (dot, dir, file)
            'access' => array('read' => true, 'write' => false),
            'info' => array( // info
               'size' => 0, // velikost v bytech
               'modified' => null, // datum a čas modifikace
               'type' => null, // typ souboru nebo adresáře (image, flash, movie, doc, home, public, atd)
               'dimension' => array('w' => 0, 'h' => 0), // rozměry (obr a flash)
               'sizeFormated' => 0, // velikost v bytech
               ),
             // nové
            'itemclass' => null, // typ souboru nebo adresáře (image, flash, movie, doc, home, public, atd)
            'target' => null, // typ položky (dot, dir, file, image)
             
            );

         // base info
         // cesty
         $it['realpath'] = str_replace(array($dataDir,DIRECTORY_SEPARATOR), array(VVE_DATA_DIR, URL_SEPARATOR),$item->getRealPath());
         $it['path'] = str_replace(array(AppCore::getAppDataDir(), DIRECTORY_SEPARATOR), array(DIRECTORY_SEPARATOR,URL_SEPARATOR), $item->getPath().DIRECTORY_SEPARATOR);
         // name
         $it['name'] = $item->getFilename();
         $it['info']['size'] = filesize($item->getRealPath());
         $it['info']['sizeFormated'] = vve_create_size_str(filesize($item->getRealPath()));
         // access controll
         $it['access']['write'] = $this->isWritable($item->getRealPath());

         if($item->isDir() AND !$item->isDot()){
            $it['type'] = self::TYPE_DIR;
            $it['itemclass'] = 'dir';
            // home dir
            if($reqDir.$item->getFilename().URL_SEPARATOR == URL_SEPARATOR.self::DIR_HOME.URL_SEPARATOR
               OR $reqDir.$item->getFilename().URL_SEPARATOR == URL_SEPARATOR.self::DIR_HOME.URL_SEPARATOR.Auth::getUserName().URL_SEPARATOR){
               $it['info']['type'] = 'home';
               $it['itemclass'] = 'home';
            }
            // public dir
            if($reqDir.$item->getFilename().URL_SEPARATOR == URL_SEPARATOR.self::DIR_PUBLIC.URL_SEPARATOR){
               $it['info']['type'] = 'public';
               $it['itemclass'] = 'public';
            }
            $it['target'] = $it['path'].$item->getFilename().'/';
//            $size = 0;
//            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($item->getRealPath())) as $file){
//               if($file->getFileName() != ".."){
//                  $size+=$file->getSize();
//               }
//            }
//            $it['info']['size'] = $size;
//            $it['info']['sizeFormated'] = vve_create_size_str($size);
         } else if($item->isDot()){
            $it['type'] = self::TYPE_DOT;
            $it['target'] = dirname($it['path']);
            if(dirname($it['path']) != "/"){
               $it['target'] .= "/";
            }
            $it['itemclass'] = 'dot';
         }
         // file
         else {
            $it['type'] = self::TYPE_FILE;
            // typ listu
            if($_REQUEST['type'] == 'image' AND (($info = @getimagesize($item->getRealPath())) == false OR $info[2] == IMAGETYPE_SWF OR $info[2] == IMAGETYPE_SWC)){
               continue;
            } else if($_REQUEST['type'] == 'media' AND !in_array(pathinfo($item->getRealPath(), PATHINFO_EXTENSION), self::$mediaExtensions)){
               continue;
            }
            if(($info = @getimagesize($item->getRealPath())) != false){ // img + flash
               switch ($info[2]) {
                  case IMAGETYPE_SWF:
                  case IMAGETYPE_SWC:
                     $fType = 'flash';
                     break;
                  default:
                     $fType = 'image';
                     break;
               }
               $it['info']['dimension']['w'] = $info[0];
               $it['info']['dimension']['h'] = $info[1];
            } else { // other
               $ext = strtolower(pathinfo($item->getRealPath(), PATHINFO_EXTENSION));
               switch ($ext) {
                  case 'doc':case 'docx':case 'odt':case 'ott':case 'rtf':
                     $fType = 'doc';
                     break;
                  case 'txt':
                     $fType = 'txt';
                     break;
                  case 'pdf':
                     $fType = 'pdf';
                     break;
                  case 'xml':case 'html':case 'phtml':case 'htm':
                     $fType = 'xml';
                     break;
                  case 'zip':case 'rar':case '7z':case 'gz':case 'tar':case 'bz2':
                     $fType = 'archive';
                     break;
                  case 'xls':case 'xlsx':case 'xlt':case 'ods':case 'ots':case 'csv':
                     $fType = 'xls';
                     break;
                  case 'js':
                     $fType = 'js';
                     break;
                  case 'mov':case 'avi':case 'wmv':case 'mpg':case 'mp2':case 'mpv':case 'mpeg':case 'ogg':case 'flv':
                     $fType = 'video';
                     break;
                  case 'mp3':case 'wav':case 'flac':
                     $fType = 'audio';
                     break;
               }
            }
            $it['itemclass'] = $fType;
            $it['info']['type'] = $fType;
         }

         $items[] = $it;
      }
      $this->template()->items = $this->sort($items);
      $this->template()->current = $reqDir;
      $this->template()->access = array('write' => $this->isWritable($currDirRealPath), 'read' => true);
   }

   public function getDirsController()
   {
      // base vars
      $dirPublic = AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_PUBLIC.DIRECTORY_SEPARATOR;
      $dirHome = AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_HOME.DIRECTORY_SEPARATOR.Auth::getUserName().DIRECTORY_SEPARATOR;
   
      // iterate over public dir only if admin
      if(Auth::isAdmin()){
         if(!empty($this->allowedDirs)){
            
            $allowedDirs = array();
            
            foreach ($this->allowedDirs as $dir) {
               $allowedDirs[] = $dir;
               $dir = realpath( AppCore::getAppDataDir().str_replace('/', DIRECTORY_SEPARATOR, $dir));
               $ite = new RecursiveDirectoryIterator($dir );
               foreach (new RecursiveIteratorIterator($ite, RecursiveIteratorIterator::SELF_FIRST)
                     as $name => $item) {
                  if($item->isDir() && $item->isWritable()
                        && ( strpos($item->getPathname(), "small") === false
                              && strpos($item->getPathname(), "medium") === false ) ){
                     array_push($allowedDirs, $this->encodeDir($item->getPathname()));
                  }
               }
            }
            $this->template()->dirsAllowed = $allowedDirs;
         }
         
         $publicDirs = array("/".Component_TinyMCE_Browser::DIR_PUBLIC."/");
         if(!is_dir($dirPublic)){// create dir if not exist;
            @mkdir( $dirPublic );
         }
         $ite = new RecursiveDirectoryIterator($dirPublic );
         foreach (new RecursiveIteratorIterator($ite, RecursiveIteratorIterator::SELF_FIRST) 
            as $item) {
            // @todo vymyslet jak filtrovat small a medium, protože to jsou adresáře galerií
            if($item->isDir() && $item->isWritable() && $item->getFilename() != "." && $item->getFilename() != ".."
               && ( strpos($item->getPathname(), "small") === false
               && strpos($item->getPathname(), "medium") === false ) ){
               array_push($publicDirs, $this->encodeDir($item->getPathname()));
            }
         }
         // assign to output
         $this->template()->dirsPublic = $publicDirs ;
      }
      
      // interate over user dir
      $homeDirs = array("/".Component_TinyMCE_Browser::DIR_HOME."/".Auth::getUserName()."/");
      if(!is_dir($dirHome)){// create dir if not exist;
         @mkdir( $dirHome ); 
      }
      $ite = new RecursiveDirectoryIterator($dirHome );
      foreach (new RecursiveIteratorIterator($ite, RecursiveIteratorIterator::SELF_FIRST) 
         as $item) {
         // @todo vymyslet jak filtrovat small a medium, protože to jsou adresáře galerií
         if($item->isDir() && $item->isWritable() && $item->getFilename() != "." && $item->getFilename() != ".."
              && ( strpos($item->getPathname(), "small") === false
            && strpos($item->getPathname(), "medium") === false ) ){
            array_push($homeDirs, $this->encodeDir($item->getPathname()));
         }
      }
      // assign to output
      $this->template()->dirsHome = $homeDirs ;
   }
   
   protected function encodeDir($dir)
   {
      $dir = str_replace(
         array(
            AppCore::getAppDataDir(),
            DIRECTORY_SEPARATOR
         ), 
         array(
            "/",
            "/"
         ), 
         $dir );
      
      if(substr($dir, -1, 1) != "/"){
         $dir .= "/";
      }
      
      return $dir;
   }
   
   /*
    * Převede adresář na reálnou cestu
    */
   protected function decodeDir($dir)
   {
      $dir = preg_replace(
         array(
            "/\./",
            "/\/{2,}/",
            "/\//"
         ), 
         array(
            "",
            "/",
            DIRECTORY_SEPARATOR
         ), 
         $dir );
      if(substr($dir, -1, 1) != DIRECTORY_SEPARATOR){
         $dir .= DIRECTORY_SEPARATOR;
      }
      if(substr($dir, 0, 1) == DIRECTORY_SEPARATOR){
         $dir = substr($dir, 1);
      }
      
      return AppCore::getAppDataDir().$dir;
   }
   
   
   /**
    * Vrací požadovanou cestu předanou v požadavku (např: /home/admin/fotky/)
    * @return string
    */
   private function getDir()
   {
      if(!isset($_REQUEST[self::REQ_PATH]) || $_REQUEST[self::REQ_PATH] == self::DIR_HOME || $_REQUEST[self::REQ_PATH] == null){ // domácí adresář
         $dirName = URL_SEPARATOR.self::DIR_HOME.URL_SEPARATOR.Auth::getUserName().URL_SEPARATOR;
      } else { // ostatní adresáře
         $dirName = preg_replace('/[^\/]+\/\.\.\//i', '', $_REQUEST[self::REQ_PATH]); // check chars
         if($dirName == '..'.URL_SEPARATOR) $dirName = URL_SEPARATOR; // if is root remove parents
      }
      return $dirName;
   }

   /**
    * Testuje jestli je cesta zapisovatelná (např: /var/www/localhost/data/)
    * @param <type> $dir real path to dir
    * @return <type>
    */
   private function isWritable($item)
   {
      return self::hasWritableRights($item);
   }
   
   public static function hasWritableRights($item)
   {
      if(file_exists($item) && !is_writable($item)){
         return false;
      }
      // kontrola home directory
      if(strpos($item, AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_HOME
              .DIRECTORY_SEPARATOR.Auth::getUserName().DIRECTORY_SEPARATOR) === 0){
         return true; 
      }
      if(Auth::isAdmin() && (
          strpos($item, AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_PUBLIC.DIRECTORY_SEPARATOR) === 0
          || strpos($item, AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_HOME.DIRECTORY_SEPARATOR) === 0
          )){
         return true;
      }
      return false;
   }


   private function chekWritableDir($dir)
   {
      if(!$this->isWritable($dir))
         throw new Auth_Exception ($this->tr('Nemáte dostatečná práva pro úpravu této položky'));
   }

   private function checkRights()
   {
      if(!Auth::isLogin())
      {
         throw new Auth_Exception($this->tr('Nemáte dostatečná práva k zápisu. Asi jste byl odhlášen'));
      }
   }

   private function sort($items)
   {
//      function srt($a, $b){
//         $typeA = $a["type"];
//         $typeB = $b["type"];
//         if($typeA == 'dot') $typeA = 'dir';
//         if($typeB == 'dot') $typeB = 'dir';
//         if(($typeA == "dir" OR $typeB == "dir") AND $typeA != $typeB){
//            if($typeA == "dir"){return -1;} else {return 1;}
//         } else {
//            return strcasecmp($a["name"], $b["name"]);
//         }
//      }

//      usort($items, 'srt');
      usort($items, create_function('$a, $b', '$typeA = $a["type"];
         $typeB = $b["type"];
         if($typeA == "dot") $typeA = "dir";
         if($typeB == "dot") $typeB = "dir";
         if(($typeA == "dir" OR $typeB == "dir") AND $typeA != $typeB){
            if($typeA == "dir"){return -1;} else {return 1;}
         } else {
            return strcasecmp($a["name"], $b["name"]);
         }'));

      return $items;
   }

   public function uploadController()
   {
      $this->checkRights();
      $dir = substr(AppCore::getAppDataDir(),0,-1).$this->getDir();
      $this->chekWritableDir($dir);
      $form = $this->createUploadForm($dir);
      if($form->isValid()) {
         $files = $form->file->getValues();
         $dir = $this->getUploadPath($form->path->getValues());
         
         // kontrola zápisu
         
         foreach ($files as $file) {
            $f = new File($file);
            $f->move($dir);
            $this->infoMsg()->addMessage(sprintf($this->tr('Soubor "%s" byl nahrán'), $file['name']));
         }
         if($form->iframe->getValues() == 1){
            AppCore::getInfoMessages()->changeSaveStatus(false);
            echo '<script type="text/javascript">';
            echo 'document.domain = \''.Url_Request::getDomain().'\';';
            echo 'info = '.json_encode(AppCore::getInfoMessages()->getMessages()).';';
            echo 'error = '.json_encode(AppCore::getUserErrors()->getMessages()).';';
            echo '</script>';
            die;
         }
      }
      
   }
   
   /**
    * Metoda pro vytvoření formuláře pro upload
    * @return Form 
    * @todo přidat validaci ostatních souborů? (spíše zakázat php, html, js, atd.)
    */
   private function createUploadForm($datadir = null)
   {
      $this->checkRights();
      if($datadir == null){
         $datadir = $this->getDir();
      }
      $form = new Form('upload_');
      $form->setAction($this->link()->onlyAction('upload', 'php'));
      
      $eIframe = new Form_Element_Hidden('iframe', 'iframe');
      $eIframe->setValues('1');
      $form->addElement($eIframe);
      
      $ePath = new Form_Element_Hidden('path', 'path');
      $form->addElement($ePath);
      
      $eType = new Form_Element_Hidden('type', 'type');
      $eType->setValues(isset($_GET['t']) ? $_GET['t'] : Component_TinyMCE_Browser::LIST_TYPE_FILES);
      $form->addElement($eType);
      
      $file = new Form_Element_File('file', $this->tr('Soubor'));
      $file->setMultiple(true);
      $file->addValidation(new Form_Validator_NotEmpty());

      if($eType->getValues() == self::LIST_TYPE_IMAGES) {
         $validOnlyImage = new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tga', 'wmf'));
         $file->addValidation($validOnlyImage);
      } else if($eType->getValues() == self::LIST_TYPE_MEDIA) {
         $validOnlyImage = new Form_Validator_FileExtension(array(
            'swf', // falsh
            'mp4','m4v', 'ogv', 'mov' , 'flv', 'rm', 'qt', 'avi', // video
            'mp3', 'ogg', 'wma', 'wav' // audio
            ));
         $file->addValidation($validOnlyImage);
      } else {
      }
      $form->addElement($file);
      $submit = new Form_Element_Submit('send');
      $form->addElement($submit);
      return $form;
   }
   
   /**
    * Vrací adresář pro upload souborů
    * @param string $path
    * @return string
    */
   protected function getUploadPath($path = false)
   {
      if(!$path){
         $path = self::DIR_PUBLIC."/";
      }
      
      // tady dodělat kontroly adresáře (..;//; a podobně)
      $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
      return AppCore::getAppDataDir().$path;
   }

   public function createDirController()
   {
      $this->checkRights();
      if($_POST['name'] == null || $_POST['item'] == null){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      $item = new ItemInfo($_POST['item'].vve_cr_safe_file_name($_POST['name']));
      if(!self::hasWritableRights($item->getDir())){
         throw new Auth_Exception($this->tr('Pro vytvořerní adresáře nemáte dostatečná opravnění'));
      }
      mkdir($item->getRealPath());
      $this->infoMsg()->addMessage(sprintf($this->tr('Složka %s byla vytvořena'), $item->getName()));
   }

   public function deleteController()
   {
      $this->checkRights();
      $item = new ItemInfo($_POST['item']);
      if(!$item->exist()){
         throw new Exception(sprintf($this->tr("Objekt %s neexistuje"), $item->getName()));
      }
      // kontrola oprávnění
      if(!$item->hasWritableRight()){
         throw new Exception(sprintf($this->tr("Objekt %s nelze smazat"), $item->getName()));
      }
      
      $this->template()->realpath = $item->getRealPath();
      $this->template()->name = $item->getName();
      $this->template()->dir = $item->getDir();
      $this->template()->isdir = $item->isDir();
      $this->template()->hasrights = $item->hasWritableRight();
      
      // smazání
      if($item->isDir()){
         FS_Dir::deleteStatic($item->getRealPath());
      } else {
         @unlink($item->getRealPath());
      }
      $this->infoMsg()->addMessage(sprintf($this->tr('Objekt "%s" byl smazán '), $item->getName()));
   }
   // kontroler pro přejmenování
   public function renameController()
   {
      $this->checkRights();
      if($_POST['name'] == null || $_POST['item'] == null){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      $item = new ItemInfo($_POST['item']);
      $name = vve_cr_safe_file_name($_POST['name']);
      // kontrola oprávnění
      if(!$item->hasWritableRight()){
         throw new Exception(sprintf($this->tr("Objekt %s nelze přejmenovat"), $item->getName()));
      }
      
      $file = new File($item->getRealPath());
      $file->rename($name);
      
      $this->template()->name = $name;
      $this->template()->path = $item->getRealPath();
      
      $this->infoMsg()->addMessage(sprintf($this->tr('Objekt %s byl přejmenován na %s'), $item->getName(), $name));
   }

   public function copyController()
   {
      $this->checkRights();
      if($_POST['item'] == null || $_POST['target'] == null){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      
      $item = new ItemInfo($_POST['item']);
      $target = new ItemInfo($_POST['target']);
      if(!self::hasWritableRights($target->getRealPath().DIRECTORY_SEPARATOR.$item->getName())){
         throw new Exception(sprintf($this->tr("Nemáte právo zápisu do objektu %s"), $target->getName()));
      }
      
      $this->template()->item = $item->getRealPath();
      $this->template()->target = $target->getRealPath();
      
      if($item->isDir()){
         $dir = new FS_Dir($item->getRealPath());
         $dir->copy($target->getRealPath());
      } else {
         $file = new File($item->getRealPath());
         $file->copy($target->getRealPath());
      }
      $this->infoMsg()->addMessage(sprintf($this->tr('Objekt %s byl kopírován do %s'), $item->getName(), $target->getName()));
   }

   public function moveController()
   {
      $this->checkRights();
      if($_POST['item'] == null || $_POST['target'] == null){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      
      $item = new ItemInfo($_POST['item']);
      $target = new ItemInfo($_POST['target']);
      
      if(!self::hasWritableRights($target->getRealPath().DIRECTORY_SEPARATOR.$item->getName())){
         throw new Exception(sprintf($this->tr("Nemáte právo zápisu do objektu %s"), $target->getName()));
      }
      if(!$item->hasWritableRight()){
         throw new Exception(sprintf($this->tr("Nemáte právo mazání objektu %s"), $item->getName()));
      }
      
      $this->template()->item = $item->getRealPath();
      $this->template()->target = $target->getRealPath();
      
      if($item->isDir()){
         $dir = new FS_Dir($item->getRealPath());
         $dir->copy($target->getRealPath());
         $dir->delete();
      } else {
         $file = new File($item->getRealPath());
         $file->move($target->getRealPath());
//         copy($item->getRealPath(), $target->getRealPath());
//         unlink($item->getRealPath());
      }
      $this->infoMsg()->addMessage(sprintf($this->tr('Objekt %s byl přesunut do %s'), $item->getName(), $target->getName()));
   }
   
   
   /* Images function */
   /**
    * Create new resized image
    */
   public function imageResizeController()
   {
      $this->checkRights();
      if(!isset($_REQUEST['item']) || !isset($_REQUEST['width']) || !isset($_REQUEST['height']) || !isset($_REQUEST['ratio']) || !isset($_REQUEST['crop'])){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      $item = new ItemInfo($_REQUEST['item']);
      if(!$item->exist()){
         throw new UnexpectedValueException($this->tr('Obrázek neexistuje'));
      }
      if(!self::hasWritableRights($item->getDir())){
         throw new UnauthorizedAccessException($this->tr('Nemáte oprávnění zápisu do této složky'));
      }
      
      $width = (int)$_REQUEST['width'];
      $height = (int)$_POST['height'];
      $crop = (bool)$_POST['crop'];
      $ration = (bool)$_POST['ratio'];
      $resizeType = File_Image_Base::RESIZE_AUTO;
      if($crop){
         $resizeType = File_Image_Base::RESIZE_CROP;
      } else {
         if(!$ration){
            $resizeType = File_Image_Base::RESIZE_EXACT;
         }
      }
      
      $image = new File_Image($item->getRealPath());
      if(isset($_REQUEST['createNew']) && (bool)$_REQUEST['createNew']){
         $newFileName = $image->getBaseName().'_'.$width.'x'.$height.($crop ? 'c' : '').($ration ? '' : 'r').'.'.$image->getExtension();
         $image = $image->copy($item->getDir(), true, $newFileName);
      }
      $image->getData()->resize($width, $height, $resizeType);
      $image->save();
      
      $this->infoMsg()->addMessage(sprintf('Obrázek byl upraven.'));
   }

   /**
    * rotate img
    */
   public function imageRotateController()
   {
      $this->checkRights();
      if(!isset($_REQUEST['item']) || !isset($_REQUEST['degree'])){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      $item = new ItemInfo($_REQUEST['item']);
      if(!$item->exist()){
         throw new UnexpectedValueException($this->tr('Obrázek neexistuje'));
      }
      if(!self::hasWritableRights($item->getDir())){
         throw new UnauthorizedAccessException($this->tr('Nemáte oprávnění zápisu do této složky'));
      }
      
      $degree = (int)$_REQUEST['degree'];
      $image = new File_Image($item->getRealPath());
      if(isset($_REQUEST['createNew']) && (bool)$_REQUEST['createNew']){
         $newFileName = $image->getBaseName().'_'.$degree.'.'.$image->getExtension();
         $image = $image->copy($item->getDir(), true, $newFileName);
      }
      $image->getData()->rotate($degree);
      $image->save();
      
      $this->infoMsg()->addMessage(sprintf('Obrázek byl otočen.'));
   }
   
   public function imageFlipController()
   {
      $this->checkRights();
      if(!isset($_REQUEST['item']) || !isset($_REQUEST['flip'])){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      $item = new ItemInfo($_REQUEST['item']);
      if(!$item->exist()){
         throw new UnexpectedValueException($this->tr('Obrázek neexistuje'));
      }
      if(!self::hasWritableRights($item->getDir())){
         throw new UnauthorizedAccessException($this->tr('Nemáte oprávnění zápisu do této složky'));
      }
      
      $flip = $_REQUEST['flip'] == IMG_FLIP_HORIZONTAL ? 'fh' : 'fv';
      $image = new File_Image($item->getRealPath());
      if(isset($_REQUEST['createNew']) && (bool)$_REQUEST['createNew']){
         $newFileName = $image->getBaseName().'_'.$flip.'.'.$image->getExtension();
         $image = $image->copy($item->getDir(), true, $newFileName);
      }
      $image->getData()->flip((int)$_REQUEST['flip']);
      $image->save();
      
      $this->infoMsg()->addMessage(sprintf('Obrázek byl převrácen.'));
   }
   
   public function imageWatermarkController()
   {
      $this->checkRights();
      if(!isset($_REQUEST['item']) || !isset($_REQUEST['text']) || !isset($_REQUEST['color'])){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      $item = new ItemInfo($_REQUEST['item']);
      if(!$item->exist()){
         throw new UnexpectedValueException($this->tr('Obrázek neexistuje'));
      }
      if(!self::hasWritableRights($item->getDir())){
         throw new UnauthorizedAccessException($this->tr('Nemáte oprávnění zápisu do této složky'));
      }
      
      $image = new File_Image($item->getRealPath());
      if(isset($_REQUEST['createNew']) && (bool)$_REQUEST['createNew']){
         $newFileName = $image->getBaseName().'_wtr.'.$image->getExtension();
         $image = $image->copy($item->getDir(), true, $newFileName);
      }
      $color = str_replace('#', '', $_REQUEST['color']);
      $color = substr($color, 0, 6);
      $colorBg = null;
      if(isset($_REQUEST['colorBg']) && $_REQUEST['colorBg'] != null){
         $colorBg = str_replace('#', '', $_REQUEST['colorBg']);
         $colorBg = substr($colorBg, 0, 6);
      }
      $params = array(
          'color' => $color,
          'bgColor' => $colorBg,
          'horizontal' => isset($_REQUEST['posX']) ? $_REQUEST['posX'] : 'right',
          'vertical' => isset($_REQUEST['posY']) ? $_REQUEST['posY'] : 'bottom',
          'alpha' => $colorBg == null ? 1 : 0.5,
      );
      
      $image->getData()->textWatermark($_REQUEST['text'], $params);
      $image->save();
      
      $this->infoMsg()->addMessage(sprintf('Obrázek byl převrácen.'));
   }
   
   /**
    * grayscale img
    */
   public function imageFilterController()
   {
      $this->checkRights();
      if(!isset($_REQUEST['item']) || !isset($_REQUEST['filter'])){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny parametry'));
      }
      $item = new ItemInfo($_REQUEST['item']);
      if(!$item->exist()){
         throw new UnexpectedValueException($this->tr('Obrázek neexistuje'));
      }
      if(!self::hasWritableRights($item->getDir())){
         throw new UnauthorizedAccessException($this->tr('Nemáte oprávnění zápisu do této složky'));
      }
      
      $image = new File_Image($item->getRealPath());
      $fileSufix = '_filter';
      
      if(is_numeric($_REQUEST['filter'])){
         $args = array((int)$_REQUEST['filter']);
         switch ($_REQUEST['filter']) {
            case IMG_FILTER_BRIGHTNESS:
               $args[] = (int)$_REQUEST['arg'];
               $fileSufix = 'brig';
               break;
            case IMG_FILTER_CONTRAST:
               $args[] = (int)$_REQUEST['arg'];
               $fileSufix = 'contrast';
               break;
            case IMG_FILTER_EDGEDETECT:
               $fileSufix = 'edge';
               break;
            case IMG_FILTER_GAUSSIAN_BLUR:
               $fileSufix = 'gblur';
               break;
            case IMG_FILTER_GRAYSCALE:
               $fileSufix = 'gray';
               break;
            case IMG_FILTER_NEGATE:
               $fileSufix = 'negate';
               break;
            case IMG_FILTER_PIXELATE:
               $args[] = (int)$_REQUEST['arg'];
               $fileSufix = 'pixelate';
               break;
            case IMG_FILTER_SELECTIVE_BLUR:
               $fileSufix = 'sblur';
               break;
            default:
               throw new InvalidArgumentException($this->tr('Nepodporovaný filtr'));
         }
         $image = new File_Image($item->getRealPath());
         if(isset($_REQUEST['createNew']) && (bool)$_REQUEST['createNew']){
            $newFileName = $image->getBaseName().'_'.$fileSufix.'.'.$image->getExtension();
            $image = $image->copy($item->getDir(), true, $newFileName);
         }
         call_user_func_array(array($image->getData(), 'filter'), $args);
         $image->save();
         
      } else {
         switch ($_REQUEST['filter']) {
            case 'sepia':
               if(isset($_REQUEST['createNew']) && (bool)$_REQUEST['createNew']){
                  $newFileName = $image->getBaseName().'_sepia_'.(int)$_REQUEST['arg'].'_'.(int)$_REQUEST['arg'].'.'.$image->getExtension();
                  $image = $image->copy($item->getDir(), true, $newFileName);
               }
               
               $image->getData()
                   ->filter(IMG_FILTER_GRAYSCALE)
                   ->filter(IMG_FILTER_COLORIZE, (int)$_REQUEST['arg'], (int)$_REQUEST['arg'], 0);
               $image->save();
               break;
            default :
               throw new InvalidArgumentException($this->tr('Nepodporovaný filtr'));
         }
      }
      
      $this->infoMsg()->addMessage(sprintf('Filtr by aplikován.'));
   }

   public function createSystemImagesController()
   {
      $this->checkRights();
      $dir = $dirMed = $dirSmall = substr(AppCore::getAppDataDir(), 0,-1).$this->getDir();
      $this->chekWritableDir($dir);

      foreach ($_POST[self::REQ_FILE] as $file) {
         $fInfo = pathinfo($dir.$file);
         $fileName = $fInfo['filename'].'_small.'.$fInfo['extension'];
         $fileNameMed = $fInfo['filename'].'_medium.'.$fInfo['extension'];

         $dirMed = $dirSmall = $dir;
         try {
            if ($_POST['createdirs'] == 'true') {
               $dirSmall = $dir.'small' . DIRECTORY_SEPARATOR;
               $fileName = $fInfo['basename'];
               $d = new Filesystem_Dir($dirSmall);
               $d->checkDir();
               $fileNameMed = $fInfo['basename'];
               $dirMed = $dir.'medium' . DIRECTORY_SEPARATOR;
               $d = new Filesystem_Dir($dirMed);
               $d->checkDir();
               unset($d);
            }
            $image = new Filesystem_File_Image($file, $dir);
            $crop = true;
            if (defined('VVE_IMAGE_THUMB_CROP')) $crop = VVE_IMAGE_THUMB_CROP; // Cube CMS 6.4r5 or higer
            $image->saveAs($dirSmall, VVE_IMAGE_THUMB_W, VVE_IMAGE_THUMB_H, $crop, $fileName);
            // only if is bigger
//            if ($image->getOriginalWidth() >= VVE_DEFAULT_PHOTO_W OR $image->getOriginalHeight() >= VVE_DEFAULT_PHOTO_H) {
               $image->saveAs($dirMed, VVE_DEFAULT_PHOTO_W, VVE_DEFAULT_PHOTO_H, false, $fileNameMed);
//            }
         } catch (UnexpectedValueException $exc) {
            $this->errMsg()->addMessage(sprintf('Obrázek %s se nepodařilo vytvořit.', $file));
         }
         unset ($image);
      }
      sleep(1);
      $this->infoMsg()->addMessage(sprintf('Obrázky byly vytvořeny.'));
   }

   

   public function getUsersController()
   {
      $dir = substr(AppCore::getAppDataDir(), 0,-1).$this->getDir().  addslashes($_POST[self::REQ_NAME].DIRECTORY_SEPARATOR);
      $users = array();
      if(file_exists($dir.self::ACL_FILE)){
         $users = explode(';', file_get_contents($dir.self::ACL_FILE));
      }
      $model = new Model_Users();
      $this->template()->users = array();
      foreach ($model->records() as $user) {//$user->{Model_Users::COLUMN_ID} == Auth::getUserId() OR 
         if($user->{Model_Users::COLUMN_ID_GROUP} == VVE_DEFAULT_ID_GROUP) continue;
         $this->template()->users[] = array('name' => $user->{Model_Users::COLUMN_USERNAME}.' "'.$user->{Model_Users::COLUMN_NAME}.' '.$user->{Model_Users::COLUMN_SURNAME}.'"',
            'id' => $user->{Model_Users::COLUMN_ID}, 'selected' => in_array($user->{Model_Users::COLUMN_ID}, $users));
      }
   }

   public function storeDirPermsController()
   {
      $this->checkRights();
      $dir = substr(AppCore::getAppDataDir(), 0,-1).$this->getDir().addslashes($_POST[self::REQ_NAME]);
      $this->chekWritableDir(substr(AppCore::getAppDataDir(), 0,-1).$this->getDir());
      if(!is_dir($dir))
         throw new UnexpectedValueException($this->tr('Nebyl zadán korektní adresář.'));

      if($_POST['users'] != 'null' && is_array($_POST['users'])){
         $user = implode(';', $_POST['users']);
         file_put_contents($dir.DIRECTORY_SEPARATOR.self::ACL_FILE, $user);
      } else {
         $file = new Filesystem_File(self::ACL_FILE, $dir);
         if($file->exist()) $file->delete();
      }
      $this->infoMsg()->addMessage($this->tr('Práva byla nastavena'));

   }
}

class ItemInfo extends Object {
   private $item;

   public function __construct($item)
   {
      $this->item = $item;
   }
   
   public function getRealPath()
   {
      $prefix = (strpos($this->item, 'data') === 0 ? AppCore::getAppWebDir() : AppCore::getAppDataDir());
      $path = $this->normalizePath($prefix.str_replace(array('/'), array(DIRECTORY_SEPARATOR), $this->item));
      return $path;
   }
   
   public function getDir()
   {
      $item = $this->getRealPath();
      return pathinfo($item, PATHINFO_DIRNAME).DIRECTORY_SEPARATOR;
   }
   
   public function getName()
   {
      return pathinfo($this->item, PATHINFO_BASENAME);
   }
   
   public function isWritable()
   {
      return is_writable($this->getRealPath());
   }
   
   public function isDir()
   {
      return is_dir($this->getRealPath());
   }
   
   public function hasWritableRight()
   {
      return Component_TinyMCE_Browser::hasWritableRights($this->getRealPath());
   }
   
   public function exist()
   {
      return file_exists($this->getRealPath());
   }
   
   private function normalizePath($path) {
      return array_reduce(explode('/', $path), create_function('$a, $b', '
         if($a === 0) $a = "/";

         if($b === "" || $b === ".")
             return $a;

         if($b === "..")
             return dirname($a);

         return preg_replace("/\/+/", DIRECTORY_SEPARATOR, "$a/$b");
     '), 0);
}

}
