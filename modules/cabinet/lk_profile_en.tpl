<h3>Profile</h3>


<table class="lk_profile" style="margin-right:10px;">
	<tr>
		<td style="width:170px;">Login: </td>
		<td><input type="text" style="cursor:not-allowed" readonly value="{LK_LOGIN}"  /></td>
    </tr>
	<tr>
		<td>E-mail: </td>
		<td><input type="text" style="cursor:not-allowed" readonly value="{LK_EMAIL}" /></td>
    </tr>
</table>
<br>
<form method="post" name="profile_form">

<h3>Changing password </h3>
<table class="lk_profile">
	<tr> 
		<td>Enter new password : </td>
		<td><input type="password" name="jcms2_lk_passw1" placeholder="New password" /></td>
    </tr>
	<tr>
		<td>Enter new password once again: </td>
		<td><input type="password" name="jcms2_lk_passw2" placeholder="New password once again" /></td>
    </tr>
</table>
<button name="form_submit" value="1" style="width:270px; margin-left:150px;">Change the password</button>
</form>