<div class="page_body box-shadow">
    <h4 style="margin-top:0px;">Информация о системе</h4>
    <table border="0" class="table_sysinfo">
      <tr>
        <td>Версия Jensen CMS</td>
        <td>{JENSENCMS_VERSION}</td>
      </tr>
      <tr>
        <td>Версия PHP</td>
        <td>{PHP_VERSION}</td>
      </tr>
      <tr>
        <td>Версия MySQL</td>
        <td>{MYSQL_VERSION}</td>
      </tr>
      <tr>
        <td>Версия PHP GD</td>
        <td>{GD_VERSION}</td>
      </tr>
      <tr>
        <td>GZIP компрессия</td>
        <td>{GZIP_STATUS}</td>
      </tr>
      <tr>
        <td>PHP safe_mode</td>
        <td>{PHP_SAFEMODE}</td>
      </tr>
      <tr>
        <td>PHP register_globals</td>
        <td>{PHP_REGGLOB}</td>
      </tr>
      <tr>
        <td>PHP magic_quotes_gpc</td>
        <td>{PHP_MAGQUOTESGPC}</td>
      </tr>
      <tr>
        <td>PHP magic_quotes_runtime</td>
        <td>{PHP_MAGQUOTESRNT}</td>
      </tr>
      <tr>
        <td>PHP magic_quotes_sybase</td>
        <td>{PHP_MAGQUOTESSB}</td>
      </tr>
      <tr>
        <td>Apache mod_rewrite</td>
        <td>{HTTPD_MODREWRITE}</td>
      </tr>
      <tr>
        <td>Дата и время на сервере</td>
        <td>{SERVER_TIME}</td>
      </tr>
      <tr>
        <td>ОС сервера и версия ядра</td>
        <td>{SERVER_OS}</td>
      </tr>
      <tr>
        <td>Свободно операт. памяти</td>
        <td>{RAM}</td>
      </tr>
     <tr>
        <td>Свободно места на диске</td>
        <td>{HDD}</td>
      </tr>
    </table>
    <h4>Установленные модули Jensen CMS</h4>
    <div class="gui_table">
        <table class="noselect">
            <thead>
                <tr>
                    <td style="text-align:center !important;">Модуль</td>
                    <td>Описание модуля</td>
                    <td><span class="help" rel="tooltip" title="Дополнительные компоненты модуля">Доп. Компоненты модуля</span></td>
                    <td>Элементы меню</td>
                </tr>
            </thead>
            <tbody>
{TABLE_DATA}
            </tbody>
        </table>
	</div>
</div>
<style type="text/css">
.gui_table thead td{
	text-align:left !important;	
}
.table_sysinfo tr>td:first-child{
	text-align:right !important;
	width:230px !important;
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