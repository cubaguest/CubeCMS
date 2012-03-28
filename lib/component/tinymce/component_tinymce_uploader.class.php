<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of component_tinymce_rowser
 * @author cuba
 */
class Component_TinyMCE_Uploader extends Component_TinyMCE {
   public function imageUploadController()
   {
      if(!Auth::isLogin())
      {
         throw new Auth_Exception($this->tr('Nemáte dostatečná práva k zápisu. Asi jste byl odhlášen'));
      }
      
      $uploadData = array(
         'imageUrl' => "",
         'imageFullUrl' => "",
         'errMsg' => array(),
         'infoMsg' => array(),
         'success' => false,
      );
      
      // upload obrázku do adresáře
      
      $form = new Form("cai_");
      
      $eFile = new Form_Element_File('uploadFile', $this->tr('Obrázek'));
      $eFile->addValidation(new Form_Validator_NotEmpty($this->tr('Soubor nebyl vybrán')));
      $eFile->addValidation(new Form_Validator_FileExtension('jpg;jpeg;png;bmp;gif'));
      $form->addElement($eFile);
      
      $eDir = new Form_Element_Select('dirName');
      $form->addElement($eDir );
      
      $eDirNew = new Form_Element_Text('dirNewName');
      $form->addElement($eDirNew );
      
      $eResize = new Form_Element_Checkbox('resize');
      $form->addElement($eResize);
      $eResW = new Form_Element_Text('resizeUserW');
      $form->addElement($eResW );
      $eResH = new Form_Element_Text('resizeUserH');
      $form->addElement($eResH );
      
      $eThumb = new Form_Element_Checkbox('createThumbnail');
      $form->addElement($eThumb);
      $eThumbW = new Form_Element_Text('thumbnailW');
      $form->addElement($eThumbW );
      $eThumbH = new Form_Element_Text('thumbnailH');
      $form->addElement($eThumbH );
      $eThumbCrop = new Form_Element_Checkbox('thumbnailC');
      $form->addElement($eThumbCrop);
      
      $eSubmit = new Form_Element_Submit("upload");
      $form->addElement($eSubmit );
      
      try {
         $file = null;
         if($form->isSend() ){
            $file = new File_Image($form->uploadFile->getValues() );
            $uploadData['isImg'] = $file->isImage();
            if(!$file->isImage() ){
               // exception here ??
               $form->uploadFile->setError($this->tr('Soubor není platný obrázek'));
            }
         }
         
         if($form->isValid() && $file instanceof File_Image ){
            // kontrola složky
            $realPaht = $this->decodeDir($form->dirName->getValues());
            $urlPath = Url_Request::getBaseWebDir().VVE_DATA_DIR.$this->encodeDir($realPaht);
            
            if($form->dirNewName->getValues() != null){
               $newDirName = $form->dirNewName->getValues();
               // create safe dir name
               $newDirName = $this->sanitizeDir($newDirName);
               
               if(!is_dir($realPaht.$newDirName) && !@mkdir($realPaht.$newDirName, 0777, true)){
                  throw new UnexpectedValueException($this->tr('Adresář nelze vytvořit'));
               }
               // append new dir to realPath and urlPath
               $realPaht .= $newDirName;
               $urlPath = Url_Request::getBaseWebDir().VVE_DATA_DIR.$this->encodeDir($realPaht);
            }
            
            // copy image to target dir and create new object
            $fileResized = $file->copy($realPaht.DIRECTORY_SEPARATOR, true);
            
            // assign image url to data
            $uploadData['imageUrl'] = $urlPath.$fileResized->getName();
            
            
            // zpracování obrázku
            if($form->resize->getValues() == true){
               // resize images
               $imgData = $fileResized->getData()
                  ->resize(
                     $form->resizeUserW->getValues() != null ? (int)$form->resizeUserW->getValues() : VVE_DEFAULT_PHOTO_W, 
                     $form->resizeUserH->getValues() != null ? (int)$form->resizeUserH->getValues() : VVE_DEFAULT_PHOTO_H, 
                     File_Image_Base::RESIZE_AUTO )
                  ->save();
            }
            
            if($form->createThumbnail->getValues() == true){
               $thW = $form->thumbnailW->getValues() != null ? (int)$form->thumbnailW->getValues() : VVE_IMAGE_THUMB_W;
               $thH = $form->thumbnailH->getValues() != null ? (int)$form->thumbnailH->getValues() : VVE_IMAGE_THUMB_H;
               $thC = $form->thumbnailC->getValues() == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO;
               
               $fInfo = pathinfo((string)$fileResized);
               $thumbFileName = $fInfo['filename'] . '_' . $thW . 'x' . $thH . '.' . $fInfo['extension'];
               
               $uploadData['crop'] = $thC;
               
               $thumbFile = $file->copy($realPaht, true, $thumbFileName);
               $thumbFile->getData()
                  ->resize($thW, $thH, $thC)
                  ->save();
               
               // swap images
               $uploadData['imageFullUrl'] = $uploadData['imageUrl'];
               $uploadData['imageUrl'] = $urlPath.$thumbFile->getName();
            }
            
            $file->delete(); // clean cache
         
            $this->infoMsg()->addMessage($this->tr('Obrázek byl nahrán'), false);
            $uploadData['success'] = true;
         }
      } catch(Exception $e ) {
         // add string to errors
         $this->errMsg()->addMessage((string)$e);
      }
      
      $uploadData['errMsg'] = $this->errMsg()->getMessages();
      $uploadData['infoMsg'] = $this->infoMsg()->getMessages();
      
      
      // Musí být přes script, protože jinak je CrossDomain request, který neprojde
      echo "<script>"."\n";
      echo "document.domain = '".Url_Request::getDomain()."';"."\n";
      echo "var data = ".json_encode($uploadData ).";"."\n";
      echo "window.parent.TinyMCEFileUploader.uploadDone(data)"."\n";
      echo "</script>"."\n";
      flush();
      die;
   }
   
