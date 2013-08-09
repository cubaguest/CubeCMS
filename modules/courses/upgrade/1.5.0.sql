ALTER TABLE `{PREFIX}courses`
ADD COLUMN `akreditace_mpsv` VARCHAR(45) NULL DEFAULT NULL  AFTER `show_in_list` ,
ADD COLUMN `akreditace_msmt` VARCHAR(45) NULL DEFAULT NULL  AFTER `akreditace_mpsv` ,
ADD COLUMN `target_groups` VARCHAR(45) NULL DEFAULT NULL  AFTER `akreditace_msmt` ,
ADD COLUMN `time_start` VARCHAR(45) NULL DEFAULT NULL  AFTER `target_groups` ;
