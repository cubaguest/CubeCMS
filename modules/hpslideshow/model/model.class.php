<?php

/*
 * Třída modelu s detailem galerie
 */

class HPSlideShow_Model extends Model_ORM_Ordered {

    const DB_TABLE = 'hpslideshow_images';

    /**
     * Názvy sloupců v databázi pro tabulku s obrázky
     * @var string
     */
    const COLUMN_ID = 'id_image';
    const COLUMN_ID_CAT = 'id_category';
    const COLUMN_LABEL = 'image_label';
    const COLUMN_LINK = 'image_link';
    const COLUMN_ACTIVE = 'image_active';
    const COLUMN_ORDER = 'image_order';
    const COLUMN_FILE = 'image_file';
    
    const COLUMN_VALID_FROM = 'valid_from';
    const COLUMN_VALID_TO = 'valid_to';
    const COLUMN_SLOGAN_BACKGROUND = 'slogan_background';

    protected function _initTable() {
        $this->setTableName(self::DB_TABLE, 't_ph_imgs');

        $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
        $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));

        $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(400)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
        $this->addColumn(self::COLUMN_LINK, array('datatype' => 'varchar(100)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));

        $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
        $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
        $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(40)', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));

        $this->addColumn(self::COLUMN_VALID_FROM, array('datatype' => 'timestamp', 
            'pdoparam' => PDO::PARAM_STR, 
            'nn' => true, 
            'default' => 'CURRENT_TIMESTAMP'));
        $this->addColumn(self::COLUMN_VALID_TO, array('datatype' => 'timestamp', 
            'pdoparam' => PDO::PARAM_STR, 
            'nn' => false));
        $this->addColumn(self::COLUMN_SLOGAN_BACKGROUND, array('datatype' => 'tinyint(1)', 
            'pdoparam' => PDO::PARAM_BOOL, 
            'default' => false));
        
        $this->setPk(self::COLUMN_ID);
        $this->setOrderColumn(self::COLUMN_ORDER);

        $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Category', Model_Category::COLUMN_CAT_ID);
    }

}
