<?php

/**
 * Třída pro výpis odkazů na kategorie včetně jazykových mutací
 */
class Component_TinyMCE_List_Categories extends Component_TinyMCE_List {

   protected function loadItems()
   {
      $model = new Model_Category();


      $struct = Category_Structure::getStructure(Category_Structure::ALL)->getCategoryPaths(' > ', null, function(Category $cat) {
         return (string)$cat->getLink();
      });

      foreach ($struct as $link => $catName) {
         $label = Locales::isMultilang() ? 
                 sprintf($this->tr('Stránka[%s]: %s'), strtoupper(Locales::getLang()), $catName) 
                 : sprintf($this->tr('Stránka: %s'), $catName);
         $this->addItem($label, str_replace(Url_Link::getMainWebDir(), "/", $link));
      }
      
      if(Locales::isMultilang()){
         foreach (Locales::getAppLangs() as $lang) {
            if($lang == Locales::getLang()){
               continue;
            }

            $struct = Category_Structure::getStructure(Category_Structure::ALL)->getCategoryPaths(' > ', function(Category $cat) use ($lang) {
               $data = $cat->getDataObj();
               return ( isset($data[Model_Category::COLUMN_NAME][$lang]) && $data[Model_Category::COLUMN_NAME][$lang] != null) ? $data[Model_Category::COLUMN_NAME][$lang] : false;
            }, function(Category $cat) use ($lang) {
               $linkObj = new Url_Link(true);
               $data = $cat->getDataObj();
               if($data[Model_Category::COLUMN_URLKEY][$lang] == null){
                  return false;
               }
               return (string)$linkObj->lang($lang)->category($data[Model_Category::COLUMN_URLKEY][$lang]);
            });
            foreach ($struct as $link => $catName) {
               $label = sprintf($this->tr('Stránka[%s]: %s'), strtoupper($lang), $catName);
               $this->addItem($label, str_replace(Url_Link::getMainWebDir(), "/", $link));
            }
         }
      }
   }
}
