<div class="page_body box-shadow jcms_newsEdit">
    <h4 style="margin-top:0px;"><u onclick="JCMS.navigator('/module/news')" class="cursor">Управление новостными страницами</u> &rarr; Редактирование новостной страницы #{NEWS_ID}</h4>
    <form>
        <table class="table_newsAdd" width="100%">
            <tr>
                <td>Дата создания</td>
                <td>{NEWS_DATECREATE}</td>
            </tr>
            <tr>
                <td class="require"><span class="_tooltip" rel="tooltip" title="Укажите заголовок новости, который будет отображаться в заголовке окна браузер и на странице сайта">Заголовок</span></td>
                <td><input type="text" value="{NEWS_TITLE}" name="news_title" placeholder="Введите заголовок новости, например Тестовая новость" /></td>
            </tr>
            <tr>
                <td style="vertical-align:top;" class="require">Текст новости</td>
                <td colspan="2"><textarea  style="width:100%;" name="news_shortText">{NEWS_SHORTTEXT}</textarea></td>
            </tr>
            <tr>
                <td class="require"><span class="_tooltip" rel="tooltip" title="Выберите текущее состояние новостной страницы.<br /><b>ВКЛЮЧЕНА</b> - в этом режиме, просмотре страницы доступен всем посетителям сайта.<br /><b>ОТКЛЮЧЕНА</b> - в этом режиме, просмотр страницы доступен только авторизованным в панели управления Jensen CMS пользователям.<br /><b>ОТЛОЖЕНА</b> - в этом режиме, просмотр страницы доступен только авторизованным в панели управления Jensen CMS пользователям, до даты указанной в поле &laquo;Дата публикации&raquo;. По наступлению этой даты статус автоматически изменится на &laquo;Включена&raquo;">Состояние</span></td>
                <td>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default active">
                            <input type="radio" checked="checked" name="news_status" value="1" /> Включена
                        </label> 
                        <label class="btn btn-default">
                            <input type="radio" name="news_status" value="0" /> Отключена
                        </label> 
                    </div>
                </td>
            </tr>
        </table><br />
        <div style="float:left;">
            <button href="#myModal"  class="btn btn-danger jcms_pageEditPageSave" role="button" type="button" data-toggle="modal">Удалить новостную страницу</button>
        </div>
        <div style="float:right;">
            <button class="btn btn-success jcms_newsEditNewsSave">Сохранить изменения</button>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="form_submit" value="1" />
    </form>
</div>
<form target="_blank" method="post" action="{NEWS_LINK}" id="NewsPreviewForm"></form>

<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Подтверждение операции</h4>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить новостную страницу №{NEWS_ID}?</p>
                <p class="text-danger"><small>Новостная страница будет удалена безвозвратно и без возможности восстановления.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="newsDeleteConfirm">УДАЛИТЬ</button>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
.table_newsAdd thead td{
	text-align:left !important;	
}

.table_newsAdd tr>td:first-child{
	text-align:right !important;
	width:200px !important;
	padding-right:5px;
}
.table_newsAdd tr>td:first-child{
	width:400px;
}
.table_newsAdd tr>td:first-child:after{
	content:" :"
}
.table_newsAdd tr>td.require:before{
	content:"* ";
	color:red;
}
.table_newsAdd tr>td{
	padding:3px;
}
</style>

<script type="text/javascript">
JCMS.load_css("{HOME_URL}/admin/modules/core/css/bootstrap-tagsinput.css");
JCMS.load_script("{HOME_URL}/admin/modules/core/js/bootstrap-tagsinput.min.js", function(){
	$("input[name='news_meta[keywords]']").tagsinput();
});
</script>