// JavaScript Document

if( !JCMS.modules ) JCMS.modules = {};

JCMS.modules.sysinfo = {
	showPage: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			this.navigator('/core/sysinfo',1);
			this.preloader(0);
		}, data);

		
	},
}