function formElemSwitchLang(linkObj, lang){
   // zkrytí všech
   var parent = $(linkObj).parent("td");

   parent.find('label[lang]').hide();
   parent.find(".elem_container_class[lang]").hide();
   // odznačení
   parent.find("a.formLinkSelLang").removeClass('formLinkSelLang');

   $(linkObj).addClass('formLinkSelLang');
   parent.find("label[lang="+lang+"]").show();
   parent.find(".elem_container_class[lang="+lang+"]").show();
   return false;
}

function formShowOnlyLang(lang){
   // zkrytí všech
   $("form label[lang]").hide();
   $("form .elem_container_class[lang]").hide();
   // odznačení
   $("form a.formLinkSelLang").removeClass('formLinkSelLang');

   $("form a[lang="+lang+"]").addClass('formLinkSelLang');
   $("form label[lang="+lang+"]").show();
   $("form .elem_container_class[lang="+lang+"]").show();
}