<div class="page_body box-shadow jcms_pageAdd">
    <h4 style="margin-top:0px;"><u onclick="JCMS.navigator('/core/users/')" class="cursor">Пользователи и группы</u> &rarr; Редактирование аккаунта #{ID}</h4>
    <form>
        <table class="table_pageAdd" width="100%">
            <tr>
                <td class="require">Логин</td>
                <td><input type="text" value="{ADMIN_LOGIN}" name="user_login" placeholder="например: user" /></td>
            </tr>
            <tr>
                <td>Новый пароль</td>
                <td>
    <div class="input-group">
                 <input type="password" class="form-control" name="user_passw1" placeholder="введите новый пароль" />
                <span class="input-group-btn">
                  <button type="button" class="btn btn-default" id="passwGenerate">Сгенерировать</button>
                </span>
            </div>            
                </td>
            </tr>
            <tr>
                <td>Новый пароль ещё раз</td>
                <td><input type="password" name="user_passw2" placeholder="введите новый пароль ещё раз" /></td>
            </tr>
            <tr>
                <td class="require">Имя пользователя</td>
                <td><input type="text" name="user_name" value="{ADMIN_NAME}" placeholder="например: Андрей" /></td>
            </tr>
            <tr>
                <td class="require">Адрес эл. почты</td>
                <td><input type="text" name="user_email" value="{ADMIN_EMAIL}" placeholder="например: name@domain.ru" /></td>
            </tr>
            <tr>
                <td class="require">Группа доступа</td>
                <td><select name="user_group">{ADMIN_GROUP}</select></td>
            </tr>
            <tr>
                <td class="require">Статус</td>
                <td>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default active"><input type="radio" checked="checked" name="user_status" value="1" /> ВКЛ.</label> 
                        <label class="btn btn-default"><input type="radio" name="user_status" value="0" /> ВЫКЛ.</label> 
                    </div>                
                </td>
            </tr>
        </table>
        <div style="float:left;">
            <button href="#myModal"  class="btn btn-danger jcms_pageUserDelete" role="button" type="button" data-toggle="modal">Удалить аккаунт</button>
        </div>
        <div style="float:right;">
            <button class="btn btn-success jcms_pageAddPageSave">Сохранить изменения</button>
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
                <p>Вы действительно хотите удалить аккаунт #{ID}?</p>
                <p class="text-danger"><small>Указанный аккаунт будет удален безвозвратно и без возможности восстановления!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="userDeleteConfirm">УДАЛИТЬ</button>
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
.table_pageAdd tr>td:first-child:after{
	content:" :"
}
.table_pageAdd tr>td.require:before{
	content:"* ";
	color:red;
}
.table_pageAdd tr>td{
	padding:3px;
}
</style>