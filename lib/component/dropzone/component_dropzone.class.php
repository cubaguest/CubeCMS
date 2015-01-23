<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of component_dropzone
 *
 * @author cuba
 */
class Component_Dropzone extends Component {

   protected function init()
   {
      $this->setConfig('postData', array());
      $this->setConfig('maxFileSize', 2*1024*1024); // 2MB
      $this->setConfig('maxFiles', 10);
      $this->setConfig('selector', '.dropzone');
      $this->setConfig('path', AppCore::getAppCacheDir().uniqid('dropzone'));
      $this->setConfig('images', array());
   }
   
   public function mainView()
   {
      $jsPlugin = new Component_DropZone_JsPlugin();
      Template::addJsPlugin($jsPlugin);
      $images = $this->getConfig('images');
      if(empty($images) && is_dir($this->getConfig('path'))){
         // load dirs
         $images = array();
         $finfo = finfo_open(FILEINFO_MIME_TYPE);
         foreach(glob($this->getConfig('path').'/*.*') as $filename){
//            name: "myimage.jpg", size: 12345, type: 'image/jpeg'
            
            $images[] = array(
                'name' => basename($filename),
                'size' => filesize($filename),
                'mime' => finfo_file($finfo, $filename),
                'url' => Utils_Url::pathToSystemUrl($filename),
                'id' => 0,
            );
         }
         $this->setConfig('images', $images);
      }
      $this->template = (string)$jsPlugin->getJSCode($this->config);
   }
   
   public function uploadFileController()
   {
      if(!isset($_POST['dropzone_path'])){
         throw new Exception($this->tr('Nebyla předána cesta k uploadu'));
      }
      
      $form = new Form('dropzone_');
      $elemPath = new Form_Element_Hidden("path");
      $form->addElement($elemPath);
      // naplnění kvůli nasatvení uploadu
      $form->path->populate();
      $path = Utils_Dir::secureUpload($form->path->getValues());
      $path = new FS_Dir($path);
      $path->check(); // kontrola vytvoření
      
      $elemFile = new Form_Element_File("file", $this->tr('Soubor'));
      $elemFile->setOverWrite(false);
      $elemFile->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $elemFile->setUploadDir((string)$path);
      $form->addElement($elemFile);
      
      $this->template()->uploaded = false;
      // uploading file
      if($form->isValid()){
         $this->template()->uploaded = true;
         $file = $form->file->createFileObject('File');
         $this->template()->filename = $file->getName();
      }
      
   }
   
   /**
    * Vrací pole s nahranými soubory
    * @return array
    */
   public function getFiles()
   {
      return array();
   }
   
   public function deleteFileController()
   {
      if(!isset($_POST['file']) || !isset($_POST['path'])){
         throw new UnexpectedValueException($this->tr('Nebyly předány všechny parametry'));
      }
      
      $file = $_POST['file'];
      $path = Utils_Dir::secureUpload($_POST['path']);
      
      if(is_file($path.DIRECTORY_SEPARATOR.$file)){
         unlink($path.DIRECTORY_SEPARATOR.$file);
         $this->template()->respond = 'success';
      }
   }
   
   public function getUploadedFilesController()
   {
      
   }
}
