function addtoarea(text, textareaname) {
 /*       var txtarea = document.forms[textareaname];
        text = '' + text + '';*/
 /*       if (txtarea.createTextRange && txtarea.caretPos) {
                var caretPos = txtarea.caretPos;
                caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
                txtarea.focus();
        } else {*/
 /*               document.forms[textareaname]value  += text;*/
          /*txtarea.focus();*/
/*  }*/
	document.forms.note[textareaname].focus();
 	document.forms.note[textareaname].value=document.forms.note[textareaname].value+text;
}