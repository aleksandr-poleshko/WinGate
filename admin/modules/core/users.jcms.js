// JavaScript Document

if( !JCMS.modules ) JCMS.modules = {};

JCMS.modules.users = {
	// для главной страницу модуля
	showMainPage: function(data){
		var self = this;
		self.navigator('/core/users',1);
		this.showPage(data['template'], function(data){
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/dataTables.jquery.min.js", function(data){
				JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/dataTables.bootstrap.js", function(data){
					JCMS.modules.users.loadTable1 = false;
					JCMS.modules.users.loadTable2 = false;
					// таблица управление юзерами
					JCMS.modules.users.datatable1 = $('#usersViewTable').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable1Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблицы...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_pagesTableSearch1').attr('disabled', false);
								} else {
									$('input, button', '.jcms_pagesTableSearch1').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.users.loadTable1 = 1;
						if( JCMS.modules.users.loadTable2 == 1 ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){
								JCMS.ajax("", JCMS.navigator('/core/users/edit?user_id='+id,1));			
							}
                        });
					}).ajax.reload(); // первая прорисовка таблицы AJAX данными
					$("div.jcms_pagesTableSearch1>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.users.datatable1.search(form[0].value).draw();
						return false;
					});
					// таблица управление группами
					JCMS.modules.users.datatable2 = $('#groupsViewTable').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable2Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблицы...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_pagesTableSearch2').attr('disabled', false);
								} else {
									$('input, button', '.jcms_pagesTableSearch2').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.users.loadTable2 = 1;
						if( JCMS.modules.users.loadTable1 == 1 ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){
								JCMS.ajax("", JCMS.navigator('/core/users/edit?group_id='+id,1));			
							}
                        });
					}).ajax.reload(); // первая прорисовка таблицы AJAX данными
					$("div.jcms_pagesTableSearch2>form").eq(1).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.users.datatable2.search(form[0].value).draw();
						return false;
					});
					
					$('.jcms_pageAddUser').click(function(e) {
                        JCMS.ajax("", JCMS.navigator('/core/users/add_user',1));
                    });
					$('.jcms_pageAddGroup').click(function(e) {
                        JCMS.ajax("", JCMS.navigator('/core/users/add_group',1));
                    });
					self.tooltipInit();
					if( window.JCMS_usersActiveList == 'groups' ){
						$("a[href='#panel2']").click();
					}
					window.JCMS_usersActiveList = undefined;
				}, data);
			}, data);
		}, data);		
	},
	
	showPage_addUser: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			this.navigator('/core/users/add_user',1);
				// обработчик отправки формы
				$("div.jcms_pageAdd>form").eq(0).unbind('submit').submit(function(e) {
					self.ajaxForm(this);
					
					return false;
				});
				$("#passwGenerate").click(function(e) {
					JCMS.modules.users.genPassw($("input[name=user_passw1], input[name=user_passw2]"), 10);                    
                });
				JCMS.preloader(0);
		}, data);
	},

	showPage_addGroup: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			this.navigator('/core/users/add_group',1);
				// обработчик отправки формы
				$("div.jcms_pageAdd>form").eq(0).unbind('submit').submit(function(e) {
					window.JCMS_usersActiveList='groups';
					self.ajaxForm(this);
					
					return false;
				});
				$("#passwGenerate").click(function(e) {
					JCMS.modules.users.genPassw($("input[name=user_passw1], input[name=user_passw2]"), 10);                    
                });
				JCMS.preloader(0);
		}, data);
	},

	showPage_editUser: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			// обработчик отправки формы
			$("div.jcms_pageAdd>form").eq(0).unbind('submit').submit(function(e) {
				self.ajaxForm(this);				
				return false;
			});
			$('select[name=user_group] option[value='+String(data['group_id'])+']').prop('checked', true);
			$('input[name=user_status][value='+String(data['admin_status'])+']').click();
			JCMS.message("Для изменения пароля введите новый пароль в соответствующее поле. Что-бы оставить текущий пароль, оставьте поле &laquo;Новый пароль&raquo; не заполненным.","", 'warning');
			$("#passwGenerate").click(function(e) {
				JCMS.modules.users.genPassw($("input[name=user_passw1], input[name=user_passw2]"), 10);                    
			});
		    // кнопка удаления страницы (диалог подтверждения)
			$('#userDeleteConfirm').click(function(e) {
				JCMS.ajax("action=deleteUser");
			});
			JCMS.preloader(0);
		}, data);
	},

	showPage_editGroup: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			// обработчик отправки формы
			$("div.jcms_pageAdd>form").eq(0).unbind('submit').submit(function(e) {
				window.JCMS_usersActiveList='groups';
				JCMS.interfaceLoad = false;
				self.ajaxForm(this);				
				return false;
			});
		    // кнопка удаления страницы (диалог подтверждения)
			$('#groupDeleteConfirm').click(function(e) {
				window.JCMS_usersActiveList='groups';
				JCMS.ajax("action=deleteGroup");
			});
			JCMS.preloader(0);
		}, data);
	},

	result: function(data){
		JCMS.preloader(0);
		if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
		if( data['addUser'] == 'success' || data['addGroup'] == 'success' || data['deleteUser'] == 'success' || data['deleteGroup'] == 'success' || data['editUser'] == 'success' || data['editGroup'] == 'success' ){
			this.navigator('/core/users');
		}
	},
	
	genPassw: function(obj, len){
		if( window.genPasswInterval != undefined ) clearInterval(window.genPasswInterval);
		if( !len || len == undefined ) len = 8;
		var result = '';
        var words = 'abcdefghkmnpqastuvwxyzABCDEFGHKMNPQASTUVWXYZ23456789';
		window.genPasswInterval = setInterval(function(){
			position = Math.floor ( Math.random() * words.length-1);
			result = result + words.substring(position, position+1);
			if( result.length >= len ) clearInterval(window.genPasswInterval);
			$(obj).val(result+JCMS.modules.users.genStr(len-result.length-1));
			$(obj).attr('type', 'text');
		},50);
	},
	
	genStr: function(len){
		if( len == undefined ) len = 10;
		var result = '';
		var words = 'abcdefghkmnpqastuvwxyzABCDEFGHKMNPQASTUVWXYZ23456789';
		for(i=0; i<len; ++i) {
			position = Math.floor ( Math.random() * words.length - 1 );
			result = result + words.substring(position, position + 1);
		}
        return result;
	}
}
