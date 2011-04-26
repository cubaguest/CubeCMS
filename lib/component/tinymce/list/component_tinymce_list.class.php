<?php
/**
 * Třída pro komnponenty Wysiwing editoru TinyMCE
 */
class Component_TinyMCE_List extends TrObject {
   const LIST_TYPE_LINK = "Link";
   const LIST_TYPE_IMAGE = "Image";
   const LIST_TYPE_MEDIA = "Media";
   const LIST_TYPE_TEMPLATE = "Template";


   protected $items = array();

   public function __construct()
   {
      $this->loadItems();
   }
   
   public function getItems()
   {
      return $this->items;
   }

   protected function loadItems(){}

   public function addItem($name, $link, $label = null) {
      $this->items[] = array('name' => $name, 'link' => (string)$link, 'label' => $label);
   }

   /**
    * Metoda pro vrácení seznamu odkazu
    * @param typ seznamu (konstanty třídy)
    *
    * var tinyMCETemplateList = [
    * // Name, URL, Description
    * ["Simple snippet", "templates/snippet1.htm", "Simple HTML snippet."],
    * ["Layout", "templates/layout1.htm", "HTML Layout."]
    * ];
    * @see http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/template#Example_of_an_external_list
    */
   public static function tinyMceString($items, $type = self::LIST_TYPE_LINK) {
      $itemsArr = array();
      foreach ($items as $item) {
         $str = '"'.$item['name'].'","'.$item['link'].'"';
         if($item['label'] != null){
            $str .= ',"'.$item['label'].'"';
         }
         $itemsArr[] = '['.$str.']';
      }
      //end
      return 'var tinyMCE'.$type.'List = new Array ('."\n" . implode(",\n", $itemsArr) . "\n" . ');' . "\n";
   }
}
?>
