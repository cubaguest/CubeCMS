<?php
interface DbInterface{

	public function query($sqlQuery);
	public function select();
	public function fetchAssoc($sqlQuery);

}
?>