(function( $ ){
    // Methods
    var m = {
        init: function(form,o){
            // SELECTBOX, CHECKBOX, RADIO BUTTON CUSTOMIZER
            form.find('select, input[type=checkbox], input[type=radio]')
            .each(function(){
                var type = this.type == 'radio' ? 'radio' : (this.type == 'checkbox' ? 'checkbox' : 'select');
                //console.log(type);
                $('<p class="'+ type +'">')
                .append($('<span>').html($(this).find('option:selected').html()))
                .insertAfter($(this)).append($(this));
                $(':checked').parent().addClass('checked');
                //.css({'width':$(this).width()})
                //.insertBefore(this);
                $(this).bind('focus, mouseover',(function(){
                    $(this).parent().addClass('focus');
                }));
                $(this).bind('blur, mouseout',(function(){
                    $(this).parent().removeClass('focus');
                }));
                
                $(this).parent().parent().mouseover(function(){
                    $(this).find('p').addClass('focus');
                });
                $(this).parent().parent().mouseout(function(){
                    $(this).find('p').removeClass('focus');
                });
                $(this).click(function(){
                    $(':not(:checked)').parent().removeClass('checked');
                    $(':checked').parent().addClass('checked');
                });
                $(this).change(function(){
                    $(this).prev()
                    .html($(this).find('option:selected').html());
                });
            });
            // FILE
            form.find('input[type=file]')
            .each(function(){
                $('<p class="file">')
                    .append($('<span>').html($(this).attr('title')))
                    .insertAfter($(this)).append($(this));
            });
            // BUTTON, SUBMIT CUSTOMIZER
            form.find('button, input[type=submit]')
            .mousedown(function(){
                $(this).addClass('down');
            }).mouseup(function(){
                $(this).removeClass('down');
            });
            // SUBMIT
            form.submit(function(e){
                e.preventDefault();
                form.find('.result').empty();
                form.find('.error').removeClass('error');
                form.find('input[type=submit]').attr('disabled','disabled');
                form.find('.loader').css('display','inline-block');
                $.ajax({
                    url: o.prefix + form.attr('action') + o.suffix,
                    type: 'post',
                    data: form.serialize() + '&' + o.data,
                    dataType: o.dataType,
                    success: function(r){
                        o.complete(form,r);
                    }
                });                
            });
            return false;
        },
        // Default complete function
        complete: function(form,r){
            //tool.result(r.code,r.status,r.message,r.fields,r.redirect,r.debug);
            //r.code = r.code ? r.code : 800
            form.find('input[type=submit]').removeAttr('disabled');
            form.find('.loader').css('display','none');
            var content = '<p>'+ r.message +'</p>';
            //+(r.debug?'<p class="debug">'+ r.debug +'</p><div class="clear"></div>':'')
            //+'<div class="close" onclick="$(\'#result\').html(\'\');">'+_lang.close+'</div>';
            //this.pop(content);
            //$('#result').addClass(r.code>=400 ? (r.code>=600 ? 'error' : 'alert') : 'success');
            if(r.redirect) location.href = r.redirect;
            return 0;
        }
    };
    $.fn.forma = function(o) {
        // Options
        o = $.extend({
            prefix    : '',
            suffix    : '',
            dataType  : 'json',
            data      : 'js=1',
            valuelabel: 0,
            complete  : m.complete
        }, o);
        this.each(function(){
            m.init($(this),o);
        });
    };
})( jQuery );

$('document').ready(function(){
    $('form').forma();
});