<h3>Customer support</h3>
<br>
<button name="form_submit" value="1" onclick="addTicket();" class="inline" style="border-radius:5px;"><b>Create a new request</b></button><br><br>

<table style="margin-right:10px;" class="module_table moduleTickets">
	<thead>
    	<tr>
            <td align="center">Number</td>
            <td>Header</td>
            <td align="center">Date of creation</td>
            <td align="center">Status</td>
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