var contact = {
    captcha: function(){
        $('#captchaImg').attr('src',$('#captchaImg').attr('src')+'?'+Math.random());
    },
    pop: function(content){
        $('#result').removeAttr('class');
        $('#result').html('<div class="pop rounded">'
            +'<div class="container">'+ content +'<div class="clear"></div>'
            +'</div></div>');
        $(document).bind('keydown',function(e){
            if(e.keyCode == 27){ // esc
                $('#result').empty();
                $(document).unbind();
            }
        });
        return 0;
    },
    result: function(code, status, message, fields, redirect, debug){
        code = code ? code : 800
        $('#submit').attr('disabled','');
        if(fields) for (var i in fields)
            $('#'+fields[i]).focus().addClass('marked');
        var content = '<h1>'+ status +'</h1><p>'+ message +'</p>'
            +(debug?'<p class="debug">'+ debug +'</p><div class="clear"></div>':'')
            +'<div class="close" onclick="$(\'#result\').html(\'\');">'+_lang.close+'</div>';
        contact.pop(content);
        $('#result').addClass(code>=400 ? (code>=600 ? 'error' : 'alert') : 'success');
        if(redirect) location.href = redirect;
        return 0;
    },
    form: function (initiation,complete){
        $('#form').bind('submit', function(e) {
            e.preventDefault();
            $('#submit').attr('disabled','disabled');
            $('#result').empty();
            $('.marked').removeClass('marked');
            $('#loader').css('display','inline');
            if(initiation) initiation();
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                data: $(this).serialize() + '&js=1',
                dataType: 'json',
                success: function(r){
                    if(r) if(r.code == 200 && complete) _complete(r.message);
                    else contact.result(r.code,r.status,r.message,r.fields,r.redirect,r.debug);
                    contact.captcha();
                    $('#submit').removeAttr('disabled');
                    $('#loader').css('display','none');
                }
            });
        },$(this));
    }
};


