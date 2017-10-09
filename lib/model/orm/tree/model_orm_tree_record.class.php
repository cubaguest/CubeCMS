<?php

/**
 * Záznam modelu binárního stromu
 *
 * @author cuba
 */
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

   public function getFlatChildrens() {
      $childs = array();
      foreach ($this->getNodes() as $node) {
          $childs[$node->getPK()] = $node;
          /* @var $node Model_ORM_Tree_Record */
          if(!$node->isEmpty()){
              $childs += $node->getFlatChildrens();
          }
      }
      return $childs;
   }
}
