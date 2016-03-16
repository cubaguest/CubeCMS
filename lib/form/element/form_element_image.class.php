<?php

class Form_Element_Image extends Form_Element_File {

   const DELETE_CURRENT_IMAGE = 1;

   protected $inputDir;
   protected $imagesKey = null;

   /**
    *
    * @var Form_Element_Checkbox
    */
   protected $deleteComponent = false;
   protected $images = array();

   public function __construct($name, $label = null, $prefix = null)
   {
      parent::__construct($name, $label, $prefix);
      $this->setMultiple(false);
      $this->cssClasses['containerClass'] = 'image-selector';
      $this->cssClasses['containerElementsClass'] = 'image-selector-inputs';
      $this->cssClasses['containerDeleteClass'] = 'image-selector-delete-checkbox';
      $this->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
   }

   public function populate()
   {
      $originalImage = $this->getUnfilteredValues();
      // pokud je komponenta pro smazání a je zároveň odeslána pro smazání, smaže se původní obrázek
      if ($this->deleteComponent instanceof Form_Element_Checkbox) {
         $this->deleteComponent->populate();
         if ($this->deleteComponent->getValues() == true && $this->getValues() != null) {
            // remove old image and set to null 
            $file = new File($this->getValues(), $this->uploadDir);
            if($file->exist()){
               $file->delete();
            }
            $this->setFilteredValues(null);
            $originalImage = null;
         }
      }
      
      parent::populate();
      // je nahrán nový
      if($this->getValues() != null){
         // odstranit starý
         if(is_string($originalImage)){
            $file = new File($originalImage, $this->uploadDir);
            if($file->exist()){
               $file->delete();
            }
         }
      } 
      // je starý 
      else if($originalImage != null){
         if(is_string($originalImage)){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileInfo = array (
               'name' => $originalImage,
               'path' => new FS_Dir($this->uploadDir),
               'size' => filesize($this->uploadDir.$originalImage),
               'mime' => finfo_file($finfo, $this->uploadDir.$originalImage),
               'type' => finfo_file($finfo, $this->uploadDir.$originalImage).';',
               'extension' => pathinfo($this->uploadDir.$originalImage, PATHINFO_EXTENSION),
            );
            $this->setFilteredValues($fileInfo);
         }
      }
      
//      var_dump('before pop', $this);
      
//      if(!$this->getValues() && $this->imageSelector->getValues()){
//         $finfo = finfo_open(FILEINFO_MIME_TYPE);
//         $fileInfo = array (
//            'name' => $this->imageSelector->getValues(),
//            'path' => new FS_Dir($this->uploadDir),
//            'size' => filesize($this->uploadDir.$this->imageSelector->getValues()),
//            'mime' => finfo_file($finfo, $this->uploadDir.$this->imageSelector->getValues()),
//            'type' => finfo_file($finfo, $this->uploadDir.$this->imageSelector->getValues()).';',
//            'extension' => pathinfo($this->uploadDir.$this->imageSelector->getValues(), PATHINFO_EXTENSION),
//         );
//         $this->setFilteredValues($fileInfo);
//      }
   }

   public function control($renderKey = null)
   {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $this->html()->clearContent();
      if (!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }
      // tady bude if při multilang
      $this->html()->setAttrib('type', 'file');

      $mianContainer = new Html_Element('div');
      $mianContainer
          ->setAttrib('id', 'image-selector-' . $this->getName() . '_' . $rKey)
          ->addClass($this->cssClasses['containerClass'])
          ->addClass('clearfix');



      // náhled
      $previewBox = new Html_Element('div');
      $previewBox->addClass('image-selector-img-box');
      $preview = new Html_Element('img');
      $preview
          ->setAttrib('data-emptysrc', Utils_Url::pathToSystemUrl(AppCore::getAppLibDir()) . Template::IMAGES_DIR . "/no_image.png")
          ->setAttrib('data-targetpath', Utils_Url::pathToSystemUrl($this->uploadDir))
          ->setAttrib('alt', "");
      $file = $this->getValues();
//      var_dump($this->getValues());
      if (is_array($file)) {
         $preview->setAttrib('src', Utils_Url::pathToSystemUrl($file['path'] . $file['name']))
             ->setAttrib('data-originalsrc', Utils_Url::pathToSystemUrl($file['path'] . $file['name']));
      } else if ($file != null) {
         $preview->setAttrib('src', Utils_Url::pathToSystemUrl(Utils_Image::cache($this->getUploadDir() . $file, 100, 100)))
             ->setAttrib('data-originalsrc', Utils_Url::pathToSystemUrl(Utils_Image::cache($this->getUploadDir() . $file, 100, 100)));
      } else {
         $preview->setAttrib('src', Utils_Url::pathToSystemUrl(AppCore::getAppLibDir()) . Template::IMAGES_DIR . "/no_image.png")
             ->setAttrib('data-originalsrc', Utils_Url::pathToSystemUrl(AppCore::getAppLibDir()) . Template::IMAGES_DIR . "/no_image.png");
      }
      $previewBox->addContent((string) $preview);

      $mianContainer->addContent($previewBox);

      // inputy
      $container = clone $this->containerElement;
      $container
          ->addClass($this->cssClasses['containerElementsClass'])
      ;

      $this->html()->setAttrib('name', $this->getName());

      $this->html()->setAttrib('id', $this->getName() . '_' . $rKey);

      $inputBox = new Html_Element('div');
      $inputBox->addClass('image-selector-input-box')->addClass('input-group');
      $inputBox->addContent($this->html());
      $inputBox->addContent('<a href="#" class="input-group-btn button-clear-image-selector"><span class="icon icon-remove"></span></a>');

      $container->addContent($inputBox);

      if ($this->getValues() != null && $this->deleteComponent) {
         $wrapDelete = new Html_Element('div');
         $wrapDelete->addClass($this->cssClasses['containerDeleteClass']);

         $wrapDelete->addContent($this->deleteComponent->control());
         $wrapDelete->addContent($this->deleteComponent->label(null, true));
         $container->addContent($wrapDelete);
      }

      $mianContainer->addContent($container);

      if ($renderKey == null) {
         $this->renderedId++;
      }

      return $mianContainer;
   }

   public function setImage($imagepath)
   {
      $this->setValues($imagepath);
   }

   public function setAllowDelete($allow = true)
   {
      if ($allow) {
         $this->deleteComponent = new Form_Element_Checkbox('_delete', $this->tr('Smazat uložený obrázek'));
         $this->deleteComponent->html()->addClass('image-selector-checkbox');
      } else {
         $this->deleteComponent = false;
      }
   }

//   public function setValues($values, $key = null)
//   {
//      $this->currentImage = $values;
//   }

   public function getImage()
   {
      return $this->getValues();
   }

   public function getValues($key = null)
   {
//      var_dump('get values', $this);

//      die;

      return parent::getValues($key);
   }

   public function scripts($renderKey = null)
   {
      $tpl = new Template(new Url_Link());
      $tpl->addFile('js://engine:components/form/imageselector.js');
   }

}
