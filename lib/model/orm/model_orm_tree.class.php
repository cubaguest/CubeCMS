<?php

/**
 * Description of model_orm_tree
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
      $this->lock(self::LOCK_WRITE);
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

   public function removeNode(Model_ORM_Tree_Record $node)
   {
      $m = new static();
      $m->where($this->getLeftColumn() . ' > :lleft AND ' . $this->getRightColumn() . ' < :rright', array('lleft' => $node->getLeft(), 'rright' => $node->getRight()))
              ->delete();
//         mysql_query("DELETE FROM strom WHERE lft >= $row[lft] AND rgt <= $row[rgt]");
   }

   public function delete($pk = null)
   {
      /* @var $node Model_ORM_Tree_Record */
      $node = self::getRecord($pk);
      $this->removeNode($node);
      parent::delete($pk);
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

class Model_ORM_Tree_Record extends Model_ORM_Record implements Iterator {

   /**
    *
    * @var Model_ORM_Tree
    */
   protected $model;
   protected $childs = false;

   /**
    * Vrací aktuální potomky
    * @return type
    */
   public function getNodes()
   {
      $this->loadTree();
      return $this->childs;
   }

   protected function loadTree($limitedColumns = array())
   {
      if ($this->childs !== false) {
         return;
      }
      // omezení stromu
      $whereColumns = array(
          $this->model->getPkName() . ' != :nodeid', // nechceme aktuální node
          $this->model->getLeftColumn() . ' >= :lft',
          $this->model->getRightColumn() . ' <= :rgt',
      );
      $whereBinds = array(
          'nodeid' => $this->getPK(),
          'lft' => $this->{$this->model->getLeftColumn()},
          'rgt' => $this->{$this->model->getRightColumn()},
      );
      $colums = $this->model->getLimitedColumns();
      $colums += $limitedColumns;
      foreach ($colums as $key => $column) {
         $whereColumns[] = $column . ' = :col_' . $key;
         $whereBinds[':col_' . $key] = $this->{$column};
      }
      $mm = clone $this->model;
      $items = $mm
              ->where(implode(' AND ', $whereColumns), $whereBinds)
              ->order($this->model->getLeftColumn())
              ->records();
      if (!$items) {
         $this->childs = array();
         return;
      }

      $tree = $this->createTree($items, $items[0]->{$this->model->getLeftColumn()} - 1);
      $this->childs = $tree ? $tree : array();
   }

   protected function createTree($nodes, $left = 0, $right = null, $maxDepth = false)
   {
      $tree = array();
      foreach ($nodes as $data) {
         if ($data->{$this->model->getLeftColumn()} == $left + 1 && (is_null($right) || $data->{$this->model->getRightColumn()} < $right)) {
            $data->setNodes($this->createTree($nodes, $data->{$this->model->getLeftColumn()}, $data->{$this->model->getRightColumn()}));

            $tree[$data->{$this->model->getPkName()}] = $data;
            $left = $data->{$this->model->getRightColumn()};
         }
      }
      return $tree;
   }

   public function getParent()
   {
      if ($this->{$this->model->getLeftColumn()} == 1) {
         return $this;
      }

      $model = clone $this->model;
// SELECT * FROM `cube_cms_custom_menu_items` WHERE `menu_item_box` = 'right' AND `id_lft` < 9 AND `id_rgt` > 10 ORDER BY id_lft DESC LIMIT 0,1
      // omezení stromu
      $whereColumns = array(
          $this->model->getLeftColumn() . ' < :lpos AND ' . $this->model->getRightColumn() . ' > :rpos',
      );
      $whereBinds = array('lpos' => $this->getLeft(), 'rpos' => $this->getRight());
      $colums = $this->model->getLimitedColumns();
      foreach ($colums as $key => $column) {
         $whereColumns[] = $column . ' = :col_' . $key;
         $whereBinds[':col_' . $key] = $this->{$column};
      }
      $parent = $model->where(implode(' AND ', $whereColumns), $whereBinds)
              ->order(array($this->model->getLeftColumn() => Model_ORM::ORDER_DESC))
              ->record();
      return $parent;
   }

