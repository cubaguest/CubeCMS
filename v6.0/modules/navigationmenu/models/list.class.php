<?php
/*
 * Třída modelu s listem Novinek
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class NavigationMenu_Models_List extends Model_PDO {
	const DB_TABLE = 'navigation_panel';
   
   const COL_ID = 'id_link';
   const COL_URL = 'url';
   const COL_NAME = 'name';
   const COL_ICON = 'icon';
   const COL_TYPE = 'type';
   const COL_INDEX = 'indexing';
   const COL_PARAMS = 'params';
   const COL_ORDER = 'ord';

   public function getList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
          ." ORDER BY ".self::COL_TYPE.",".self::COL_ORDER);

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function getSubdomainsList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE ".self::COL_TYPE." = 'subdomain'"
          ." ORDER BY ".self::COL_TYPE.",".self::COL_ORDER);

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function getProjectsList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE ".self::COL_TYPE." = 'project'"
          ." ORDER BY ".self::COL_TYPE.",".self::COL_ORDER);

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function saveLink($name,$url,$icon = null,$type = 'subdomain', $order = 100, $params = null, $id = null){
      $dbc = new Db_PDO();
      if($id === null) {
      // nový záznam
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." (`".self::COL_NAME."`, `".self::COL_ICON."`, `".self::COL_URL."`,
        `".self::COL_TYPE."`,`".self::COL_PARAMS."`,`".self::COL_ORDER."`) VALUES (:name, :icon, :urllink, :type, :params, :ord)");
         $dbst->bindValue(':icon', $icon);
      } else {
      // existující záznam
         $sqlicon = null;
         if($icon != null){
            $sqlicon = "`".self::COL_ICON."` = ".$dbc->quote($icon).",";
         }

         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE). " SET"
                ." `".self::COL_NAME."` = :name,".$sqlicon
                ." `".self::COL_URL."` = :urllink, "
                ." `".self::COL_TYPE."` = :type, `".self::COL_PARAMS."` = :params,"
                ." `".self::COL_ORDER."` = :ord"
                ." WHERE (".self::COL_ID." = :idlink)");
         $dbst->bindValue(':idlink', $id);
      }

      $dbst->bindValue(':name', $name);
      $dbst->bindValue(':urllink', $url);
      $dbst->bindValue(':type', $type);
      $dbst->bindValue(':params', $params);
      $dbst->bindValue(':ord', $order);

      $dbst->execute();
      return $dbst;
   }

   public function getItem($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
             ." WHERE (".self::COL_ID." = :iditem)");

      $dbst->execute(array(':iditem' => (int)$id));

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda pro vymazání odkazu
    * @param int $id -- id odkazu
    */
   public function deleteItem($id) {
      $dbc = new Db_PDO();
      return $dbc->query("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          . " WHERE ".self::COL_ID." = ".$dbc->quote((int)$id));
   }

}

?>