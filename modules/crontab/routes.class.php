<?php
class CronTab_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('tasksList', "tasks.json", 'tasksList','tasks.json');
      $this->addRoute('taskEdit', 'edit-task.php', 'taskEdit', 'edit-task.php', 'XHR_Respond_VVEAPI');
	}
}

?>