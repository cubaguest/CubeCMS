<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class DataStore_Controller extends Controller {
//    const PARAM_ALLOW_PRIVATE = 'allow_private';

   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      // pokud nebyl datový adresář vytvořen, vytvoří se
      if(!is_dir($this->category()->getModule()->getDataDir())){
         mkdir($this->category()->getModule()->getDataDir(), 0777, true);
      }
      $this->view()->writable = $this->category()->getRights()->isWritable();

      $path = $this->getRequestParam('path', '/');

      // načtení položek v adresáři
      $this->loadDirItems($path);
      if($this->rights()->isWritable()){
         // vytváření adresáře
         $this->createDir($path);
         // mazání položky/položek
         $this->deleteItem($path);
         $this->deleteItems($path);
         $this->moveItems($path);
         // přejmenování
         $this->renameItem($path);
      }
//      $pathItems = explode('/', $path);
      $pathItems = array();
      preg_match_all('/[^\/]*\//', $path, $pathItems);
      $outNav = array();
      $curPath = null;
      foreach ($pathItems[0] as $item) {
         $curPath .= $item;
         array_push($outNav, array('name' => $item, 'link' => (string)$this->link()->param('path', $curPath)) );

      }

      $this->view()->pathNav = $outNav;

      $this->view()->path = $path;
   }

   private function createDir($path)
   {
      $form = new Form('create_dir_');
      $elemName = new Form_Element_Hidden('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());

      $form->addElement($elemName);

      $elemSubmit = new Form_Element_Submit('submit', $this->tr('Vytvořit adresář'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
         $name = vve_cr_safe_file_name($form->name->getValues());

         $newDir = $this->category()->getModule()->getDataDir(false).$path.$name;

         if (!@mkdir($newDir, 0777, true)) {
            throw new InvalidArgumentException(sprintf($this->tr('Chyba při vatváření adresáře %s'),$name));
         }

         $this->infoMsg()->addMessage($this->tr('Adresář byl vytvořen'));
         $this->link()->reload();
      }
      $this->view()->formCreateDir = $form;
   }

   private function deleteItem($path)
   {
      $form = new Form('delete_item_');
      $elemName = new Form_Element_Hidden('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);

      $elemSubmit = new Form_Element_SubmitImage('submit', $this->tr('Smazat'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
         $name = $form->name->getValues();

         $itemPath = $this->category()->getModule()->getDataDir(false).$path.$name;

         if (is_dir($itemPath) && !@rmdir($itemPath)) {
            throw new InvalidArgumentException(sprintf($this->tr('Chyba při mazání adresáře %s'),$name));
         } else if(is_file($itemPath) && !@unlink($itemPath)){
            throw new InvalidArgumentException(sprintf($this->tr('Chyba při mazání souboru %s'),$name));
         }
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link()->reload();
      }
      $this->view()->formDeleteItem = $form;
   }

   private function  deleteItems($path)
   {
      $form = new Form('delete_items_');
      $elemName = new Form_Element_Hidden('items', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->setDimensional();
      $elemName->setValues(null);
      $form->addElement($elemName);

      $elemSubmit = new Form_Element_Submit('submit', $this->tr('Smazat'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
         $names = $form->items->getValues();
         $itemPath = $this->category()->getModule()->getDataDir(false) . $path;

         foreach ($names as $name) {
            if($name == "") continue;
            try {
               if (is_dir($itemPath . $name) && !@rmdir($itemPath . $name)) {
                  throw new InvalidArgumentException(sprintf($this->tr('Chyba při mazání adresáře %s'), $name));
               } else if (is_file($itemPath . $name) && !@unlink($itemPath . $name)) {
                  throw new InvalidArgumentException(sprintf($this->tr('Chyba při mazání souboru %s'), $name));
               }
            } catch (InvalidArgumentException $exc) {
               $this->errMsg()->addMessage(sprintf($this->tr('Položku %s se nepodařilo smazat'), $name));
            }
         }
         $this->infoMsg()->addMessage($this->tr('Položky byly smazány'));
         $this->link()->reload();
      }
      $this->view()->formDeleteItems = $form;
   }

   private function renameItem($path)
   {
      $form = new Form('rename_item_');
      $elemName = new Form_Element_Hidden('oldname', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);

      $elemNewName = new Form_Element_Hidden('newname', $this->tr('Nový název'));
      $elemNewName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemNewName);

      $elemSubmit = new Form_Element_SubmitImage('submit', $this->tr('Přejmenovat'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
         $oldname = $form->oldname->getValues();
         $newname = vve_cr_safe_file_name($form->newname->getValues());

         $itemPath = $this->category()->getModule()->getDataDir(false).$path;
         if (!@rename($itemPath.$oldname, $itemPath.$newname)) {
            throw new InvalidArgumentException(sprintf($this->tr('Chyba při přejmenování položky %s na položku %'),$oldname, $newname));
         }
         $this->infoMsg()->addMessage($this->tr('Položka byla přejmenována'));
         $this->link()->reload();
      }
      $this->view()->formRenameItem = $form;
   }

   private function moveItems($path)
   {
      $form = new Form('move_items_');
      $elemItems = new Form_Element_Hidden('items', $this->tr('Položky'));
      $elemItems->addValidation(new Form_Validator_NotEmpty());
      $elemItems->setDimensional();
      $form->addElement($elemItems);

      //načtení struktury
      $dir = $this->category()->getModule()->getDataDir(false);
      $ite = new RecursiveDirectoryIterator($dir);
      $iterator = new RecursiveIteratorIterator($ite, RecursiveIteratorIterator::SELF_FIRST);
      $dirs = array($this->tr('Kořen') => '/');
      foreach ($iterator as $cur) {
         if($cur->isDir() && substr($cur->getFilename(), 0, 1) !== '.'){
            $dirs[str_repeat('. ', $iterator->getDepth()+1).$cur->getFilename()] = str_replace($dir, '/', $cur->getPathname().'/');
         }
      }

      $elemDirs = new Form_Element_Select('targetdir', $this->tr('Cílový adresář'));
      $elemDirs->setOptions($dirs);
      $elemDirs->setValues($path);
      $form->addElement($elemDirs);

      $elemNewDir = new Form_Element_Text('newdir', $this->tr('Nový adresář'));
      $form->addElement($elemNewDir);


      $elemSubmit = new Form_Element_Submit('submit', $this->tr('Přesunout'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
         $items = $form->items->getValues();

         $targetPath = $this->category()->getModule()->getDataDir(false).
            preg_replace(array('/\.\.\/?/'), array('/'), $form->targetdir->getValues()).DIRECTORY_SEPARATOR;
         if($form->newdir->getValues() != null && !file_exists($targetPath.$form->newdir->getValues()) && !is_dir($targetPath.$form->newdir->getValues())){
            $newName = vve_cr_safe_file_name($form->newdir->getValues());
            mkdir($targetPath.$newName, 0777);
            $targetPath .= $newName.DIRECTORY_SEPARATOR;
         }

         foreach ($items as $item) {
            $itemPath = $this->category()->getModule()->getDataDir(false).$path;
//            Debug::log($targetPath.$item,$itemPath.$item);

            if(strpos($targetPath, $itemPath.$item) !== false){
               // kntnrola jestli se nepřesunuje do sebe sama
               $this->errMsg()->addMessage(sprintf($this->tr('Položku "%s" nelze přesunout do sebe'), $item));
            } else {
               // pokud existuje starý soubor, je přidán náhodný string za název souboru
               $itemNewName = $item;
               if(file_exists($targetPath.$item)){
                  $path_parts = pathinfo($targetPath.$item);
                  $itemNewName = $path_parts['filename'].'_'.time().'.'.$path_parts['extension'];
               }
               // přesun
               if(!rename($itemPath.$item, $targetPath.$itemNewName)){
                  Debug::log($itemPath.$item, $targetPath.$itemNewName);
                  $this->errMsg()->addMessage(sprintf($this->tr('Položku "%s" se nepodařilo přesunout. Chyba při přesunu.'), $item), true);
               }
            }
         }
         $this->infoMsg()->addMessage($this->tr('Položka byla přesunuta'));
         $this->link()->reload();
      }
      $this->view()->formMoveItems = $form;
   }


   public function uploadFileController()
   {
      $this->checkWritebleRights();
      $this->view()->allOk = false;
      $this->saveFileForm($this->getRequestParam('path', '/'));
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }
   
   private function saveFileForm($path = '/') {
      $addForm = new Form('upload_');
      $elemFile = new Form_Element_File('file', $this->tr('Soubor'));
      $elemFile->setUploadDir($this->category()->getModule()->getDataDir().$path);
      $addForm->addElement($elemFile);

//      $elemPath = new Form_Element_Hidden('path');
//      $elemPath->setValues($path);
//      $addForm->addElement($elemPath);

      $addSubmit = new Form_Element_Submit('send',$this->tr('Nahrát'));
      $addForm->addElement($addSubmit);

      if($addForm->isValid()) {
         
         $this->infoMsg()->addMessage($this->tr('Soubor byl nahrán'));
      }
      return $addForm;
   }

   public function itemsListController()
   {
      $this->checkReadableRights();
      $this->view()->writable = $this->category()->getRights()->isWritable();

      $newDir = $this->getRequestParam('path', '/');
      $this->loadDirItems($newDir);

   }

   private function loadDirItems($dir = '/')
   {
      if($dir != '/'){
         $this->view()->parentPath = str_replace('//', '/', dirname($dir).'/');
      }

      $dirAbs = $this->category()->getModule()->getDataDir().$dir;
      $dirUrl = $this->category()->getModule()->getDataDir(true).$dir;
      $this->view()->items = $files = $dirs = array();
      try {

         foreach (new DirectoryIterator($dirAbs) as $fileInfo) {
            /**
             * @var SplFileInfo
             */
            $fileInfo;

            // ne zkryté a tečky
            if($fileInfo->isDot()) continue;

            $info = array(
                  'name' => $fileInfo->getFilename(),
                  'size' => 0,
                  'isdir' => false,
                  'url' => null,
                  'path' => null,
                  'ext' => null,
                  'dwurl' => null,
                  'mtime' => $fileInfo->getMTime(),
            );

            if($fileInfo->isDir()){
               $info['isdir'] = true;
               $info['path'] = $dir.$fileInfo->getFilename().'/';
               array_push($files, $info);
            } else {
               $info['size'] = $fileInfo->getSize();
               $info['url'] = $dirUrl.$fileInfo->getFilename();
               $info['ext'] = strtolower(pathinfo($fileInfo->getPathname(), PATHINFO_EXTENSION));
               $dwLink = new Url_DownloadLink($dirAbs, $fileInfo->getFilename());
               $info['dwurl'] = (string)$dwLink;
               unset ($dwLink);
               array_push($dirs, $info);
            }
         }

         switch ($this->getRequestParam('sort', 'name_a')) {
            case 'name_d':
               usort($files, array($this, "sortNamesDesc"));
               usort($dirs, array($this, "sortNamesDesc"));
               break;
            case 'time_d':
               usort($files, array($this, "sortMTimeDesc"));
               usort($dirs, array($this, "sortMTimeDesc"));
               break;
            case 'time_a':
               usort($files, array($this, "sortMTimeAsc"));
               usort($dirs, array($this, "sortMTimeAsc"));
               break;
            case 'name_a':
            default:
               usort($files, array($this, "sortNamesAsc"));
               usort($dirs, array($this, "sortNamesAsc"));
               break;
         }
         $this->view()->curSort = $this->getRequestParam('sort', 'name_a');

         $this->view()->items = array_merge($files, $dirs);

      } catch (Exception $exc) {
         $this->errMsg()->addMessage($exc->getMessage());
      }

   }

   private function sortNamesAsc($a, $b)
   {
      return strcmp($a["name"], $b["name"]);
   }

   private function sortNamesDesc($a, $b)
   {
      return strcmp($b["name"],$a["name"]);
   }

   private function sortMTimeAsc($a, $b){
      return ($a["mtime"] < $b["mtime"]) ? -1 : 1;
   }

   private function sortMTimeDesc($a, $b){
      return ($a["mtime"] > $b["mtime"]) ? -1 : 1;
   }
}

?>
