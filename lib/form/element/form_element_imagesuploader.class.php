<?php
class Form_Element_ImagesUploader extends Form_Element_File {
   
   protected $maxFileSize = 2097152;
   protected $maxFiles = 10;
   
   protected $inputDir;
   protected $imagesKey = null;

   public function __construct($name, $label = null, $prefix = null)
   {
      $this->inputDir = new Form_Element_Hidden($this->getName(true).'_dirName');
      parent::__construct($name, $label, $prefix);
      $this->dirKey = md5(microtime().$_SERVER['REMOTE_ADDR']);
      $this->setMultiple(true);
   }
   
   public function populate()
   {
      $this->inputDir->populate();
      parent::populate();
      
      $componentDZ = new Component_Dropzone();
      $componentDZ->setConfig('path', AppCore::getAppWebDir().str_replace('..', '', $this->inputDir->getValues()));
      $this->setValues($componentDZ->getFiles());
   }
   
   public function setMaxFileSize($sizeBytes)
   {
      $this->addValidation(new Form_Validator_FileSize($sizeBytes));
      $this->maxFileSize = $sizeBytes;
   }
   
   public function setMaxFiles($count)
   {
      $this->maxFiles = $count;
   }
   
   public function setUploadDir($dir)
   {
      parent::setUploadDir($dir);
      $this->inputDir->setValues(str_replace(AppCore::getAppWebDir(), '', $dir));
   }

   public function setImagesKey($key)
   {
      $this->imagesKey = $key;
   }
   
   public function control($renderKey = null) 
   {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $this->html()->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }
      // tady bude if při multilang
      $this->html()->setAttrib('type', 'file');
      
      $wrap = new Html_Element('div');
      $wrap->addClass('dropzone');
      $wrap->setAttrib('id', 'dropzone-'.$this->renderedId);
      
      $inputWrap = new Html_Element('div');
      $inputWrap->addClass('fallback');
      
      $container = clone $this->containerElement;
      $container->addClass('fallback');
      if($this->isMultiple()){
         $this->html()->setAttrib('multiple', 'multiple');
         if($this->dimensional === true){
            $container
                ->addClass($this->cssClasses['containerClass'])
                ->addClass($this->cssClasses['multipleClass'])
                ->addClass($this->cssClasses['multipleClassLast'])
                ->addClass('input-group');

            $this->html()->setAttrib('name', $this->getName()."[]");
            $this->html()->setAttrib('id', $this->getName().'_'.$rKey);

            $container->setContent($this->html());
            $container->addContent($this->getMultipleButtons(true, true), true);
         } else {
            if($this->dimensional == null){
               $this->html()->setAttrib('id', $this->getName()."_".$rKey);
            } else {
               $this->html()->setAttrib('id', $this->getName().'_'.$rKey."_".$this->dimensional);
            }
            $this->html()->setAttrib('name', $this->getName().'['.$this->dimensional.']');
            $container->setContent($this->html());
         }

      } else {
         $this->html()->setAttrib('name', $this->getName());
         $this->html()->setAttrib('id', $this->getName().'_'.$rKey);
         $container->setContent($this->html());
      }
      
      $componentDZ = new Component_Dropzone();
      if(!$this->isMultiple()){
         $componentDZ->setConfig('maxFiles', 1);
      } else {
         $componentDZ->setConfig('maxFiles', $this->maxFiles);
      }
      $componentDZ->setConfig('selector', '#dropzone-'.$this->renderedId);
      $componentDZ->setConfig('postData',array('target' => 'path') );
      $componentDZ->setConfig('path', $this->inputDir->getValues() );
      $componentDZ->setConfig('imageskey', $this->imagesKey );
      
      $inputWrap->setContent($container);
      $wrap->addContent($inputWrap);
      $wrap->addContent((string)$componentDZ);
      $wrap->addContent($this->inputDir->control());

      if($renderKey == null){
         $this->renderedId++;
      }
      return $wrap;
   }
   
   /**
    * Vrací pole se soubory
    * @return type
    */
   public function getFiles()
   {
      return array();
   }
}