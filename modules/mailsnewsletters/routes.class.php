<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class MailsNewsletters_Routes extends Routes {
	function initRoutes() {
      // fronta odesílání
      $this->addRoute('queue', 'queue', 'queue', 'queue/');
      
      // list newsletterů
      $this->addRoute('list', 'list', 'list', 'list/');
      $this->addRoute('newsletterPreview', "newsletter/preview-(?P<id>[0-9]+).html", 'newsletterPreview' ,'newsletter/preview-{id}.html');
      
      $this->addRoute('tplEdit', "tempaltes/edit-(?P<id>[0-9]+)", 'tplEdit','tempaltes/edit-{id}/');
      $this->addRoute('tplPreview', "tempaltes/preview-(?P<id>[0-9]+).html", 'tplPreview' ,'tempaltes/preview-{id}.html');
      $this->addRoute('tplAdd', 'templates/add/', 'tplAdd', 'templates/add/');
      $this->addRoute('tplUpload', 'templates/upload/', 'tplUpload', 'templates/upload/');
      $this->addRoute('tpls', 'templates/', 'tpls', 'templates/');
      
      $this->addRoute('replacements', 'replacements.json', 'replacements', 'replacements.json', 'XHR_Respond_VVEAPI');
      $this->addRoute('tinyMCEtplsList', 'templates.js', 'tinyMCEtplsList', 'templates.js');
	}
}

?>