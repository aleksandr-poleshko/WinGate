<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"><a href="#jcms_core1" aria-controls="main" role="tab" data-toggle="tab">Основные настройки</a></li>
  <li role="presentation"><a href="#jcms_core2" aria-controls="main" role="tab" data-toggle="tab">Безопасность</a></li>
  <li role="presentation"><a href="#jcms_core3" aria-controls="main" role="tab" data-toggle="tab">Аудит событий</a></li>
  <li role="presentation"><a href="#jcms_core4" aria-controls="main" role="tab" data-toggle="tab">Настройки почты</a></li>
</ul>

<div class="tab-content">
	<div role="tabpanel" class="tab-pane fade in active" id="jcms_core1">
        <table>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Укажите заголовок, который будет отображаться в заголовке окна браузера.<br><b>По умолчанию:</b> <tt>{HOSTNAME}</tt>">Название сайта</span></td>
              <td><input type="text" name="site_title" value="{SITE_TITLE}" placeholder="{HOSTNAME}" /></td>
           </tr>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Укажите полный URL адрес сайта.<br><b>По умолчанию:</b> <tt>{DEF_SITE_URL}</tt>">Полный URL сайта</span></td>
              <td><input type="text" name="site_url" value="{SITE_URL}" placeholder="{DEF_SITE_URL}" /></td>
            </tr>
            <tr>
              <td class="td_multiLine"><span class="_tooltip" rel="tooltip" title="Укажите краткое описание сайта. Эти данные могут быть использованы поисковыми системами для составления краткого описания страницы в списке результатов. Рекомендуемая длина 150-200 символов.<br>Определяют мета-тэг &laquo;DESCRIPTION&raquo;<br><b>По умолчанию:</b> &mdash;">Описание сайта</span></td>
              <td><textarea name="site_description" placeholder="">{SITE_DESCRIPTION}</textarea></td>
            </tr>
            <tr>
              <td class="td_multiLine"><span class="_tooltip" rel="tooltip" title="Укажите через запятую ключевые слова для сайта. Эти данные могут быть использованы поисковыми системами для определения позиции страницы в списке результатов. Рекомендуемая количество слов &mdash; 5-10шт.<br>Определяют мета-тэг &laquo;KEYWORDS&raquo;<br><b>По умолчанию:</b> &mdash;">Ключевые слова</span></td>
              <td><textarea name="site_keywords" placeholder="">{SITE_KEYWORDS}</textarea></td>
            </tr>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Выберите состояние сайта. При отключенном сайте, просмотр страниц будет невозможен.<br><b>По умолчанию:</b> работает">Режим работы</span></td>
              <td><select name="site_status">{SITE_STATUSES}</select></td>
            </tr>
            <tr class="sysconig_advancedStatusBlock td_multiLine">
              <td><span class="_tooltip" rel="tooltip" title="Укажите причину отключения сайта(необязательно). Когда сайт выключен, данный текст будет показан на всех страниах сайта.<br><b>По умолчанию:</b> &mdash;">Причина отключения</span></td>
              <td><textarea name="site_statusComment" placeholder="например: Извините, на сайте проводятся профилактические работы.">{SITE_STATUSCOMMENT}</textarea></td>
            </tr>
            <tr>
              <td class="td_multiLine"><span class="_tooltip" rel="tooltip" title="Выберите модули, которые необходимо задействовать в работе сайта.<br><b>По умолчанию:</b> &mdash;">Управления модулями</span></td>
              <td>{MODULES_CONTROL}</td>
            </tr>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Выберите из списка страницу, которая будет являться стартовой страницей(отображается когда не передано какого-либо запроса)<br><b>По умолчанию:</b> &mdash;">Страница &laquo;по умолчанию&raquo;</span></td>
              <td><select name="def_module"><option value="">- выберите из списка -</option>{DEF_MODULES}</select></td>
            </tr>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Выберите отображать ли ошибки PHP и БД при работе на сайте. Информация о критических ошибках PHP и общих ошибках скриптов будет показана в любом случае. Данный параметр определяет именно показ детальной информации об ошибке, трассировки и т.п... <b>Рекомендуется включать только на время тестов и отладки.</b> Все ошибки сохраняются в системных логах веб-сервера в любом случае.">Показывать ошибки на сайте</span></td>
              <td><select name="show_php_errors">{SHOW_PHP_ERROR}</select></td> 
            </tr>
           <tr>
              <td><span class="_tooltip" rel="tooltip" title="Минимизация HTML кода сайта включает в себя удаление всего лишнего и не влияющего на работу сайта из HTML кода(комментарии, переносы строк, табуляция и т.д.)">Минимизировать HTML код</span></td>
              <td><select name="minify_output">{MINIFY_OUTPUT_}</select></td> 
            </tr>
        </table>
	</div>
	<div role="tabpanel" class="tab-pane fade" id="jcms_core2">
       <table>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Укажите время &laquo;неактивности&raquo; пользователя, через которое сессия авторизации будет автоматически завершена.<br><b>По умолчанию:</b> 30 мин.">Время жизни &laquo;неактивной&raquo; сессии</span></td>
              <td><div class="input-group" style="width:100px;"><input type="text" class="form-control"  name="auth_inactiveSessionLifetime" value="{AUTH_INACTIVESESSIONLIFETIME}" placeholder="30" maxlength="3" /><span class="input-group-addon">мин.</span></div></span></td>
            </tr>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Укажите время, через которое &laquo;Короткая&raquo; сессия авторизации будет автоматически завершена.<br><b>По умолчанию:</b> 60 мин.">Время жизни &laquo;короткой&raquo; сессии</span></td>
              <td><div class="input-group" style="width:100px;"><input type="text" class="form-control"  name="auth_shortSessionLifetime" value="{AUTH_SHORTSESSIONLIFETIME}" placeholder="60" maxlength="3" /><span class="input-group-addon">мин.</span></div></span></td>
            </tr>
            <tr>
              <td><span rel="tooltip" class="_tooltip" title="Укажите один или несколько IP адресов(через запятую) которым нужно разрешить доступ к панели управления. Если ничего не указано, доступ будет разрешен с любого IP. <b>Будьте внимательны при вводе, неверная установка параметра приведет к блокировке доступа к панели!</b> В случае блокировки, изменить этот параметр можно только вручную отредактировав параметр <tt>&laquo;auth_allowIP&raquo;</tt> в файле конфигурации <tt>&laquo;/config/jcms.config.php&raquo;</tt><br><b>Ваш текущий IP: </b>{__CURRENT_IP__}<br /><b>По умолчанию:</b> разрешен с любого IP">Разрешённые IP адреса</span></td>
              <td><input name="auth_allowIP" maxlength="15" placeholder="например: 192.168.1.1" value="{AUTH_ALLOWIP}" /></td>
            </tr>
		</table>
	</div>
	<div role="tabpanel" class="tab-pane fade" id="jcms_core3">
       <table>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Укажите максимальное количество записей в &laquo;Журнале аудита&raquo;. При превышении этого количества, старые записи будут удалятся.<br><b>По умолчанию:</b> 5000 шт.">Количество записей</span></td>
              <td><div class="input-group" style="width:100px;"><input type="text" class="form-control"  name="syslog_maxEvents" value="{SYSLOG_MAXEVENTS}" placeholder="5000" maxlength="5" /><span class="input-group-addon">шт.</span></div></span></td>
            </tr>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Укажите срок хранения записей &laquo;Журнала аудита&raquo;. По истечению этого срока, старые данные буду удалены.<br><b>По умолчанию:</b> 180 дн.">Срок хранения</span></td>
              <td><div class="input-group" style="width:100px;"><input type="text" class="form-control"  name="syslog_maxEvents" value="{SYSLOG_MAXEVENTS}" placeholder="180" maxlength="5" /><span class="input-group-addon">дн.</span></div></span></td>
            </tr>
		</table>
	</div>
	<div role="tabpanel" class="tab-pane fade" id="jcms_core4">
		<p style="padding:10px;"><b>В этой вкладке необходио указать данные для подключения к почтовому сервер по SMTP для отправки сообщения всеми скриптами Jensen CMS.</b> Как правило эти письма не подразумевают ответа на них получателем, поэтому рекомедуется использовать адрес вида: <tt>noreply@domain.ru</tt></p>
       <table>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Укажите данные для подключения к почтовому серверу по SMTP">Данные для подключения</span></td>
              <td><div class="input-group" style="width:;"><input type="text" class="form-control"  name="smtp_username" value="{SMTP_USERNAME}" placeholder="SMTP логин" /><span class="input-group-addon" style="border-left:none;border-right:none;"><b>:</b></span><input type="password" class="form-control"  name="smtp_password" value="{SMTP_PASSWORD}" placeholder="SMTP пароль" /><span class="input-group-addon" style="border-left:none;border-right:none;">@</span><input type="text" class="form-control"  name="smtp_server" value="{SMTP_SERVER}" placeholder="SMTP сервер" /><span class="input-group-addon" style="border-left:none;border-right:none;"><b>:</b></span><input type="text" class="form-control"  name="smtp_port" value="{SMTP_PORT}" placeholder="SMTP порт" /></div></td>
            </tr>
            <tr>
              <td><span class="_tooltip" rel="tooltip" title="Укажите данные которые будут подставлены в поле &laquo;От кого (From)&raquo;. Эти данные должны быть действительными, т.к. некоторые серверы могут отклонять несуществующие адреса.">Отправитель письма</span></td>
              <td><div class="input-group" style="width:;"><input type="text" class="form-control"  name="smtp_from_name" value="{SMTP_FROM_NAME}" placeholder="имя отправителя" /><span class="input-group-addon" style="border-left:none;border-right:none;"> &lt;</span><input type="email" class="form-control"  name="smtp_from_email" value="{SMTP_FROM_EMAIL}" placeholder="email адрес ящика" /><span class="input-group-addon" style="border-left:none;">&gt;</span></div></td>
            </tr>
		</table>
	</div>
</div>
<style type="text/css">
.tab-content .tab-pane{
	border:solid 1px #ddd;
	border-top:none;
	border-bottom-left-radius:4px;
	border-bottom-right-radius:4px;
}
.td_multiLine{
	vertical-align:top !important;	
}
.bootstrap-tagsinput input{
	border:none !important;	
}
</style>

<script type="text/javascript">
JCMS.load_css("{HOME_URL}/admin/modules/core/css/bootstrap-tagsinput.css");
JCMS.load_script("{HOME_URL}/admin/modules/core/js/bootstrap-tagsinput.min.js", function(){
	$("input[name=auth_allowIP]").tagsinput({maxChars:15});
	$("input[name=auth_allowIP]").on('beforeItemAdd', function(event) { /* проверка IP на валидность */ if(!/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(event.item)){ event.cancel = true; }});    
});
$('select[name=site_status]').change(function(e) {
  if( $(this).val() == 'off' ){
	  $('.sysconig_advancedStatusBlock').show();
  } else {
	  $('.sysconig_advancedStatusBlock').hide();
  }
}).change();
</script>