<?php
class Projects_SiteMap extends SiteMap {
	public function run() {
      // kategorie
       
      $modelProjects = new Projects_Model_Projects();
      
      $projects = $modelProjects->joinFK(Projects_Model_Projects::COLUMN_ID_SECTION)
         ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idc', 
            array('idc' => $this->category()->getId()))
         ->order(array(Projects_Model_Sections::COLUMN_NAME, Projects_Model_Projects::COLUMN_TIME_EDIT))
         ->limit(0, $this->getMaxItems())
         ->records()
         ;
      
      if($projects != false){
         $this->setCategoryLink(new DateTime($projects[0]->{Projects_Model_Projects::COLUMN_TIME_EDIT} ));
         
         foreach ($projects as $record) {
            $this->addItem($this->link()->route('project', array(
               'prkey' => $record->{Projects_Model_Projects::COLUMN_URLKEY},
               'seckey' => $record->{Projects_Model_Sections::COLUMN_URLKEY}
               )),
               $record->{Projects_Model_Sections::COLUMN_NAME}.' - '.$record->{Projects_Model_Projects::COLUMN_NAME},
               new DateTime($record->{Projects_Model_Projects::COLUMN_TIME_EDIT}));
         }
      }
	}
}
?>