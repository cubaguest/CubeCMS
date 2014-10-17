<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class AdminIPBlock_View extends View {
   public function listIPView(){
      echo json_encode($this->respond);
   }
}