<div class="page_body box-shadow jcms_pageAdd">
    <h4 style="margin-top:0px;"><u onclick="window.JCMS_usersActiveList='groups';JCMS.navigator('/core/users/');" class="cursor">Пользователи и группы</u> &rarr; Редактирование группы #{ID}</h4>
    <form>
        <table class="table_pageAdd" width="100%"> 
            <tr>
                <td class="require">Название группы</td>
                <td><input type="text" value="{GROUP_TITLE}" name="group_title" /></td>
            </tr>
            <tr>
                <td class="td_multiLine">Описание</td>
                <td><textarea class="form-control" name="group_descr">{GROUP_DESCRIPTION}</textarea></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:left !important;">Выберите необходимые модули и полномочия для этой группы пользователей :</td>
            </tr>
            <tr>
                <td colspan></td>
                <td style="display:inline-block">{PERM_TABLE}</td>
            </tr>
        </table>
        <div style="float:left;">
            <button href="#myModal"  class="btn btn-danger" role="button" type="button" data-toggle="modal">Удалить группу</button>
        </div>
        <div style="float:right;">
            <button class="btn btn-success">Сохранить изменения</button>
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
                <p>Вы действительно хотите удалить группу #{ID}?</p>
                <p class="text-danger"><small>Группа и все её участники будут удалены безвозвратно и без возможности восстановления!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="groupDeleteConfirm">УДАЛИТЬ</button>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
.table_pageAdd thead td{
	text-align:left !important;	
}

.table_pageAdd tr>td:first-child{
	text-align:right !important;
	width:200px !important;
	padding-right:5px;
}
.table_pageAdd tr>td:first-child{
	width:400px;
}
.table_pageAdd tr>td:not([colspan]):first-child:after{
	content:" :"
}
.table_pageAdd tr>td.require:before{
	content:"* ";
	color:red;
}
.table_pageAdd tr>td{
	padding:3px;
}
.td_multiLine{
	vertical-align:top !important;	
}
ul.perm_list>li{
	border-bottom:solid 1px silver;
	margin-bottom:10px;
}
ul.perm_list{
	padding-left:0px;	
}
ul.perm_list>li{
	padding-left:10px;	
	padding-right:10px;
}
ul.perm_list li div{
	padding-left:6px;
	display:none;
}
ul.perm_list li div ul{
	border-left:solid 1px silver;
	padding-left:10px;
}
ul.perm_list li{
	list-style:none;	
	text-align:left;
	font-weight:normal !important;
	white-space:nowrap !important;
}
ul.perm_list li div ul label{
	font-weight:normal;	
}
</style>
<script>
$('div', $("ul.perm_list>li>input[type=checkbox]:checked").parent()).show();
$("ul.perm_list>li>input[type=checkbox]").change(function(e) {
    if( $(this).prop('checked') ){
		$('div', $(this).parent()).show("blind");
		$('div input[type=checkbox]', $(this).parent()).prop('checked', true);
	} else {
		$('div', $(this).parent()).hide("blind");
		$('div input[type=checkbox]', $(this).parent()).prop('checked', false);
	}
});
</script>






