Shadowbox.init();

// Unset sessions via ajax onclick dismiss btn which has css class '.close'.
// Button must have id attribute the same as session name
function dismissSession(){
    $('.close').click(function(){
        var sess = $(this).attr('id');
        $.ajax({
            url: '/error/dismissSession',
            type: "post",
            data: {
                sess: sess
            }
        });
    });
}

$(document).ready(function(){
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });
    
    $('.btn-trash').click(function(){
        var title = $(this).attr('title');
        title = title ? ' ' + title : '';
        return confirm("Na pewno usunąć projekt o nazwie" + title + "?") ? true : false;
    });
    
    dismissSession();
    
    $('.contact').popover({
        html:true        
    }).click(function(){
        $(this).parent('li').toggleClass('active');
    });
    
    $('.advanced_search').popover({
        html:true
    });
    
    $('#deweloperCarousel,#gospodarczeCarousel,#realizacjeCarousel').carousel({
        interval: false
    });
    
    $('body #jednorodzinneCarousel').carousel({
        interval: 4000,
        hover: 'pause'
    });
    
    $('.scrollup').click(function() {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });   
    
    $('h2 a').tooltip('show');
    $('a[onclick]').tooltip({
        placement:'bottom'
    });
    
    var $thisHref, $tabs, $tabsHref, $thumb;
    
    $('.thumbnails li').mouseenter(function(){
        $(this).find('a:first').css({
            background: '#EE5F5B'
        });
        $thisHref = $(this).find('a:first').attr('href'); 
        $tabs = $('.nav-tabs > li > a[href="' + $thisHref + '"]');
        $tabs.attr('class','btn-danger');                     
    }).mouseleave(function(){
        $(this).find('a:first').css({
            background:'transparent'
        });
        $tabs.removeClass('btn-danger');
    });
    
    $('.nav-tabs > li > a').mouseenter(function(){
        $tabsHref = $(this).attr('href'); 
        $thumb = $('.thumbnails a[href="' + $tabsHref + '"]');
        $thumb.css({
            background: '#EE5F5B'
        });                     
    }).mouseleave(function(){
        $thumb.css({
            background:'transparent'
        });
    });
    
});



