            <ul class="nav navbar-nav navbar-right" id="jcms_userbar">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user" style="position:relative; top:2px;"></span> {USERNAME} <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                    <li><a href="{SITE_URL}" target="_blank">Просмотр сайта</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#" onclick="JCMS.navigator('core/profile'); return false;">Профиль</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#" onclick="JCMS.load_module('core/auth', 'JCMS.modules.auth.logout'); return false;">Выход</a></li>
                    </ul>
                </li>
            </ul>
