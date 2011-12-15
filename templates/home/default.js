// SLIDEZONE
$(function() { 
    // CUSTOM EFFECT
    var pos = 0;
    var custom = function(slidezone){
        var w = slidezone.width();
        var h = slidezone.height();
        var m = (940 - w) *.5;
        var cols = 10;
        var src = slidezone.current.find('img').attr('src');
        var boxWidth = Math.round(w/cols);
        var boxHeight = h;
        var direction = (pos++ % 2 == 0);
        slidezone.title.html('<i>'+slidezone.current.attr('title')+'</i>');
        var container = slidezone.find('.slidezone-container');
        for(var col = 0; col < cols; col++){
            var rand = direction ? col+1 : cols - col;
            container.append(
                $('<a>',{'class':'mask'})
                .css({ 
                    position:'absolute',
                    opacity:0,
                    top:0,
                    left:(boxWidth*col), 
                    width:boxWidth,
                    height:boxHeight,
                    'background-image':'url("'+ src +'")',
                    'background-position':'-'+ (col * boxWidth + m) +'px 0px',
                    'background-repeat':'no-repeat'
                }).delay(100*rand)
                .animate({
                    opacity:'1'
                },800,'slidezone')
                .attr('href',slidezone.current.attr('href'))
                );
        }
    };
    // SLIDEZONE DEFINATION
    $('.slidezone').slidezone({
        delay       : 5000,
        slideOut    : function(){ 
            $('.slidezone').find('.junk').remove();
            $('.slidezone').find('a.mask:visible').addClass('junk');
        },
        slideIn     : custom
    });
});


    
// CAROUSEL DEFINATION
//$('.carousel').carousel();
/*
    i.click(function(){
        $(this).find('section').pop({'width':'420px','className':'carousel-section'})
    });
});    */

/*  
(function($){
    var s;
    var m = {
        init: function(el){
            $('#pop').remove();
            var pop = $('<div id="pop">').append(
                $('<div id="pop-overlay">'),
                $('<div id="pop-aligner">').append(
                    $('<div id="pop-border">').append(
                        $('<div id="pop-container" class="'+s.className+'">').append(
                            el.html()
                            ,'<div class="clear"></div>'
                            )))).hide().appendTo(document.body).fadeIn('fast');
            $('#pop-overlay').height($(document).height());
            $('#pop-border').width( s.width || $('#pop-border').width());
            var a = $('#pop-aligner');
            a.animate({
                'margin-top':-($('#pop-border').outerHeight() *.5)
            },200);
            a.find('img').load(function(){
                a.animate({
                    'margin-top':-($('#pop-border').outerHeight() *.5)
                },200);
            });
            $(document).bind('keydown',function(e){
                if(e.keyCode == 27){ // esc
                    pop.fadeOut();
                    $(document).unbind();
                }
            });
            $('#pop-aligner, #pop-overlay').click(function(){
                pop.fadeOut();
                $(document).unbind();
            });
            $('#pop-border, #pop-container').click(function(e){
                e.preventDefault();
            });
        }
    };
    $.fn.pop = function(o) {
        // Settings
        s = $.extend({
            width     : 0,
            height    : 0,
            className : ''
        }, s);
        if(o) $.extend(s, o);
        this.each(function(){
            m.init($(this));
        });
    };
})(jQuery);
*/