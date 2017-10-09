<?php

/**
 * Model binárního stromu
 *
 * @author cuba
 */
class Model_ORM_Tree extends Model_ORM {

   protected $leftColumn = 'lft';
   protected $rightColumn = 'rgt';
   protected $levelColumn = 'level';
   protected $limitingColumns = array();
   protected $rowClass = 'Model_ORM_Tree_Record';

   protected function setLeftColumn($name)
   {
      $this->leftColumn = $name;
      $this->addColumn($this->leftColumn, array('datatype' => 'int', 'nn' => true, 'index' => true));
   }

   protected function setLevelColumn($name)
   {
      $this->levelColumn = $name;
      $this->addColumn($this->levelColumn, array('datatype' => 'int', 'nn' => true, 'index' => true, 'default' => 1));
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

   public function getLeftColumn()
   {
      return $this->leftColumn;
   }

   public function getLevelColumn()
   {
      return $this->levelColumn;
   }

   public function getRightColumn()
   {
      return $this->rightColumn;
   }

   public function getLimitedColumns()
   {
      return $this->limitingColumns;
   }

   /**
    * Vrací kořen podle daných kritérií, pokud jsou
    */
   public function getRoot($limitedColums = array())
   {
      if (!empty($limitedColums)) {
         
      }
      return $this->where(($this->where != null ? ' AND ' : null) . $this->leftColumn . ' = 1', array(), true)->record();
   }

   /**
    * Vrací všechny kořeny
    * @return Model_ORM_Tree_Record[] pole s root prvky
    */
   public function getRoots()
   {
      $roots = $this->where(($this->where != null ? ' AND ' : null) . $this->leftColumn . ' = 1', array(), true)->records();
//      foreach ($roots as &$root) {
//         $this->loadTree($root);
//      }
      return $roots;
   }

   protected function beforeSave(\Model_ORM_Record $record, $type = 'U')
   {
      $limitedCols = $this->getLimitedColumns();
      if (!empty($limitedCols)) {
         foreach ($limitedCols as $col) {
            if ($record->{$col} == null) {
               $record->{$col} = uniqid('root_');
            }
         }
      }
      parent::beforeSave($record, $type);
   }

   public static function getPath($idNode = null)
   {
      
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
   public function addNode($parent, Model_ORM_Tree_Record $record, $index = false)
   {
      if (!$parent instanceof Model_ORM_Tree_Record) {
         $parent = self::getRecord($parent);
      }

      // neposunovat samy sebe
      if ($parent->getPK() == $record->getPK()) {
         return;
      }
      if (!$parent || $parent->isNew()) {
         throw new UnexpectedValueException('Neplatný rodič');
      }

      $whereColumns = array();
      $whereBinds = array();
      foreach ($this->limitingColumns as $key => $column) {
         $record->{$column} = $parent->{$column};
         $whereColumns[] = $column . ' = :col_' . $key;
         $whereBinds[':col_' . $key] = $parent->{$column};
      }

      // pozice není zadána, vkládá se na konec
      if (!$index) {
         $pLeft = $parent->{$this->leftColumn};
         $pRight = $parent->{$this->rightColumn};
         $newLeft = $parent->{$this->rightColumn};
         $newRight = $parent->{$this->rightColumn} + 1;
      } else {
         // load tree by parent and get childs?
      }
      $this->lock(self::LOCK_WRITE);
      // update pravé strany
      $m = new static();
      $m->where($this->rightColumn . ' >= :prtg', array('prtg' => $pRight));
      if (!empty($whereColumns)) {
         $m->where(' AND ' . implode(' AND ', $whereColumns), $whereBinds, true);
      }
      $m->update(array(
          $this->rightColumn => array('stmt' => $this->rightColumn . ' + 2')
      ));

      // update levé strany
      $m = new static();
      $m->where($this->leftColumn . ' > :plft', array('plft' => $pRight));
      if (!empty($whereColumns)) {
         $m->where(' AND ' . implode(' AND ', $whereColumns), $whereBinds, true);
      }
      // update pravé strany
      $m->update(array($this->leftColumn => array('stmt' => $this->leftColumn . ' + 2')));

      $record->{$this->leftColumn} = $newLeft;
      $record->{$this->rightColumn} = $newRight;
      $record->{$this->levelColumn} = $parent->{$this->levelColumn} + 1;
      $record->save();

      // aktualizace limited colums všech potomků

      $this->unLock();

      return $record;
   }

   public function removeNodeOnly(Model_ORM_Tree_Record $node)
   {
      $m = new static();
      $m->where($this->getLeftColumn() . ' > :lleft AND ' . $this->getRightColumn() . ' < :rright', 
              array('lleft' => $node->getLeft(), 'rright' => $node->getRight()))
              ->delete();
//         mysql_query("DELETE FROM strom WHERE lft >= $row[lft] AND rgt <= $row[rgt]");
   }

   public function delete($pk = null)
   {
      if(is_int($pk)){
         $pk = self::getRecord($pk);
      }
      if($pk instanceof Model_ORM_Tree_Record){
          // smazáni sebe a potomku
          $m = new static();
          $width = $pk->getRight() - $pk->getLeft() + 1;
          $m->where($this->getLeftColumn() . ' BETWEEN :lleft AND  :rright', 
                  array(
                      ':lleft' => $pk->getLeft(),
                      ':rright' => $pk->getRight()
                  ))
                  ->delete();
          
          // přesun potomků
          $m->where($this->getRightColumn() . ' > :rright', array(
              ':rright' => $pk->getRight()
          ))
          ->update(array(
                $this->getRightColumn() => array(
                    'stmt' => $this->getRightColumn().' - :wwidth',
                    'values' => array(':wwidth' => $width)      
                  )
              ));
          
          $m->where($this->getLeftColumn() . ' > :lleft', array(
              ':lleft' => $pk->getRight()
          ))
          ->update(array(
                $this->getLeftColumn() => array(
                    'stmt' => $this->getLeftColumn().' - :wwidth',
                    'values' => array(':wwidth' => $width)      
                  )
              ));
          
      } else {
        parent::delete($pk);
      }
      
      
   }

   /**
    * Přesune uzel do nového nodu
    * @param type $node
    * @param type $target
    * @param type $position
    */
   public function moveNode(Model_ORM_Tree_Record $node, Model_ORM_Tree_Record $target, $position = false)
   {
      // doc
      // http://stackoverflow.com/questions/2801285/move-node-in-nested-sets-tree
      // http://stackoverflow.com/questions/889527/move-node-in-nested-set
      // https://rogerkeays.com/how-to-move-a-node-in-nested-sets-with-sql
      // https://php.vrana.cz/traverzovani-kolem-stromu-presuny-uzlu.php
      // kontrola jestli jsou stejné limited colums, jinak dochází k přesunu mezi stromy a tam se bude používat add a remove (samozřejmě bez mazání)
      $sameTree = true;
      foreach ($this->limitingColumns as $col) {
         if ($node->{$col} != $target->{$col}) {
            $sameTree = false;
         }
      }

      $nodeWidth = ($node->{$this->getRightColumn()} - $node->{$this->getLeftColumn()} + 1);
      $nodeOriginalLeft = $node->getLeft();
      $nodeOriginalRight = $node->getRight();
      $parent = $node->getParent();
      
      
      if (!$parent || $parent->getPK() == $node->getPK()) {
         throw new UnexpectedValueException($this->tr('Nelze přesunovat kořen ve stejném stromu'));
      }
      $targetChildNodes = array_values($target->getNodes());

      $nodeNewLimitedValues = array();
      
      // na danou pozici
      if ($position !== false ) {
         // řadí se před node
         if($position < count($targetChildNodes)){
            $insertBeforeNode = $targetChildNodes[$position];
            /* @var $inserBeforeNode Model_ORM_Tree_Record */
            $newpos = $insertBeforeNode->getLeft();
         } 
         // řadí se za node
         else if(!empty ($targetChildNodes)) {
            $insertAfterNode = end($targetChildNodes);
            $newpos = $insertAfterNode->getRight() + 1;
         } else {
            $newpos = $target->getRight();
         }
      }
      // na konec
      else {
         if (!empty($targetChildNodes)) {
            /* @var $insertAfterNode Model_ORM_Tree_Record */
            $insertAfterNode = end($targetChildNodes);
            $newpos = $insertAfterNode->getRight() + 1;
         } else {
            $newpos = $target->getRight();
         }
      }
      $distance = $newpos - $nodeOriginalLeft;

      $tmpPos = $nodeOriginalLeft;
//      var_dump('Před: $tmpPos ' . $tmpPos.' $distance ' . $distance. ' $newpos ' . $newpos. ' $nodeWidth ' . $nodeWidth);

      if ($sameTree) {
         if ($distance < 0) {
            $distance -= $nodeWidth;
            $tmpPos += $nodeWidth;
         }
      } else {
         if ($distance < 0) {
//            $distance -= $nodeWidth;
         }
      }

//      var_dump('Po: $tmpPos ' . $tmpPos.' $distance ' . $distance. ' $newpos ' . $newpos. ' $nodeWidth ' . $nodeWidth);
      //die;
      // omazující sloupce 
      $whereColumns = array('1=1');
      $whereBinds = array();
      foreach ($this->getLimitedColumns() as $key => $column) {
         $whereColumns[] = $column . ' = :col_' . $key;
         $whereBinds[':col_' . $key] = $node->{$column};
      }
      $whereColumns = implode(' AND ', $whereColumns);

      $whereColumnsNew = array('1=1');
      $whereBindsNew = array();
      foreach ($this->getLimitedColumns() as $key => $column) {
         $whereColumnsNew[] = $column . ' = :col_' . $key;
         $whereBindsNew[':col_' . $key] = $target->{$column};
         $nodeNewLimitedValues[$column] = $target->{$column};
      }
      $whereColumnsNew = implode(' AND ', $whereColumnsNew);

      $model = new static();
      // create new space for subtree
//      var_dump('vytvoření místa');
      $model->where(($sameTree ? $whereColumns : $whereColumnsNew) . ' AND ' . $this->getLeftColumn() . ' >= :newpos', array_merge(($sameTree ? $whereBinds : $whereBindsNew), array(':newpos' => $newpos)))
              ->update(array(
                  $this->getLeftColumn() => array('stmt' => $this->getLeftColumn() . ' + ' . $nodeWidth),))
              ;
//      var_dump('Nastav lft = lft + '.$nodeWidth, $model->getSQLQuery());
      $model->where(($sameTree ? $whereColumns : $whereColumnsNew) . ' AND ' . $this->getRightColumn() . ' >= :newpos', array_merge(($sameTree ? $whereBinds : $whereBindsNew), array(':newpos' => $newpos)))
              ->update(array(
                  $this->getRightColumn() => array('stmt' => $this->getRightColumn() . ' + ' . $nodeWidth)))
              ;
//      var_dump('Nastav rgh = rgh + '.$nodeWidth, $model->getSQLQuery());

//         var_dump($model->getSQLQuery());
//      var_dump('Přesun stromu');
      // move subtree into new space
      $model->where($whereColumns . ' AND ' . $this->getLeftColumn()
                      . ' >= :oldpos AND ' . $this->getRightColumn() . ' < :oldpos2', array_merge($whereBinds, array( ':oldpos2' => (int) $tmpPos + (int) $nodeWidth, ':oldpos' => $tmpPos)))
              ->update(array_merge(($sameTree ? array() : $nodeNewLimitedValues), array(
                  $this->getLeftColumn() => array('stmt' => $this->getLeftColumn() . ' + ' . $distance),
                  $this->getRightColumn() => array('stmt' => $this->getRightColumn() . ' + ' . $distance)
                  )))
              ;
//      var_dump('Nastav lft = lft + '.$distance.' rgh = rgh + '.$distance.' a '.($sameTree ? null : implode(' = ', $nodeNewLimitedValues)), $model->getSQLQuery());
      
//      var_dump('odebráí starého místa');
      // var_dump($model->getSQLQuery());die();
      // remove old space vacated by subtree
      $model->where($whereColumns . ' AND ' . $this->getLeftColumn() . ' > :oldrpos', array_merge($whereBinds, array(':oldrpos' => $nodeOriginalRight)))
              ->update(array(
                  $this->getLeftColumn() => array('stmt' => $this->getLeftColumn() . ' - ' . $nodeWidth), ))
              ;
//      var_dump('Nastav lft = lft - '.$nodeWidth, $model->getSQLQuery());
      //var_dump($model->getSQLQuery());die();

      $model->where($whereColumns . ' AND ' . $this->getRightColumn() . ' > :oldrpos', array_merge($whereBinds, array(':oldrpos' => $nodeOriginalRight)))
              ->update(array(
                  $this->getRightColumn() => array('stmt' => $this->getRightColumn() . ' - ' . $nodeWidth) ))
              ;
//      var_dump('Nastav rgh = rgh - '.$nodeWidth, $model->getSQLQuery());
      
//      die;
      
      $node->invalidate();
      $node->updateLevels($target->getLevel()+1);
   }

}
