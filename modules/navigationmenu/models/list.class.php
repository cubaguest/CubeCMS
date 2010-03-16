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
   const COL_FOLLOW = 'follow';
   const COL_PARAMS = 'params';
   const COL_ORDER = 'ord';
   const COL_NEW_WIN = 'newwin';

   public function getList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".$this->getDbTable()
          ." ORDER BY ".self::COL_TYPE.",".self::COL_ORDER." DESC");

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function getSubdomainsList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".$this->getDbTable()
          ." WHERE ".self::COL_TYPE." = 'subdomain'"
          ." ORDER BY ".self::COL_TYPE.",".self::COL_ORDER." DESC");

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function getProjectsList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".$this->getDbTable()
          ." WHERE ".self::COL_TYPE." = 'project'"
          ." ORDER BY ".self::COL_TYPE.",".self::COL_ORDER." DESC");

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function getGroupsList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".$this->getDbTable()
          ." WHERE ".self::COL_TYPE." = 'group'"
          ." ORDER BY ".self::COL_TYPE.",".self::COL_ORDER." DESC");

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function getPartnersList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".$this->getDbTable()
          ." WHERE ".self::COL_TYPE." = 'partner'"
          ." ORDER BY ".self::COL_TYPE.",".self::COL_ORDER." DESC");

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function saveLink($name,$url,$icon = null,$type = 'subdomain', $order = 100, 
           $params = null, $newWindow = 0, $follow = 1, $id = null){
      $dbc = new Db_PDO();
      if($id === null) {
      // nový záznam
         $dbst = $dbc->prepare("INSERT INTO ".$this->getDbTable()
             ." (`".self::COL_NAME."`, `".self::COL_ICON."`, `".self::COL_URL."`,
        `".self::COL_TYPE."`,`".self::COL_PARAMS."`,`".self::COL_ORDER."`,`".self::COL_NEW_WIN."`,`".self::COL_FOLLOW."`)"
                 ." VALUES (:name, :icon, :urllink, :type, :params, :ord, :newwin, :follow)");
         $dbst->bindValue(':icon', $icon);
      } else {
      // existující záznam
         $sqlicon = null;
         if($icon != null){
            $sqlicon = "`".self::COL_ICON."` = ".$dbc->quote($icon).",";
         }

         $dbst = $dbc->prepare("UPDATE ".$this->getDbTable(). " SET"
                ." `".self::COL_NAME."` = :name,".$sqlicon
                ." `".self::COL_URL."` = :urllink, "
                ." `".self::COL_TYPE."` = :type, `".self::COL_PARAMS."` = :params,"
                ." `".self::COL_ORDER."` = :ord,"
                ." `".self::COL_FOLLOW."` = :follow,"
                ." `".self::COL_NEW_WIN."` = :newwin"
                ." WHERE (".self::COL_ID." = :idlink)");
         $dbst->bindValue(':idlink', $id);
      }

      $dbst->bindValue(':name', $name, PDO::PARAM_STR);
      $dbst->bindValue(':urllink', $url, PDO::PARAM_STR);
      $dbst->bindValue(':type', $type, PDO::PARAM_STR);
      $dbst->bindValue(':params', $params, PDO::PARAM_STR);
      $dbst->bindValue(':ord', $order, PDO::PARAM_INT);
      $dbst->bindValue(':newwin', $newWindow, PDO::PARAM_BOOL);
      $dbst->bindValue(':follow', $follow, PDO::PARAM_BOOL);

      $dbst->execute();
      return $dbst;
   }

   public function getItem($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".$this->getDbTable()
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
      return $dbc->query("DELETE FROM ".$this->getDbTable()
          . " WHERE ".self::COL_ID." = ".$dbc->quote((int)$id));
   }

   private function getDbTable(){
      if(defined('VVE_NAVIGATION_MENU_TABLE') AND VVE_NAVIGATION_MENU_TABLE != null){
         return VVE_NAVIGATION_MENU_TABLE;
      } else {
         return Db_PDO::table(self::DB_TABLE);
      }
   }
}

?>