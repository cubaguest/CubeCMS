<script type="text/javascript">
   Dropzone.autoDiscover = false;
   $("div<?php echo $params['selector'] ?>").dropzone({
      url: "<?php echo $linkUpload ?>",
      dictDefaultMessage: "<?php echo $this->tr('Pro nahrání zde klikněte nebo přetáhněte soubory') ?>",
      dictFallbackMessage: "<?php echo $this->tr('Váš prohlížeč nepodporuje drag&drop.') ?>",
      dictInvalidFileType: "<?php echo $this->tr('Vložen nesprávný typ souboru.') ?>",
      dictCancelUpload: "<?php echo $this->tr('Zrušit') ?>",
      dictRemoveFile: false,
      dictFileTooBig: "<?php echo $this->tr('Soubor je příliš velký. Maximálně {{maxFilesize}} MB.') ?>",
      dictMaxFilesExceeded: "<?php echo $this->tr('Nahrán maximální počet souborů ({{maxFiles}}) a nelze nahrát další.') ?>",
      maxFilesize: <?php echo round($params['maxFileSize'] / (1024 * 1024)) ?>,
      maxFiles: <?php echo $params['maxFiles'] ?>,
      addRemoveLinks: false,
      paramName: 'dropzone_file',
      acceptedFiles: 'image/jpeg,image/png',
      previewTemplate: 
            '<div class="dz-preview dz-image-preview">'
               +'<div class="dz-image"><img data-dz-thumbnail="" alt="" src=""></div>'
               +'<div class="dz-details">'
                  +'<div class="dz-size"><span data-dz-size=""></span></div>'
                  +'<div class="dz-filename"><span data-dz-name=""></span></div>'
               +'</div>'
               +'<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>'
               +'<div class="dz-error-message"><span data-dz-errormessage=""></span></div>'
               +'<div class="dz-success-mark"><span class="icon icon-check-circle-o"></span></div>'
               +'<div class="dz-error-mark"><span class="icon icon-exclamation-triangle"></span></div>'
               +'<a class="dz-remove" href="javascript:undefined;" data-dz-remove=""><span class="icon icon-remove"></span> <?php ps($this->tr('Odebrat'))?></a>'
            +'</div>',
      removedfile: function (file) {
         var _ref = file.previewElement;
         if (file.previewElement) {
            if ((_ref = file.previewElement) !== null) {
               $.ajax({
                  url: '<?php echo $linkDelete ?>',
                  type: "POST",
                  data: {file: file._fileName, path: "<?php echo $params['path'] ?>"},
                  success: function () {
                     _ref.parentNode.removeChild(file.previewElement);
                  }
               });
            }
         }
         return this._updateMaxFilesReachedClass();
      }
      , sending: function (file, xhr, formData) {
         formData.append("dropzone_path", "<?php echo $params['path'] ?>");
         formData.append("_dropzone__check", "send");
<?php foreach ($params['postData'] as $key => $value) { ?>
            formData.append("<?php echo $key ?>", "<?php echo $value ?>");
<?php } ?>
      },
      init: function () {
<?php foreach ($params['images'] as $key => $image) { ?>
            var file_<?php echo $key ?> = {
               name: "<?php echo $image['name'] ?>",
               _fileName: "<?php echo $image['name'] ?>",
               size: <?php echo $image['size'] ?>,
               type: '<?php echo $image['mime'] ?>',
               id: <?php echo (int) $image['id'] ?>
            };
            this.options.addedfile.call(this, file_<?php echo $key ?>);
            this.options.thumbnail.call(this, file_<?php echo $key ?>, "<?php echo $image['url'] ?>");
<?php } ?>
//         this.options.maxFiles = this.options.maxFiles - <?php echo count($params['images']) ?>;
//         return this._updateMaxFilesReachedClass();
      },
      success: function (file, response) {
         if(response.uploaded === true){
            file._fileName = response.filename;
            if (file.previewElement) {
               $('.dz-filename>span', file.previewElement).text(response.filename).data('dz-name', response.filename);
               file.previewElement.classList.add("dz-success");
            }
         } else {
            // remove tpl
            $(file.previewElement).remove();
            CubeCMS.Msg.show(response);
         }
         return;
      }
   });
</script>