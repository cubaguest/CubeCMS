<?php
class BandsProgram_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('edit', "edit", 'edit','edit/');
      $this->addRoute('exportProgram', "export.(?P<output>(?:pdf)|(?:xml)|(?:html))", 'exportProgram','export.{output}');
      $this->addRoute('programItem', "item.html", 'programItem','item.html');
      $this->addRoute('normal', null, 'main', null);
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/'); // cesta z modulu bands
	}
}

?>