function formElemSwitchLang(linkObj, lang){
   // zkrytí všech
   var parent = $(linkObj).parent("td");

   parent.find('label[lang]').hide();
   parent.find(".form-elem-container[lang]").hide();
   // odznačení
   parent.find("a.form-link-lang-sel").removeClass('form-link-lang-sel');

   $(linkObj).addClass('form-link-lang-sel');
   parent.find("label[lang="+lang+"]").show();
   parent.find(".form-elem-container[lang="+lang+"]").show();
   return false;
}

function formShowOnlyLang(lang){
   // zkrytí všech
   $("form label[lang]").hide();
   $("form .form-elem-container[lang]").hide();
   // odznačení
   $("form a.form-link-lang-sel").removeClass('form-link-lang-sel');

   $("form a[lang="+lang+"]").addClass('form-link-lang-sel');
   $("form label[lang="+lang+"]").show();
   $("form .form-elem-container[lang="+lang+"]").show();
}