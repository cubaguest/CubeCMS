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
      echo $this->template();flush();
//      exit();
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
      title : "Cube File Browser", width : 750, height : 500, resizable : "yes", inline : "yes",  close_previous : "no"
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
            'type' => 'file', // typ položky (dot, dir, file, image)
            'access' => array('read' => true, 'write' => false),
            'info' => array( // info
               'size' => 0, // velikost v bytech
               'modified' => null, // datum a čas modifikace
               'type' => null, // typ souboru nebo adresáře (image, flash, movie, doc, home, public, atd)
               'dimension' => array('w' => 0, 'h' => 0) // rozměry (obr a flash)
               )
            );

         // base info
         // cesty
         $it['realpath'] = str_replace(array($dataDir,DIRECTORY_SEPARATOR), array(VVE_DATA_DIR, URL_SEPARATOR),$item->getRealPath());
         $it['path'] = str_replace(array(AppCore::getAppDataDir(), DIRECTORY_SEPARATOR), array(DIRECTORY_SEPARATOR,URL_SEPARATOR), $item->getPath().DIRECTORY_SEPARATOR);
         // name
         $it['name'] = $item->getFilename();
         $it['info']['size'] = filesize($item->getRealPath());
         // access controll
         $it['access']['write'] = $this->isWritable($item->getRealPath());

         if($item->isDir() AND !$item->isDot()){
            $it['type'] = self::TYPE_DIR;
            // home dir
            if($reqDir.$item->getFilename().URL_SEPARATOR == URL_SEPARATOR.self::DIR_HOME.URL_SEPARATOR
               OR $reqDir.$item->getFilename().URL_SEPARATOR == URL_SEPARATOR.self::DIR_HOME.URL_SEPARATOR.Auth::getUserName().URL_SEPARATOR){
               $it['info']['type'] = 'home';
            }
            // public dir
            if($reqDir.$item->getFilename().URL_SEPARATOR == URL_SEPARATOR.self::DIR_PUBLIC.URL_SEPARATOR){
               $it['info']['type'] = 'public';
            }
            
         } else if($item->isDot()){
            $it['type'] = self::TYPE_DOT;
         }
         // file
         else {
            $it['type'] = self::TYPE_FILE;
            // typ listu
            if($_POST['type'] == 'image' AND (($info = @getimagesize($item->getRealPath())) == false OR $info[2] == IMAGETYPE_SWF OR $info[2] == IMAGETYPE_SWC)){
               continue;
            } else if($_POST['type'] == 'media' AND !in_array(pathinfo($item->getRealPath(), PATHINFO_EXTENSION), self::$mediaExtensions)){
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
            $it['info']['type'] = $fType;
         }

         $items[] = $it;
      }
      $this->template()->items = $this->sort($items);
      $this->template()->current = $reqDir;
      $this->template()->access = array('write' => $this->isWritable($currDirRealPath), 'read' => true);
   }

   /**
    * Vrací požadovanou cestu předanou v požadavku (např: /home/admin/fotky/)
    * @return string
    */
   private function getDir()
   {
      if(!isset($_POST[self::REQ_PATH]) || $_POST[self::REQ_PATH] == self::DIR_HOME || $_POST[self::REQ_PATH] == null){ // domácí adresář
         $dirName = URL_SEPARATOR.self::DIR_HOME.URL_SEPARATOR.Auth::getUserName().URL_SEPARATOR;
      } else { // ostatní adresáře
         $dirName = preg_replace('/[^\/]+\/\.\.\//i', '', $_POST[self::REQ_PATH]); // check chars
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
      $w = is_writable($item);
      // kontrola home directory
      if($w && preg_match('/^'.preg_quote(AppCore::getAppDataDir().self::DIR_HOME.DIRECTORY_SEPARATOR
            .Auth::getUserName().DIRECTORY_SEPARATOR,'/')."/",$item)){
         return true; 
      }
      
      if(Auth::isAdmin()){
         // kontrola public
         if($w && preg_match('/^'.preg_quote(AppCore::getAppDataDir().self::DIR_PUBLIC.DIRECTORY_SEPARATOR,'/')
               ."/", $item)){
            return true;
         } 
         // allowed dirs
         foreach ($this->allowdDirs as $aDir) {
            $aDir = str_replace('/', DIRECTORY_SEPARATOR, $aDir);
            if($w && preg_match('/^'.preg_quote(realpath(AppCore::getAppDataDir().$aDir),'/')."/", $item)){
               return true;
            } 
         }
      }
      
      // kontrola podle práv
      $aclf = pathinfo($item, PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.self::ACL_FILE;
      // in dir with ACL
      if(is_dir($item) && file_exists($aclf) && in_array(Auth::getUserId(), explode(';',file_get_contents($aclf))) ){
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
         $this->infoMsg()->addMessage($this->tr('Soubor byl nahrán'));
      }
   }
   
   /**
    * Metoda pro vytvoření formuláře pro upload
    * @return Form 
    * @todo přidat validaci ostatních souborů? (spíše zakázat php, html, js, atd.)
    */
   private function createUploadForm($datadir)
   {
      $form = new Form('upload_');
      
      $file = new Form_Element_File('file');
      $file->addValidation(new Form_Validator_NotEmpty());

      if($_POST[self::REQ_LIST_TYPE] == self::LIST_TYPE_IMAGES) {
         $validOnlyImage = new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tga', 'wmf'));
         $file->addValidation($validOnlyImage);
      } else if($_POST[self::REQ_LIST_TYPE] == self::LIST_TYPE_MEDIA) {
         $validOnlyImage = new Form_Validator_FileExtension(array(
            'swf', // falsh
            'mp4','m4v', 'ogv', 'mov' , 'flv', 'rm', 'qt', 'avi', // video
            'mp3', 'ogg', 'wma', 'wav' // audio
            ));
         $file->addValidation($validOnlyImage);
      } else {
      }
      $file->setUploadDir($datadir);
      $form->addElement($file);
      $submit = new Form_Element_Submit('send');
      $form->addElement($submit);
      return $form;
   }

   public function createDirController()
   {
      $this->checkRights();
      $dir = substr(AppCore::getAppDataDir(),0,-1).$this->getDir();
      $this->chekWritableDir($dir);
      $newDir = vve_cr_safe_file_name($_POST[self::REQ_NEWNAME]);
      $dir = new Filesystem_Dir($dir.$newDir);
      if($dir->exist()){
         throw new UnexpectedValueException(sprintf('Složka "%s" již existuje', $newDir));
      } else {
         $dir->createDir();
         $this->infoMsg()->addMessage(sprintf('Složka "%s" byla vytvořena', $newDir));
      }
   }

   public function deleteController()
   {
      $this->checkRights();
      if(!isset ($_POST['items'])){
         throw new UnexpectedValueException($this->tr('Nebyl předán parametr se seznamem položek'));
      }
      foreach ($_POST['items'] as $item) {
         $path = substr(AppCore::getAppDataDir(),0,-1).str_replace(URL_SEPARATOR, DIRECTORY_SEPARATOR, $item[0]);
         try {
            if($item[1] == '..') throw new UnexpectedValueException($this->tr('Nadřazený adresář nelze smazat'));
            $this->chekWritableDir($path);
            // kontrola adresáře na zápis
            if (is_dir($path . $item[1])) {
               $dir = new Filesystem_Dir($path . $item[1]);
               $dir->rmDir();
               $this->infoMsg()->addMessage(sprintf($this->tr('Adresář "%s" byl smazán '), $item[1]));
            } else {
               $file = new Filesystem_File($item[1], $path);
               $file->delete();
               $this->infoMsg()->addMessage(sprintf($this->tr('Soubor "%s" byl smazán '), $item[1]));
            }
         } catch (UnexpectedValueException $exc) {
            $this->errMsg()->addMessage($exc->getMessage());
         }
      }
      sleep(1);
   }
   // kontroler pro přejmenování
   public function renameController()
   {
      $this->checkRights();
      if(!isset ($_POST['items'])){
         throw new UnexpectedValueException($this->tr('Nebyl předán parametr se seznamem položek'));
      }
      foreach ($_POST['items'] as $item) {
         $path = substr(AppCore::getAppDataDir(),0,-1).str_replace(URL_SEPARATOR, DIRECTORY_SEPARATOR, $item[0]);
         try {
            if($item[1] == '..') throw new UnexpectedValueException($this->tr('Nadřazený adresář nelze přejmenovat'));
            $this->chekWritableDir($path);
            $newName = vve_cr_safe_file_name($item[2]);

            if(!rename($path.$item[1], $path.$newName)){
               throw new UnexpectedValueException(sprintf($this->tr('Položku %s se nepodařilo přejmenovat'), $item[1]));
            }
            $this->infoMsg()->addMessage(sprintf($this->tr('Položka %s byla přejmenována na %s '), $item[1], $newName));
         } catch (UnexpectedValueException $exc) {
            $this->errMsg()->addMessage($exc->getMessage());
         }
      }
      sleep(1);
   }

   /* Images function */
   /**
    * Create new resized image
    */
   public function imageResizedController()
   {
      $this->checkRights();
      $dir = substr(AppCore::getAppDataDir(), 0,-1).$this->getDir();
      $this->chekWritableDir($dir);
      $w = (int)$_POST['newW'];
      $h = (int)$_POST['newH'];

      $crop = false;
      if($_POST['crop'] == 'true'){
         $crop = true;
      }

      foreach ($_POST[self::REQ_FILE] as $file){
         try {
            $image = new Filesystem_File_Image($file, $dir);
            $fInfo = pathinfo($dir . $file);
            $newName = $fInfo['filename'] . '_' . $w . 'x' . $h . '.' . $fInfo['extension'];
            $image->saveAs($dir, $w, $h, $crop, $newName);
            unset($image);
         } catch (Exception $exc) {
            $this->errMsg()->addMessage(sprintf('Chyba při vatváření obrázku "%s": %s', $file, $exc->getMessage()));
         }
      }
      $this->infoMsg()->addMessage(sprintf('Obrázky byly vytvořeny.'));
   }

   /**
    * rotate img
    */
   public function imageRotateController()
   {
      $this->checkRights();
      $dir = substr(AppCore::getAppDataDir(), 0,-1).$this->getDir();
      $this->chekWritableDir($dir);
      $r = (int)$_POST['degree'];
      foreach ($_POST[self::REQ_FILE] as $file) {
         try {
            $image = new Filesystem_File_Image($file, $dir);
            $image->rotateImage(-(int) $r);
            $image->save();
            unset($image);
         } catch (Exception $exc) {
            $this->errMsg()->addMessage(sprintf('Chyba při otáčení obrázku "%s": %s', $file ,$exc->getMessage()));
         }
      }
      $this->infoMsg()->addMessage(sprintf('Obrázek byl otočen.'));
      sleep(1);
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

   public function copyController()
   {
      $this->checkRights();
      $targetDir = substr(AppCore::getAppDataDir(),0,-1).  str_replace(URL_SEPARATOR, DIRECTORY_SEPARATOR, $_POST['target']);
      $this->chekWritableDir($targetDir);
      foreach ($_POST['items'] as $item) {
         $path = substr(AppCore::getAppDataDir(),0,-1).str_replace(URL_SEPARATOR, DIRECTORY_SEPARATOR, $item[0]);
         try {
            $this->chekWritableDir($targetDir);
            if (!@copy($path.$item[1], $targetDir . $item[1])) {
               throw new UnexpectedValueException(sprintf($this->tr('Položku "%s" se nepodařilo kopírovat.'), $item[1]));
            }
            if($_POST['move'] == 'true') {
               if(is_dir($path.$item[1]) AND !@rmdir($path.$item[1])){
                  throw new UnexpectedValueException(sprintf($this->tr('Položku "%s" se nepodařilo vymazat.'), $item[1]));
               } else if(is_file ($path.$item[1]) AND !@unlink($path.$item[1])) {
                  throw new UnexpectedValueException(sprintf($this->tr('Položku "%s" se nepodařilo vymazat.'), $item[1]));
               }
            }
            if($_POST['move'] == 'true') {
               $this->infoMsg()->addMessage(sprintf('Položka "%s" byla přesunuta.', $item[1]));
            } else {
               $this->infoMsg()->addMessage(sprintf('Položka "%s" byla kopírována.', $item[1]));
            }
         } catch (UnexpectedValueException $exc) {
            new CoreErrors($exc);
         }
      }
      sleep(1);
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
?>
