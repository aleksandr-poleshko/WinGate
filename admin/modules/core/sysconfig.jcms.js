// JavaScript Document

if( !JCMS.modules ) JCMS.modules = {};

JCMS.modules.sysconfig = {
	showPage: function(data){
		var self = this;
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			this.navigator('/core/sysconfig',1);
			var form = $('form.jcms_sysConfig');
			if( data['read_only'] != undefined && data['read_only'] == 1 ){
				// отключаем все элементы формы
				form.find('input, textarea, button, select, div.bootstrap-tagsinput').attr('disabled','disabled');
				form.unbind('submit').submit(function(e) {
					return false;
				});
			} else {
				// обработчики событий
				form.unbind('submit').submit(function(e) {
					self.ajaxForm(this, JCMS.modules.sysconfig.result);			
					return false;
				});
			}
			this.preloader(0);
		}, data);		
	},
	
	result: function(data){
		if( data['result'] == 2 ){
			// ошибка при открытии страницы
			if( data['template'] != undefined && $.trim(data['template']) != '' ){
				this.showPage(data['template']);
			}
		} else
		if( data['result'] == 1 ){
			// результат редактирования формы
			if( data['saveConfig_result'] == 'success' ){
				this.preloader(1, "Сохранение настроек...<br/>Пожалуйста подождите...");
				setTimeout("window.location.reload();", 1500);
				return;				
			} else {
				if( data['template'] != undefined && $.trim(data['template']) != '' ){
					this.getCurPageShowed().prepend(data['template']);
				}
			}
		}
		this.preloader(0);
	}
}