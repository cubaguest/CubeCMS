<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class Text_Model extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'texts';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_text';
   const COLUMN_ID_CATEGORY = 'id_item';
   const COLUMN_ID_USER_EDIT = 'id_user';
   const COLUMN_SUBKEY = 'subkey';
   const COLUMN_CHANGED_TIME = 'changed';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_LABEL = 'label';
   const COLUMN_DATA = 'data';

   const DEFAULT_SUBKEY = 'nokey';

   /**
    * Pole s hodnotami pro převod z jazykového pole na řetězec
    * @var array
    * @todo odstranit
    */
   private $insUpdtValues = array();
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_texts');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER_EDIT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
//      $this->addColumn(self::COLUMN_ID_USER_LAST_EDIT, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 1));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_SUBKEY, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => 'nokey'));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(300)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CHANGED_TIME, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_DATA, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
//      $this->addColumn(self::COLUMN_CHANGED_TIME, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
//      $this->addColumn(self::COLUMN_SHOWED, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_CAT_ID);
      $this->addForeignKey(self::COLUMN_ID_USER_EDIT, 'Model_Users', Model_Users::COLUMN_ID);
//      $this->addForeignKey(self::COLUMN_ID_USER_LAST_EDIT, 'Model_Users', Model_Users::COLUMN_ID);
//      $this->addRelatioOneToMany(self::COLUMN_ID, 'Text_Model_PrivateUsers', Text_Model_PrivateUsers::COLUMN_T_H_U_ID_TEXT);
   }
   
   /**
    * Metoda provede načtení textu z db podle kategorie a subklíče
    *
    * @return string -- načtený text
    */
   public function getText($idCat, $subkey = self::DEFAULT_SUBKEY) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS text
             WHERE (text.".self::COLUMN_ID_CATEGORY." = :idc AND text.".self::COLUMN_SUBKEY." = :subkey)");
      $dbst->bindValue(':subkey', $subkey, PDO::PARAM_STR);
      $dbst->bindValue(':idc', $idCat, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();
   }

   /**
    * Metoda provede načtení textu z db podle id
    *
    * @return string -- načtený text
    */
   public function getTextId($id, $subkey = self::DEFAULT_SUBKEY) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS text
             WHERE (text.".self::COLUMN_ID." = :id AND text.".self::COLUMN_SUBKEY." = :subkey)");
      $dbst->bindValue(':subkey', $subkey, PDO::PARAM_STR);
      $dbst->bindValue(':id', $id, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();
   }

   /**
    * Metoda ukloží zadaný text do db
    * @param <type> $texts
    * @param <type> $label
    * @param <type> $idCat
    * @param <type> $subKey
    * @param <type> $id
    * @return <type>
    */
   public function saveText($texts, $label, $idCat, $subKey = self::DEFAULT_SUBKEY, $id = null) {
      // zjištění jestli existuje záznam
      // globalní prvky
      $dbc = new Db_PDO();
      $this->setIUValues(array(self::COLUMN_TEXT => $texts,
          self::COLUMN_TEXT_CLEAR => vve_strip_tags($texts)));
      if ($label !== null) {
          $this->setIUValues(array(self::COLUMN_LABEL => $label));
      }
      
      if ($id === null) {
         $dbst = $dbc->prepare("SELECT * FROM " . Db_PDO::table(self::DB_TABLE)
                 . " WHERE (" . self::COLUMN_ID_CATEGORY . " = :idCat AND "
                 . self::COLUMN_SUBKEY . " = :subkey)");
         $dbst->bindValue(':subkey', $subKey, PDO::PARAM_STR);
         $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
         $dbst->execute();
         $data = $dbst->fetchObject();

//         var_dump($data);flush();exit();

         $count = $dbst->rowCount();

         

         if ($count != 0) {
            // je už uloženo
            $dbst = $dbc->prepare("UPDATE " . Db_PDO::table(self::DB_TABLE)
                            . " SET " . $this->getUpdateValues()
                            . " WHERE (" . self::COLUMN_ID_CATEGORY . " = :idCat AND " . self::COLUMN_SUBKEY . " = :subkey)");
            $dbst->execute(array(':idCat' => $idCat, ':subkey' => $subKey));
            $id = $data->{self::COLUMN_ID};
         } else {
            // není uloženo
            $this->setIUValues(array(self::COLUMN_ID_CATEGORY => $idCat, self::COLUMN_SUBKEY => $subKey));
            $dbc->query("INSERT INTO " . Db_PDO::table(self::DB_TABLE)
                    . " " . $this->getInsertLabels() . " VALUES " . $this->getInsertValues());
            $id = $dbc->lastInsertId();
         }
      } else {
         // je už uloženo
         $dbst = $dbc->prepare("UPDATE " . Db_PDO::table(self::DB_TABLE)
                         . " SET " . $this->getUpdateValues()
                         . " WHERE (" . self::COLUMN_ID . " = :id AND " . self::COLUMN_SUBKEY . " = :subkey)");
         $dbst->execute(array(':id' => (int)$id, ':subkey' => $subKey));
         $id = $dbc->lastInsertId();
      }

      return $id;
   }

   public function getLastChange($idCat, $subKey = self::DEFAULT_SUBKEY) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_CHANGED_TIME." AS tm FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID_CATEGORY." = :idcategory AND ".self::COLUMN_SUBKEY." = :subkey)");
