function initSportEvents(opts){

   $.datepicker.setDefaults( $.datepicker.regional[ "<?=Locales::getLang()?>" ] );

   var dates = $('input#event_dateBegin_1, input#event_dateEnd_1').datepicker({
      showButtonPanel: true, showOtherMonths: true, selectOtherMonths: true,
      onSelect: function( selectedDate ) {
         var option = this.id == "event_dateBegin_1" ? "minDate" : "maxDate",
            instance = $( this ).data( "datepicker" ),
            date = $.datepicker.parseDate(
               instance.settings.dateFormat ||
                  $.datepicker._defaults.dateFormat,
               selectedDate, instance.settings );
         dates.not( this ).datepicker( "option", option, date );
      }
   });

   // místa
   $( 'input[name="event_place"]' ).autocomplete({
      source: function( request, response ) {
         $.ajax({
            url: opts.urlPlaces,
            dataType: "json",
            data: { limit: 20, search: request.term },
            success: function( data ) {
               response( $.map( data.results, function(item){
                     return item.place_name+' (ID:'+item.id_place+')'
                  })
               );
            }
         });
      }
   });

}
