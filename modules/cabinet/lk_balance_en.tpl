<h3>Balance refill</h3><br>
<form method="post">
	<p>Put the amount: 
    $ <input name="summ" type="text" style="padding:5px 10px; width:60px; height:auto; border:solid 1px silver; margin:0;" value="100" maxlength="4" />
		
		<input type="hidden" name="type" value="3" />
    <button name="form_submit" value="1" class="inline" style="border-radius:5px;"><b>Refill</b></button></p>
   
</form> 
<div class="message message-warning" style="{WM_HIDE_MSG}"><span></span>To pay with BTC, USDT or any others cryptocurrency without commission, write to the contacts listed below.<br>Telegram: <a href="https://t.me/wingateproxy" target="_blank">@wingateproxy</a><br></div><br>
<h3>Transaction history</h3>
<table style="margin-right:10px;" class="module_table">
	<thead>
    	<tr>
            <td align="center">Date</td>
            <td align="center">Amount</td>
            <td width="100%">Description</td>
        </tr>
	</thead>
    <tbody>
{TBODY}
    </tbody>
</table>