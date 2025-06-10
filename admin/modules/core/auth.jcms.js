// JavaScript Document

if( !JCMS.modules ) JCMS.modules = {};

JCMS.modules.auth = {
	showForm: function(data){
		this.showPage(data['template'], function(data){
			this.navigator('/',1);
			this.tooltipInit();
			$('img#captcha_img').click();
			// вешаем обработчик формы
			var form = $('form.jcms_authForm');
			var self = this;
			$("input[name=jcms_authToken]").val(data['authToken1']);
			JCMS.setCookie("jcms2_authToken", data['authToken2'], Math.floor(new Date().getTime() / 1000)+3600*24*30, "/");		/* "/" */				
			if( $('input[name=jcms_authUsername]').val() == '' ) $('input[name=jcms_authUsername]').val(JCMS.getCookie("jcms2_auth_login"));
			$('input#password, input[name=jcms_authCaptcha]').val('');
			if( $('input[name=jcms_authUsername]').val() == '' ){ $('input[name=jcms_authUsername]').focus(); } else { $('input[name=jcms_authPassword]').focus(); }
			form.unbind('submit').submit(function(e) {
				self.ajaxForm(this, JCMS.modules.auth.result);
				$('input#password, input[name=jcms_authCaptcha]').val('');
				
				return false;
			});
			this.preloader(0);
		}, data);		
	},
	logout: function(){
		this.ajax("auth=logout");
	},
	result: function(data){
		if( data['auth_result'] == 'success' ){
			JCMS.setCookie("jcms2_auth_token", data['auth_token'], Math.floor(new Date().getTime() / 1000)+3600*24*30, "/"); /* "////" */			
			var mess = [
				"Здравствуйте, "+(data['admin_name'])+"!", "Вы успешно авторизованы в панели управления сайтом!<br>При отсутствии активности более "+String(data['auth_inactiveSessionLifetime'])+" мин., сессия авторизации будет автоматически закрыта."+(data['shortSession']?"<br>Выбран режим &laquo;Короткая сессия&raquo; - сессия авторизации будет автоматически завершена через "+String(data['auth_shortSessionLifetime'])+" мин.":'')
			];
			if( data['result'] != 1 ){
				data['__mess__'] = mess;
				this.showPage(data['template'], function(data){ this.message(data['__mess__'][0], data['__mess__'][1], 'notice'); }, data);
				this.preloader(0);
			} else {
				this.message(mess[0], mess[1], 'notice');
				this.preloader(0);
			}
		} else
		if( data['auth_result'] == 'logout' && data['no_redir'] == undefined ){
			if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);this.preloader(0);}
			this.navigator('/');
		} else {	
			if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);this.preloader(0);}			
		}
		if( data['authToken1'] && data['authToken2'] ){
			$("input[name=jcms_authToken]").val(data['authToken1']);
			JCMS.setCookie("jcms2_authToken", data['authToken2'], Math.floor(new Date().getTime() / 1000)+3600*24*30, "/"); /* "////" */
		} else {
			JCMS.setCookie("jcms2_authToken", "", Math.floor(new Date().getTime() / 1000)-3600, '/');
		}
		$('img#captcha_img').click();
	}
	
}