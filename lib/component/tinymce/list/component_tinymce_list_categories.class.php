<?php
/**
 * Třída pro výpis odkazů na kategorie včetně jazykových mutací
 */
class Component_TinyMCE_List_Categories extends Component_TinyMCE_List {

   protected function  loadItems() {
      $model = new Model_Category();
      
      $cats = $model->onlyWithAccess()
            ->order(array(Model_Category::COLUMN_NAME => Model_ORM::ORDER_ASC))->records();

      $linkObj = new Url_Link(true);
      foreach($cats as $cat) {
         
         foreach (Locales::getAppLangs() as $lang) {
            if($cat[Model_Category::COLUMN_URLKEY][$lang] == null){
               continue;
            }
            
            $label = Locales::isMultilang() 
               ? sprintf($this->tr('Stránka[%s]: %s'), strtoupper($lang), $cat[Model_Category::COLUMN_NAME][$lang] ) 
               : sprintf($this->tr('Stránka: %s'), $cat[Model_Category::COLUMN_NAME][$lang] );
         
            $link = (string)$linkObj
               ->lang($lang != Locales::getDefaultLang() ? $lang : null)
               ->category($cat[Model_Category::COLUMN_URLKEY][$lang]);
         
            $this->addItem($label, 
               str_replace(Url_Link::getMainWebDir(), "/", $link) );
         }
            
      }
   }

}
