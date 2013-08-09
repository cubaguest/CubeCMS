-- tagy
CREATE TABLE IF NOT EXISTS `{PREFIX}articles_tags` (
  `id_article_tag` INT NOT NULL  AUTO_INCREMENT,
  `article_tag_name` VARCHAR(20) NOT NULL ,
  `article_tag_counter` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`id_article_tag`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `{PREFIX}articles_tags_has_articles` (
  `articles_tags_id_article_tag` INT NOT NULL ,
  `articles_id_article` SMALLINT(5) UNSIGNED NOT NULL ,
  PRIMARY KEY (`articles_tags_id_article_tag`, `articles_id_article`) ,
  INDEX `fk_articles_tags_has_articles_arti1` (`articles_id_article` ASC) ,
  INDEX `fk_articles_tags_has_articles_arti` (`articles_tags_id_article_tag` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

-- optimalizace
ALTER TABLE `{PREFIX}articles`
ADD COLUMN `article_priority` INT NULL DEFAULT 0  AFTER `author` , 
ADD COLUMN `article_priority_end_date` DATE NULL DEFAULT NULL AFTER `article_priority`, 
ADD INDEX `urlkey_cs_id_cat` (`id_cat` ASC, `urlkey_cs` ASC),
ADD INDEX `urlkey_en_id_cat` (`id_cat` ASC, `urlkey_en` ASC),
ADD INDEX `urlkey_de_id_cat` (`id_cat` ASC, `urlkey_de` ASC),
ADD INDEX `urlkey_sk_id_cat` (`id_cat` ASC, `urlkey_sk` ASC) ;
