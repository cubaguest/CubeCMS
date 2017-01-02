<?php
class Form_Element_ImageSelector extends Form_Element_File {
   
   protected $maxFileSize = 2097152;
   
   protected $inputDir;
   protected $imagesKey = null;
   
   protected $imageSelector;
   protected $images = array();
   
   protected $imageIsUplaoded = false;


   public function __construct($name, $label = null, $prefix = null)
   {
      parent::__construct($name, $label, $prefix);
      $this->setMultiple(false);
      $this->cssClasses['containerClass'] = 'image-selector';
      $this->cssClasses['containerElementsClass'] = 'image-selector-inputs';
      
      $this->imageSelector = new Form_Element_Select('_selector', $this->tr('Uložené'));
      $this->imageSelector->addOption($this->tr('Žádný'), null);
      $this->imageSelector->html()->addClass('image-selector-select');
      $this->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
   }
   
   public function populate()
   {
      parent::populate();
      $this->imageSelector->populate();
      if($this->getValues()){
         $this->imageIsUplaoded = true;
      }
      if(!$this->getValues() && $this->imageSelector->getValues()){
         $finfo = finfo_open(FILEINFO_MIME_TYPE);
         $fileInfo = array (
            'name' => $this->imageSelector->getValues(),
            'path' => new FS_Dir($this->uploadDir),
            'size' => filesize($this->uploadDir.$this->imageSelector->getValues()),
            'mime' => finfo_file($finfo, $this->uploadDir.$this->imageSelector->getValues()),
            'type' => finfo_file($finfo, $this->uploadDir.$this->imageSelector->getValues()).';',
            'extension' => pathinfo($this->uploadDir.$this->imageSelector->getValues(), PATHINFO_EXTENSION),
         );
         $this->setFilteredValues($fileInfo);
         $this->filesMoved = true; // pokud byl již validován
      }
   }
   
   protected function loadStoredImages()
   {
      // image selector
      if(file_exists($this->uploadDir)){
         $dirIterator = new DirectoryIterator($this->uploadDir);
         foreach ($dirIterator as $item) {
            if($item->isDir() OR $item->isDot()) {
               continue;
            }
            // orpavdu tady?
            $this->imageSelector->addOption($item->getFilename(), $item->getFilename());
            
            $this->images[$item->getFilename()] = $item->getFilename();
         }
      }
   }
   
   public function setMaxFileSize($sizeBytes)
   {
      $this->maxFileSize = $sizeBytes;
   }
   
   /**
    * Metoda nastaví adresář pro nahrání souboru
    * @param string $dir -- adresář
    * @return Form_Element_File
    */
   public function setUploadDir($dir) {
      $_this = parent::setUploadDir($dir);
      $this->loadStoredImages();
      return $_this;
   }
   
   public function setPrefix($prefix)
   {
      parent::setPrefix($prefix);
      $this->imageSelector->setPrefix($this->getName());
   }
   
   
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $this->html()->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }
      // tady bude if při multilang
      $this->html()->setAttrib('type', 'file');
      
      $mianContainer = new Html_Element('div');
      $mianContainer
          ->setAttrib('id', 'image-selector-'.$this->getName().'_'.$rKey)
          ->addClass($this->cssClasses['containerClass'])
//          ->addClass('input-group')
          ->addClass('clearfix');
      
      // náhled
      $previewBox = new Html_Element('div');
      $previewBox->addClass('image-selector-img-box');
      $preview = new Html_Element('img');
      $preview
         ->setAttrib('data-emptysrc', Utils_Url::pathToSystemUrl(AppCore::getAppLibDir()).Template::IMAGES_DIR."/no_image.png")
         ->setAttrib('data-targetpath', Utils_Url::pathToSystemUrl($this->uploadDir))
         ->setAttrib('alt', "");
      if($this->imageSelector->getValues() != null){
         $preview->setAttrib('src', Utils_Url::pathToSystemUrl($this->uploadDir).$this->imageSelector->getValues());
      } else {
         $preview->setAttrib('src', Utils_Url::pathToSystemUrl(AppCore::getAppLibDir()).Template::IMAGES_DIR."/no_image.png");
      }
      $previewBox->addContent((string)$preview);
      
      $mianContainer->addContent($previewBox);
      
      // inputy
      $container = clone $this->containerElement;
      $container
          ->addClass($this->cssClasses['containerElementsClass'])
          ;

      $this->html()->setAttrib('name', $this->getName());
      
      $this->html()->setAttrib('id', $this->getName().'_'.$rKey);

      $container->addContent($this->html());
      $this->loadStoredImages();
      $container->addContent($this->imageSelector->control());
      
      // tlačítko na otevření galerie
      $galleryButton = new Html_Element('a', $this->tr('z galerie'));
      $galleryButton
          ->setAttrib('href', '#modal-image-selector-'.$this->getName().'_'.$rKey)
          ->addClass('image-selector-gallery-button')
          ->addClass('modal-open-button')
          ->setAttrib('data-modal', '#modal-image-selector-'.$this->getName().'_'.$rKey)
          ->addClass('btn')->addClass('btn-primary')->addClass('btn-small')
          ;
      $container->addContent($galleryButton);
      $mianContainer->addContent($container);
      
      $mianContainer->addContent($this->createGalleryContent($rKey));

      
      if($renderKey == null){
         $this->renderedId++;
      }

      return $mianContainer;
   }
   
   protected function createGalleryContent($rKey)
   {
      $tpl = new Template(new Url_Link());
      $tpl->rKey = $rKey;
      $tpl->elementName = $this->getName();
      $tpl->selected = $this->imageSelector->getvalues();
      $tpl->images = $this->images;
      $tpl->imgurl = Utils_Url::pathToSystemUrl($this->uploadDir);
      $tpl->addFile('tpl://engine:components/form/imageselector.phtml');
      return (string)$tpl;
   }
   
   public function setImages($images)
   {
      
   }
   
   public function isUploadedImage()
   {
      return $this->imageIsUplaoded;
   }
   
   public function setValues($values, $key = null)
   {
      $this->imageSelector->setValues($values);
   }
   
   /**
    * Prvek s výběrem obrázků
    * @return Form_Element_Select
    */
   public function getSelector()
   {
      return $this->imageSelector;
   }
   
   public function getImage()
   {
      
   }
   
   public function scripts($renderKey = null)
   {
      $tpl = new Template(new Url_Link());
      $tpl->addFile('js://engine:components/form/imageselector.js');
      
//      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
//      $str = parent::scripts($rKey);
//      $str .= str_replace(
//          array(
//             '__KEY__', 
//             '__ENAME__', 
//          ), 
//          array(
//            $rKey-1,
//            $this->getName(),
//          ), 
//          file_get_contents(AppCore::getAppLibDir().Template::JAVASCRIPTS_DIR.DIRECTORY_SEPARATOR
//              .'components'.DIRECTORY_SEPARATOR.'form'.DIRECTORY_SEPARATOR.'imageselector.js'));
//      return $str;
   }
}
