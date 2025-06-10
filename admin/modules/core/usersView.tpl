<div class="page_body box-shadow">
    <h4 style="margin-top:0px;">Пользователи и группы</h4>
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
                <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel1">Управление аккаунтами пользователей</a></h4></div>
                <div id="panel1" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div class="jcms_pagesTableSearch1" style=" float:left;">
                            <form>
                            <div class="input-group" style="width:600px; float:left; margin-right:4px;">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                                <input type="text" class="form-control" name="searchText" placeholder="Введите здесь текст для поиска в таблице">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default" onclick="$(this).parents('form').submit();return false;">Найти</button>
                                </span>
                            </div>
                            </form>
                        </div>
                        <div style="float:right;">
                            <a class="btn btn-success jcms_pageAddUser">Добавить пользователя</a>
                        </div>
                        <div class="clear"></div>
                        <div class="gui_table">
                            <table id="usersViewTable">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Логин</td>
                                    <td>Имя</td>
                                    <td>Эл. почта</td>
                                    <td>Группа</td>
                                    <td>Дата&nbsp;регистрации</td>
                                    <td>Статус</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
        </div>
        
        <div class="panel panel-default">
                <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel2">Управление группами и правами доступа</a></h4></div>
                <div id="panel2" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="jcms_pagesTableSearch2" style=" float:left;">
                            <form>
                            <div class="input-group" style="width:600px; float:left; margin-right:4px;">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                                <input type="text" class="form-control" name="searchText" placeholder="Введите здесь текст для поиска в таблице">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default" onclick="$(this).parents('form').submit();return false;">Найти</button>
                                </span>
                            </div>
                            </form>
                        </div>
                        <div style="float:right;">
                            <a class="btn btn-success jcms_pageAddGroup">Добавить группу</a>
                        </div>
                        <div class="clear"></div>
                        <div class="gui_table">
                            <table id="groupsViewTable">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Заголовок</td>
                                    <td>Описание</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
        </div>
	</div>
</div>

<script>
function panel_click(){
	$(this).unbind('click'); // снимаем обработчик для избежания рекурсии
	$("h4>a",this).click(); // открыаем выбраную панель
	$(this).click(function(e){ panel_click.call(this); }); // возращает обработчик клика по панели
}
var panels = $('.page_body .panel-heading').click(function(e) { panel_click.call(this); });

</script>
<style type="text/css">
.gui_table tbody tr td:nth-child(4), .gui_table tbody tr td:nth-child(5){
	text-align:center !important;
}
.panel-heading{
	cursor:pointer !important;	
}
.jcms_sysConfig table{
	width:100%;	
}
.jcms_sysConfig table tr>td{
	padding:3px;
}
.jcms_sysConfig table tr>td:first-child:not([colspan]){
	text-align:right;
	width:250px !important;
}
.jcms_sysConfig table tr>td:first-child:not([class|=subTitle]):not([colspan]):after{
	content:" :"
}

.status0{
	color:red;	
}
.status1{
	color:green;	
}
.status2{
	color:orange; 	
}
.adminOnline, .adminOffline{
	border-radius:15px;	
	background-color:#090;
	width:10px;
	height:10px;
	display:inline-block;
}
.adminOffline{
	background-color:orange;
}
</style>