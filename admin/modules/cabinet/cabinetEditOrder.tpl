<div class="page_body box-shadow jcms_orderAdd">
    <h4 style="margin-top:0px;"><u onclick="window.JCMS_pagesActiveList='3';JCMS.navigator('/module/cabinet')" class="cursor">Управление пользователями сайта</u> &rarr; Редактирование <span></span> заказа №{ORDER_ID}</h4>
    <form>
        <table class="table_orderEdit" width="100%">
            <tr>
                <td><b>Время сервера</b></td>
                <td><b>{CURRENT_TIMESTAMP}</b></td>
            </tr>
            <tr>
                <td>Пользователь</td>
                <td><a href="{SITE_URL}/admin/module/cabinet/edit_client?id={CLIENT_ID}" target="_blank">[#{CLIENT_ID}] {CLIENT_LOGIN}</a></td>
            </tr>
            <tr>
                <td>Дата создания</td>
                <td>{ORDER_ADDDATE}</td>
            </tr>
            <tr>
                <td>Дата окончания</td>
                <td><input type="text" value="{ORDER_DATE}" style="width:130px;" name="order_date" class="datepicker" placeholder="ДД.ММ.ГГГГ" /> <input type="text" value="{ORDER_TIME}" style="width:130px;" name="order_time" placeholder="ЧЧ:ММ:СС" /></td>
            </tr>
            <tr class="testModeHidden">
                <td>Тариф</td>
                <td><a href="{SITE_URL}/admin/module/cabinet/edit_tariff?id={CLIENT_ID}" target="_blank">[#{TARIFF_ID}] {TARIFF_TITLE}</a></td>
            </tr>
            <tr class="testModeHidden"> 
                <td>Сумма заказа</td>
                <td>${ORDER_SUMM}</td>
            </tr>
            <tr> 
                <td valign="baseline">Настройки</td>
                <td>IP 1: <input type="text" value="{ORDER_IP1}" style="width:150px; margin-bottom:5px;" maxlength="15" name="order_ip[]" /><span class="testModeHidden"><br>IP 2: <input type="text" value="{ORDER_IP2}" style="width:150px;" name="order_ip[]" maxlength="15" /></span></td>
            </tr>
            <tr>
                <td>Статус</td>
                <td>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default" style="color:#f0ad4e;"><input type="radio" name="order_status" value="0" /> В обработке</label> 
                        <label class="btn btn-default" style="color:green;"><input type="radio" name="order_status" value="1" /> Активен</label> 
                        <label class="btn btn-default" style="color:orange;"><input type="radio" checked="checked" name="order_status" value="-1" /> Заморожен</label> 
                        <label class="btn btn-default"><input type="radio" name="order_status" value="2" /> Завершен</label> 
                        <label class="btn btn-default" style="color:red;"><input type="radio" checked="checked" name="order_status" value="3" /> Заблокирован</label> 
                    </div>                
                </td>
            </tr>
            
        </table><br />
        <div style="float:right;">
            <button class="btn btn-success">Сохранить изменения</button>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="form_submit" value="1" />
        <input type="hidden" name="short_id" value="{SHORT_ID}" />
    </form>
</div>

<style type="text/css">
.table_orderEdit thead td{
	text-align:left !important;	
}

.table_orderEdit tr>td:first-child{
	text-align:right !important;
	width:200px !important;
	padding-right:5px;
}
.table_orderEdit tr>td:first-child{
	width:400px;
}
.table_orderEdit tr>td:not(.nolist):first-child:after{
	content:" :"
}
.table_orderEdit tr>td.require:before{
	content:"* ";
	color:red;
}
.table_orderEdit tr>td{
	padding:3px;
}
.pages_list label{
	font-weight:normal;
	cursor:pointer;
}
.pages_list ul{
	list-style:none;
	margin:0px;
	padding:0px;
}
</style>