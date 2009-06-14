function suggestPassword() {
   var pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
   var passwordlength;    // do we want that to be dynamic?  no, keep it simple :)
   passwordlength = Math.round(Math.random()*6)+6;
   var passwd = document.getElementById('generated_pw');
   passwd.value = '';

   for ( i = 0; i < passwordlength; i++ )
   {
      passwd.value += pwchars.charAt( Math.floor( Math.random() * pwchars.length ) )
   }
   return passwd.value;
}