   public function fileUploadController()
   {
      if(!Auth::isLogin())
      {
         throw new Auth_Exception($this->tr('Nemáte dostatečná práva k zápisu. Asi jste byl odhlášen'));
      }
      
      $uploadData = array(
         'url' => "",
         'errMsg' => array(),
         'infoMsg' => array(),
         'success' => false,
      );
      
      // upload obrázku do adresáře
      
      $form = new Form("cal_");
      
      $eFile = new Form_Element_File('uploadFile', $this->tr('Soubor'));
      $eFile->addValidation(new Form_Validator_NotEmpty($this->tr('Soubor nebyl vybrán')));
      $eFile->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::ALL));
      $form->addElement($eFile);
      
      $eDir = new Form_Element_Select('dirName');
      $form->addElement($eDir );
      
      $eDirNew = new Form_Element_Text('dirNewName');
      $form->addElement($eDirNew );
      
      $eSubmit = new Form_Element_Submit("upload");
      $form->addElement($eSubmit );
      
      try {
         
         if($form->isValid()){
            $file = new File($form->uploadFile->getValues() );
            // kontrola složky
            $realPaht = $this->decodeDir($form->dirName->getValues());
            $urlPath = Url_Request::getBaseWebDir().VVE_DATA_DIR.$this->encodeDir($realPaht);
            
            if($form->dirNewName->getValues() != null){
               $newDirName = $form->dirNewName->getValues();
               // create safe dir name
               $newDirName = $this->sanitizeDir($newDirName);
               
               if(!is_dir($realPaht.$newDirName) && !@mkdir($realPaht.$newDirName, 0777, true)){
                  throw new UnexpectedValueException($this->tr('Adresář nelze vytvořit'));
               }
               // append new dir to realPath and urlPath
               $realPaht .= $newDirName;
               $urlPath = Url_Request::getBaseWebDir().VVE_DATA_DIR.$this->encodeDir($realPaht);
            }
            
            // copy image to target dir and create new object
            $fileUplaoded = $file->copy($realPaht.DIRECTORY_SEPARATOR, true);
            
            // assign image url to data
            $uploadData['url'] = $urlPath.$fileUplaoded->getName();
            
            $file->delete(); // clean cache
         
            $this->infoMsg()->addMessage($this->tr('Soubor byl nahrán'), false);
            $uploadData['success'] = true;
         }
      } catch(Exception $e ) {
         // add string to errors
         $this->errMsg()->addMessage((string)$e);
      }
      
      $uploadData['errMsg'] = $this->errMsg()->getMessages();
      $uploadData['infoMsg'] = $this->infoMsg()->getMessages();
      
      
      // Musí být přes script, protože jinak je CrossDomain request, který neprojde
      echo "<script>"."\n";
      echo "document.domain = '".Url_Request::getDomain()."';"."\n";
      echo "var data = ".json_encode($uploadData ).";"."\n";
      echo "window.parent.TinyMCEFileUploader.uploadDone(data)"."\n";
      echo "</script>"."\n";
      flush();
      die;
   }
   
   public function dirsListController()
   {
      // base vars
      $dirPublic = AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_PUBLIC.DIRECTORY_SEPARATOR;
      $dirHome = AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_HOME.DIRECTORY_SEPARATOR.Auth::getUserName().DIRECTORY_SEPARATOR;
   
      // iterate over public dir
      $publicDirs = array("/".Component_TinyMCE_Browser::DIR_PUBLIC."/");
      $ite = new RecursiveDirectoryIterator($dirPublic, FilesystemIterator::SKIP_DOTS );
      foreach (new RecursiveIteratorIterator($ite, RecursiveIteratorIterator::SELF_FIRST) 
         as $name => $item) {
         // @todo vymyslet jak filtrovat small a medium, protože to jsou adresáře galerií
         if($item->isDir() && $item->isWritable() 
            && ( strpos($item->getPathname(), "small") === false
            && strpos($item->getPathname(), "medium") === false ) ){
            
            array_push($publicDirs, $this->encodeDir($item->getPathname()));
         }
      }
      // assign to output
      $this->template()->dirsPublic = $publicDirs ;
      
      // interate over user dir
      $homeDirs = array("/".Component_TinyMCE_Browser::DIR_HOME."/");
      if(is_dir($dirHome)){
         $ite = new RecursiveDirectoryIterator($dirHome, FilesystemIterator::SKIP_DOTS );
         foreach (new RecursiveIteratorIterator($ite, RecursiveIteratorIterator::SELF_FIRST) 
            as $name => $item) {
            // @todo vymyslet jak filtrovat small a medium, protože to jsou adresáře galerií
            if($item->isDir() 
               && ( strpos($item->getPathname(), "small") === false
               && strpos($item->getPathname(), "medium") === false ) ){
            
               array_push($homeDirs, $this->encodeDir($item->getPathname()));
            }
         }
      }
      // assign to output
      $this->template()->dirsHome = $homeDirs ;
   }
   
   /**
    * Peřevede reálnou cestu na část url adresy ve složce data
    * @param type $dir
    * @return string 
    */
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
   
   protected function sanitizeDir($dir)
   {
      $dir = vve_cr_url_key($dir, false); // safe name
      
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
      
      return $dir;
   }
}
?>
