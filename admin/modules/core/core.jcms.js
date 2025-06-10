// JavaScript Document

JCMS = {
	debug: 1,
	path: "",
	initComplete: false,
	interfaceLoad: false,
	loadedScripts: [],
	def_title: "",
	isCtrl: false, // флаг: нажат ли Ctrl
	init: function(def_config){
		self = this;
		if( def_config != undefined ){
			// устанавливаем значения конфига по умолчанию
			$.each(def_config, function(key,val){
				self[key] = val;
			});
			if( JCMS.path != '/' && JCMS.path.substr(-1,1) == '/' ) JCMS.path = JCMS.path.substr(0, JCMS.path.length-1);
		}
		if( this.def_title == "" ){ this.def_title = document.title; }
		// загрузка js скриптов...
		var scripts = [];
		scripts.push(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/bootstrap.min.js");
		scripts.push(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/jquery-ui.min.js");		
		scripts.push(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/form.jquery.js");
		scripts.push(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/md5.jquery.js");
		var counter = 0;
		var self = this;
		for(var i=0; i<scripts.length; i++){
			this.loadedScripts.push(scripts[i]);
			$.getScript(scripts[i]).done(function(script,textStatus ){
				if( self.initComplete == -1 ) return; 
				counter++;
				if( counter == scripts.length ){
					// все скрипты успешно загружены...
					self.initComplete=true;
					if( document.location.pathname.replace(new RegExp("^"+JCMS.path.replace("/","\/"),"g"),"") == '/' ){ self.ajax(); } else { self.navigator(); }
					// для pushState истории, отлавливаем клик по кнопке назад в браузере и перерисовываем страницу...
					$(window).bind('popstate', function() {
						self.ajax();
					});
				}
			}).fail(function(jqxhr, settings, exception){
				if( self.initComplete == -1 ) return; 
				self.initComplete=-1;
				self.message("КРИТИЧЕСКАЯ ОШИБКА JENSEN CMS!","Не удалось загрузить файл &laquo;<a href=\""+String(this.url)+"\" target=\"_blank\">"+String(this.url.slice(0,this.url.indexOf('\?')))+"</a>&raquo;. [js/core.init]", 'error'); self.preloader(0); return false;
			});
		}
		$(document).keydown(function(event){ if(event.which=="17") JCMS.isCtrl = true; });		
		$(document).keyup(function(){ JCMS.isCtrl = false; });
	},
	showPage: function(page_body, callback, callback_data){
		JCMS.preloader(1);
		var self = this;
		if( $("#ajax_page1").stop(null,1,1).css('display') == 'none' ){ 
			$("#ajax_page1").html(page_body);
			$("#ajax_page1").stop(null,1,1).fadeIn(500, function(){  if( !self.load_callback(self, callback, callback_data) ){ JCMS.preloader(0); } });
			$("#ajax_page2").stop(null,1,1).fadeOut(250, function(){ $("#ajax_page2").html(''); });			
		} else {
			$("#ajax_page2").html(page_body);
			$("#ajax_page2").stop(null,1,1).fadeIn(500, function(){  if( !self.load_callback(self, callback, callback_data) ){ JCMS.preloader(0); } });
			$("#ajax_page1").stop(null,1,1).fadeOut(250, function(){ $("#ajax_page1").html('');});			
		}
	},
	/* Возращает ссылку на dom объект с блоком текущей ajax страницы */
	getCurPageShowed: function(){
		if( $("#ajax_page1").stop(null,1,1).css('display') != 'none' ){ 
			return $("#ajax_page1");
		} else {
			return $("#ajax_page2");
		}
	},
	message: function(title, text, type, obj){
		console.log(obj);
		obj = $(obj);
		switch(type){
			case "error": type = "danger"; break;
			case "warning": type = "warning"; break;
			case "notice": type = "success"; break;
			case "info": type = "info"; break;
			default: type="info";	
		}
		$('<div class="alert alert-'+String(type)+'" style="display:none;"><a href="#" class="close" data-dismiss="alert" title="Закрыть">&times;</a><b>'+String(title)+'</b><br />'+String(text)+'</div>').prependTo(obj?obj:JCMS.getCurPageShowed()).fadeIn(300);
	},
	preloader: function(action, custom_text){
		if( action == 1 ){
			if( window.JCMS_preloaderOrigText != undefined ){ $("#loader-text").html(window.JCMS_preloaderOrigText); }
			if( custom_text != undefined ){ window.JCMS_preloaderOrigText=$("#loader-text").html(); $("#loader-text").html(custom_text+"<br /><span></span>");  }
			if( $("#loader-wrapper").css('display') == 'none' ){ 
				$("#loader-wrapper, #loader-text").stop(null,1,1).fadeIn(150);
			}
		} else {
			if( $("#loader-wrapper").css('display') != 'none' ){ 
				$("#loader-wrapper, #loader-text").stop(null,1,1).fadeOut(300,'linear', function(){ if( window.JCMS_preloaderOrigText != undefined ){ $("#loader-text").html(window.JCMS_preloaderOrigText); } });
			}
		}
	},
	/* навигатор по ссылкам */
	navigator:function(e, type){
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
			$("body").click(); // скрываем открытую вкладку меню...
		}
		// убираем слэш с конца запроса
		while(1){
			if( pathname != '/' && pathname.substr(-1,1) == '/' ) pathname = pathname.substr(0, pathname.length-1); else break;
		}
		// правим смещение пути относительно коня сайта
		pathname = (this.path+'/'+(pathname.replace(new RegExp("^"+JCMS.path.replace("/","\/"),"g"),"")));
		// удаляем задвоенные слеши
		while(1){
			var regexp = /\/\//g;
			if( regexp.test(pathname) ){
				pathname = pathname.replace(regexp,'/');
			} else {
				break;
			}
		}
		if( type == 2 ) return pathname+_pathname;
		// меняем урл
		if( document.location.pathname != pathname+_pathname){
			history.pushState(null, null, pathname+_pathname);
		}
		if( type != undefined ) return pathname+_pathname;
		JCMS.navigator_curl = pathname+_pathname;
		this.ajax();		
		return;
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
//		  var date = new Date();
//		  date.setTime(date.getTime()+(365*10*24*60*60*1000));
//		  expires=date.toGMTString();
		  document.cookie = name + "=" + escape(value) +
			((expires) ? "; expires=" + expires : "") +
			((path)?"; path=" + path :"" ) + 
			((domain) ? "; domain=" + domain : "") +
			((secure) ? "; secure" : "");
	},
	/* получает запрошенный GET параметр из запроса */
	$_GET:function(key) {
		var s = window.location.search;
		s = s.match(new RegExp(key + '=([^&=]+)'));
		return s ? s[1] : false;
	},
	/* begin ajax events */
	ajaxBeforeSend: function(jqXHR,settings){ if( JCMS.ajax_noloader != 1 ) JCMS.preloader(1); },
	ajaxError: function(jqXHR,textStatus,errorThrown){ JCMS.preloader(0); JCMS.message("AJAX ERROR! [js/core.ajax]","<b>Код ответа:</b> "+jqXHR.status+" "+jqXHR.statusText+'<br><b>Ответ сервера:</b> '+jqXHR.responseText+(errorThrown.stack?"<br><b>Стэк-трейс:</b><br><i>"+errorThrown.stack.replace("\n","<br>")+"</i>":''), 'error'); },
	ajaxComplete: function(jqXHR,textStatus){ JCMS.ajax_nohide=false;JCMS.ajax_noclear=false;JCMS.ajax_noloader=false; },
	ajaxDone: function(data, textStatus, jqXHR, callback){
		if( this.debug ) console.log('JCMS_AJAX: ',data);
		if( data['access_deny'] == 1 ){
			this.navigator('/',1);
			JCMS.preloader(0);
			this.showPage(data['template']);
			JCMS.ajax=function(){};
			$(window).unbind('popstate');
			return;
		}
		if( !data ) return;		
		// если нужно, перед вызовом колбэк функции из ответа, загружаем скрипты, затем выполняем колбэк функцию.
		if( data['load_module'] ){
			// загружаем модуль, и если в ответе передана callback функция, выполняем её
			if( $.isArray(data['callback']) ){ data['callback'].push(callback); } else { data['callback'] = [data['callback'], callback]; }
			this.load_module(data['load_module'], data['callback'], data);
		} else {
			// если в ответе передана callback функция, выполняем её
			this.load_callback(this, data['callback'], data);
			// выполняем колбэк переданный в функцию...
			this.load_callback(this, callback, data);
		}
		// обработчик запроса не определён, выводим ответ на страницу...
		if( callback == undefined && !$.isFunction(eval(callback)) && !$.isArray(eval(callback)) && data['callback'] == undefined && !$.isFunction(eval(data['callback'])) && !$.isArray(eval(data['callback'])) ){	
			if( data['template'] != undefined ){
				this.showPage(data['template']);
			} else {
				this.message("Обработчик ответа не определён!", "Результат выполнения запроса неизвестен.<br>Сервер вернул ответ:<br><i><pre style=\"background:none; border:none; padding:0px; margin:0px; color:inherit;\">"+$.param(data)+"</pre></i>", 'error');
			}
		}
		if( data['module_title'] != undefined ){
			document.title = (data['module_title']!=""?data['module_title']+" | ":"")+this.def_title;
		}
		
		this.tooltipInit();			
	},
	/* end ajax events */
	ajax: function(data, url, callback, is_multi, progressCallback){
		if( data == undefined ){ data = document.location.search!=undefined?document.location.search.replace("?",""):""; }
		if( url == undefined ){ url = document.location.href; }
		var self = this;
		if( this.interfaceLoad != true ){ data += "&load_interface=1"; }		
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
			beforeSend: function(jqXHR,settings){ self.ajaxBeforeSend(jqXHR, settings) },
			error: function(jqXHR,textStatus,errorThrown){ self.ajaxError(jqXHR,textStatus,errorThrown) },
			complete: function(jqXHR,textStatus){ self.ajaxComplete(jqXHR,textStatus) },
			success: function(data,textStatus,jqXHR){ self.ajaxDone(data, textStatus, jqXHR, callback) },
		});
	},
	ajaxForm: function(form_obj, callback){
		var self = this;
		$(form_obj).ajaxSubmit({
			type:'POST', dataType:'json', cache:false,
			beforeSubmit: function(jqXHR,settings){ self.ajaxBeforeSend(jqXHR, settings) },
			error: function(jqXHR,textStatus,errorThrown){ self.ajaxComplete(jqXHR,textStatus); self.ajaxError(jqXHR,textStatus,errorThrown) },
			success: function(data,textStatus,jqXHR){ self.ajaxComplete(jqXHR,textStatus); self.ajaxDone(data, textStatus, jqXHR, callback) },
		});
	},
	reloadCaptcha: function(e){
		$(e).attr('src', this.path+"/../captcha.jpg?_="+this.rand(1000000000000,9999999999999));
	},
	rand:function(min, max){
		return (Math.floor(Math.random() * (max - min + 1)) + min);
	},
	tooltipInit: function(){
		$('[rel=tooltip]').bs_tooltip({placement:'right', trigger:'hover'});
	},
	load_module: function(mod_name, callback, callback_data){
		var mod_url = document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/"+mod_name+".jcms.js";
		// проверяем не загружался ли этот модуль ранее. Нет необходимости загружать одинаковые скрипты несколько раз...
		if( $.inArray(mod_url, this.loadedScripts) == -1 ){
			// модуль ранее не загружался...
			this.loadedScripts.push(mod_url);
			var self = this;
			$.getScript(mod_url, function(){
				// если передано несколько колбэков, выполняем последовательно по одному... 
				self.load_callback(self, callback, callback_data);
			});	
		} else {
			// модуль уже загружался ранее... выполняем колбэк
			this.load_callback(this, callback, callback_data);
		}
	},
	
	load_script: function(url, callback, callback_data){
		// проверяем не загружался ли этот скрипт ранее. Нет необходимости загружать одинаковые скрипты несколько раз...
		if( $.inArray(url, this.loadedScripts) == -1 ){
			// скрипт ранее не загружался...
			this.loadedScripts.push(url);
			var self = this;
			$.getScript(url, function(){
				// если передано несколько колбэков, выполняем последовательно по одному... 
				self.load_callback(self, callback, callback_data);
			});	
		} else {
			// скрипт уже загружался ранее... выполняем колбэк
			this.load_callback(this, callback, callback_data);
		}
	},
	load_css: function(url){
		// проверяем не загружался ли этот модуль ранее. Нет необходимости загружать одинаковые скрипты несколько раз...
		if( $.inArray(url, this.loadedScripts) == -1 ){
			// скрипт ранее не загружался...
			this.loadedScripts.push(url);
			$('<link rel="stylesheet" type="text/css" href="'+url+'" />').appendTo("head");
		}
	},
	/* выполнение одного или нескольких колбэк функций */
	load_callback: function(self, callback, callback_data){
		// если передано несколько колбэков, выполняем последовательно по одному... 
		if( callback == undefined ){ return false; }
		var res=false;
		if( $.isArray(callback) ){
			var self = this;
			$.each(callback, function(key,val){
				if( val == undefined ){ return; }
				if( $.isFunction(eval(val)) ){ res = true; eval("("+val+").call(self,callback_data);"); } else { self.message("Callback функция &laquo;"+String(val.toString())+"&raquo; не выполнена! [js/core.load_callback#1]","", 'error'); JCMS.preloader(0); }
			});
		} else {
			// передан один колбэк, выполняем...
			if( $.isFunction(eval(callback)) ){ res = true; eval("("+callback+").call(this,callback_data);"); } else { self.message("Callback функция &laquo;"+String(callback.toString())+"&raquo;не выполнена! [js/core.load_callback#2]", "Модуль не загружен или функция не была определена!", 'error'); JCMS.preloader(0); }
		}
		return res;		
	},
	load_interface: function(data){
		if( this.interfaceLoad ) return;
		this.interfaceLoad = true;
		$("div#navbar").hide();
		$("div#navbar>").remove(); // удаляем старый если был...
		$(data['userbar_template']).appendTo($("div#navbar"));
		var nav = $(data['menu_template']).prependTo($("div#navbar"));
		$("a[href!=#]",nav).click(function(e) {
			JCMS.navigator($(this).attr('href'));
			$('body').click();
			return false;
        });
		$("div#navbar").stop(null,1,1).fadeIn(300);
		if( data['result'] == 0 ){
			$(this.getCurPageShowed()).prepend(data['template']);
			this.preloader(0);
		}
	},
	unload_interface: function(data){
		if( !this.interfaceLoad ) return;
		this.interfaceLoad = false;
		var self=this;
		$("div#navbar").stop(null,1,1).fadeOut(300, function(){ $(this).html(''); });
	},
	/* склонение окончаний. Пример: '1 слов'+JCMS.getNumEnding(1, ['слово','слова','слов']) - выведет 1 слово */
	getNumEnding: function(number, endingArray)
	{
		number = number % 100;
		if (number>=11 && number<=19) {
			ending=endingArray[2];
		}
		else {
			var i = number % 10;
			switch(i)
			{
				case (1): ending = endingArray[0]; break;
				case (2):
				case (3):
				case (4): ending = endingArray[1]; break;
				default: ending=endingArray[2];
			}
		}
		return ending;
	}
}