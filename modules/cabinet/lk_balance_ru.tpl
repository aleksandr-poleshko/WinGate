<h3>Пополнение баланса</h3><br>
<form method="post">
	<p>Введите сумму: 
    $ <input name="summ" type="text" style="padding:5px 10px; width:60px; height:auto; border:solid 1px silver; margin:0;" value="100" maxlength="4" />

        <input type="hidden" name="type" value="3" />
    <button name="form_submit" value="1" class="inline" style="border-radius:5px;"><b>Пополнить</b></button></p>
   
</form>
<div class="message message-warning" style="{WM_HIDE_MSG}"><span></span>Для оплаты BTC, USDT или любой другой криптовалютой без комиссии напишите по контактам указанным ниже.<br>Telegram: <a href="https://t.me/wingateproxy" target="_blank">@wingateproxy</a><br></div><br>
<h3>История операций</h3>
<table style="margin-right:10px;" class="module_table">
	<thead>
    	<tr>
            <td align="center">Дата</td>
            <td align="center">Сумма</td>
            <td width="100%">Описание</td>
        </tr>
	</thead>
    <tbody>
{TBODY}
    </tbody>
</table>