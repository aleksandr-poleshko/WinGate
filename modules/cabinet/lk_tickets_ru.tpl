<h3>Техническая поддержка</h3>
<br>
<button name="form_submit" value="1" onclick="addTicket();" class="inline" style="border-radius:5px;"><b>Создать новый запрос</b></button><br><br>

<table style="margin-right:10px;" class="module_table moduleTickets">
	<thead>
    	<tr>
            <td align="center">Номер</td>
            <td>Заголовок</td>
            <td align="center">Дата создания</td>
            <td align="center">Статус</td>
        </tr>
	</thead>
    <tbody>
{TBODY}
    </tbody>
</table>
<script>
$(document).ready(function(){ 
	$(".moduleTickets>tbody>tr:not(.empty)").click(function(e) {
		document.location = document.location+"/"+$("td:eq(0)",this).text();
    });
});
</script>