//      $dbst->bindParam(':idcategory', $idCat);
      $dbst->execute(array(':idcategory' => $idCat, ':subkey' => $subKey));
      $fetch = $dbst->fetchObject();
      if($fetch == false) {
         return false;
      } else {
         return $fetch->tm;
      }
   }
   
   /* tohle odstranit */
   
   /**
    * Metoda nastaví pole modelu pro vytvoření řetězců pro insert update
    * @param array $columns -- pole s názvy sloupců a hodotami
    * @param char $separator -- oddělovač mezi názvy sloupců pokud jsou v poli (option default: '_')
    * @deprecated není nutný, je tu kvůli starým metodám
    */
   protected function setIUValues($columns, $separator = '_', $clPrefix = null) {
      foreach ($columns as $clKey => $clVal) {
         if(!is_null($clPrefix)) {
            $prefix = $clPrefix.$separator;
         } else {
            $prefix = null;
         }
         if(is_array($clVal)) {
            $this->setIUValues($clVal, $separator, $prefix.$clKey);
         } else {
            $this->insUpdtValues[$prefix.$clKey] = $clVal;
         }
      }
   }
   
   /**
    * Metoda vrací řetězec pro příkaz update
    * @return string
    */
   public function getUpdateValues() {
      $pdo = new Db_PDO();//`label_cs`= 'Saul Griffith's lofty',
      $returnStr = null;
      //      var_dump($this->insUpdtValues);
      foreach ($this->insUpdtValues as $key => $variable) {
         //         $returnStr .= '`'.$key.'` = '.$pdo->quote($variable).", ";
         if($variable == null) {
            $var = 'NULL';
         } else {
            $var = $pdo->quote($variable);
         }
         $returnStr .= $key.' = '.$var.", ";
      };
      return substr($returnStr, 0, strlen($returnStr)-2);
   }
   
   /**
    * Metoda vrací řetězec s názvy sloupců pro vložení do insertu
    * @return string
    */
   public function getInsertLabels($separator = '_') {
      $returnStr = "(";
      foreach (array_keys($this->insUpdtValues) as $variable) {
         //         $returnStr .= '´'.$variable.'´, ';
         $returnStr .= $variable.', ';
      };
      return substr($returnStr, 0, strlen($returnStr)-2).")";
   }
   
    /**
    * Metoda vrací řetězec s názvy sloupců pro vložení do insertu
    * @return string
    */
   public function getInsertValues() {
      $pdo = new Db_PDO();
      $returnStr = "(";
      foreach (array_values($this->insUpdtValues) as $variable) {
         if(is_bool($variable) AND $variable) {
            $returnStr .= '1, ';
         } else if(is_bool($variable) AND !$variable) {
            $returnStr .= '0, ';
         } else if($variable == null OR $variable == '') {
            $returnStr .= "NULL, ";
         } else {
            $returnStr .= $pdo->quote($variable).", ";
         }
      };
      return substr($returnStr, 0, strlen($returnStr)-2).")";
   }
}

?>