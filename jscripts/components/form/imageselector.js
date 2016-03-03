$(document).ready(function(){
   var $container = $('.image-selector');
   $container.on('change keyup keydown', 'select.image-selector-select', function(){
      var $box = $(this).closest('.image-selector');
      var img = ($(this).val() === "" 
         ? $box.find('.image-selector-img-box img').data('emptysrc') 
         : $box.find('.image-selector-img-box img').data('targetpath') + $(this).val());
      $box.find('.image-selector-img-box img').prop('src', img);
      // change modal
      $box.find('#modal-image-selector-__KEY__ .active').removeClass('active');
      $box.find('#modal-image-selector-__KEY__ a[data-value="'+$(this).val()+'"]').addClass('active');
   });
   $container.find('select.image-selector-select').change();
   $container.find('input[type="file"]').change();
   if (!!window.FileReader) {
      $container.on('change', 'input[type="file"]', function(){
         var $that = $(this);
         var reader = new FileReader();
         reader.readAsDataURL(this.files[0]);
         reader.onload = function (oFREvent) {
            $that.closest('.image-selector').find('.image-selector-img-box img').prop('src', oFREvent.target.result) ;
         };
      });
   }
   
   $container.on('click', '.image-selector-img-box', function(){
      $(this).closest('.image-selector').find('input[type="file"]').first().click();
   });
   $container.on('click', '.modal-image-selector-image', function(){
      $(this).parent().find('.active').removeClass('active');
      $(this).addClass('active');
      // to selector
      $(this).closest('.image-selector').find('select.image-selector-select').val($(this).data('value')).change();
      CubeCMS.Modal.close( '#'+$(this).closest('.cubecms-modal').prop('id') );
      return false;
   });
});
