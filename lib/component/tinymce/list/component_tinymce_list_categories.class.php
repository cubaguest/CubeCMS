<?php
/**
 * Třída pro komnponenty Wysiwing editoru TinyMCE
 */
class Component_TinyMCE_List_Categories extends Component_TinyMCE_List {
   protected $filesFilter = '(?:doc|dot|docx|dotx|xls|xlt|xlsx|xltx|ppt|pot|pptx|potx|odf|otf|ods|ots|odp|otp|zip|rar|pdf)';

   protected function  loadItems() {
      $model = new Model_Category();

      $cats = $model->join(Model_Category::COLUMN_CAT_ID, array('t_r' => 'Model_Rights'), null,
                  array(Model_Rights::COLUMN_ID_GROUP, Model_Rights::COLUMN_RIGHT))
               ->where('t_r.' . Model_Rights::COLUMN_ID_GROUP . ' = :idgrp'
                  .' AND t_r.' . Model_Rights::COLUMN_RIGHT . " LIKE 'r__'"
                  .' AND '.Model_Category::COLUMN_URLKEY.' IS NOT NULL',
                  array("idgrp" => Auth::getGroupId()))
            ->order(array(Model_Category::COLUMN_NAME => Model_ORM::ORDER_ASC))->records();

      $link = new Url_Link(true);

      foreach($cats as $cat) {
         $this->addItem($this->tr('Kategorie: ').$cat->{Model_Category::COLUMN_NAME}, (string)$link->category($cat->{Model_Category::COLUMN_URLKEY}));
      }
   }

}
?>
