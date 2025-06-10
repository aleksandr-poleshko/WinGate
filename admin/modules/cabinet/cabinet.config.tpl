<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"><a href="#jcms_cabinet1" aria-controls="main" role="tab" data-toggle="tab">Парнёрская программа</a></li>
</ul>

<div class="tab-content">
	<div role="tabpanel" class="tab-pane fade in active" id="jcms_cabinet1">
        <table>
            <tr>
              <td>Сообщение вывода на WebMoney</td>
              <td><select name="wm_mode">{WM_MODES}</select></td>
           </tr>
            <tr>
              <td>Режим выплат</td>
              <td><select name="referals[mode]">{REFERALS_MODE}</select></td> 
            </tr>
            <tr>
              <td valign="top" style="padding-top:10px;">Ставки</span></td>
              <td id="referalsRates"><button type="button" class="btn btn-default" onClick="referalsRatesBlockAdd();">Добавить ставку</button></td>
            </tr>
        </table>
	</div>
</div>


<div style="display:none;">
    <div id="referalsRatesBlock_dummy">
       <table>
            <tr>
              <td class="subTitle"><div class="input-group" style="width:250px;"><span class="input-group-addon">$</span><input type="text" class="form-control"  data-name="rate1" placeholder="0" /><span class="input-group-addon" style="border-left:none;border-right:none;"><b>&rArr;</b></span><input type="text" class="form-control" data-name="rate2" placeholder="0" /><span class="input-group-addon">%</span></div></td>
              <td><button type="button" class="btn btn-danger" title="Удалить блок" onClick="$(this).parents('table').eq(0).remove();"><span class="glyphicon glyphicon-remove"></span></button></td>
            </tr>
		</table>
    </div>
</div>
<script>
var referalsRatesBlock_counter = 0;
function referalsRatesBlockAdd(summ, rate){
	dummy = $($("#referalsRatesBlock_dummy").clone().html());
	referalsRatesBlock_counter++;
	$("input[data-name=rate1]",dummy).attr("name", "referals[rates]["+String(referalsRatesBlock_counter)+"][0]").attr('data-name',null).val(summ);
	$("input[data-name=rate2]",dummy).attr("name", "referals[rates]["+String(referalsRatesBlock_counter)+"][1]").attr('data-name',null).val(rate);
	
	var dummy = $(dummy).appendTo("#referalsRates", "div#jcms_cabinet1");

	$('input', dummy).off('keyup keydown keypress change').keyup(function(e){
		$(this).val($(this).val().replace(/[^0-9.]/gim, ''));
		if( $(this).val() < 0 ) $(this).val('');
	}).change(function(e) {
		$(this).keyup();
	}).keydown(function(e) { 
		$(this).keyup();
	}).keypress(function(e) { 
		$(this).keyup();
	});
}
setTimeout("{REFERALS_RATES}",500);
</script>
