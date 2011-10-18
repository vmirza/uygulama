var getPage;
var theme = {
    initialize: function(e){
        // AJAX Supports
        AJAX = ("onhashchange" in window) && AJAX ? true : false;
        // NAV LINKS CLICK EVENT
        if(AJAX){
            $('nav a').click(function(e){
                e.preventDefault();
                $('nav a.selected').removeClass('selected');
                $(this).addClass('selected');
                // set hash location to start xhttp request
                location.href = '#!'+$(this).attr('href');
            }).css({
                'opacity':0,
                'margin-top':0,
                'z-index':100
            });
            $('nav a').each(function(ID){
                $(this).delay(200).animate({
                    'opacity':1
                }, {
                    duration: 200*(ID+1)
                });
            });
        }
        this.menu();
        // LANGUAGES
        $('#languages a').click(function(){
            $.post('/', {
                'LANG':$(this).data('val')
            }, function(){
                location.href = href ? href : location.href;
            });
        });
        // LOGO - NAV
        $('#logo img').load(function(){
            $('nav').css({
                'left':$(this).width()
            });
        });
        // RESIZE + MEDIA QUERY FIXED
        var normal = function(){
            // LOGO - NAV
            $('nav').css({
                'left':$('#logo img').width()
            });
        };
        var tablet = function(){
            // LOGO - NAV
            $('nav').css({
                'left':$('#logo img').width()
            });
            $('#content').width($('#page').width()-$('#aside').width()-10);
        };
        var mobile = function(){
        };
        var layout = function(){
            var w = $(document).width();
            if(w <= 610){
                mobile()
            } else if(w > 610 && w < 960){
                tablet();
            } else {
                normal();
            }
        };
        $(window).resize(function(){
            layout();
        });
        layout();
    },
    // PAGE MENU LINKS CLICK EVENT
    menu: function(){
        if(AJAX)
            $('menu a').click(function(e){
                e.preventDefault();
                $('menu a.selected').removeClass('selected');
                $(this).addClass('selected');
                // set hash location to start xhttp request
                location.href = '#!'+$(this).attr('href');
            }).hide();
        $('menu a').fadeIn();
    }
};
$(document).ready(function() {});