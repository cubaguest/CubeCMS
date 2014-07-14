<script type="text/javascript">
   Dropzone.autoDiscover = false;
   $("div<?=$params['selector']?>").dropzone({
      url: "<?=$linkUpload?>", 
      dictDefaultMessage: "<?=$this->tr('Pro nahrání zde klikněte nebo přetáhněte soubory')?>", 
      dictFallbackMessage: "<?=$this->tr('Váš prohlížeč nepodporuje drag&drop.')?>", 
      dictInvalidFileType: "<?=$this->tr('Vložen nesprávný typ souboru.')?>", 
      dictCancelUpload: "<?=$this->tr('Zrušit')?>", 
      dictRemoveFile: "<?=$this->tr('Odebrat')?>", 
      dictFileTooBig: "<?=$this->tr('Soubor je příliš velký. Maximálně {{maxFilesize}}.')?>", 
      dictMaxFilesExceeded: "<?=$this->tr('Nahrán maximální počet souborů ({{maxFiles}}) a nelze nahrát další.')?>", 
      maxFilesize: <?=round($params['maxFileSize']/(1024*1024))?>, 
      maxFiles: <?=$params['maxFiles']?>, 
      addRemoveLinks: true, 
      paramName: 'dropzone_file', 
      acceptedFiles: 'image/jpeg,image/png', 
      removedfile: function(file) {
         var _ref = file.previewElement;
         if (file.previewElement) {
            if ((_ref = file.previewElement) != null) {
               $.ajax({
                  url: '<?=$linkDelete?>',
                  type: "POST",
                  data: {file: file._fileName, path : "<?=$params['path']?>" },
                  success: function() {
                     _ref.parentNode.removeChild(file.previewElement);
                  }
               });
            }
         }
         return this._updateMaxFilesReachedClass();
      }
      ,sending: function(file, xhr, formData) {
         formData.append("dropzone_path", "<?=$params['path']?>");
         formData.append("_dropzone__check", "send");
         <? foreach ($params['postData'] as $key => $value) {?>
            formData.append("<?=$key?>", "<?=$value?>");
         <?}?>
      },
      init: function () {
         <?  foreach ($params['images'] as $key => $image) {?>
            var file_<?=$key?> = { 
               name: "<?=$image['name']?>", 
               _fileName: "<?=$image['name']?>", 
               size: <?=$image['size']?>, 
               type: '<?=$image['mime']?>', 
               id: <?=(int)$image['id']?> 
            };
            this.options.addedfile.call(this, file_<?=$key?>);
            this.options.thumbnail.call(this, file_<?=$key?>, "<?=$image['url']?>");
         <?}?>
         this.options.maxFiles = this.options.maxFiles - <?=count($params['images'])?>;
         return this._updateMaxFilesReachedClass();
      },
      success : function(file, response){
         file._fileName = response.filename;
         if (file.previewElement) {
            $('.dz-filename>span', file.previewElement).text(response.filename).data('dz-name', response.filename);
            file.previewElement.classList.add("dz-success");
         }
         return;
      }
   });
</script>