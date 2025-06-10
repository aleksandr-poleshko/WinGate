<div class="page_body box-shadow jcms_clientAdd">
    <h4 style="margin-top:0px;"><u onclick="window.JCMS_pagesActiveList='1';JCMS.navigator('/module/cabinet')" class="cursor">Управление пользователями сайта</u> &rarr; Редактирование аккаунта #{ID}</h4>
    <form>
        <table class="table_clientAdd" width="100%">
            <tr>
                <td>Дата регистрации</td>
                <td>{CLIENT_REGDATE}</td>
            </tr>
            <tr>
                <td>IP регистрации</td>
                <td><img src="{SITE_URL}/include/geo_ip/img/{CLIENT_REGIP_FLAG_IMG}.png" title="{CLIENT_REGIP_FLAG_TITLE}"/> <a href="https://2ip.com.ua/ru/services/information-service/site-location?ip={CLIENT_REGIP}&a=act" target="_blank">{CLIENT_REGIP}</a></td> 
            </tr>
            <tr>
                <td>Дата последнего входа</td>
                <td>{CLIENT_LASTAUTHDATE}</td> 
            </tr>
            <tr>
                <td>Последний IP</td>
                <td><img src="{SITE_URL}/include/geo_ip/img/{CLIENT_LASTIP_FLAG_IMG}.png" title="{CLIENT_LASTIP_FLAG_TITLE}"/> <a href="https://2ip.com.ua/ru/services/information-service/site-location?ip={CLIENT_LASTIP}&a=act" target="_blank">{CLIENT_LASTIP}</a></td> 
            </tr>
            <tr>
                <td valign="baseline">Последний UserAgent</td>
                <td>{CLIENT_LASTUSERAGENT}</td> 
            </tr>
            <tr>
                <td>Оборот</td>
                <td>${CLIENT_OBOROT}</td> 
            </tr>
            <tr>
                <td>Баланс</td>
                <td>${CLIENT_BALANCE}</td> 
            </tr>
            <tr>
                <td class="require">Логин</td>
                <td><input type="text" value="{CLIENT_LOGIN}" name="client_login" placeholder="user" /></td>
            </tr>
            <tr>
                <td class="">Новый пароль</td>
                <td>
    <div class="input-group">
                 <input type="password" class="form-control" name="client_passw1" placeholder="введите пароль" />
                <span class="input-group-btn">
                  <button type="button" class="btn btn-default" id="passwGenerate">Сгенерировать</button>
                </span>
            </div>            
                </td>
            </tr>
            <tr>
                <td class="">Новый пароль ещё раз</td>
                <td><input type="password" name="client_passw2" placeholder="введите пароль ещё раз" /></td>
            </tr>
            <tr>
                <td class="require">Адрес эл. почты</td>
                <td><input type="text" name="client_email" value="{CLIENT_EMAIL}" placeholder="например: name@domain.ru" /></td>
            </tr>
            <tr>
                <td class="require"><span class="_tooltip" rel="tooltip" title="Выберите статус аккаунта пользователя.<br /><b>Активирован</b> - разрешен вход в ЛК на сайте и все функции.<br /><b>Не активирован</b> - вход в ЛК сайта невозможен, пока не будет подтвежден адрес эл. почты.<br /><b>Заблокирован</b> - запрещен вход в ЛК на сайте, недоступны все функции ЛК.">Статус</span></td>
                <td>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default active" style="color:#f0ad4e;"><input type="radio" checked="checked" name="client_status" value="0" /> Не активирован</label> 
                        <label class="btn btn-default" style="color:green;"><input type="radio" name="client_status" value="1" /> Активирован</label> 
                        <label class="btn btn-default" style="color:red;"><input type="radio" name="client_status" value="2" /> Заблокирован</label> 
                    </div>                
                </td>
            </tr>
            <tr>
                <td valign="top">Заметки</td>
                <td><textarea type="text" name="client_adminNotes" style=""></textarea></td>
            </tr>
            <tr>
                <td nowrap>Перс. партн. ставка</td>
                <td nowrap><div class="input-group" style="width:150px;"><input type="text" maxlength="2" class="form-control" value="{CLIENT_REF_RATE}" name="client_ref_rate" placeholder="как по тарифу" /><span class="input-group-addon">%</span></div></td>
            </tr>
            <tr>
                <td>Партнёрский оборот</td>
                <td>${CLIENT_PARTN_OBOROT}</td>
            </tr>
            <tr>
                <td colspan="2" class="nolist" style="text-align:left !important;">Приведенные реф. пользователи:
<div class="gui_table"><table>
	<thead>
		<tr>
        	<th>Логин</th>
        	<th>Выплата</th>
            <th nowrap>IP регистрации</th>
            <th nowrap>Последний IP</th>
            <th nowrap>Посл. входа</th>
            <th nowrap>Последний UserAgent</th>
        </tr>
    </thead>
	<tbody>
{CLIENT_PARTN_TBODY}
	</tbody>
</table>
</div>                </td>
            </tr>
        </table><br />
        <div style="float:right;">
            <a class="btn btn-success" href="/admin/module/cabinet/add_balance?client_id={ID}" onClick="JCMS.navigator('/module/cabinet/add_balance?client_id={ID}'); return false;" style="margin-right:10px;">Добавить фин. операцию</a>
            <a class="btn btn-success" target="_blank" href="{SITE_URL}/login?god_mode={ID}" style="margin-right:10px;">Открыть ЛК пользователя</a>
            <button class="btn btn-success jcms_clientAddSave">Сохранить изменения</button>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="form_submit" value="1" />
    </form>
</div>
<script>
$('.gui_table table tbody tr').unbind('click').click(function(e) {
	if( e.target.localName  == 'a' ){ 
		// игнорируем клики по ссылкам
		return false;
	}
	var id = $(this).attr('data-client_id');
	if( id > 0 ){
		var url = '/module/cabinet/edit_client?id='+id;
		var newWin = window.open(document.location.protocol+"//"+document.location.host+JCMS.navigator(url,2), url);
		newWin.focus();
	}
});
</script>
<style type="text/css">
.gui_table td, .gui_table th{
	padding:5px 10px;	
	width:auto !important;
}
.gui_table td:nth-child(2), .gui_table td:nth-child(5){
	text-align:center;		
}
.table_clientAdd thead td{
	text-align:left !important;	
}

.table_clientAdd>thead>tr>td:first-child, .table_clientAdd>tbody>tr>td:first-child{
	text-align:right !important;
	width:200px !important;
	padding-right:5px;
}
.table_clientAdd tr>td:first-child{
	width:400px;
}
.table_clientAdd>thead>tr>td:not(.nolist):first-child:after,.table_clientAdd>tbody>tr>td:not(.nolist):first-child:after{
	content:" :"
}
.table_clientAdd tr>td.require:before{
	content:"* ";
	color:red;
}
.table_clientAdd tr>td{
	padding:3px;
}
</style>