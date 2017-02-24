ALTER TABLE `{PREFIX}custom_blocks_embeds` CHANGE `block_item_index` `block_item_index` VARCHAR(10) NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}custom_blocks_files` CHANGE `block_item_index` `block_item_index` VARCHAR(10) NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}custom_blocks_images` CHANGE `block_item_index` `block_item_index` VARCHAR(10) NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}custom_blocks_texts` CHANGE `block_item_index` `block_item_index` VARCHAR(10) NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}custom_blocks_videos` CHANGE `block_item_index` `block_item_index` VARCHAR(10) NULL DEFAULT NULL;