   public function getRoot()
   {
      if ($this->{$this->model->getLeftColumn()} == 1) {
         return $this;
      }

      $model = clone $this->model;

      // omezení stromu
      $whereColumns = array(
          $this->model->getLeftColumn() . ' = 1',
      );
      $whereBinds = array();
      $colums = $this->model->getLimitedColumns();
      foreach ($colums as $key => $column) {
         $whereColumns[] = $column . ' = :col_' . $key;
         $whereBinds[':col_' . $key] = $this->{$column};
      }
      $node = $model->where(implode(' AND ', $whereColumns), $whereBinds)
              ->order(array($this->model->getLeftColumn() => Model_ORM::ORDER_ASC))
              ->record();
      return $node;
   }

   /**
    * První node v řádku
    */
   public function getFirstChildNode()
   {
      
   }

   /**
    * Poslední node v řádku
    */
   public function getLastChildNode()
   {
      
   }

   /**
    * Node na dané pozici v řádku
    */
   public function getChildNodeAt($position)
   {
      
   }

   /**
    * Vrací celý strom od aktuálního prvky
    * @return type
    */
   public function getTree()
   {
      return $this->getNodes();
   }

   public function addNode(Model_ORM_Tree_Record $node, $index = false)
   {
      $this->model->addNode($this, $node, $index);
      // reload node
      $this->invalidate();
   }

   public function haveNodes()
   {
      return !$this->isEmpty();
   }

   public function isEmpty()
   {
      $this->loadTree();
      return empty($this->childs);
   }

   public function removeNode()
   {
      $this->model->removeNode($this);
   }

   public function setAsRoot()
   {
      $this->{$this->model->getLeftColumn()} = 1;
      $this->{$this->model->getRightColumn()} = 2;
      $this->save();
      return $this;
   }

   public function isRoot()
   {
      return $this->{$this->model->getLeftColumn()} == 1;
   }

   public function insertAfter(Model_ORM_Tree_Record $node)
   {
      
   }

   public function insertBefore(Model_ORM_Tree_Record $node)
   {
      
   }

   public function moveAfter(Model_ORM_Tree_Record $node)
   {
      
   }

   public function moveBefore(Model_ORM_Tree_Record $node)
   {
      
   }

   public function moveNodeAtIndex($position)
   {
      
   }

   public function invalidate()
   {
      // find root element
      if ($this->isRoot()) {
         $this->childs = false;
      } else {
         $this->getRoot()->invalidate();
      }

      return $this;
   }

   public function updateLevels($newLevel)
   {
      // find root element
      $this->{$this->model->getLevelColumn()} = $newLevel;
      $this->save();
      if (!$this->isEmpty()) {
         foreach ($this as $node) {
            $node->updateLevels($newLevel + 1);
         }
      }
      return $this;
   }

   /**
    * Nastaví limitedColumns do všech potomků
    */
   public function setlimitedColumns()
   {
      if (!$this->isEmpty()) {
         // tohle by se dalo přepsat do jednoho dotazu
         foreach ($this as $child) {
            foreach ($this->model->getLimitedColumns() as $col) {
               $child->{$col} = $this->{$col};
            }
            $child->save();
            if (!$child->isEmpty()) {
               $child->setlimitedColumns();
            }
         }
      }
   }

   /*
    * metody pro přístup k pozici
    */

   public function getLeft()
   {
      return $this->{$this->model->getLeftColumn()};
   }

   public function getRight()
   {
      return $this->{$this->model->getRightColumn()};
   }

   public function getLevel()
   {
      return $this->{$this->model->getLevelColumn()};
   }

   /* Iterator */

   public function rewind()
   {
      return reset($this->childs);
   }

   public function current()
   {
      return current($this->childs);
   }

   public function key()
   {
      return key($this->childs);
   }

   public function next()
   {
      return next($this->childs);
   }

   public function valid()
   {
      return key($this->childs) !== null;
   }

}
