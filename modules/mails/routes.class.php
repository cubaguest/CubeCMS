<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Mails_Routes extends Routes {
	function initRoutes() {
      // fronta odesílání
      $this->addRoute('sendMailsQueue', 'sendmails/queue', 'sendMailsQueue', 'sendmails/queue/');
      // seznam odeslaných emailů
      $this->addRoute('sendMailsList', 'sendmails', 'sendMailsList', 'sendmails/');
      
      // ajax úpravy
      $this->addRoute('searchMail', 'searchmail.php', 'searchMail', 'searchmail.php', 'XHR_Respond_VVEAPI');
      // ajax odeslání
      $this->addRoute('sendMail', 'sendmail.php', 'sendMail', 'sendmail.php', 'XHR_Respond_VVEAPI');
	}
}

?>