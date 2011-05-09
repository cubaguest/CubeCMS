<?php
/**
 * Třída Modelu pro práci s konfiguračními volbami systému (globálními)
 * Třída, která umožňuje pracovet s modelem konfiguračních voleb
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ Cube CMS 7.3 $Revision: $
 * @author			$Author: $ $Date:  $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */

class Model_ConfigGlobal extends Model_Config {
   private $globalTable = 'cubecms_global_config';
   private $mainTable = null;

   protected function  _initTable() {
      parent::_initTable();
      $this->mainTable = $this->getTableName();
      $this->setTableName($this->globalTable, 't_ccfg_g', false);
   }

   /**
    * Metoda nastaví vnitřní SQL dotaz na výbě záznamů z obou konfigurací
    */
   public function mergedConfigValues(){
   /* SELECT * FROM
      (SELECT `key`, `value` FROM vypecky_config UNION ALL SELECT `key`, `value` FROM cubecms_global_config) AS t
      GROUP BY t.`key`*/

      $this->currentSql = 'SELECT * FROM (SELECT `'.self::COLUMN_KEY.'`, `'.self::COLUMN_VALUE.'` FROM '.$this->mainTable.' UNION ALL SELECT `'.self::COLUMN_KEY.'`, `'.self::COLUMN_VALUE.'`'
         .' FROM `'.$this->getTableName().'`) AS t GROUP BY t.`'.self::COLUMN_KEY.'`';
      return $this;
   }
}
?>