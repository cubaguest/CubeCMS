<?php
/**
 * Třída pro komnponenty Wysiwing editoru TinyMCE
 * Načtení šablon
 */
class Component_TinyMCE_TPLList_System extends Component_TinyMCE_TPLList {

   protected $tplGroup = Templates_Model::TEMPLATE_TYPE_TEXT;

   public function  __construct() {
      $this->loadList();
   }
   protected function  loadList() {
      // šablony z modulu
      $modelTpl = new Templates_Model();

      $tpllist = $modelTpl->getTemplates($this->tplGroup);
      if(!empty ($tpllist)){
         // link
         $link = new Url_Link_ModuleStatic(true);
         $link->module('templates')->action('template', 'html');
         foreach ($tpllist as $tpl) {
            $this->addTpl($tpl->{Templates_Model::COLUMN_NAME}, $link->param('id', $tpl->{Templates_Model::COLUMN_ID}), $tpl->{Templates_Model::COLUMN_DESC});
         }
      }
   }
}
?>
