<div class="page_body box-shadow">
    <h4 style="margin-top:0px;">Управление пользователями сайта</h4>
    Текущее время: {CURRENT_TIMESTAMP}
    <div class="panel-group" id="accordion">
    
    
        <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel1">Управление пользователями сайта</a></h4></div>
            <div id="panel1" class="panel-collapse collapse in">
                <div class="panel-body">
                    <div class="jcms_clientsTable1Search" style=" float:left;">
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
                    <div class="clear"></div>
                    <div class="gui_table">
                        <table id="clientsViewTable1">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Логин</td>
                                    <td>Email</td>
                                    <td>Баланс</td>
                                    <td>Последний вход</td>
                                    <td>Последний IP</td>
                                    <td>Статус</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel2">Финансовые операции</a></h4></div>
			<div id="panel2" class="panel-collapse collapse">
				<div class="panel-body">
                   <div class="jcms_clientsTable2Search" style=" float:left;">
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
                        <a class="btn btn-success jcms_balanceAdd">Добавить операцию</a>
                    </div>
                    <div class="clear"></div>
                    <div class="gui_table">
                        <table id="clientsViewTable2" class="noselect">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Дата</td>
                                    <td>Сумма</td>
                                    <td>Описание</td>
                                    <td>Клиент</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
        </div>
         
        <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel3">История заказов</a></h4></div>
 			<div id="panel3" class="panel-collapse collapse">
				<div class="panel-body">
                   <div class="jcms_clientsTable3Search" style=" float:left;">
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
                    <div class="clear"></div>
                    <div class="gui_table">
                        <table id="clientsViewTable3">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">Номер</span></td>
                                    <td>Логин</td>
                                    <td>Тариф</td>
                                    <td>Дата начала</td>
                                    <td>Дата окончания</td>
                                    <td>Сумма</td>
                                    <td>Статус</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel4">Управление тарифами</a></h4></div>
 			<div id="panel4" class="panel-collapse collapse">
				<div class="panel-body">
                   <div class="jcms_clientsTable4Search" style=" float:left;">
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
                        <a class="btn btn-success jcms_tariffAdd">Добавить новый тариф</a>
                    </div>
                    <div class="clear"></div>
                    <div class="gui_table"> 
                        <table id="clientsViewTable4">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Название</td> 
                                    <td>Описание</td>
                                    <td>Статус</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel5">Статистика по реферальной программе</a></h4></div>
 			<div id="panel5" class="panel-collapse collapse">
				<div class="panel-body">
                   <div class="jcms_clientsTable5Search" style=" float:left;">
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
                    <div class="clear"></div>
                    <div class="gui_table">
                        <table id="clientsViewTable5">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Логин</td>
                                    <td>Оборот</td>
                                    <td>%</td>
                                    <td>IP регистрациия</td>
                                    <td>Последн. IP</td>
                                    <td>Последний вход</td>
                                    <td>Рефералов</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel6">Техническая поддержка</a></h4></div>
 			<div id="panel6" class="panel-collapse collapse">
				<div class="panel-body">
                   <div class="jcms_clientsTable6Search" style=" float:left;">
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
                    <div class="clear"></div>
                    <div class="gui_table">
                        <table id="clientsViewTable6">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Клиент</td>
                                    <td>Заголовок</td>
                                    <td>Дата создания</td>
                                    <td>Статус</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel7">Заказы на тестовый доступ</a></h4></div>
 			<div id="panel7" class="panel-collapse collapse">
				<div class="panel-body">
                   <div class="jcms_clientsTable7Search" style=" float:left;">
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
                    <div class="clear"></div>
                    <div class="gui_table">
                    <div class="gui_table">
                        <table id="clientsViewTable7">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">Номер</span></td>
                                    <td>Логин</td>
                                    <td>Дата создания</td>
                                    <td>Дата окончания</td>
                                    <td>Дубль</td>
                                    <td>Статус</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                </div>
			</div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#panel8">Черный список доменов электронной почты</a></h4></div>
 			<div id="panel8" class="panel-collapse collapse">
				<div class="panel-body">
                   <div class="jcms_clientsTable8Search" style=" float:left;">
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
                        <a class="btn btn-success jcms_emailBlackListAdd">Добавить новый домен</a>
                    </div>
                    <div class="clear"></div>
                    <div class="gui_table">
                        <table id="clientsViewTable8">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Домен</td>
                                    <td>Дата добавления</td> 
                                    <td>Клиент</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
        </div>

    </div>
</div>
<style type="text/css">
.gui_table tbody tr td:nth-child(4), .gui_table tbody tr td:nth-child(5){
	text-align:center;
}
.gui_table table#clientsViewTable4 tbody tr td:nth-child(6){
	text-align:left;
}
.gui_table table#clientsViewTable2 tbody tr td:nth-child(4), .gui_table table#clientsViewTable2 tbody tr td:nth-child(5), .gui_table table#clientsViewTable2 tbody tr td:nth-child(6),.gui_table table#clientsViewTable5 tbody tr td:nth-child(5){
	text-align:left; 
}
#clientsViewTable2 tbody td:nth-child(2), #clientsViewTable2 tbody td:nth-child(3), #clientsViewTable3 tbody td:nth-child(7), #clientsViewTable3 tbody td:nth-child(6), #clientsViewTable4 tbody td:nth-child(3),.gui_table table#clientsViewTable5 tbody tr td:nth-child(3), .gui_table table#clientsViewTable5 tbody tr td:nth-child(4), .gui_table table#clientsViewTable5 tbody tr td:nth-child(7), .gui_table table#clientsViewTable5 tbody tr td:nth-child(8){
	text-align:center; 
	white-space:nowrap;	
}
#clientsViewTable3 tbody td:nth-child(2), #clientsViewTable3 tbody td:nth-child(3), #clientsViewTable3 tbody td:nth-child(4), #clientsViewTable3 tbody td:nth-child(5),.gui_table table#clientsViewTable5 tbody tr td:nth-child(5),.gui_table table#clientsViewTable5 tbody tr td:nth-child(6){
	white-space:nowrap;	
}
.susr_st0{ color:orange; }
.susr_st1{ color:green; }
.susr_st2{ color:red; }
.panel-group {
    margin-bottom:0px;
}
.panel-heading{
	cursor:pointer !important;	
}</style>
<script>
function panel_click(){
	$(this).unbind('click'); // снимаем обработчик для избежания рекурсии
	$("h4>a",this).click(); // открыаем выбраную панель
	$(this).click(function(e){ panel_click.call(this); }); // возращает обработчик клика по панели
}
var panels = $('.page_body .panel-heading').click(function(e) { panel_click.call(this); });
/*setTimeout("$('.page_body .panel-heading').eq(7).click();",1000);*/
</script>