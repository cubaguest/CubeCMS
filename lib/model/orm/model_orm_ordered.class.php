<?php
/**
 * Třída poskytující ORM práci s řazenými záznamy v databázi.
 * třída poskytuje základní prvky pro práci s prvky v databázi. načítání, ukládání mazání dat.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 8.0.4 $Revision: $
 * @author			$Author: $ $Date: $
 * 						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro práci s modelem v databázi (ORM)
 */
class Model_ORM_Ordered extends Model_ORM {
   /**
    * Sloupce pro omezení řazení
    * @var array
    */
   protected $limitedColumns = array();
   
   /**
    * Sloupec s pořadím
    * @var string
    */
   protected $orderColumn = null;
   
   public function __construct()
   {
      parent::__construct();
      // nastavíme výchozí řazení
      $this->order(array($this->orderColumn => Model_ORM::ORDER_ASC));
   }
   
   /**
    * Metoda pro nastavení limitujících sloupců pro řazení (např. id kategorie)
    * @param array $columns
    * @return Model_ORM_Ordered - sám sebe
    */
   protected function setLimitedColumns($columns)
   {
      $this->limitedColumns = $columns;
      return $this;
   }
   
   /**
    * Metoda pro nastavení sloupce který je určen pro řazení (např. item_order)
    * @param string $column
    * @return Model_ORM_Ordered - sám sebe
    */
   protected function setOrderColumn($column)
   {
      $this->orderColumn = $column;
      return $this;
   }
   
   /**
    * Metoda upraví pořadí záznamů při ukládání
    * @param Model_ORM_Record $record
    * @param string $type - U|I
    */
   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      if($record->isNew()){
         $m = clone $this;
         $m->reset();
         
         if(!empty($this->limitedColumns)){
            $whereString = array();
            $whereBinds = array();
            foreach ($this->limitedColumns as $index => $column) {
               $whereString[] = $column.' = :col_'.$index;
               $whereBinds[':col_'.$index] = $record->{$column};
            }
            $m->where(implode(' AND ', $whereString), $whereBinds);
         }
         
         $count = $m->count();
         $record->{$this->orderColumn} = $count + 1;
      }

      parent::beforeSave($record, $type);
   }
   
   /**
    * Metoda upraví pořadí záznamů při mazání
    * @param Model_ORM_Record $record
    * @param mixed $type - primární klíč nebo Model_ORM_Record
    */
   protected function beforeDelete($pk)
   {
      // najdeme záznam
      $m = clone $this;
      $m->reset();
      $record = $m->record($pk);
      if($record){
         $whereString = array($this->orderColumn.' > :ord');
         $whereBinds = array('ord' => $record->{$this->orderColumn});
         
         if(!empty($this->limitedColumns)){
            foreach ($this->limitedColumns as $index => $column) {
               $whereString[] = $column.' = :col_'.$index;
               $whereBinds[':col_'.$index] = $record->{$column};
            }
         }
         
         $m->where(implode(' AND ', $whereString), $whereBinds)
             ->update(array($this->orderColumn => array('stmt' => $this->orderColumn.' - 1')));
      }
      parent::beforeDelete($pk);
   }
   
   protected function afterDelete($pk = false)
   {
      if(!$pk){
         // tady přidat přeuspořádání pokud se smazalo více záznamů
      }
   }
   
   /**
    * Přesun záznamu na novou pozici
    * @param int $newPos
    */
   public static function setRecordPosition($idRecord, $newPos)
   {
      $m = new self();
      $record = $m->record($idRecord);
      if(!$record || $record->isNew()){
         throw new UnexpectedValueException(sprintf($this->tr('Záznam s ID: %s se nepodařilo najít, nelze jej tedy přesunout'), $idRecord));
      }
      $record->setRecordPosition($newPos);
   }
   
   public function getLimitedColumns()
   {
      return $this->limitedColumns;
   }
   
   public function getOrderColumn()
   {
      return $this->orderColumn;
   }
}

class Model_ORM_Ordered_Record extends Model_ORM_Record {
   public function setRecordPosition($newPosition = 1)
   {
      // není třeba aktualizovat, pokud jsou záznamy stejné
      if($this->{$this->model->getOrderColumn()} == $newPosition || $this->isNew()){
         return;
      }
      
      $whereString = array();
      $whereBinds = array();
      $lColumns = $this->model->getLimitedColumns();
      if(!empty($lColumns)){
         foreach ($lColumns as $index => $column) {
            $whereString[] = $column.' = :col_'.$index;
            $whereBinds[':col_'.$index] = $this->{$column};
         }
      }
      
      if($newPosition > $this->{$this->model->getOrderColumn()}){
         // move down
         $whereString[] = $this->model->getOrderColumn()." > :oldOrder";
         $whereBinds['oldOrder'] = $this->{$this->model->getOrderColumn()};
         $whereString[] = $this->model->getOrderColumn()." <= :newOrder";
         $whereBinds['newOrder'] = $newPosition;
         $updateStmt = $this->model->getOrderColumn().' - 1';
         
      } else {
         // move up
         $whereString[] = $this->model->getOrderColumn()." < :oldOrder";
         $whereBinds['oldOrder'] = $this->{$this->model->getOrderColumn()};
         $whereString[] = $this->model->getOrderColumn()." >= :newOrder";
         $whereBinds['newOrder'] = $newPosition;
         $updateStmt = $this->model->getOrderColumn().' + 1';
      }
      $m = clone $this->model;
      $m->reset();
      if(!empty($whereString)){
         $m->where(implode(' AND ', $whereString), $whereBinds);
      }
      $m->update(array($this->model->getOrderColumn() => array('stmt' => $updateStmt)));
      // update row
      $this->{$this->model->getOrderColumn()} = $newPosition;
      $this->save();
   }
}