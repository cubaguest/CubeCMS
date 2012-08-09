<?php
/**
 * Třída Modelu pro práci s panely modulů.
 * Třída, která umožňuje pracovet s modely panelů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s panely
 */

class Model_Panel extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'panels';

   /**
    * Konstanty s názzvy sloupců
    */
   const COLUMN_ID = 'id_panel';
//    const COLUMN_ID_CAT = 'id_category';
   const COLUMN_ID_CAT = 'id_cat';
   const COLUMN_ID_SHOW_CAT = 'id_show_cat';
   const COLUMN_NAME = 'pname';
   const COLUMN_ORDER = 'porder';
   const COLUMN_POSITION    = 'position';
   const COLUMN_PARAMS    = 'pparams';
//   const COLUMN_TPL    = 'template';
   const COLUMN_ICON    = 'picon';
   const COLUMN_BACK_IMAGE    = 'pbackground';
   const COLUMN_IMAGE    = 'pbackground';
   const COLUMN_FORCE_GLOBAL    = 'panel_force_global';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_panels');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_SHOW_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_POSITION, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_PARAMS, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_ICON, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null, 'aliasFor' => 'icon'));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null, 'aliasFor' => 'background'));
      $this->addColumn(self::COLUMN_FORCE_GLOBAL, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Category', Model_Category::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_SHOW_CAT, 'Model_Category', Model_Category::COLUMN_ID);
   }

   public function savePanelPos($idPanel, $newPos) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
              ." SET ".self::COLUMN_ORDER." = :newpos"
              ." WHERE ".self::COLUMN_ID." = :id");

      return $dbst->execute(array(':id' => $idPanel, ':newpos' => $newPos));
   }

   /**
    * Metoda načte všechny kategorie
    * @param bool $allCategories -- jestli mají být vráceny všechny kategorie
    * @param bool $withRights -- jestli mají být načtený pouze kategorie na které ma uživatel práva
    * @return PDOStatement -- objekt s daty
    */
   public function getPanelsList($idCat = 0, $withRights = false) {
      $dbc = Db_PDO::getInstance();

      if($withRights) {
         $dbst = $dbc->prepare("SELECT *, panel.icon AS picon FROM ".Db_PDO::table(self::DB_TABLE)." AS panel"
                 ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON cat."
                 .Model_Category::COLUMN_CAT_ID." = panel.".self::COLUMN_ID_CAT
                 ." JOIN ".Model_Rights::getRightsTable()." AS rights ON rights."
                 .Model_Rights::COLUMN_ID_CATEGORY." = cat.".Model_Category::COLUMN_CAT_ID
                 ." WHERE (rights.".Model_Rights::COLUMN_ID_GROUP." = :idgrp AND rights.".Model_Rights::COLUMN_RIGHT." LIKE 'r__')"
                 ." AND (panel.".self::COLUMN_ID_SHOW_CAT." = :idcat)"
                 ." ORDER BY panel.".self::COLUMN_POSITION." ASC, panel.".self::COLUMN_ORDER." DESC");
         $dbst->bindValue(":idgrp",Auth::getGroupId() , PDO::PARAM_INT);
      } else {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS panel
         JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON cat.".Model_Category::COLUMN_CAT_ID." = panel.".self::COLUMN_ID_CAT
                 ." WHERE (panel.".self::COLUMN_ID_SHOW_CAT." = :idcat)"
                 ." ORDER BY panel.".self::COLUMN_POSITION." ASC, panel.".self::COLUMN_ORDER." DESC");
      }

      $dbst->bindValue(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst->fetchAll(PDO::FETCH_CLASS, 'Model_LangContainer');
   }

   /**
    * Metoda přidá práva ke kategorii do dotazu
    * @return Model_Category 
    */
   public function withRights($catColumns = null, $rightColumns = array())
   {
      $rightColumns = array_merge(array(
         Model_Rights::COLUMN_ID_GROUP, Model_Rights::COLUMN_RIGHT, 
//         'right_assign' => 'IFNULL(`t_r`.`'.Model_Rights::COLUMN_RIGHT.'`,  `t_cat`.`'.Model_Category::COLUMN_DEF_RIGHT.'`)' 
         ),
         $rightColumns);
      
      $this->joinFK(array('t_cat' => self::COLUMN_ID_CAT), $catColumns)
         ->join(array('t_cat' => Model_Rights::COLUMN_ID_CATEGORY), array('t_r' => 'Model_Rights'), null,
            $rightColumns, self::JOIN_LEFT, ' AND t_r.'.Model_Rights::COLUMN_ID_GROUP . ' = :idgrp', array('idgrp' => (int)Auth::getGroupId()));
      return $this;
   }
   
   public function onlyWithAccess()
   {
      return $this->withRights()->where(" ( SUBSTRING(`".Model_Rights::COLUMN_RIGHT."`, 1, 1) = 'r' OR "
         ." ( `".Model_Rights::COLUMN_RIGHT."` IS NULL AND SUBSTRING(`".Model_Category::COLUMN_DEF_RIGHT."`, 1, 1) = 'r' )) ", 
         array(), true);
   }
   
   public function setGroupPermissions() {
      $this->setSelectAllLangs(false);
      $this->joinFK(array('t_cat' => self::COLUMN_ID_CAT))
         ->join(array('t_cat' => Model_Category::COLUMN_ID),
                array('t_r' => 'Model_Rights'), Model_Rights::COLUMN_ID_CATEGORY,
                array('right' => 'IFNULL(`t_r`.`right`,  `t_cat`.`default_right`)',
                  Model_Category::COLUMN_ID => 'IFNULL(t_r.'.Model_Rights::COLUMN_ID_CATEGORY.',  `t_cat`.'.Model_Category::COLUMN_ID.')'),
                self::JOIN_LEFT,
                ' AND t_r.'.Model_Rights::COLUMN_ID_GROUP . ' = :idgrp',
                array('idgrp' => (int)Auth::getGroupId()));
      $this->where("IFNULL( t_r.".Model_Rights::COLUMN_RIGHT." ,  t_cat.".Model_Category::COLUMN_DEF_RIGHT."  ) LIKE 'r__'", array());
      return $this;
   }

   public function getPanel($idPanel) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT *, panel.icon AS picon FROM ".Db_PDO::table(self::DB_TABLE)." AS panel"
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON cat."
                 .Model_Category::COLUMN_CAT_ID." = panel.".self::COLUMN_ID_CAT
              ." WHERE (panel.".self::COLUMN_ID." = :idpanel)");
      $dbst->bindValue(':idpanel', $idPanel, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetch(PDO::FETCH_CLASS);
   }

   public function deletePanel($id) {
      $dbc = Db_PDO::getInstance();
      $st = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
              . " WHERE ".self::COLUMN_ID." = :id");
      return $st->execute(array(':id' => $id));
   }

   /**
    * Metoda uloží parametry kategorie
    * @param int $catId -- id kategorie
    * @param string $params -- serializované pole s parametry
    */
   public function saveParams($panelId, $params){
      // pokud je pole serializujeme
      if(is_array($params)) $params = serialize($params);
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".self::COLUMN_PARAMS." = :params WHERE ".self::COLUMN_ID." = :idpanel");
      $dbst->bindValue(':params', $params, PDO::PARAM_STR);
      $dbst->bindValue(':idpanel', $panelId, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vrací jestli má kategorie nějaké panely
    * @param int $idc -- id kategorie
    * @return bool -- true pokud má panely
    */
   public function havePanels($idc) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID_CAT." = :idc)");
      $dbst->execute(array(':idc'=> (int)$idc));
      if($dbst->fetchObject() === false) return false;
      return true;
   }
}

?>