<div class="page_body box-shadow jcms_clientAdd" data-chat_id="{TICKET_ID}">
    <h4 style="margin-top:0px;"><u onclick="window.JCMS_pagesActiveList='6';JCMS.navigator('/module/cabinet')" class="cursor">Управление пользователями сайта</u> &rarr; Тикет #{TICKET_ID}</h4>
    <form>
        <table class="table_clientAdd" width="100%">
            <tr>
                <td class="require">Пользователь</td>
                <td><a href="{SITE_URL}/admin/module/cabinet/edit_client?id={CLIENT_ID}" target="_blank">[#{CLIENT_ID}] {CLIENT_LOGIN}</a></td>
            </tr>
            <tr>
                <td>Дата создания</td>
                <td>{TICKET_ADDDATE}</td>
            </tr>
            <tr>
                <td>Статус</td>
                <td><select name="ticket_status" style="width:200px;">{TICKET_STATUS}</select></td>
            </tr>
        </table>
        <input type="hidden" name="action" value="newStatus" />
		<input type="hidden" name="form_submit" value="1" />
    </form>
    <h5><b>{TICKET_TITLE}</b></h5> 
    <div class="ticket_chatlog" data-last_msg="0"></div>
    <form method="post" class="addMsgForm">
        <h5><b>Написать новое сообщение:</b></h5> 
        <textarea style="height:100px; resize:none; border:solid 1px silver;" name="text"></textarea>
        <br />
        <div style="float:right;">
            <button class="btn btn-success">Отправить сообщение</button>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="action" value="newMsg" />
        <input type="hidden" name="form_submit" value="1" />
    </form>
</div>
<style type="text/css">

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
.ticket_chatlog{
	height:300px;
	overflow:auto;	
	border:solid 1px silver;
	background:#fff;
}
.ticket_chatlog>div{
	margin:5px;
	padding:5px;
	border:solid 1px silver;
	clear:both;
}
.ticket_chatlog>div.admin_msg>span{
	color:#5b9ec0;
}
.ticket_chatlog>div.client_msg>span{
	color:#999;
}
.ticket_chatlog>div>span:nth-child(1){
	float:left;
}
.ticket_chatlog>div>span:nth-child(2){
	float:right;
}
.clear{ clear:both; }
</style>