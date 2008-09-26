function pocet(size, textarea, idnumber){
   /*var size = size || 700;*/
  	/*size = size || 700;*/
  	/*if (typeof size == "undefined") {
		size = 700;
	}*/
	if( typeof(size) == 'undefined' ){
		size = 700;
	}

	if( typeof(textarea) == 'undefined' ){
		textarea = 'area';
	}
	if( typeof(idnumber) == 'undefined' ){
		idnumber = 'cislo';
	}

   area = document.getElementById(textarea);
	alength = area.value.length;
	if(alength > size){
		area.value = area.value.substr(0,size);
		document.getElementById(idnumber).innerHTML="max";
		document.getElementById(idnumber).style.color="red";
	} else {
		document.getElementById(idnumber).innerHTML=alength;
		document.getElementById(idnumber).style.color="black";
	}
}
