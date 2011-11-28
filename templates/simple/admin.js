// the function for non strict file names 
/*
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
*/
/*------------------------------------------------------------------*/
var foreignChars = {
// Sings
'32':"_",		// space
'162':"cent",	// ¢ - Cent sign
'163':"libra",	// £ - Pound sign
'165':"jen",	// ¥ - Yen sign
'176':"stupen",	// ° - Degree sign
'8356':"lira",	// ₤ - Lira sign
// Latin 1 and Turkish
'224':"a", '225':"a", '226':"a", '227':"a", '228':"a", '229':"a", '230':"a", '257':"a", '259':"a", '261':"a",	// à, á, â, ã, ä, å, æ, ā, ă, ą
'231':"c", '263':"c", '265':"c", '267':"c", '269':"c",	// ç, ć, ĉ, ċ, č
'271':"d", '273':"d",  // ď, đ 
'232':"e", '233':"e", '234':"e", '235':"e", '275':"e", '277':"e", '279':"e", '281':"e", '283':"e",    // è, é, ê, ë, ē, ĕ, ė, ę, ě
'285':"g", '287':"g", '289':"g", '291':"g",	// ĝ, ğ, ġ, ģ
'293':"h", '295':"h",	// ĥ,ħ 
'105':"i", '236':"i", '237':"i", '238':"i", '239':"i", '297':"i", '299':"i", '301':"i", '303':"i", '305':"i", '307':"i",    // ı, ì, í, î, ï, ĩ, ī, ĭ, į, ı, ĳ
'309':"j",  // ĵ
'311':"k", '312':"k",  // ķ, ĸ
'314':"l", '316':"l", '318':"l", '320':"l", '322':"l",	// ĺ, ļ, ľ, ŀ, ł
'240':"eth",// ð
'241':"n", '324':"n", '326':"n", '327':"n", '328':"n", '329':"n", '331':"n",	// ñ, ń, ņ, ņ, ň, ŉ, ŋ 
'242':"o", '243':"o", '244':"o", '245':"o", '246':"o", '248':"o", '333':"o", '335':"o", '337':"o",    // ò, ó, ô, õ, ö, ø, ō, ŏ, ő 
'341':"r", '345':"r", '343':"r",    // ŕ, ř, ŗ
'347':"s", '349':"s", '351':"s", '353':"s",    // ś, ŝ, ş, š
'355':"t", '357':"t", '359':"t",  // ţ, ť, ŧ
'254':"thorn",  // þ
'223':"ss", // ß
'249':"u", '250':"u", '251':"u", '252':"u", '361':"u", '363':"u", '365':"u", '367':"u", '369':"u", '371':"u",    // ù, ú, û, ü, ũ, ū, ŭ, ů, ű, ų
'373':"w", // ŵ
'253':"y", '255':"y", '375':"y",  // ý, ÿ, ŷ
'378':"z", '380':"z", '382':"z",	// ź, ż, ž
// Greek characters
'912':"i",	// ΐ 
'940':"a",	// ά
'941':"e",	// έ
'942':"i",	// ή
'943':"i",	// ί
'945':"a",	// α
'946':"b",	// β
'947':"g",	// γ
'948':"d",	// δ
'949':"e",	// ε
'950':"z",	// ζ
'951':"i",	// η
'952':"th",	// θ
'953':"i",	// ι
'954':"k",	// κ
'955':"l",	// λ
'956':"m",	// μ
'957':"n",	// ν
'958':"ks",	// ξ
'959':"o",	// ο
'960':"p",	// π
'961':"r",	// ρ
'962':"s",	// ς
'963':"s",	// σ
'964':"t",	// τ
'965':"i",	// υ
'966':"f",	// φ
'967':"x",	// χ
'968':"ps",	// ψ
'969':"o",	// ω
'970':"i",	// ϊ
'971':"ou",	// ϋ
'972':"o",	// ό
'973':"i",	// ύ
'974':"o",	// ώ
// Azbuka characters
'1072':"a", // а 
'1073':"b", // б 
'1074':"v", // в 
'1075':"g", // г 
'1076':"d", // д 
'1077':"e", // е 
'1078':"zh", // ж 
'1079':"z", // з 
'1080':"i", // и 
'1081':"j", // й 
'1082':"k", // к 
'1083':"l", // л 
'1084':"m", // м 
'1085':"n", // н 
'1086':"o", // о 
'1087':"p", // п 
'1088':"r", // р 
'1089':"s", // с 
'1090':"t", // т 
'1091':"u", // у 
'1092':"f", // ф 
'1093':"h", // х 
'1094':"c", // ц 
'1095':"ch", // ч 
'1096':"sh", // ш 
'1097':"sch", // щ 
'1099':"y", // ы 
'1101':"e", // э 
'1102':"yu", // ю 
'1103':"ya", // я 
'1105':"yo", // ё 
};
var urn = function(v){
	val = '';
	var v = v.toLowerCase();
	var l = 0;
	while(l < v.length){
		var code = v.charCodeAt(l);
		val += ((code < 48 || code > 57) && (code < 97 || code > 122) && code != 45 && code != 95)
					? (foreignChars[code] ? foreignChars[code] : ''):String.fromCharCode(code);
		l++;
	}
	return val.replace(/(_-|[-]{2,})/g, '_').replace(/([_]+)/g, '_').replace(/[_]$/, '');
};
