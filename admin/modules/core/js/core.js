// JavaScript Document

JCMS = {
	path: "/admin",
	
	init: function(){
		
	},
	show_page: function(){
		JCMS.preloader(1);
		if( $("#ajax_page1").css('display') == 'none' ){ 
			$("#ajax_page1").stop(1,1).fadeIn(300, function(){ JCMS.preloader(0); });
			$("#ajax_page2").stop(1,1).fadeOut(300);
		} else {
			$("#ajax_page1").stop(1,1).fadeOut(300);			
			$("#ajax_page2").stop(1,1).fadeIn(300, function(){ JCMS.preloader(0); });
		}
	},
	/* Возращает ссылку на dom объект с блоком текущей ajax страницы */
	getCurPageShowed: function(){
		if( $("#ajax_page1").css('display') != 'none' ){ 
			return $("#ajax_page1");
		} else {
			return $("#ajax_page2");
		}
	},
	message: function(text, type, obj){
		switch(type){
			case "error": type = "danger"; break;
			case "warning": type = "warning"; break;
			case "notice": type = "success"; break;
			case "info": type = "info"; break;
			default: type="info";	
		}
		$('<div class="alert alert-'+String(type)+' fade in"><a href="#" class="close" data-dismiss="alert" title="Закрыть">&times;</a>'+String(text)+'</div>').prependTo(obj?obj:JCMS.getCurPageShowed());
	},
	preloader: function(action){
		if( action == 1 ){
			if( $("#loader-wrapper").css('display') == 'none' ){ 
				$("#loader-wrapper, #loader-text").stop(1,1).fadeIn(300);
			}
		} else {
			if( $("#loader-wrapper").css('display') != 'none' ){ 
				$("#loader-wrapper, #loader-text").stop(1,1).fadeOut(300);
			}
		}
	},
	/* навигатор по ссылкам */
	navigator:function(e, ret_path){
		if( typeof e == 'string' ){
			var tmp = e.toLowerCase().split("?");
			pathname = tmp[0];
			_pathname = tmp[1]!=undefined?"?"+String(tmp[1]):"";
		} else
		if( e == undefined ){
			pathname = document.location.pathname;
			_pathname = document.location.search!=undefined?document.location.search:"";
		} else {
			pathname = e.target.pathname;			
			_pathname = e.target.search!=undefined?e.target.search:"";
		}
		if( pathname == '/' ){ return true; }
		var url = [];
		var _url = pathname.toLowerCase().split("/");
		if( _url.length == 0 ){ return false; }
		/* удаляем пустые элементы */
		for(var i=0,n=0,m=0;i<_url.length;i++){if(_url[i]!=""){ if(n==0){n++;url[m]=_url[i]; } else { url[m]=_url[i].charAt(0).toUpperCase() + _url[i].substr(1, _url[i].length-1);} m++; } }
		if( url.length == 0 ){ return false; }
		if( !JCMS.in_array(url[0], JCMS.navigator_urls) ){ return true; } // ссылка не должна быть обработано навигатором, передаем управление обратно
		var func = url.join("");
		var script = url.slice(0,2).join("");
		if( ret_path ){ return func; }
		func = func;
		// меняем урл
		$("body").click(); // скрываем открытую вкладку меню...
		history.pushState(null, null, pathname+_pathname);
		JCMS.navigator_curl = pathname+_pathname;
		JCMS.body(0);
		// если компонент еще не был загружен, загружаем сначала файл с компонентом...
		if( typeof eval("JCMS."+func) != 'function' ){
			$.getScript(document.location.protocol+"//"+document.location.host+"/system/modules/arm/"+script+"/"+script+".arm.js");
		}
		var self = this;
		// затем подключаем сам компонент
		$.proxy(setTimeout(function(){
			if( typeof eval("JCMS."+func) == 'function' ){
				try{
					// запускаем обработчик
					if( eval("JCMS."+func+"()") != true ){
						alert("Внутренняя ошибка! [jcms.core.navigator#3]");
					}
				} catch( ex ){
					alert("Ошибка JavaScript: "+ex.message+". [jcms.core.navigator#2]\n\n"+ex.stack);
				}
			} else {
				alert("Ошибка: обработчик запроса не определён! [jcms.core.navigator#1]");
			}
		},500),self);
		
		return false;
	},
	navigatorDaemon: function(){
		var tmp = document.location.pathname+(document.location.search!=undefined?document.location.search:"");
		if( tmp != JCMS.navigator_curl ){ JCMS.navigator(tmp); }
		clearTimeout(JCMS.navigator_timer);
		JCMS.navigator_timer = setTimeout("JCMS.navigatorDaemon();",500);
	},
	isFunc: function(func_name){
		if (typeof func_name == 'string'){
			return (typeof window[func_name] == 'function');
		} else{
			return (func_name instanceof Function);
		}
	},
	in_array: function(needle, haystack, strict) {
		var found = false, key, strict = !!strict;
	
		for (key in haystack) {
			if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
				found = true;
				break;
			}
		}
	
		return found;
	},
	/* получить куки */
	getCookie:function(name) {
		var cookie = " " + document.cookie;
		var search = " " + name + "=";
		var setStr = null;
		var offset = 0;
		var end = 0;
		if (cookie.length > 0) {
			offset = cookie.indexOf(search);
			if (offset != -1) {
				offset += search.length;
				end = cookie.indexOf(";", offset)
				if (end == -1) {
					end = cookie.length;
				}
				setStr = unescape(cookie.substring(offset, end));
			}
		}
		return(setStr);
	},
	/* сохранить в куки */		
	setCookie: function(name, value, expires, path, domain, secure) {
		  document.cookie = name + "=" + escape(value) +
			((expires) ? "; expires=" + expires : "") +
			((path) ? "; path=" + path : "") +
			((domain) ? "; domain=" + domain : "") +
			((secure) ? "; secure" : "");
	},
	/* получает запрошенный GET параметр из запроса */
	$_GET:function(key) {
		var s = window.location.search;
		s = s.match(new RegExp(key + '=([^&=]+)'));
		return s ? s[1] : false;
	},
	ajax: function(data, url, callback, is_multi, progressCallback){
		//console.log(data);

		if( data == undefined ){ data = document.location.search!=undefined?document.location.search.replace("?",""):""; }
		if( url == undefined ){ url = document.location.href; }
		$.ajax({
			type:'POST', url:url, dataType:'json', data:data, cache:false, contentType: (!is_multi?"application/x-www-form-urlencoded":false),mimeType:(!is_multi?"application/x-www-form-urlencoded":"multipart/form-data"), processData : (!is_multi?true:false),
			xhr: function() {
					var myXhr = $.ajaxSettings.xhr();
					if(myXhr.upload && progressCallback){
						myXhr.upload.addEventListener('progress',progressCallback, false);
					}
					myXhr.data = data;
				return myXhr;
			},
			beforeSend: function(jqXHR,settings){ JCMS.preloader(1); },
			error: function(jqXHR,textStatus,errorThrown){ alert("AJAX ERROR! [jcms.core.ajax#1]\n\nОтвет сервера: "+jqXHR.status+" "+jqXHR.statusText+(errorThrown.stack?"\n\n"+errorThrown.stack:'')); },
			complete: function(jqXHR,textStatus){ JCMS.preloader(0); JCMS.ajax_nohide=false;JCMS.ajax_noclear=false; },
			success: function(data,textStatus,jqXHR){
				console.log('AJAX response data: ',data);
				if( data ){
					data['load_module'] = data['load_module'].join('/');
					console.log(data['load_module']);
					if( data['load_module'] ){
//						$.getScript(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/"+data['load_module']+".jcms.js");
					}
					// если в ответе передана callback функция, выполняем её, иначе выполняе
					if( data['callback'] && (typeof eval(data['callback']) == 'function') ){ eval(data['callback']+"(data);"); } else if( callback != undefined && typeof callback == 'function' ){ callback(data); }
				}
				$("a.navigator", document).unbind('click').click(function(e) { return JCMS.navigator(e); });
			},
		});
	},
	init:function(){
		JCMS.preloader(0);
		if( document.location.pathname.replace(new RegExp("^"+JCMS.path.replace("/","\/"),"g"),"") == '/' ){ JCMS.ajax(); } else { JCMS.navigator(); }
		$("a.navigator", document).unbind('click').click(function(e) { return JCMS.navigator(e); });
		JCMS.navigator_timer = setTimeout("JCMS.navigatorDaemon();",500);
	},
}



//setInterval("JCMS.show_page();", 3000);
JCMS.init();