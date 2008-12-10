<?php
/*
 * Třída modelu se sekcí
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class BlogDetailModel extends DbModel {
	/**
	 * Názvy sloupců v tabulce s blogy (blog)
	 * @var string
	 */
	const COLUM_LABEL = 'label_cs';
//	const COLUM_LABEL_LANG_PREFIX = 'label_';
	const COLUM_TEXT = 'text_cs';
//	const COLUM_TEXT_LANG_PREFIX = 'text_';
	const COLUM_URLKEY = 'urlkey';
	const COLUM_TIME = 'time';
	const COLUM_ID_USER = 'id_user';
	const COLUM_ID = 'id_blog';
	const COLUM_ID_SECTION = 'id_section';
	const COLUM_ID_ITEM = 'id_section';
	const COLUM_DELETED = 'deleted';
	const COLUM_DELETED_BY_ID_USER = 'deleted_by_id_user';
}

?>