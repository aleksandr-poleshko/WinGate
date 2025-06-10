// JavaScript Document

if( !JCMS.modules ) JCMS.modules = {};

JCMS.modules.cabinet = {
	// для главной страницы модуля 
	showMainPage: function(data){
		var self = this;
		self.navigator('/module/cabinet',1);
		this.showPage(data['template'], function(data){
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/dataTables.jquery.min.js", function(data){
				JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/dataTables.bootstrap.js", function(data){
					JCMS.modules.cabinet.loadTables = 0;
					JCMS.modules.cabinet.totalTables = 8;
					
					JCMS.modules.cabinet.datatable1 = $('#clientsViewTable1').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable1Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблиц...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_clientsTable1Search>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_clientsTable1Search').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.cabinet.loadTables++;
						if( JCMS.modules.cabinet.loadTables >= JCMS.modules.cabinet.totalTables ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							if( e.target.localName  == 'a' ){ 
								// игнорируем клики по ссылкам
								return false;
							}
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){
								var url = '/module/cabinet/edit_client?id='+id;
								if( JCMS.isCtrl ){
									var newWin = window.open(document.location.protocol+"//"+document.location.host+JCMS.navigator(url,2), url);
									newWin.focus();
								} else {
									JCMS.ajax("", JCMS.navigator(url,1));			
								}
							}
                        });
					}).order([0,'desc']).draw(); // первая прорисовка таблицы AJAX данными
					
					JCMS.modules.cabinet.datatable2 = $('#clientsViewTable2').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable2Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблиц...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_clientsTable2Search>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_clientsTable2Search').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.cabinet.loadTables++;
						if( JCMS.modules.cabinet.loadTables >= JCMS.modules.cabinet.totalTables ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
					}).order([0,'desc']).draw(); // первая прорисовка таблицы AJAX данными
					
					JCMS.modules.cabinet.datatable3 = $('#clientsViewTable3').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable3Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблиц...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_clientsTable3Search>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_clientsTable3Search').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.cabinet.loadTables++;
						if( JCMS.modules.cabinet.loadTables >= JCMS.modules.cabinet.totalTables ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							if( e.target.localName  == 'a' ){ 
								// игнорируем клики по ссылкам
								return false;
							}
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){
								var url = '/module/cabinet/edit_order?id='+id;
								if( JCMS.isCtrl ){
									var newWin = window.open(document.location.protocol+"//"+document.location.host+JCMS.navigator(url,2), url);
									newWin.focus();
								} else {
									JCMS.ajax("", JCMS.navigator(url,1));			
								}
							}
                        });
					}).order([[6,'asc']]).draw(); // первая прорисовка таблицы AJAX данными
					
					JCMS.modules.cabinet.datatable4 = $('#clientsViewTable4').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable4Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблиц...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_clientsTable4Search>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_clientsTable4Search').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.cabinet.loadTables++;
						if( JCMS.modules.cabinet.loadTables >= JCMS.modules.cabinet.totalTables ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							if( e.target.localName  == 'a' ){ 
								// игнорируем клики по ссылкам
								return false;
							}
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){ 
								var url = '/module/cabinet/edit_tariff?id='+id;
								if( JCMS.isCtrl ){
									var newWin = window.open(document.location.protocol+"//"+document.location.host+JCMS.navigator(url,2), url);
									newWin.focus();
								} else {
									JCMS.ajax("", JCMS.navigator(url,1));			
								}
							}
                        });
					}).order([0,'desc']).draw(); // первая прорисовка таблицы AJAX данными
					
					JCMS.modules.cabinet.datatable5 = $('#clientsViewTable5').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable5Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблиц...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_clientsTable5Search>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_clientsTable5Search').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.cabinet.loadTables++;
						if( JCMS.modules.cabinet.loadTables >= JCMS.modules.cabinet.totalTables ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							if( e.target.localName  == 'a' ){ 
								// игнорируем клики по ссылкам
								return false;
							}
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){ 
								var url = '/module/cabinet/edit_client?id='+id;
								if( JCMS.isCtrl ){
									var newWin = window.open(document.location.protocol+"//"+document.location.host+JCMS.navigator(url,2), url);
									newWin.focus();
								} else {
									JCMS.ajax("", JCMS.navigator(url,1));			
								}
							}
                        });
					}).order([2,'desc']).draw(); // первая прорисовка таблицы AJAX данными
					
					JCMS.modules.cabinet.datatable6 = $('#clientsViewTable6').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable6Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблиц...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_clientsTable6Search>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_clientsTable6Search').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.cabinet.loadTables++;
						if( JCMS.modules.cabinet.loadTables >= JCMS.modules.cabinet.totalTables ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							if( e.target.localName  == 'a' ){ 
								// игнорируем клики по ссылкам
								return false;
							}
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){ 
								var url = '/module/cabinet/view_ticket?id='+id;
								if( JCMS.isCtrl ){
									var newWin = window.open(document.location.protocol+"//"+document.location.host+JCMS.navigator(url,2), url);
									newWin.focus();
								} else {
									JCMS.ajax("", JCMS.navigator(url,1));			
								}
							}
                        });
					}).order([0,'desc']).draw(); // первая прорисовка таблицы AJAX данными
					
					JCMS.modules.cabinet.datatable7 = $('#clientsViewTable7').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable7Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблиц...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_clientsTable6Search>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_clientsTable6Search').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.cabinet.loadTables++;
						if( JCMS.modules.cabinet.loadTables >= JCMS.modules.cabinet.totalTables ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							if( e.target.localName  == 'a' ){ 
								// игнорируем клики по ссылкам
								return false;
							}
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){ 
								var url = '/module/cabinet/edit_order?id='+id;
								if( JCMS.isCtrl ){
									var newWin = window.open(document.location.protocol+"//"+document.location.host+JCMS.navigator(url,2), url);
									newWin.focus();
								} else {
									JCMS.ajax("", JCMS.navigator(url,1));			
								}
							}
                        });
					}).order([0,'desc']).draw(); // первая прорисовка таблицы AJAX данными
					
					JCMS.modules.cabinet.datatable8 = $('#clientsViewTable8').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTable8Data",
							type: 'POST',
							beforeSend: function(){
								JCMS.preloader(1, 'Загрузка таблиц...<br/>Пожалуйста подождите...');
							},
							error:function(){
								JCMS.preloader(0);
							},
							dataSrc: function(data){
								if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
								if( data['recordsTotal'] > 0 ){
									$('input, button', '.jcms_clientsTable6Search>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_clientsTable6Search').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.modules.cabinet.loadTables++;
						if( JCMS.modules.cabinet.loadTables >= JCMS.modules.cabinet.totalTables ) JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							if( e.target.localName  == 'a' ){ 
								// игнорируем клики по ссылкам
								return false;
							}
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){ 
								var url = '/module/cabinet/edit_email_blacklist?id='+id;
								if( JCMS.isCtrl ){
									var newWin = window.open(document.location.protocol+"//"+document.location.host+JCMS.navigator(url,2), url);
									newWin.focus();
								} else {
									JCMS.ajax("", JCMS.navigator(url,1));			
								}
							}
                        });
					}).order([0,'desc']).draw(); // первая прорисовка таблицы AJAX данными
										
					$("div.jcms_clientsTable1Search>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.cabinet.datatable1.search(form[0].value).draw();
						return false;
					});
					$("div.jcms_clientsTable2Search>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.cabinet.datatable2.search(form[0].value).draw();
						return false;
					});
					$("div.jcms_clientsTable3Search>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.cabinet.datatable3.search(form[0].value).draw();
						return false;
					});
					$("div.jcms_clientsTable4Search>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.cabinet.datatable4.search(form[0].value).draw();
						return false;
					});
					$("div.jcms_clientsTable5Search>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.cabinet.datatable5.search(form[0].value).draw();
						return false;
					});
					$("div.jcms_clientsTable6Search>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.cabinet.datatable6.search(form[0].value).draw();
						return false;
					});
					$("div.jcms_clientsTable7Search>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.cabinet.datatable7.search(form[0].value).draw();
						return false;
					});
					$("div.jcms_clientsTable8Search>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.cabinet.datatable8.search(form[0].value).draw();
						return false;
					});

					$('.jcms_balanceAdd').click(function(e) {
                        JCMS.ajax("", JCMS.navigator('/module/cabinet/add_balance',1));
                    });
					$('.jcms_tariffAdd').click(function(e) {
                        JCMS.ajax("", JCMS.navigator('/module/cabinet/add_tariff',1));
                    });
					$('.jcms_emailBlackListAdd').click(function(e) {
                        JCMS.ajax("", JCMS.navigator('/module/cabinet/add_email_blacklist',1));
                    });
					
					if( window.JCMS_pagesActiveList > 1 ){
						$("a[href='#panel"+String(window.JCMS_pagesActiveList)+"']").click();
					}
					window.JCMS_pagesActiveList = undefined;
					self.tooltipInit();
				}, data);
			}, data);
		}, data);		
	},

	showPage_editClient: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			// обработчик отправки формы
			$("div.jcms_clientAdd>form").eq(0).unbind('submit').submit(function(e) {
				self.ajaxForm(this);				
				return false; 
			});
			$('input[name=client_status][value='+String(data['client_status'])+']').click();
			$('textarea[name=client_adminNotes]').text(data['client_adminNotes']);
			JCMS.message("Для изменения пароля введите новый пароль в соответствующее поле. Что-бы оставить текущий пароль, оставьте поле &laquo;Новый пароль&raquo; не заполненным.","", 'warning');
			$("#passwGenerate").click(function(e) {
				JCMS.modules.cabinet.genPassw($("input[name=client_passw1], input[name=client_passw2]"), 10);                    
			});
			JCMS.preloader(0);
		}, data);
	},
	
	showPage_addBalance: function(data){
		var self = this;
		this.showPage(data['template'], function(data){
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/bootstrap-datepicker.min.js", function(data){
				JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/bootstrap-datepicker.ru.min.js", function(data){
					JCMS.load_css(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/css/bootstrap-datepicker.min.css");
					this.tooltipInit();
					// обработчик отправки формы
					$("div.jcms_clientAdd>form").eq(0).unbind('submit').submit(function(e) {
						window.JCMS_pagesActiveList='2';
						self.ajaxForm(this);				
						return false;
					});	  
					$('.datepicker').bs_datepicker({
						format: "dd.mm.yyyy",
						language: "ru",
						orientation: "bottom left",
					    autoclose: true,
						zIndexOffset:9999999,
						startDate: false,
					});
					JCMS.preloader(0);
				}, data);
			}, data);
		}, data);		
	},
	
	showPage_editOrder: function(data){
		this.showPage(data['template'], function(data){
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/bootstrap-datepicker.min.js", function(data){
				JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/bootstrap-datepicker.ru.min.js", function(data){
					JCMS.load_css(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/css/bootstrap-datepicker.min.css");
					this.tooltipInit();
					// обработчик отправки формы
					$("div.jcms_orderAdd>form").eq(0).unbind('submit').submit(function(e) {
						window.JCMS_pagesActiveList='3';
						self.ajaxForm(this);				
						return false; 
					});
					$('input[name=order_status][value='+String(data['order_status'])+']').click();
					$('.datepicker').bs_datepicker({
						format: "dd.mm.yyyy",
						language: "ru",
						orientation: "bottom left",
						autoclose: true,
						zIndexOffset:9999999,
						startDate: false,
					});	
					$('input[name="order_ip[]"]').off('keyup keydown keypress change').keyup(function(e){
						$(this).val($(this).val().replace(/[^.0-9]/gim, ''));
					}).change(function(e) {
						$(this).keyup();
					}).keydown(function(e) {
						$(this).keyup();
					}).keypress(function(e) { 
						$(this).keyup();
					});
					
					if( data['isTestOrder'] == 1 ){
						$('.testModeHidden').hide(); 
						$('.jcms_orderAdd h4 span').html('тестового');
						$('.jcms_orderAdd h4 u').attr('onclick', '').off('click').click(function(e) {
                            window.JCMS_pagesActiveList='7';
							JCMS.navigator('/module/cabinet');
                        });
						$('<tr><td>Дубль?</td><td><span style="color:'+(data['isTestFake']==1?'red; font-weight:bold;':'green')+'">'+(data['isTestFake']==1?'ДА':'НЕТ')+'</span></td></tr>').insertAfter($('table.table_orderEdit tr.testModeHidden').last());
						$("div.jcms_orderAdd>form").eq(0).unbind('submit').submit(function(e) {
							window.JCMS_pagesActiveList='7';
							self.ajaxForm(this);				
							return false; 
						});
					}

					JCMS.preloader(0);
				}, data);
			}, data);
		}, data);
	},

	showPage_addTariff: function(data){
		var self = this;
		this.showPage(data['template'], function(data){
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/bootstrap-datepicker.min.js", function(data){
				JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/bootstrap-datepicker.ru.min.js", function(data){
					JCMS.load_css(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/css/bootstrap-datepicker.min.css");
					this.tooltipInit();
					// обработчик отправки формы
					$("div.jcms_clientAdd>form").eq(0).unbind('submit').submit(function(e) {
						window.JCMS_pagesActiveList='4';
						self.ajaxForm(this);				
						return false;
					});	  
					$('.datepicker').bs_datepicker({
						format: "dd.mm.yyyy",
						language: "ru",
						orientation: "bottom left",
					    autoclose: true,
						zIndexOffset:9999999,
						startDate: false,
					});
					JCMS.preloader(0);
				}, data);
			}, data);
		}, data);		
	},

	showPage_editTariff: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			// обработчик отправки формы
			$("div.jcms_clientAdd>form").eq(0).unbind('submit').submit(function(e) {
				window.JCMS_pagesActiveList='4';
				self.ajaxForm(this);				
				return false; 
			});
			$('input[name=tariff_status][value='+String(data['tariff_status'])+']').click();

			JCMS.preloader(0);
		}, data); 
	},

	showPage_viewTicket: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			// обработчик отправки формы

			$('input[name=tariff_status][value='+String(data['tariff_status'])+']').click();
			// отпрака сообщения
			$("div.jcms_clientAdd>form").eq(1).unbind('submit').submit(function(e) {
				if( $.trim($('textarea[name=text]', this).val()) == '' ){
					alert("Сообщение не может быть пустым!");
					return false;
				}
				var str = $("div.jcms_clientAdd>form").eq(1).serialize(); 
				$('textarea[name=text], button', $('form.addMsgForm')).prop('disabled', 1);
				$.post(document.location, str, function(data){ 
					data = $.parseJSON(data);
					if( data['status'] != 1 ){
						$('textarea[name=text], button', $('form.addMsgForm')).prop('disabled', 0);
						if( data['error'] ) alert(data['error']); else alert('Error!');
					} else {
						setTimeout("$('textarea[name=text], button', $('form.addMsgForm')).prop('disabled', 0);",5000);
						$('textarea[name=text]', $('form.addMsgForm')).val('');
					}
				});
				return false;
			});
			// изменение статуса
			$("div.jcms_clientAdd>form").eq(0).unbind('submit').submit(function(e) {
				var str = $("div.jcms_clientAdd>form").eq(0).serialize(); 
				$('select').prop('disabled', 1);
				$.post(document.location, str, function(data){ 
					data = $.parseJSON(data);
					if( data['status'] != 1 ){
						$('select').prop('disabled', 0);
						if( data['error'] ) alert(data['error']); else alert('Error!');
					} else {
						setTimeout("$('select').prop('disabled', 0);",5000);
					}
				});
				return false;				
			});
			$("select[name=ticket_status]").off('change').change(function(e) {
               $("div.jcms_clientAdd>form").eq(0).submit(); 
            });
			JCMS.preloader(0);
			JCMS.modules.cabinet.ticketChat();
		}, data);
	},

	showPage_addEmailBlackList: function(data){
		var self = this;
		this.showPage(data['template'], function(data){
					this.tooltipInit(); 
					// обработчик отправки формы
					$("div.jcms_clientAdd>form").eq(0).unbind('submit').submit(function(e) {
						window.JCMS_pagesActiveList='8';
						self.ajaxForm(this);				
						return false;
					});
					JCMS.preloader(0);
		}, data);		
	},

	showPage_editEmailBlackList: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			// обработчик отправки формы
			$("div.jcms_clientAdd>form").eq(0).unbind('submit').submit(function(e) {
				window.JCMS_pagesActiveList='8';
				self.ajaxForm(this);				
				return false;  
			});
					// кнопка удаления страницы (диалог подтверждения)
					$('#bannerDeleteConfirm').click(function(e) {
						JCMS.ajax("action=delete_domain");
					}); 
			
			$('.jcms_clientAdd h4 span').html('Редактирование домена #'+data['id']);
			$('#buttonBlock').fadeIn();
			$('.jcms_clientAddSave').text('Сохранить изменения');
			JCMS.preloader(0);
		}, data);
	},
	

	result: function(data){
		JCMS.preloader(0);
		if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
		if( data['add'] == 'success' || data['delete'] == 'success' || data['edit'] == 'success' ){
			this.navigator('/module/cabinet');
		}
	},

	ticketChat: function(){
		$.post(document.location, {action:"getChatLog", lastMsg:$('.ticket_chatlog').attr('data-last_msg'), 'form_submit':1}, function(data){
			data = $.parseJSON(data);
			if( $('div[data-chat_id="'+String(data['ticket_id'])+'"]').length < 1 ) return false;
			setTimeout(function(){ JCMS.modules.cabinet.ticketChat();}, 5000);
			$('select[name=ticket_status] option[value='+String(data['ticket_status'])+']').prop('selected', 1);
			if( data['status'] == 1 ){
				if( data['chatlog'] && data['chatlog'].length > 0 ){ 
					console.log($('div[data-chat_id="'+String(data['ticket_id'])+'"] .ticket_chatlog'));
					if( $('.ticket_chatlog').attr('data-last_msg') != 0 ) var sound = true; else var sound = false;
					$('.ticket_chatlog').attr('data-last_msg', data['lastMsg']);
					$.each(data['chatlog'], function(key,val){
						$('<div class="'+String(val['type']=='admin'?'client':'admin')+'_msg"><span>'+String(val['type']=='admin'?'Администратор':'Клиент')+'</span><span title="'+String(val['timestamp'])+'">'+String(val['time'])+'</span><div class="clear"></div><div>'+String(val['text'])+'</div></div>').appendTo($('div[data-chat_id="'+String(data['ticket_id'])+'"] .ticket_chatlog'));
						if( val['type'] == 'client' && sound == true) JCMS.modules.cabinet.playNewMesage(); // есть новое сообщение
					});
					setTimeout(function(){$('.ticket_chatlog').animate({ scrollTop:$('.ticket_chatlog')[0].scrollHeight}, 1000);},500); // скролл в конец
				}
			}
		});	 
	},
	/* демон для проверки новых тикетов */
	ticketDaemon: function(){
		$.post('/admin/module/cabinet', {action:"ticketDaemon", 'form_submit':1}, function(data){
			if( window.__ticketDaemon ) clearTimeout(window.__ticketDaemon);
			window.__ticketDaemon = setTimeout("JCMS.modules.cabinet.ticketDaemon();", 30000);
			data = $.parseJSON(data);
			if( data['status'] == 1 ){
				if( data['newThreads'] > 0 ){ // есть новые тикеты
					JCMS.modules.cabinet.playNewThread(); // есть новое сообщение					
				}
			}
		});	
	},
	playNewMesage: function(){
		var file = '/templates/sounds/newMessage';
		var audioTag = $("<audio>", {autoplay: true, style: "display: none"}).append(
		'<source src="'+file+'.wav" type="audio/x-wav" />' 
		+'<source src="'+file+'.mp3" type="audio/mpeg" codecs="mp3" />' 
		+'<embed src="'+file+'.wav" type="audio/x-wav" hidden="true" autostart="true" loop="false" />'
		);
		var obj = $(audioTag).appendTo('body');
		if ($.isFunction(audioTag.get(0).play)){ audioTag.attr('data-file', file); }
		setTimeout(function(){$(obj).remove();},1500);
	},
	
	playNewThread: function(){
		var file = '/templates/sounds/newThread';
		var audioTag = $("<audio>", {autoplay: true, style: "display: none"}).append(
		'<source src="'+file+'.wav" type="audio/x-wav" />' 
		+'<source src="'+file+'.mp3" type="audio/mpeg" codecs="mp3" />' 
		+'<embed src="'+file+'.wav" type="audio/x-wav" hidden="true" autostart="true" loop="false" />'
		);
		var obj = $(audioTag).appendTo('body');
		if ($.isFunction(audioTag.get(0).play)){ audioTag.attr('data-file', file); }
		setTimeout(function(){$(obj).remove();},1500);
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
			$(obj).val(result+JCMS.modules.cabinet.genStr(len-result.length-1));
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
			JCMS.modules.cabinet.ticketDaemon();
