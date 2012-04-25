/* Czech initialisation for the jQuery UI date picker plugin. */
/* Written by Tomas Muller (tomas@tomas-muller.net). */
jQuery(function($){
	$.timepicker.regional['cs'] = {
		currentText: 'Te\u010f',
		closeText: 'hotovo',
		ampm: false,
		amNames: ['AM', 'A'],
		pmNames: ['PM', 'P'],
		timeFormat: 'hh:mm tt',
		timeSuffix: '',
		timeOnlyTitle: 'Vyberte \u010das',
		timeText: '\u010cas',
		hourText: 'Hodin',
		minuteText: 'Minut',
		secondText: 'Sekund',
		millisecText: 'Milisekund',
		timezoneText: '\u010casová zóna'
   };
	$.timepicker.setDefaults($.timepicker.regional['cs']);
});
