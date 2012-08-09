<?php
/*
 * Třída modelu s detailem galerie
*/
class PhotoGalery_Model_Images extends Model_ORM {
   const DB_TABLE = 'photogalery_images';
   /**
    * Názvy sloupců v databázi pro tabulku s obrázky
    * @var string
    */
   const COLUMN_ID 					= 'id_photo';
   const COLUMN_ID_CAT           = 'id_category';
   const COLUMN_ID_ART           = 'id_article';
   const COLUMN_NAME             = 'name';
   const COLUMN_DESC             = 'desc';
   const COLUMN_TIME_EDIT 			= 'edit_time';
   const COLUMN_FILE 				= 'file';
   const COLUMN_ORDER 				= 'ord';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ph_imgs');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_ART, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(300)', 'nn' => true, 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 
         'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_DESC, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_TIME_EDIT, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
   }
   
   /**
    *
    * @param <type> $idCat
    * @param <type> $idArt
    * @param <type> $num
    * @return PDOStatement
    */
   public function getImages($idCat, $idArt, $num = 0) {
      $dbc = Db_PDO::getInstance();
      $limit = null;
      if($num != 0) {
         $limit = " LIMIT 0,".(int)$num;
      }
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID_CAT." = :idcat) AND (".self::COLUMN_ID_ART." = :idart)"
              ." ORDER BY ".self::COLUMN_ORDER." ASC"
              .$limit);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->bindParam(':idart', $idArt, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   public function getImage($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID." = :idimage)");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindParam(':idimage', $id, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst->fetch();
   }

   /**
    * Metoda vrací náhodný obrázek k článku
    * @param <type> $idCat
    * @param <type> $idArt
    * @return <type>
    */
   public function getRandImage($idCat, $idArt) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID_ART." = :idart) AND (".self::COLUMN_ID_CAT." = :idcat)"
              ." ORDER BY RAND() LIMIT 1");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->bindParam(':idart', $idArt, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst->fetch();
   }

   public function saveImage($idCat, $idArticle, $file = null, $name = null, $desc = null, $ord = '0', $idImage = null) {
      // globalní prvky
      $this->setIUValues(array(self::COLUMN_ID_CAT => $idCat,
              self::COLUMN_ID_ART => $idArticle,
              self::COLUMN_ORDER => $ord));

      if($file != null) {
         $this->setIUValues(array(self::COLUMN_FILE => $file));
      }

      if($name != null) {
         //vatvoření pole s popisky
         if(!is_array($name)) {
            $langs = Locales::getAppLangs();
            $names = array();
            foreach ($langs as $l) {
               $names[$l]=$name;
            }
            $name=$names;
         }
         $this->setIUValues(array(self::COLUMN_NAME => $name));
      }

      if($desc != null) {
         $this->setIUValues(array(self::COLUMN_DESC => $desc));
      }

      $dbc = Db_PDO::getInstance();

      if($idImage === null) {
         // provádí se insert
         $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());
         return $dbc->lastInsertId();
      } else {
         // provádí se update
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".$this->getUpdateValues()
                 ." WHERE ".self::COLUMN_ID." = :id");
         $dbst->bindParam(':id', $idImage, PDO::PARAM_INT);
         return $dbst->execute();
      }
   }

   public function setPosition($id, $pos){
      $dbc = Db_PDO::getInstance();
      // provádí se update
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_ORDER.' = :pos'
                 ." WHERE ".self::COLUMN_ID." = :id");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      $dbst->bindParam(':pos', $pos, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda smaže zadaný obrázek
    * @param integer $idImg
    * @return bool
    */
   public function deleteImage($idImg) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $idImg, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function getCountImages($idCat, $idArt) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(PhotoGalery_Model_Images::DB_TABLE)
              ." WHERE (".PhotoGalery_Model_Images::COLUMN_ID_CAT ." = '".$idCat."')"
              ." AND (".PhotoGalery_Model_Images::COLUMN_ID_ART." = '".$idArt."')");
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda nastaví změnu obrázku
    * @param int $id -- id obrázku
    * @todo nepoužito (ověřit jak to dělat lépe)
    */
   public function setLastChange($idImage) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
              ." SET `".self::COLUMN_EDIT_TIME."` = NOW()"
              ." WHERE (".self::COLUMN_ID." = :idimage)");
      $dbst->bindParam(':idimage', $idImage, PDO::PARAM_INT);
      return $dbst->execute();
   }

   
   /* compatibility */
   /**
    * Metoda nastaví pole modelu pro vytvoření řetězců pro insert update
    * @param array $columns -- pole s názvy sloupců a hodotami
    * @param char $separator -- oddělovač mezi názvy sloupců pokud jsou v poli (option default: '_')
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
    * Metoda vrací řetězec pro příkaz update
    * @return string
    */
   public function getUpdateValues() {
      $pdo = Db_PDO::getInstance();//`label_cs`= 'Saul Griffith's lofty',
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
   public function getInsertValues() {
      $pdo = Db_PDO::getInstance();
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