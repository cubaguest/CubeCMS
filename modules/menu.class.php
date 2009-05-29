<?php
/**
 * Uživatelská třída menu, načítá, vytváří a organizuje rozložení menu pro výpis
 * v šabloně
 */

class Menu extends MainMenu {
   public function createMenu() {
      $categoryArray = array();
      $sectionArray = array();
      $oldIdSection = null;

      foreach ($this->menuArray as $index => $item) {
         $item["url"]=$this->getLink()->category($item[Category::COLUMN_CAT_LABEL],
            $item[Category::COLUMN_CAT_ID]);
         if($item["id_section"] != $oldIdSection){
            $oldIdSection = $item["id_section"];
            $categoryArray[$oldIdSection] = array();
            array_push($categoryArray[$oldIdSection], $item);
            $sectionArray[$item["id_section"]] = $item;
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
      $this->template()->setVar("SECTIONS_ARRAY", $sectionArray);
      $this->template()->setVar("CATEGORY_ARRAY", $categoryArray);
   }
}
?>