document.write('<script type="text/javascript" src="./jscripts/jquery/jquery-1.3.2.min.js"></script>');

function formSwitchLang(linkObj, elemName, lang){
   showOnly(elemName, lang);
   return false;
}

function showOnly(elemName, lang){
   // zkrytí všech
   $("."+elemName+'_container_class').hide();
   $("."+elemName+'_label_class').hide();
   // odznačení
   $("."+elemName+'_lang_link').removeClass('formLinkSelLang');
   // u tinymce zkrytí i potomka - editor
//   $("."+elemName+'_class').next('span.mceEditor').hide();

   // zobrazení nastaveného
   $("#"+elemName+'_container_'+lang).show();
//   $("#"+elemName+'_'+lang).next('span.mceEditor').show();
   $("#"+elemName+"_label_"+lang).show();
   $("#"+elemName+'_'+lang+"_parent").show();
   // vyznačení
   $("#"+elemName+"_lang_link_"+lang).addClass('formLinkSelLang');
}