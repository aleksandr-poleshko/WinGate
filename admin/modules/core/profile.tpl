<div class="page_body box-shadow">
    <h4 style="margin-top:0px;">Управление аккаунтом</h4>
    <table border="0" class="table_profile">
      <tr>
        <td>Логин</td>
        <td>{USER_LOGIN}</td>
      </tr>
      <tr>
        <td>Адрес эл. почты</td>
        <td>{USER_EMAIL}</td>
      </tr>
      <tr>
        <td>Имя пользователя</td>
        <td id="changeUsername"><u>{USER_NAME}</u></td>
      </tr>
      <tr>
        <td>Группа</td>
        <td>{USER_GROUP}</td>
      </tr>
      <tr>
        <td>Дата регистрации</td>
        <td>{USER_DATE_REG}</td>
      </tr>
     <tr>
        <td>Дата последнего входа</td>
        <td>{USER_DATE_LAST_AUTH}</td>
      </tr>
    </table>
<br>
<br>
    <h4 style="margin-top:0px;">Изменение пароля</h4>
<form id="changePassword">
   <table border="0" class="table_profile">
      <tr>
        <td>Текущий пароль</td>
        <td><input name="user_oldpassw" type="password" placeholder="введите текущий пароль" /></td>
      </tr>
      <tr>
        <td>Новый пароль</td>
        <td>
   			<div class="input-group">
                 <input name="user_newpassw1" type="password" value="" class="form-control" placeholder="введите новый пароль" />
                <span class="input-group-btn">
				<button type="button" class="btn btn-default" id="passwGenerate">Сгенерировать</button>
                </span>
            </div> 
        </td>
      </tr>
      <tr>
        <td>Новый пароль еще раз:</td>
        <td><input name="user_newpassw2" type="password" value="" placeholder="введите новый пароль ещё раз" /></td>
      </tr>
     <tr>
        <td colspan="2"><button class="btn btn-success jcms_pageAddPageSave">Изменить пароль</button></td>
      </tr>
    </table>
    <input type="hidden" name="action" value="changePassword" />
</form>    
</div>
<style type="text/css">
.gui_table thead td{
	text-align:left !important;	
}
.table_profile tr>td:first-child{
	text-align:right !important;
	width:200px !important;
	padding-right:5px;
	font-weight:bold;
}
.table_profile tr>td:last-child{
	width:400px;
}
.table_profile tr>td:not([colspan]):first-child:after{
	content:" :"
}
.table_profile tr>td{
	padding:3px;
}
td#changeUsername u{
	border-bottom:dashed 1px gray;
	cursor:text;
	text-decoration:none;
}
</style>
<script type="text/javascript">
$('.tooltip').bs_tooltip();
</script>