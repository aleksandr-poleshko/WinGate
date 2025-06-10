<div class="page_body box-shadow jcms_clientAdd">
    <h4 style="margin-top:0px;"><u onclick="window.JCMS_pagesActiveList='8';JCMS.navigator('/module/cabinet')" class="cursor">Черный список доменов электронной почты</u> &rarr; <span>Добавление нового домена</span></h4>
    <form>
        <table class="table_clientAdd" width="100%">
            <tr>
                <td class="require">Домен</td>
                <td><input type="text" value="{BL_DOMAIN}" name="bl_domain" /></td>
            </tr> 
            <tr>
                <td>Связано с клиентом</td>
                <td><select name="bl_client" style="width:300px;"><option>&mdash; НЕТ &mdash;</option>{CLIENTS_LIST}</select></td>
            </tr>
        </table><br />
        <div style="float:left; display:none;" id="buttonBlock">
            <button href="#myModal"  class="btn btn-danger" role="button" type="button" data-toggle="modal">Удалить домен</button>
        </div>
        <div style="float:right;">
            <button class="btn btn-success jcms_clientAddSave">Добавить домен</button>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="form_submit" value="1" /> 
    </form>
</div>
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Подтверждение операции</h4>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить домен #{ID}?</p>
                <p class="text-danger"><small>Указанный домен будет удален безвозвратно и без возможности восстановления!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="bannerDeleteConfirm">УДАЛИТЬ</button>
            </div>
        </div>
    </div>
</div>
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