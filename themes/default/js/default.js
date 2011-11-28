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
        };
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
        /*$('nav a:first').css({
            //'margin-left': '-'+$('nav a:first').css('padding-left'),
            'border-left': 0,
            'background-image': 'none'
        });*/
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
            setTimeout(function() { window.scrollTo(0, 1) }, 100);
        };
        var layout = function(){
            var w = $(document).width();
            if(w <= 610){
                mobile();
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

// ANALYTICS
var pageTracker = 0;
var href;
// DOMREADY
$(document).ready(function() {
    // THEME
    theme.initialize();
    // LOCATION HASH LISTENER :: PAGE LOADER
    if(AJAX)
        window.onhashchange = function() {
            href = location.hash.replace('#!','');
            if(!href) href = '/'+REQUESTURI;
            var loader = $('a[href="'+href+'"]').addClass('loader');
            $('#page, footer').fadeOut('fast',function(){
                $('#page').load('/page'+href,function(){
                    $('#page, footer').fadeIn('fast');
                    loader.removeClass('loader');
                    if(ANALYTICS && pageTracker) pageTracker._trackPageview(href);
                    theme.menu();
                });
            });
            if(ANALYTICS){
                if(!pageTracker) pageTracker = _gat._createTracker(ANALYTICS);
                pageTracker._trackPageview(href);
            }
        };
        
    if(ANALYTICS){
        var _gaq=[["_setAccount",ANALYTICS],['_setDomainName', '.' + DOMAIN],["_trackPageview"]];
        (function(d,t){
            var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.async=1;
            g.src=("https:"==location.protocol?'//ssl':'//www')+".google-analytics.com/ga.js";
            s.parentNode.insertBefore(g,s)
        }(document,"script"));
    }
        
});