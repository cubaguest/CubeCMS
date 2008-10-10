var bobexample=new switchcontent("switchgroup1", "div"); //Limit scanning of switch contents to just "div" elements
bobexample.setStatus('<img src="open.png" /> ', '<img src="close.png" /> ');
bobexample.setColor('darkred', 'black');
bobexample.setPersist(true);
bobexample.collapsePrevious(true); //Only one content open at any given time
bobexample.init();
