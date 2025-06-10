<h3>Техническая поддержка - Запрос №{TICKET_ID}</h3>
<br>

<h4>&nbsp;&nbsp;{TICKET_TITLE}</h4><br>
<div class="ticket_chatlog" data-last_msg="0" data-ticket_status="{TICKET_STATUS}"></div>
<form method="post" class="addMsgForm">
    <br>
    <h4>Написать новое сообщение</h4> 
    <textarea style="height:100px; resize:none; border:solid 1px silver;" name="text"></textarea>
    <button type="submit" class="inline" style="border-radius:5px;"><b>Отправить сообщение</b></button>
    <input type="hidden" name="action" value="newMsg" />
</form>
<script>$(document).ready(function(){ticketChat();});</script>