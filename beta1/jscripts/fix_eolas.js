var objects = document.getElementsByTagName("object");

function eolas(i)
{
    objects[i].outerHTML = objects[i].outerHTML;
}

for (var i=0; i<objects.length; i++)
    window.setTimeout("eolas(" + i + ")", 1);