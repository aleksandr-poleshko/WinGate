<div class="page_body box-shadow">
    <h4 style="margin-top:0px;">Конфигурация Jensen CMS</h4>

    	<form method="post" class="jcms_sysConfig">
            <div class="panel-group" id="accordion">
    {BODY}
            </div>
            <div style="text-align:right;"><button type="submit" class="btn btn-success">СОХРАНИТЬ ИЗМЕНЕНИЯ</button></div>
             <input type="hidden"  name="form_submit" value="1" />
         </form>
</div>

<script>
function panel_click(){
	$(this).unbind('click'); // снимаем обработчик для избежания рекурсии
	$("h4>a",this).click(); // открыаем выбраную панель
	$(this).click(function(e){ panel_click.call(this); }); // возращает обработчик клика по панели
}
var panels = $('.jcms_sysConfig .panel-heading').click(function(e) { panel_click.call(this); });

</script>
<style type="text/css">
.panel-heading{
	cursor:pointer !important;	
}
.jcms_sysConfig table{
	width:100%;	
}
.jcms_sysConfig table tr>td{
	padding:3px;
}
.jcms_sysConfig table tr>td:first-child:not([colspan]){
	text-align:right;
	width:250px !important;
}
.jcms_sysConfig table tr>td:first-child:not([class|=subTitle]):not([colspan]):after{
	content:" :"
}
</style>