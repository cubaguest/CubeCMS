<?php
/**
 * Třída pro komnponenty Wysiwing editoru TinyMCE
 */
class Component_TinyMCE_TPLList {
   protected $tpls = array();

   public function addTpl($name, $link, $label = null) {
      $this->tpls[] = array('name' => $name, 'label' => $label, 'link' => (string)$link);
   }

   /**
    * Metoda pro vrácení seznamu šablon
    *
    * var tinyMCETemplateList = [
    * // Name, URL, Description
    * ["Simple snippet", "templates/snippet1.htm", "Simple HTML snippet."],
    * ["Layout", "templates/layout1.htm", "HTML Layout."]
    * ];
    * @see http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/template#Example_of_an_external_list
    */
   public function  __toString() {
      $cnt = ('var tinyMCETemplateList = ['."\n");
      
      $tplsStr = array();
      foreach ($this->tpls as $tpl) {
         $tplsStr[] = '["'.$tpl['name'].'","'.$tpl['link'].'","'.$tpl['label'].'"]';
      }
      $cnt .= implode(",\n", $tplsStr)."\n";
      //end
      $cnt .= ('];'."\n");
      return $cnt;
   }

}
?>
