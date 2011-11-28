(function( $ ){
    // Methods
    var methods = {
        init: function(element, options){
            $this = this;
            // Bind options
            var slidezone =  $.extend(element, options);
            slidezone.init(slidezone);
            clearTimeout(slidezone.timer);
            slidezone.slides = slidezone.find('a');
            slidezone.active = slidezone.slides.length-1;
            $this.start(slidezone);
            // NAV
            slidezone.nav = $('<div class="slidezone-nav">').appendTo(slidezone);   
            // TITLE
            slidezone.title = $('<div class="slidezone-title">').appendTo(slidezone);            
            // INDEX
            slidezone.index = $('<div class="slidezone-index">').appendTo(slidezone.nav);
            // CUSTOM EASING
            $.easing.slidezone = function(x, t, b, c, d){
                return c*((t=t/d-1)*t*t + 1) + b;
            }
            // PREV
            $('<a class="slidezone-prev">')
            .click(function(){
                $this.prev(slidezone);
            }).appendTo(slidezone.index);
            // SLIDE INDEX
            for ( var i = 0; i <= slidezone.active;  i++ ) {
                $('<a class="index'+i+'" data-id="'+i+'">')
                .click(function(){
                    clearTimeout(slidezone.timer);
                    // slide out current slide
                    slidezone.current = $(slidezone.slides[slidezone.active]);
                    slidezone.slideOut(slidezone);
                    // slide in selected slide
                    slidezone.active = $(this).data('id');
                    slidezone.current = $(slidezone.slides[slidezone.active]);
                    slidezone.slideIn(slidezone);
                    $this.set(slidezone);
                }).appendTo(slidezone.index);
            }
            // NEXT
            $('<a class="slidezone-next">')
            .click(function(){
                $this.next(slidezone);
            }).appendTo(slidezone.index);
            // KEY CONTROL
            $(document).keydown(function(e) {
                if ( e.keyCode == 37 ) { 
                    $this.prev(slidezone);
                } else if ( e.keyCode == 39 ) { 
                    $this.next(slidezone);
                }
            });
            // slide in first slide
            slidezone.active = 0;
            slidezone.current = $(slidezone.slides[slidezone.active]);
            slidezone.slideIn(slidezone);
            $this.set(slidezone);
            // context menu
            $this.contextmenu(slidezone);
            return false;
        },
        contextmenu: function(slidezone){
            $this = this;
            slidezone.bind({
                'contextmenu':function(e){
                    e.preventDefault();
                    $('#contextmenu').remove();
                    var c = $('<div id="contextmenu">')
                    c.css({
                        position : 'absolute',
                        display  : 'none',
                        'z-index': '10000'
                    })   
                    .appendTo($('body'));
                    $('<a>').click(function(){
                        $this.next(slidezone);
                    })
                    .html(slidezone.menu[0]).appendTo(c);
                    $('<a>').click(function(){
                        $this.prev(slidezone);
                    })
                    .html(slidezone.menu[1]).appendTo(c);
                    $('<a href="http://gokercebeci.com/dev/slidezone">')
                    .html('slidezone v1.10').appendTo(c);
                    // Set position
                    var ww = $(document).width();
                    var wh = $(document).height();
                    var w = c.outerWidth(1);
                    var h = c.outerHeight(1);
                    var x = e.pageX > (ww - w) ? ww : e.pageX;
                    var y = e.pageY > (wh - h) ? wh : e.pageY;
                    c.css({
                        display : 'block',
                        top     : y,
                        left    : x
                    });
                }
            });
            $(document)
            .click(function(){
                $('#contextmenu').remove();
            })
            .keydown(function(e) {
                if ( e.keyCode == 27 ){
                    $('#contextmenu').remove();
                }
            })
            .scroll(function(){
                $('#contextmenu').remove();
            })
            .resize(function(){
                $('#contextmenu').remove();
            });
        },
        set: function(slidezone){
            $this = this;
            // clear
            slidezone.find('.active').removeClass('active');
            // set index
            slidezone.find('.index'+slidezone.active).addClass('active');
            // set timer fot next slide
            slidezone.timer = setTimeout(function () {
                $this.next(slidezone);
            },slidezone.delay);            
        },
        next: function(slidezone){
            $this = this;
            clearTimeout(slidezone.timer);
            // slide out current slide
            slidezone.current = $(slidezone.slides[slidezone.active]);
            slidezone.slideOut(slidezone);
            // slide in next slide
            slidezone.active = (slidezone.active+1) % slidezone.slides.length;
            slidezone.current = $(slidezone.slides[slidezone.active]);           
            slidezone.slideIn(slidezone);
            $this.set(slidezone);
        },
        prev: function(slidezone){
            $this = this;
            clearTimeout(slidezone.timer);
            // slide out current slide
            slidezone.current = $(slidezone.slides[slidezone.active]);
            slidezone.slideOut(slidezone);
            // slide in previous slide
            slidezone.active = ((slidezone.active-1) < 0 
                ? slidezone.slides.length+(slidezone.active-1)
                : (slidezone.active-1)) % slidezone.slides.length;
            slidezone.current = $(slidezone.slides[slidezone.active]);          
            slidezone.slideIn(slidezone);
            $this.set(slidezone);
        },
        start: function(slidezone){
            slidezone.start(slidezone);
            slidezone.slides.css({
                opacity:'0'
            });
        },  
        finish: function(slidezone){
            slidezone.finish(slidezone);
        },  
        error: function(slidezone){
            slidezone.error(slidezone);
        },  
        slideIn: function(slidezone){
            slidezone.current.animate({
                opacity:'1'
            }, 600,'slidezone');
            //title
            slidezone.title.html('<i>'+slidezone.current.attr('title')+'</i>');
        },     
        slideOut: function(slidezone){
            slidezone.current.animate({
                opacity:'0'
            },'slidezone');
            //title
            slidezone.title.html('<i>'+slidezone.current.attr('title')+'</i>');
        }
    };
    $.fn.slidezone = function(options) {
        options = $.extend({
            init    : function(){},
            start   : function(){},
            finish  : function(){},
            error   : function(){},
            delay   : 3000,
            slideIn : methods.slideIn,
            slideOut: methods.slideOut,
            menu    : ['next','previous']
        }, options);
        this.each(function(){
            methods.init($(this), options);
        });
    };
})(jQuery);