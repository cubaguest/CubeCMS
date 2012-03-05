<?php
/**
 * Třída príce s fotogalerií v TinyMCE editoru
 * @author cuba
 */
class Component_TinyMCE_Photogalery extends Component_TinyMCE {
//   const DIR_PUBLIC = 'public';
   
   private static $imagesExtensions = array('jpg','jpeg','png','tif','tiff');

   public function uploadController()
   {
      $this->checkRights();
      
      if(isset ($_POST['dir']) AND $_POST['dir'] != null){
         $dir = vve_cr_safe_file_name($_POST['dir']);
      } else {
         $dir = date('Ymd-Hm').'-'.Auth::getUserName();
      }
      
      $uploadDir = AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_PUBLIC.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;
      $form = $this->createUploadForm($uploadDir);
      
      $this->template()->file = null;
      $this->template()->created = false;
      // adresáře pro output
      $this->template()->dirsmall = VVE_DATA_DIR.'/'.Component_TinyMCE_Browser::DIR_PUBLIC.'/'.$dir.'/small/';
      $this->template()->dir = VVE_DATA_DIR.'/'.Component_TinyMCE_Browser::DIR_PUBLIC.'/'.$dir.'/medium/';
      
      if($form->isValid()) {
         
         $dirSmall = new Filesystem_Dir($uploadDir.'small');
         $dirSmall->checkDir();
         $dirMedium = new Filesystem_Dir($uploadDir.'medium');
         $dirMedium->checkDir();
         
         $image = $form->file->createFileObject('Filesystem_File_Image');
//         $image = new Filesystem_File_Image();
         $crop = true;
         if (defined('VVE_IMAGE_THUMB_CROP')) $crop = VVE_IMAGE_THUMB_CROP; // Cube CMS 6.4r5 or higer
         
         $image->saveAs($dirSmall, VVE_IMAGE_THUMB_W, VVE_IMAGE_THUMB_H, $crop);
         $image->saveAs($dirMedium, VVE_DEFAULT_PHOTO_W, VVE_DEFAULT_PHOTO_H, false);
         $this->template()->file = $image->getName();
         $image->delete();
         $this->template()->created = true;
         $this->infoMsg()->addMessage($this->tr('Soubor byl nahrán'));
      }
      
   }
   
   private function checkRights()
   {
      if(!Auth::isLogin())
      {
         $this->errMsg()->addMessage($this->tr('Nemáte dostatečná práva k zápisu. Asi jste byl odhlášen'));
         throw new UnexpectedValueException('You are not logged.');
      }
   }
   
   /**
    * Metoda pro vytvoření formuláře pro upload
    * @return Form 
    */
   private function createUploadForm($datadir)
   {
      $form = new Form('upload_');

      $file = new Form_Element_File('file');
      $file->addValidation(new Form_Validator_NotEmpty());

      $validOnlyImage = new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif'));
      $file->addValidation($validOnlyImage);

      $file->setUploadDir($datadir);
      $form->addElement($file);
      $submit = new Form_Element_Submit('send');
      $form->addElement($submit);
      return $form;
   }
   
   
   

   public function photogaleryController()
   {
      // inicializace adresářů
      /* public */
      $dir = AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_PUBLIC;
      $dir = new Filesystem_Dir($dir);
      $dir->checkDir();
      unset ($dir);
   }
   
   public function photogaleryView()
   {
//      $this->template = new Template_Core();
//      $this->template()->uploadLink = $this->link()->onlyAction('upload', 'php');
//      $this->template()->linkC = $this->link();
//      
//      $this->template()->addTplFile('filebrowser/filebrowser.phtml');
//      echo $this->template();flush();
//      exit();
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
         throw new UnexpectedValueException(sprintf(_('Adresář %s neexistuje'), '/'.VVE_DATA_DIR.$reqDir));
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
      $aclf = pathinfo($item, PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.self::ACL_FILE;
      if((preg_match('/^'.preg_quote(AppCore::getAppDataDir().self::DIR_HOME.DIRECTORY_SEPARATOR.Auth::getUserName().DIRECTORY_SEPARATOR,'/').'/', $item) // in home dir
         OR preg_match('/^'.preg_quote(AppCore::getAppDataDir().self::DIR_PUBLIC.DIRECTORY_SEPARATOR,'/').'/', $item) // in public dir
         OR (is_dir($item) AND file_exists($aclf) AND in_array(Auth::getUserId(), explode(';',file_get_contents($aclf)))) )// in dir with ACL
         AND is_writable($item)){
         return true;
      }
      return false;
   }


   private function chekWritableDir($dir)
   {
      if(!$this->isWritable($dir))
         throw new Auth_Exception (_('Nemáte dostatečná práva pro úpravu této položky'));
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
         throw new UnexpectedValueException(_('Nebyl předán parametr se seznamem položek'));
      }
      foreach ($_POST['items'] as $item) {
         $path = substr(AppCore::getAppDataDir(),0,-1).str_replace(URL_SEPARATOR, DIRECTORY_SEPARATOR, $item[0]);
         try {
            if($item[1] == '..') throw new UnexpectedValueException(_('Nadřazený adresář nelze smazat'));
            $this->chekWritableDir($path);
            // kontrola adresáře na zápis
            if (is_dir($path . $item[1])) {
               $dir = new Filesystem_Dir($path . $item[1]);
               $dir->rmDir();
               $this->infoMsg()->addMessage(sprintf(_('Adresář "%s" byl smazán '), $item[1]));
            } else {
               $file = new Filesystem_File($item[1], $path);
               $file->delete();
               $this->infoMsg()->addMessage(sprintf(_('Soubor "%s" byl smazán '), $item[1]));
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
         throw new UnexpectedValueException(_('Nebyl předán parametr se seznamem položek'));
      }
      foreach ($_POST['items'] as $item) {
         $path = substr(AppCore::getAppDataDir(),0,-1).str_replace(URL_SEPARATOR, DIRECTORY_SEPARATOR, $item[0]);
         try {
            if($item[1] == '..') throw new UnexpectedValueException(_('Nadřazený adresář nelze přejmenovat'));
            $this->chekWritableDir($path);
            $newName = vve_cr_safe_file_name($item[2]);

            if(!rename($path.$item[1], $path.$newName)){
               throw new UnexpectedValueException(sprintf(_('Položku %s se nepodařilo přejmenovat'), $item[1]));
            }
            $this->infoMsg()->addMessage(sprintf(_('Položka %s byla přejmenována na %s '), $item[1], $newName));
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
               throw new UnexpectedValueException(sprintf(_('Položku "%s" se nepodařilo kopírovat.'), $item[1]));
            }
            if($_POST['move'] == 'true') {
               if(is_dir($path.$item[1]) AND !@rmdir($path.$item[1])){
                  throw new UnexpectedValueException(sprintf(_('Položku "%s" se nepodařilo vymazat.'), $item[1]));
               } else if(is_file ($path.$item[1]) AND !@unlink($path.$item[1])) {
                  throw new UnexpectedValueException(sprintf(_('Položku "%s" se nepodařilo vymazat.'), $item[1]));
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
         throw new UnexpectedValueException(_('Nebyl zadán korektní adresář.'));

      if($_POST['users'] != 'null' && is_array($_POST['users'])){
         $user = implode(';', $_POST['users']);
         file_put_contents($dir.DIRECTORY_SEPARATOR.self::ACL_FILE, $user);
      } else {
         $file = new Filesystem_File(self::ACL_FILE, $dir);
         if($file->exist()) $file->delete();
      }
      $this->infoMsg()->addMessage(_('Práva byla nastavena'));

   }
}
?>
