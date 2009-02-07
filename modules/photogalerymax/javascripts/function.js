/**
 * Kontrole checkboxů, jestli byl alespoň jeden zaškrtnut
 */
function check(the_form,alertMessage) {
	var prvky = document.forms[the_form].elements;
	var pocet = prvky.length;
	var i;
	var isChacked = false;
	for (i=0;i<pocet;i++) {
		if (prvky[i].type=="checkbox" && prvky[i].checked) {
			return true;
		}
	}
	alert(alertMessage)
	return false;
} 

function checkAll(the_form){
	var prvky = document.forms[the_form].elements;
	var pocet = prvky.length;
	var i;
	for (i=0;i<pocet;i++) {
		if (prvky[i].type=="checkbox") {
			prvky[i].checked;
		}
	}
	return true;
}

/**
 * Checks/unchecks all checkbox in given conainer (f.e. a form, fieldset or div)
 *
 * @param   string   container_id  the container id
 * @param   boolean  state         new value for checkbox (true or false)
 * @return  boolean  always true
 */
function setCheckboxes( the_form, state ) {
    var checkboxes = document.forms[the_form].elements;

    for ( var i = 0; i < checkboxes.length; i++ ) {
        if ( checkboxes[i].type == 'checkbox' ) {
            checkboxes[i].checked = state;
        }
    }
    return true;
} // end of the 'setCheckboxes()' function
