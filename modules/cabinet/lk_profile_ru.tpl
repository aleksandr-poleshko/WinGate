<h3>Профиль</h3>


<table class="lk_profile" style="margin-right:10px;">
	<tr>
		<td style="width:170px;">Логин: </td>
		<td><input type="text" style="cursor:not-allowed" readonly value="{LK_LOGIN}"  /></td>
    </tr>
	<tr>
		<td>E-mail: </td>
		<td><input type="text" style="cursor:not-allowed" readonly value="{LK_EMAIL}" /></td>
    </tr>
</table>
<br>
<form method="post" name="profile_form">

<h3>Изменение пароля</h3>
<table class="lk_profile">
	<tr> 
		<td>Новый пароль: </td>
		<td><input type="password" name="jcms2_lk_passw1" placeholder="Новый пароль" /></td>
    </tr>
	<tr>
		<td>Новый пароль ещё раз: </td>
		<td><input type="password" name="jcms2_lk_passw2" placeholder="Новый пароль ещё раз" /></td>
    </tr>
</table>
<button name="form_submit" value="1" style="width:270px; margin-left:150px;">Изменить пароль</button>
</form>