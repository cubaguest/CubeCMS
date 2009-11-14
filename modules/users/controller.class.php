<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Users_Controller extends Controller {
	/**
	 * Minimální délka hesla
	 * @var integer
	 */
	const PASSWORD_MIN_LENGHT = 5;
	
	public function mainController() {
      $this->checkControllRights();

      $this->view()->template()->addTplFile('listUsers.phtml');
	}
	
	/**
	 * Metoda pro zobrazení detailu zástupce
	 */
	public function showController() {
	}
	
	/**
	 * Metoda pro úpravu
	 */
	public function editController() {
	}
	
	/**
	 * Metoda pro přidávání uživatelů
	 */
	public function addController() {
	}
}

?>