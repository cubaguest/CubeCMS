<?php

class Form_Element_Image extends Form_Element_FileAdv {

   const DELETE_CURRENT_IMAGE = 1;

   protected $images = array();

   public function __construct($name, $label = null, $prefix = null)
   {
      parent::__construct($name, $label, $prefix);
      $this->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $this->cssClasses['containerClass'] = 'image-selector';
      $this->cssClasses['containerElementsClass'] = 'image-selector-inputs';
      $this->cssClasses['containerDeleteClass'] = 'image-selector-delete-checkbox';
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
      $file = $this->origFile;
      if ($file != null) {
         if (is_array($file)) {
            $preview->setAttrib('src', Utils_Url::pathToSystemUrl($file['path'] . $file['name']))
                    ->setAttrib('data-originalsrc', Utils_Url::pathToSystemUrl($file['path'] . $file['name']));
         } 
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

      if ($this->origFile != null && $this->deleteComponent) {
         $wrapDelete = new Html_Element('div');
         $wrapDelete->addClass($this->cssClasses['containerDeleteClass']);

         $wrapDelete->addContent($this->deleteComponent->control($rKey));
         $wrapDelete->addContent($this->deleteComponent->label($rKey, true));
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

   public function getImage()
   {
      return $this->getValues();
   }

   public function scripts($renderKey = null)
   {
      $tpl = new Template(new Url_Link());
      $tpl->addFile('js://engine:components/form/imageselector.js');
   }

   public function setAllowDelete($allow = true)
   {
      parent::setAllowDelete($allow);
      if ($this->deleteComponent) {
         $this->deleteComponent->setLabel($this->tr('Smazat uložený obrázek'));
      }
   }

}
