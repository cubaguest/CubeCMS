<?php
/**
 * Description of model_orm_tree
 *
 * @author cuba
 */
class Model_ORM_Tree extends Model_ORM {
   
   protected $leftColumn = 'lft';
   protected $rightColumn = 'rgt';
   
   protected $limitingColumns = array();

   protected $rowClass = 'Model_ORM_Tree_Record';

   protected function setLeftColumn($name)
   {
      $this->leftColumn = $name;
      $this->addColumn($this->leftColumn, array('datatype' => 'int', 'nn' => true, 'index' => true));
   }
   
   protected function setRightColumn($name)
   {
      $this->rightColumn = $name;
      $this->addColumn($this->rightColumn, array('datatype' => 'int', 'nn' => true, 'index' => true));
   }
   
   protected function setLimitingColumns($columns = array())
   {
      $this->limitingColumns = $columns;
   }

   /**
    * Metody pro práci se stromem
    */
   
   public function getRoot()
   {
      return $this->where( ($this->where != null ? ' AND ' : null) .  $this->leftColumn.' = 1', array(), true)->record();
   }
   
   public function getParent($idNode)
   {
      
   }
   
   public function getNode($idNode)
   {
      
   }
   
   public function getFirstNode($idParent)
   {
      
   }
   
   public function getLastNode($idParent)
   {
      
   }
   
   public function getNodeAt($idParent, $position)
   {
      
   }
   
   public function getTree($idParent = null, $limitedColumns = array())
   {
      // načtení rodiče - potřebujeme left a right proměnné stromu
      if($idParent instanceof Model_ORM_Record){
         $idParent = $idParent->getPK();
      }
      $m = clone $this;
      $parent = $m->where(($this->where != null ? ' AND ' : null). $this->getPkName().' = :idp', array('idp' => $idParent), true)->record();
      
      // omezení stromu
      $whereColumns = array(
          $this->leftColumn.' >= :lft',
          $this->rightColumn.' <= :rgt',
      );
      $whereBinds = array(
         'lft' => $parent->{$this->leftColumn}, 
         'rgt' => $parent->{$this->rightColumn},
      );
      $colums = $this->limitingColumns;
      $colums += $limitedColumns;
      foreach ($colums as $key => $column) {
         $whereColumns[] = $column.' = :col_'.$key;
         $whereBinds[':col_'.$key] = $parent->{$column};
      }
      
      $items = $this
         ->where(($this->where != null ? ' AND ' : null). 
              implode(' AND ', $whereColumns), $whereBinds, true)
         ->order($this->leftColumn)
         ->records();
      Debug::log($this->getSQLQuery(), count($items));
      $tree = $this->createTree($items, $items[0]->{$this->leftColumn}-1);
      return $tree[$parent->getPK()]; // vrací přímo kořen
   }
   
   protected function createTree($nodes, $left = 0, $right = null, $maxDepth = false) {
      $tree = array();
      foreach ($nodes as $data) {
         if ($data->{$this->leftColumn} == $left + 1 && (is_null($right) || $data->{$this->rightColumn} < $right)) {
            $data->addChilds( $this->createTree($nodes, $data->{$this->leftColumn}, $data->{$this->rightColumn}) );
            
            $tree[$data->{$this->getPkName()}] = $data;
            $left = $data->{$this->rightColumn};
         }
      }
      return $tree;
  }


   public function getPath($idNode = null)
   {
      
   }
   
   public function createRoot(Model_ORM_Record $record)
   {
      $record->{$this->leftColumn} = 1;
      $record->{$this->rightColumn} = 2;
      $record->save();
   }
   
   /**
    * 
    * @param type $parent
    * @param Model_ORM_Record $record
    * @param type $index
    * @return \Model_ORM_Record
    * @throws UnexpectedValueException
    * @todo Dodělet, pokud node má potomky!!!!
    */
   public function addNode($parent, Model_ORM_Record $record, $index = false)
   {
      $this->lock(self::LOCK_WRITE);
      $idp = $parent instanceof Model_ORM_Record ? $parent->getPK() : $parent;
      $parent = self::getRecord($idp);
      if(!$parent || $parent->isNew()){
         throw new UnexpectedValueException('Neplatný rodič');
      }    

      $whereColumns = array();
      $whereBinds = array();
      foreach ($this->limitingColumns as $key => $column) {
         $record->{$column} = $parent->{$column};
         $whereColumns[] = $column.' = :col_'.$key;
         $whereBinds[':col_'.$key] = $parent->{$column};
      }

      // pozice není zadána, vkládá se na konec
      if(!$index){
         $pLeft = $parent->{$this->leftColumn};
         $pRight = $parent->{$this->rightColumn};
         $newLeft = $parent->{$this->rightColumn};
         $newRight = $parent->{$this->rightColumn}+1;
      } else {
         // load tree by parent and get childs?
      }
      // update pravé strany
      $m = new static();
      $m->where($this->rightColumn.' >= :prtg', array('prtg' => $pRight));
      if(!empty($whereColumns)){
         $m->where(' AND '. implode(' AND ',$whereColumns), $whereBinds, true);
      }
      $m->update(array( 
          $this->rightColumn => array( 'stmt' => $this->rightColumn.' + 2' ) 
          ));

      // update levé strany
      $m = new static();
      $m->where($this->leftColumn.' > :plft', array('plft' => $pRight));
      if(!empty($whereColumns)){
         $m->where(' AND '. implode(' AND ',$whereColumns), $whereBinds, true);
      }
      // update pravé strany
      $m->update(array( $this->leftColumn => array('stmt' => $this->leftColumn.' + 2') ));

      $record->{$this->leftColumn} = $newLeft;
      $record->{$this->rightColumn} = $newRight;
      $record->save();
      
      $this->unLock();
      
      // update parent???
      
      return $record;
   }
   
   public function insertAfter($idSibling, Model_ORM_Record $record)
   {
      
   }
   
   public function insertBefore($idSibling, Model_ORM_Record $record)
   {
      
   }
   
   public function moveAfter($idSibling, Model_ORM_Record $record)
   {
      
   }
   
   public function moveBefore($idSibling, Model_ORM_Record $record)
   {
      
   }
   
   public function moveNodeAtIndex($idNode, $idParent, $position)
   {
      
   }
   
   public function deleteNode($idNode)
   {
      
   }
}


class Model_ORM_Tree_Record extends Model_ORM_Record {
   protected $childs = array();

   public function getChilds()
   {
      return $this->childs;
   }
   
   public function addChilds($childs)
   {
      $this->childs = $childs;
   }
   
   public function haveChilds()
   {
      return !empty($this->childs);
   }
   

}