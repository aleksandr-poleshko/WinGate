<div class="page_body box-shadow jcms_clientAdd">
    <h4 style="margin-top:0px;"><u onclick="window.JCMS_pagesActiveList='4';JCMS.navigator('/module/cabinet')" class="cursor">Управление пользователями сайта</u> &rarr; Редактирование тарифа #{TARIFF_ID}</h4>
    <form>
        <table class="table_clientAdd" width="100%">
            <tr>
                <td class="require">Название</td>
                <td><input type="text" value="{TARIFF_TITLE}" name="tariff_title" /></td>
            </tr>
            <tr>
                <td class="require" valign="baseline">Описание</td>
                <td><textarea name="tariff_descr" style="resize:vertical; max-height:150px; min-height:50px;">{TARIFF_DESCR}</textarea></td>
            </tr>
            <tr>
                <td class="require" valign="baseline">Стоимость</td>
                <td>{TARIFFS_LIST}</td>
            </tr>
            <tr>
                <td class="require">Статус</td>
                <td>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default " style="color:red;"><input type="radio" name="tariff_status" value="0" /> Выкл.</label> 
                        <label class="btn btn-default active" style="color:green;"><input type="radio" name="tariff_status" checked="checked"  value="1" /> Вкл.</label> 
                    </div>                
                </td>
            </tr>
        </table><br />
        <div style="float:right;">
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
select[name=bal_client] option.st0{
	background-color:#FFC674;	
}
select[name=bal_client] option.st1{
	background-color:#B3FFA0;	
}
select[name=bal_client] option.st2{
	background-color:#FF7577;	
}
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