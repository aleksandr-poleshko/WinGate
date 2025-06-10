<div class="page_body box-shadow">
    <h4 style="margin-top:0px;">Управление новостными страницами</h4>
    <div class="jcms_newsTableSearch" style=" float:left;">
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
    <div style="float:right;">
        <a class="btn btn-success jcms_pageAddNews">Создать новую страницу</a>
    </div>
    <div class="clear"></div>
    <div class="gui_table">
        <table id="newsViewTable">
            <thead>
                <tr>
                    <td style="text-align:center !important;"><span rel="tooltip">ID</span></td>
<!--                    <td>ЧПУ&nbsp;заголовок</td>-->
                    <td>Заголовок&nbsp;страницы</td>
                    <td>Дата&nbsp;изменения</td>
                    <td>Автор</td>
                </tr>
            </thead>
        </table>
	</div>
</div>
<style type="text/css">
.gui_table tbody tr td:nth-child(4), .gui_table tbody tr td:nth-child(5){
	text-align:center !important;
}
</style>