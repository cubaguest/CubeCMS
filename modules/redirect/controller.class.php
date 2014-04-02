<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Redirect_Controller extends Controller {
   const DEFAULT_RESIRECT_CODE = 301;
   
   private $categoriesArray = array();
   
/**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $url = $this->category()->getParam('url', Url_Link::getMainWebDir());
      $category = Model_Category::getRecord((int)$this->category()->getParam('idcat', 0));

      if($category && !$category->isNew()){
         $url = $this->link(true)->category($category->{Model_Category::COLUMN_URLKEY});
      } else if(strpos($url, "http") !== 0){
         $url = Url_Link::getMainWebDir(). ( strpos($url,'/') === 0 ? substr($url, 1) : $url );
      }
      if($url != null){
         if(!$this->category()->getRights()->isControll() || Category::getSelectedCategory() instanceof Category_Admin){
            header('Location: '.$url, true, $this->category()->getParam('code', self::DEFAULT_RESIRECT_CODE));
            exit();
         } else {
            $this->view()->url = $url;
         }
      } else {
         $this->errMsg()->addMessage($this->tr('Zadanou kategorii nelze přeměrovat, protože není zadán odkaz přesměrování'));
      } 
   }

   /**
    *
    * @param <type> $settings
    * @param Form $form Metoda pro nastavení
    */
   protected function settings(&$settings, Form &$form) {
      $elemCats = new Form_Element_Select('category', $this->tr('Kategorie'));
      $this->createArray(Category_Structure::getStructure(Category_Structure::ALL));
      $elemCats->addOption('URL adresa níže', 0);
      foreach ($this->categoriesArray as $id => $name) {
         $elemCats->addOption($name, $id);
      }
      $form->addElement($elemCats,'basic');
      
      if(isset($settings['idcat'])) {
         $form->category->setValues($settings['idcat']);
      }
      
      $elemURL = new Form_Element_Text('url', 'Adresa pro přesměrování');
      $elemURL->setSubLabel($this->tr('Zadává se celá url adresa nebo jenom struktura (např. aktuality/aktualita/)'));
//      $elemURL->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemURL,'basic');

      if(isset($settings['url'])) {
         $form->url->setValues($settings['url']);
      }

      $elemCode = new Form_Element_Select('code', 'Typ přesměrování');
      $elemCode->setOptions(array('Trvalé' => 301, 'Dočasné' => 302));
      $form->addElement($elemCode,'basic');

      if(isset($settings['code'])) {
         $form->code->setValues($settings['code']);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['idcat'] = $form->category->getValues();
         $settings['url'] = $form->url->getValues();
         $settings['code'] = $form->code->getValues();
      }
   }
   
   private function createArray(Category_Structure $struct, $level = 0)
   {
      foreach ($struct as $item) {
         $this->categoriesArray[$item->getId()] = str_repeat('|…', $level).($level != 0 ? ' ' : null).$item->getCatObj()->getName().' '.$item->getCatObj()->getName(true);
         if(!$item->isEmpty()){
            $this->createArray($item, $level+1);
         }
      }
   }
}

?>