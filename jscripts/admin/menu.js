$(function(){
    $("ul.dropdown li").hover(function(){
        $(this).addClass("hover");
        $('ul:first',this).css('visibility', 'visible');
    }, function(){
        $(this).removeClass("hover");
        $('ul:first',this).css('visibility', 'hidden');
    
    });
    $("ul.dropdown li a[href=#]").click(function(){
       return false;
    });
    $("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");
});