// SLIDEZONE
//------------------------------------------------------------------------------
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
            $('<a>',{
                'class':'mask'
            })
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
// SLIDEZONE DEFINITION
$().ready(function(){
    if($().slidezone)
        $('.slidezone').slidezone({
            delay       : 5000,
            slideOut    : function(){ 
                $('.slidezone').find('.junk').remove();
                $('.slidezone').find('a.mask:visible').addClass('junk');
            },
            slideIn     : custom
        });
});
// CAROUSEL DEFINITION
//------------------------------------------------------------------------------
$().ready(function(){
    if($().carousel)
        $('.carousel').carousel({
            click: function(i){
                location.href = i.find('a').attr('href');
            }
        });
});