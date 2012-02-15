<?php
/**
 * Třída pro komnponenty Wysiwing editoru TinyMCE
 */
class Component_TinyMCE_List_Categories extends Component_TinyMCE_List {
   protected $filesFilter = '(?:doc|dot|docx|dotx|xls|xlt|xlsx|xltx|ppt|pot|pptx|potx|odf|otf|ods|ots|odp|otp|zip|rar|pdf)';

   protected function  loadItems() {
      $model = new Model_Category();
      
      $cats = $model->onlyWithAccess()
            ->order(array(Model_Category::COLUMN_NAME => Model_ORM::ORDER_ASC))->records();

      $link = new Url_Link(true);
      foreach($cats as $cat) {
         $this->addItem($this->tr('Kategorie: ').$cat->{Model_Category::COLUMN_NAME}, str_replace(Url_Link::getMainWebDir(), "/", (string)$link->category($cat->{Model_Category::COLUMN_URLKEY})) );
      }
   }

}
?>
