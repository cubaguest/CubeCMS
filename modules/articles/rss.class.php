<?php
class Articles_Rss extends Rss {
   public function  runController() {
      $model = new Articles_Model();

      $externalCats = explode(';', $this->category()->getParam(Articles_Controller::PARAM_MOUNTED_CATS, "") );
      if(!empty($externalCats )) {
         $wCatPl = array(':pl_'.$this->category()->getId() => $this->category()->getId() );
         foreach ($externalCats as $externalCatID) {
            $wCatPl[':pl_'.$externalCatID] = $externalCatID;
         }

         // načtení oprávnění k připojeným kategoriím
         $modelCat = new Model_Category();
         if(Auth::isAdmin()){
            $cats = $modelCat->where(Model_Category::COLUMN_ID." IN (".implode(',', array_keys($wCatPl) ).")", $wCatPl, true)
               ->records();
         } else {
            $cats = $modelCat->onlyWithAccess()
               ->where(" AND ". Model_Category::COLUMN_ID." IN (".implode(',', array_keys($wCatPl) ).")", $wCatPl, true)
               ->records();
         }

         $allowedCatsIDSPL = array();
         foreach ($cats as $c) {
            $allowedCatsIDSPL[':pl_'.$c->{Model_Category::COLUMN_ID}] = $c->{Model_Category::COLUMN_ID};
         }

         $mWhereString = Articles_Model::COLUMN_ID_CATEGORY.' IN ('.implode(',',array_keys($allowedCatsIDSPL)).')';
         $mWhereBinds = $allowedCatsIDSPL;

         $model->joinFK(Articles_Model::COLUMN_ID_CATEGORY, array(
            'curlkey' => Model_Category::COLUMN_URLKEY, Model_Category::COLUMN_ID, Model_Category::COLUMN_NAME
         ), Model_ORM::JOIN_OUTER);

      } else {
         $mWhereString = Articles_Model::COLUMN_ID_CATEGORY.' = :idc';
         $mWhereBinds = array('idc' => $this->category()->getId());
      }

      $mWhereString .=
         // články který nejsou koncepty nebo je napsal uživatel
         " AND ".Articles_Model::COLUMN_CONCEPT.' = 0 '
         // články jsoupřidány po aktuálním času nebo je napsal uživatel
         .'AND '.Articles_Model::COLUMN_ADD_TIME.' <= NOW()'
         // kategorie a vyplněný urlkey
         .'AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL ';

      $records = $model->where($mWhereString, $mWhereBinds)
         ->order( array(Articles_Model::COLUMN_ADD_TIME => Model_ORM::ORDER_DESC) )
         ->limit(0, VVE_FEED_NUM)->records();

      foreach ($records as $record) {
         if((string)$record->{Articles_Model::COLUMN_ANNOTATION} != null){
            $text = (string)$record->{Articles_Model::COLUMN_ANNOTATION};
         } else {
            $text = (string)$record->{Articles_Model::COLUMN_TEXT};
         }

         $this->getRssComp()->addItem($record->{Articles_Model::COLUMN_NAME}, $text,
                 $this->link()->route('detail', array('urlkey' => $record->{Articles_Model::COLUMN_URLKEY})),
                 new DateTime($record->{Articles_Model::COLUMN_ADD_TIME}),
                 $record->{Model_Users::COLUMN_USERNAME}, null, null);
      }
   }
}
?>
