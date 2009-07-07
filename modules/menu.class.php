<?php
/**
 * Uživatelská třída menu, načítá, vytváří a organizuje rozložení menu pro výpis
 * v šabloně
 */

class Menu extends Menu_Main {
   public function createMenu() {
      $categoryArray = array();
      $sectionArray = array();
      $oldIdSection = null;

      foreach ($this->menuArray as $index => $item) {
         $item["url"]=$this->getLink()->category($item[Model_Category::COLUMN_CAT_LABEL],
            $item[Model_Category::COLUMN_CAT_ID]);
         if($item[Model_Category::COLUMN_CAT_SEC_ID] != $oldIdSection){
            $oldIdSection = $item[Model_Category::COLUMN_CAT_SEC_ID];
            $categoryArray[$oldIdSection] = array();
            array_push($categoryArray[$oldIdSection], $item);
            $sectionArray[$item[Model_Category::COLUMN_CAT_SEC_ID]] = $item;
         } else {
            array_push($categoryArray[$oldIdSection], $item);
         }
      }

      //		odstranění odkazu sekci s jednou kategorii
      foreach ($categoryArray as $key => $value) {
         if(sizeof($categoryArray[$key]) == 1){
            unset($categoryArray[$key]);
            $sectionArray[$key]["submenu"] = false;
         } else {
            $sectionArray[$key]["submenu"] = true;
         }
      }

      //		přiřazení do šablony
      $this->template()->addTplFile("menu.phtml", true);
      $this->template()->sectionsArray = $sectionArray;
      $this->template()->categoryArray = $categoryArray;
   }
}
?>