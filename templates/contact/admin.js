// the function for non strict file names 
var get = function(ID){ return document.getElementById(ID); };
mediaUploader = function(a, m){
    switch(a){
        case 'ioerror':
        case 'securityerror':
            tool.result(600, m.type, m.text);
        break;
        case 'select':
            $('#uploaderdesc').html('<b>'+m+'</b> '+lang.file_selected
                +'<a href="javascript:get(\'mediaUploader\').upload();">'+lang.start_upload+'</a>');
        break;
        case 'add':
            $('#mediaContainer').append($('<div>',{'id':m.name,'class':'media'})
                .append($('<div>',{'class':'thumb'}))
                .append($('<div>',{'class':'name','data-url':url+',alt'}).html(m.name))
                .append($('<a>',{'class':'remove button'})
                    .click(function(){mediaRemove(0,m.name)})
                    .html('X')
            ));
        break;
        case 'remove':
        break;
        case 'progress':
            if($(get(m.target.name)).find('.progress').length){
                $(get(m.target.name)).find('.progress').css('width',(m.bytesLoaded / m.bytesTotal *100 >> 0)+'%');
            } else {
                $(get(m.target.name)).find('.thumb').html('<div class="progress">&nbsp;</div>');
            }
        break;
        case 'complete':
            m = eval('(' + m + ')');
            $(get(m.alt)).find('.thumb').html('<img src="/themes/'+ THEME +'/images/loader.gif" alt="'+m.alt+'"/>');
            $('<img>',{'src':'/'+m.thumb,'alt':m.alt})
            .load(function(){
                $(get(m.alt)).attr('id',m.ID);
                $('#'+m.ID+' .thumb').attr('id','thumb'+m.ID);
                tool.inlineedit( $('#'+m.ID+' .name').attr('id','name'+m.ID).attr('data-id',m.ID));
                $('#'+m.ID+' .thumb').empty().append($(this));
                $('#'+m.ID+' .remove').bind('click',function(){ mediaRemove(m.ID); });
                $(this).bind('click',function(){ mediaInsert('/'+m.thumb,m.alt,'/'+m.src); });
            });
        break;
    }
};
var mediaAdd = function(thumb, alt, src){
    var size = $('input[name=size]:checked').val();
    var align = $('input[name=align]:checked').val();
    var content = (size == 'big')
        ? '<img src="'+src+'" alt="'+alt+'"/>'
        : '<a href="'+src+'" class="thumb" title="'+alt+'"><img src="'+thumb+'" alt="'+alt+'" class="'+align+'" /></a>';
    //editor.selection.insertContent(content);
    $('#pagecontent').wysiwyg('insertHtml', content);
    $('#result').empty();
};
var mediaInsert = function(thumb, alt, src){
    var content = '<div class="mediaPop">'
        +'<div class="mediasize left">'
        +'<div><input id="thumb" name="size" type="radio" value="thumb" checked="checked"><label for="thumb">'+lang.thumbnail+'</label></div>'
        +'<div><input id="big" name="size" type="radio" value="big"><label for="big">'+lang.original_size+'</label></div>'
        +'</div><div class="mediaalign right">'
        +'<div><input id="alignleft" name="align" type="radio" value="left" checked="checked"><label for="alignleft">'+lang.left+'</label></div>'
        +'<div><input id="alignright" name="align" type="radio" value="right"><label for="alignright">'+lang.right+'</label></div>'
        +'<div><input id="alignnone" name="align" type="radio" value="none"><label for="alignnone">'+lang.none+'</label></div>'
        +'</div><div class="clear"></div>'
        +'<a href="javascript:mediaAdd(\''+thumb+'\',\''+alt+'\',\''+src+'\');" class="button right">'+lang.add+'</a>'
        +'<div class="clear"></div></div>';
    $(document).keydown(function(e){
        if(e.keyCode == 32){
            mediaAdd(thumb,alt,src);
            $('#result').empty();
            $(document).removeEvents();
        }
    });
   tool.pop(content);
   return false;
};
var mediaRemove = function(ID, name){
    if(name){
        get('mediaUploader').remove(name);
        $(get(name)).remove();
    } else {
        var func = 'tool.post({\'url\':\''+ url +',removeMedia\','
                +'\'data\':\'ID='+ID+'\','
                +'\'onComplete\':function(r){'
                    +'if(r.code == 200) $(\'#'+ID+'\').remove(); '
                    +'else tool.result(r.code,r.status,r.message,r.fields,r.redirect,r.debug);'
                +'}'
        +'});';
        tool.confirm(lang.delete_image, lang.are_you_sure, func);
    }
};