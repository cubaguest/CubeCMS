var fadeRunning = new Array();
var maxOpacity = 100;
var timer = 0;

function opacity(id, opacStart, opacEnd, millisec) {
    //speed for each frame
    var speed = Math.round(millisec / 100);
    var timer = 0;
    
    fadeRunning[id] = true;
    //determine the direction for the blending, if start and end are the same nothing happens
    if(opacStart > opacEnd) {
        for(i = opacStart; i >= opacEnd; i--) {
            fadeOut = setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
       
    } else if(opacStart < opacEnd) {
        for(i = opacStart; i <= opacEnd; i++)
            {
            fadeIn = setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    }
    fadeRunning[id] = false;
}

//change the opacity for different browsers
function changeOpac(opacity, id) {
    var object = document.getElementById(id).style;
    
    if (opacity > 0){
    	object.visibility='visible';
    } else {
    	object.visibility='hidden';
    }

    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
}

function shiftOpacity(id, millisec) {
	var element = document.getElementById(id);
    //if an element is invisible, make it visible, else make it ivisible
    if(element.style.opacity == 0 || typeof(element.style.opacity) == 'undefined') {
        opacity(id, 0, 100, millisec);
    } else {
        opacity(id, 100, 0, millisec);
    }
}

function fadeUp(objId, time){
	var object=document.getElementById(objId);
	var speed = time/100;
	
	if(object.state == "FADE_OFF" || object.state == undefined){
		object.state = "FADE_UP";
		fadeRunning[objId] = true;
		fading(objId, speed,2);
	} else {
		object.state = "FADE_UP";
	}
}

function fadeDown(objId, time){
	var object=document.getElementById(objId);
	var speed = time/100;
	
	if(object.state=="FADE_ON"){
		object.state="FADE_DOWN";
		fadeRunning[objId] = true;
		//setTimeout("fading('" + objId + "'," + speed + ",2)",500);
		fading(objId, speed,2);
	} else {
		object.state="FADE_DOWN";
	}
}

function fading(objId, speed, posun){
	var object=document.getElementById(objId);
		
	if(object.index == undefined){
		object.index = 0;
	}
	
	if(fadeRunning[objId]){
		if(object.state == "FADE_UP"){
			if(object.index <= maxOpacity)
				object.index=object.index+posun;

			if(object.index == maxOpacity){
				object.state="FADE_ON";
				fadeRunning[objId] = false;
			}
				
		}
		else if(object.state == "FADE_DOWN") {
			if(object.index > 0)
				object.index=object.index-posun;
			else
				object.index = 0;

			if(object.index == 0){
				object.state="FADE_OFF";
				fadeRunning[objId] = false;
			}
		}
		changeOpac(object.index, objId);
		setTimeout("fading('" + objId + "'," + speed + "," + posun + ")",speed);
	}
}
