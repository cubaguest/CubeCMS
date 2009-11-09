<?php
/*
 * Třída modelu s detailem galerie
 */
class PhotoGalery_Model_Images extends Model_PDO {
   const DB_TABLE = 'photogalery_images';
	/**
	 * Názvy sloupců v databázi pro tabulku s obrázky
	 * @var string
	 */
	const COLUMN_ID 					= 'id_photo';
	const COLUMN_ID_CAT           = 'id_category';
	const COLUMN_NAME             = 'name';
	const COLUMN_DESC             = 'desc';
	const COLUMN_TIME_EDIT 			= 'edit_time';
	const COLUMN_FILE 				= 'file';
	const COLUMN_ORDER 				= 'ord';

	public function getImages($idCat) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID_CAT." = :idcat)"
          ." ORDER BY ".self::COLUMN_ORDER);

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   public function saveImage($idCat, $file = null, $name = null, $desc = null, $ord = '0', $idImage = null) {
      // globalní prvky
      $this->setIUValues(array(self::COLUMN_TIME_EDIT => time(),
                               self::COLUMN_ID_CAT => $idCat,
                               self::COLUMN_ORDER => $ord));

      if($file != null){
         $this->setIUValues(array(self::COLUMN_FILE => $file));
      }

      if($name != null){
         //vatvoření pole s popisky
         if(!is_array($name)){
            $langs = Locale::getAppLangs();
            $names = array();
            foreach ($langs as $l) {
               $names[$l]=$name;
            }
            $name=$names;
         }
         $this->setIUValues(array(self::COLUMN_NAME => $name));
      }

      if($desc != null){
         $this->setIUValues(array(self::COLUMN_DESC => $desc));
      }

      $dbc = new Db_PDO();

      if($idImage === null){
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

   /**
    * Metoda smaže zadaný obrázek
    * @param integer $idImg
    * @return bool
    */
   public function deleteImage($idImg) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $idImg, PDO::PARAM_INT);
      return $dbst->execute();
   }
	
}

?>