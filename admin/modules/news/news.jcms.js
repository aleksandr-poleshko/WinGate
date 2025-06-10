// JavaScript Document

if( !JCMS.modules ) JCMS.modules = {};

JCMS.modules.news = {
	// для главной страницу модуля
	showMainPage: function(data){
		var self = this;
		self.navigator('/module/news',1);
		this.showPage(data['template'], function(data){
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/dataTables.jquery.min.js", function(data){
				JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/dataTables.bootstrap.js", function(data){
					JCMS.modules.news.datatable = $('#newsViewTable').DataTable({
						serverSide: true,
						ajax: {
							url:"?action=getTableData",
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
									$('input, button', '.jcms_newsTableSearch>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_newsTableSearch').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$('tbody tr', this).unbind('click').click(function(e) {
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){
								JCMS.ajax("", JCMS.navigator('/module/news/edit?id='+id,1));			
							}
                        });
					}).order([3,'desc']).draw(); // первая прорисовка таблицы AJAX данными
					$("div.jcms_newsTableSearch>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.news.datatable.search(form[0].value).draw();
						return false;
					});
					$('.jcms_pageAddNews').click(function(e) {
                        JCMS.ajax("", JCMS.navigator('/module/news/add',1));
                    });
					self.tooltipInit();
				}, data);
			}, data);
		}, data);		
	},
	
	showPage_add: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			this.navigator('/module/news/add',1);
			JCMS.preloader(1, "Загрузка редактора...<br>Пожалуйста подождите...");
			// загружаем редактор TinyMCE
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/tinymce/jquery.tinymce.min.js", function(){
				$('textarea[name=news_shortText]').tinymce({
					script_url : document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/tinymce/tinymce.min.js",
					plugins: [
							"advlist autolink charmap fullscreen image link code lists media pagebreak paste preview searchreplace table textcolor",
					],
					
					toolbar1: "copy paste | searchreplace | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect | charmap", 
					toolbar2: "bullist numlist | outdent indent blockquote | undo redo | link unlink image media | forecolor backcolor | table | removeformat | subscript superscript | code | preview | fullscreen",					
					menubar: false,
					language:'ru',
        			toolbar_items_size: 'small',
					statusbar : false,
					setup: function(editor) {
						editor.on('init', function(e) {
							JCMS.preloader(0);
						});
					}
			   });
				// обработчик отправки формы
				$("div.jcms_newsAdd>form").eq(0).unbind('submit').submit(function(e) {
					self.ajaxForm(this);
					
					return false;
				});
			}, data);
		}, data);
	},

	showPage_edit: function(data){
		this.showPage(data['template'], function(data){
			this.tooltipInit();
			JCMS.preloader(1, "Загрузка редактора...<br>Пожалуйста подождите...");
			// загружаем редактор TinyMCE
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/tinymce/jquery.tinymce.min.js", function(data){
				$('textarea[name=news_shortText]').tinymce({
					script_url : document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/tinymce/tinymce.min.js",
					plugins: [
							"advlist autolink charmap fullscreen image link code lists media pagebreak paste preview searchreplace table textcolor",
					],
					
					toolbar1: "copy paste | searchreplace | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect | charmap", 
					toolbar2: "bullist numlist | outdent indent blockquote | undo redo | link unlink image media | forecolor backcolor | table | removeformat | subscript superscript | code | preview | fullscreen",					
					menubar: false,
					language:'ru',
        			toolbar_items_size: 'small',
					statusbar : false,
					setup: function(editor) {
						editor.on('init', function(e) {
							JCMS.preloader(0);
						});
					}
			   });
				// обработчик отправки формы
				$("div.jcms_newsEdit>form").eq(0).unbind('submit').submit(function(e) {
					self.ajaxForm(this);

					return false;
				});
				$('input[name=news_status][value='+String(data['data']['news_status'])+']').click().change();
			   // кнопка удаления страницы (диалог подтверждения)
				$('#newsDeleteConfirm').click(function(e) {
					JCMS.ajax("action=delete");
				});
			}, data);
		}, data);
	},

	result: function(data){
		JCMS.preloader(0);
		if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);}
		if( data['add'] == 'success' || data['delete'] == 'success' || data['edit'] == 'success' ){
			this.navigator('/module/news');
		}
	}
}
