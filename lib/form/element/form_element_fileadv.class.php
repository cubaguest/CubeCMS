<?php

/**
 * Třída pro obsluhu INPUT prvku typu FILE
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu FILE. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby. Obsahuje take funkce pro mazání původních souborů či jeho stažení
 *
 *
 * @copyright  	Copyright (c) 2016 Jakub Matas
 * @version    	$Id: $ VVE 8.3 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_FileAdv extends Form_Element_File {

   protected $inputDir;
   protected $imagesKey = null;

   /**
    *
    * @var Form_Element_Checkbox
    */
   protected $deleteComponent = false;
   
   /**
    *
    * @var File
    */
   protected $origFile = null;

   public function __construct($name, $label = null, $prefix = null)
   {
      parent::__construct($name, $label, $prefix);
      $this->setMultiple(false);
      $this->cssClasses['containerClass'] = 'file-selector';
      $this->cssClasses['containerElementsClass'] = 'file-selector-inputs';
      $this->cssClasses['containerDeleteClass'] = 'file-selector-delete-checkbox';
   }

   public function populate()
   {
//      $originalImage = $this->getUnfilteredValues();
//      $this->origFile = $originalImage;
      // pokud je komponenta pro smazání a je zároveň odeslána pro smazání, smaže se původní obrázek
      if ($this->deleteComponent instanceof Form_Element_Checkbox) {
         $this->deleteComponent->populate();
      }

      parent::populate();
      
      // nebyl nahrán žádný soubor a byl přiřazen starý
//      if($this->origFile && $this->getValues() == null){
//         var_dump($this->origFile);
//         $this->setValues($this->origFile);
//         $this->setFilteredValues($this->origFile);
//         var_dump($this->getValues());
        
//      }
// die;
      
//      var_dump('populate', $this->getName());
   }

   public function validate()
   {
      parent::validate();
      
      if($this->isPopulated() && $this->isValid()){
         // pokud je odesláno smazání a je původní nebo odeslán nový a je původní
         if( ($this->deleteComponent && $this->deleteComponent->getValues() == true && $this->origFile) 
                 || ( $this->origFile && $this->getValues() != null) ){
            $file = new File($this->origFile);
            if (is_file((string) $file) && $file->exist()) {
//               var_dump($file);die;
               $file->delete();
            }
            $this->origFile = false;
         }
         
         // pokud nebyl odeslán a je jenom původní obrázek
         if($this->getValues() == null){
            if($this->origFile) {
               $this->setValues($this->origFile);
               $this->setFilteredValues($this->origFile);
            } else {
               $this->setValues(null);
               $this->setFilteredValues(null);
            }
         }
      }
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
              ->setAttrib('id', 'file-selector-' . $this->getName() . '_' . $rKey)
              ->addClass($this->cssClasses['containerClass'])
              ->addClass('clearfix');


      // inputy
      $container = clone $this->containerElement;
      $container
              ->addClass($this->cssClasses['containerElementsClass'])
      ;

      $this->html()->setAttrib('name', $this->getName());

      $this->html()->setAttrib('id', $this->getName() . '_' . $rKey);

      $inputBox = new Html_Element('div');
      $inputBox->addClass('file-selector-input-box')->addClass('input-group');
      $inputBox->addContent($this->html());
      $inputBox->addContent('<a href="#" class="input-group-btn button-clear-file-selector"><span class="icon icon-remove"></span></a>');

      $container->addContent($inputBox);

      $file = $this->origFile;
      if ($file != null) {
         $previewBox = new Html_Element('div');
         $previewBox->addClass('file-selector-file-box');
         $preview = new Html_Element('a');
         $preview->setContent($file['name'])
                 ->setAttrib('href', Utils_Url::pathToSystemUrl($file['path'] . $file['name']))
                 ->setAttrib('target', '_blank');
         $size = $file['size'];
         
         $previewBox->addContent(sprintf($this->tr('Aktuálně uložený soubor: %s s velikostí: %s'), (string) $preview, Utils_String::createSizeString($size)));
         $container->addContent($previewBox);
      }


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

   public function setValues($values, $key = null)
   {
//      var_dump($values);
      if(is_string($values)){
         // hodnota obsahuje i cestu
         if (strpos($values, '/') !== false) {
            if (substr($values, 0, 1) != '/') {
               $values = AppCore::getAppWebDir() . $values;
            }
            $this->setUploadDir(dirname($values) . DIRECTORY_SEPARATOR);
            $filename = basename($values);
         } 
         // neobsahuje cestu, jenom název souboru, ale je zadán upload dir
         else if(strpos($values, '/') === false && (string)$this->getUploadDir() != null) {
            $values = (string)$this->getUploadDir().$values;
         }
         if(is_file($values)){
            $this->origFile = $this->createFileDataArray(
                    basename($values), 
                    dirname($values).DIRECTORY_SEPARATOR, 
                    filesize($values), 
                     $this->getMimeType($values), $values, 
                    $this->getMimeType($values),
                    pathinfo($values, PATHINFO_EXTENSION));
         }
      } else if($values instanceof File){
         $this->origFil = $this->createFileDataArray(
                 $values->getName(), 
                 $values->getPath(), 
                 $values->getSize(), 
                 $values->getMimeType(), 
                 (string)$values, 
                 $values->getMimeType(), 
                 $values->getExtension());
      } else if(is_array ($values)) {
         $this->origFile = $values;
      }
   }

   public function setAllowDelete($allow = true)
   {
      if ($allow) {
         $this->deleteComponent = new Form_Element_Checkbox($this->getName() . '_delete', $this->tr('Smazat uložený soubor'));
         $this->deleteComponent->html()->addClass('image-selector-checkbox');
      } else {
         $this->deleteComponent = false;
      }
   }

   public function getFile($key = null)
   {
      return parent::getValues($key);
   }

}
