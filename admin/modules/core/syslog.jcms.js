// JavaScript Document

if( !JCMS.modules ) JCMS.modules = {};

JCMS.modules.syslog = {
	showPage: function(data){
		this.showPage(data['template'], function(data){
			this.navigator('/core/syslog',1);
			JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/dataTables.jquery.min.js", function(data){
				JCMS.load_script(document.location.protocol+"//"+document.location.host+JCMS.path+"/modules/core/js/dataTables.bootstrap.js", function(data){
					JCMS.modules.syslog.datatable = $('#pagesViewTable').DataTable({
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
									$('input, button', '.jcms_pagesTableSearch>').attr('disabled', false);
								} else {
									$('input, button', '.jcms_pagesTableSearch').attr('disabled', true);							
								}
								return data['data']?data['data']:[];
							},
						},
						deferLoading: data['total_rows'],
					}).on('draw',function(){
						JCMS.preloader(0); // таблица прорисована скрываем лоадер
						self.tooltipInit();
						$.each($("table#pagesViewTable tbody td>span.level"), function(key,val){
							$($(val).parents('tr').eq(0)).addClass($(val).attr('class'));
						});
						$('tbody tr', this).unbind('click').click(function(e) {
							var id = $('>td',this).eq(0).text();
							if( id > 0 ){
								JCMS.ajax("action=getEvent&id="+String(id), document.location.pathname, function(data){
									JCMS.preloader(0);
									$('<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="exampleModalLabel">Журнал аудита &rarr; Просмотр события #'+String(data['syslog_id'])+'</h4></div><div class="modal-body"><p><b>Дата события:</b> '+String(data['syslog_date'])+'<br><b>Пользователь:</b> '+String(data['admin_id'])+'<br><b>Уровень:</b> '+String(data['syslog_level'])+'<br><br>'+String(data['syslog_event'])+'</p></div><div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">ОК</button></div></div></div></div>').bs_modal({
									}).on('hidden.bs.modal', function (e) { $(this).remove(); }).on('shown.bs.modal', function (e) { JCMS.tooltipInit(); });									
								});
							}
                        });
					}).order( [[ 3, 'desc' ]] ).draw(); // первая прорисовка таблицы AJAX данными
					$("div.jcms_pagesTableSearch>form").eq(0).submit(function(e) {
						form = $(e.target).serializeArray();
						JCMS.modules.syslog.datatable.search(form[0].value).draw();
						return false;
					});		
					self.tooltipInit();	
				}, data);
			}, data);		
		}, data);
	},
}