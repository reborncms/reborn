$(document).ready(function(){   

    $('ul#user-login-nav').each(function() {
        var $dropdown = $(this);

        $("a.username", $dropdown).click(function(e) {
            e.preventDefault();
            $("a.username").addClass('active');
            $("ul.action-links", $dropdown).toggle();
            return false;
        });
    });

    $('html').click(function(){
        $("ul.action-links").hide();
        $("a.username").removeClass('active');
    });

    // Navigation FadeIn-Out
    $('nav ul li').hover(function() {
        $(this).find('ul:first').fadeIn('fast');
    },function(){
        //$(this).find('ul:first').fadeOut('fast');
    });

});