<?php
/**
 * Kontroler pro obsluhu fotogalerie
 *
 * Jedná se o jednoúrovňovou fotogalerii s textem
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author 		$Author: $ $Date:$
 *              $LastChangedBy: $ $LastChangedDate: $
 */

class PhotogaleryMed_Controller extends ArticlesWGal_Controller {
   const DEFAULT_IMAGES_IN_LIST = 4;
   
   public function init()
   {
      parent::init();
      $this->actionsLabels['main'] = $this->tr('Seznam galerií');
   }
   
   /**
    * Kontroler pro zobrazení fotogalerii
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      parent::mainController();
      // tady by mělo být načtení fotek
   }

   public function showController($urlkey) {
      $this->checkReadableRights();
      $this->setOption('deleteMsg', $this->tr('Galerie byla smazána'));
      $this->setOption('publicMsg', $this->tr('Galerie byla zveřejněna'));
      
      if(parent::showController($urlkey) === false) return false;
      
      if($this->view()->formDelete instanceof Form){
         $this->view()->formDelete->delete->setLabel($this->tr('Smazat galerii'));
      }
      if($this->view()->formPublic instanceof Form){
         $this->view()->formPublic->public->setLabel($this->tr('Zveřejnit galerii'));
      }
   }

   /**
    * Přidání galerie
    *
    * @todo  Zbytečně tady !!! proč se to dělá znovu a nedědí
    */
   public function addController() {
      $this->checkWritebleRights();
      $this->allowEmptyText = true;
      $this->setOption('actionAfterAdd', 'editphotos');

      parent::addController();
      $this->view()->form->save->setLabel(array($this->tr('Pokračovat'), $this->tr('Zrušit')));
   }

   /**
    * @todo  Zbytečně tady !!! proč se to dělá znove a nedědí
    */
   public function editController() {
      $this->allowEmptyText = true;
      parent::editController();
   }

   public function settings(&$settings,Form &$form) {
      parent::settings($settings, $form);

      $elemImgList = new Form_Element_Text('imagesinlist', 'Počet obrázků v seznamu');
      $elemImgList->setSubLabel('Výchozí: '.self::DEFAULT_IMAGES_IN_LIST.' obrázků');
      $elemImgList->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgList,'view');

      if(isset($settings['imagesinlist'])) {
         $form->imagesinlist->setValues($settings['imagesinlist']);
      }
      
      if($form->isValid()){
         $settings['imagesinlist'] = $form->imagesinlist->getValues();
      }
   }
}
?>