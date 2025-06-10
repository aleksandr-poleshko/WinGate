<div class="page_body box-shadow">
    <h4 style="margin-top:0px;">Журнал аудита Jensen CMS</h4>
                   <div class="jcms_pagesTableSearch" style=" float:left;">
                        <form>
                        <div class="input-group" style="width:600px; float:left; margin-right:4px;">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                            <input type="text" class="form-control" name="searchText" placeholder="Введите здесь текст для поиска в таблице">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" onclick="$(this).parents('form').submit();return false;">Найти</button>
                            </span>
                        </div>
                        </form>
                    </div>
                    <div class="clear"></div>
                    <div class="gui_table">
                        <table id="pagesViewTable">
                            <thead>
                                <tr>
                                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
                                    <td>Уровень</td>
                                    <td>Событие</td>
                                    <td>Дата</td>
                                    <td>Пользователь</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
</div>
<style type="text/css">
tr.error td{
	color: #a94442 !important;
	font-weight:bold;
	background: -moz-linear-gradient(top,  #ffffff 0%, #f2dede 100%) !important; /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#f2dede)) !important; /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #ffffff 0%,#f2dede 100%) !important; /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #ffffff 0%,#f2dede 100%) !important; /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #ffffff 0%,#f2dede 100%) !important; /* IE10+ */
	background: linear-gradient(to bottom,  #ffffff 0%,#f2dede 100%) !important; /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f2dede',GradientType=0 ) !important; /* IE6-8 */
}
tr.warning td{
	color:#8a6d3b !important;
	font-weight:bold;
	background: -moz-linear-gradient(top,  #ffffff 0%, #fcf8e3 100%) !important; /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#fcf8e3)) !important; /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #ffffff 0%,#fcf8e3 100%) !important; /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #ffffff 0%,#fcf8e3 100%) !important; /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #ffffff 0%,#fcf8e3 100%) !important; /* IE10+ */
	background: linear-gradient(to bottom,  #ffffff 0%,#fcf8e3 100%) !important; /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#fcf8e3',GradientType=0 ) !important; /* IE6-8 */
}
tr.notice td{
	color:#3c763d; !important;
	background: -moz-linear-gradient(top,  #ffffff 0%, #dff0d8 100%) !important; /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#dff0d8)) !important; /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #ffffff 0%,#dff0d8 100%) !important; /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #ffffff 0%,#dff0d8 100%) !important; /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #ffffff 0%,#dff0d8 100%) !important; /* IE10+ */
	background: linear-gradient(to bottom,  #ffffff 0%,#dff0d8 100%) !important; /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#dff0d8',GradientType=0 ) !important; /* IE6-8 */
}
.gui_table thead td{
	text-align:left !important;	
}
.table_sysinfo tr>td:first-child{
	text-align:right !important;
	width:200px !important;
	padding-right:5px;
	font-weight:bold;
}
.table_sysinfo tr>td:last-child{
	width:400px;
}
.table_sysinfo tr>td:first-child:after{
	content:" :"
}
</style>
<script type="text/javascript">
$('.tooltip').bs_tooltip();
</script>