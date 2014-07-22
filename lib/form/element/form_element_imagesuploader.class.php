<?php
class Form_Element_ImagesUploader extends Form_Element_File {
   
   protected $maxFileSize = 2097152;
   
   protected $inputDir;

   public function __construct($name, $label = null, $prefix = null)
   {
      $this->inputDir = new Form_Element_Hidden($this->getName(true).'_dirName');
      parent::__construct($name, $label, $prefix);
      $this->dirKey = md5(microtime().$_SERVER['REMOTE_ADDR']);
      $this->setMultiple(true);
   }
   
   public function populate()
   {
      parent::populate();
      $this->inputDir->populate();
   }
   
   public function setMaxFileSize($sizeBytes)
   {
      $this->maxFileSize = $size;
   }
   
   public function setUploadDir($dir)
   {
      parent::setUploadDir($dir);
      $this->inputDir->setValues($dir);
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
      $componentDZ->setConfig('selector', '#dropzone-'.$this->renderedId);
      $componentDZ->setConfig('postData',array('target' => 'path') );
      $componentDZ->setConfig('path', $this->inputDir->getValues() );
      
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