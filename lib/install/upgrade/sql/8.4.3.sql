 
SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}templates'
        AND table_schema = DATABASE()
        AND column_name = 'lang'
    ) = 1,
    "SELECT 1",
    "ALTER TABLE `{PREFIX}templates` ADD COLUMN `lang` VARCHAR(5) NULL DEFAULT NULL;"
));

PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

/* UPDATE_SHOP */
-- Obrázky produktů
CREATE TABLE IF NOT EXISTS `{PREFIX}shop_order_states` (
  `id_order_state` INT NOT NULL AUTO_INCREMENT,
  `id_order_state_mail_template` INT NOT NULL DEFAULT 0,
  `order_state_name_cs` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  ,
  `order_state_name_en` VARCHAR(50) NULL,
  `order_state_name_sk` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL , 
  `order_state_name_de` VARCHAR(50) NULL,
  `order_state_note` VARCHAR(100) NULL DEFAULT NULL,
  `order_state_color` VARCHAR(9) NULL DEFAULT NULL,
  `order_state_complete` TINYINT(1) NULL DEFAULT 0,
  `order_state_deleted` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id_order_state`) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_order_history` (
  `id_order_history` INT NOT NULL AUTO_INCREMENT,
  `id_order_state` INT NOT NULL,
  `id_order` INT NOT NULL,
  `order_state_history_note` VARCHAR(100) NULL DEFAULT NULL,
  `order_state_time_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order_history`) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

ALTER TABLE `{PREFIX}shop_payments` ADD `id_order_state` INT NULL DEFAULT NULL AFTER `id_payment`;

/* END_UPDATE */