<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Jensen CMS - панель управления сайтом</title>
<link rel="stylesheet" href="{HOME_URL}/admin/modules/core/css/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="{HOME_URL}/admin/modules/core/css/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" href="{HOME_URL}/admin/modules/core/css/core.css">
<link rel="icon" href="{HOME_URL}/admin/favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="{HOME_URL}/admin/favicon.ico" type="image/x-icon">
<!--[if lt IE 9]>
<script src="{HOME_URL}/admin/modules/core/js/html5shiv.min.js"></script>
<script src="{HOME_URL}/admin/modules/core/js/respond.min.js"></script>
<![endif]-->
</head> 

<body>
<nav class="navbar navbar-default box-shadow navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand text-shadow" href="{HOME_URL}/admin" onClick="JCMS.ajax(); return false;" rel="tooltip" title="Кликните чтобы быстро перезагрузить эту страницу">JENSEN CMS<br><span>система управления сайтом</span></a>
        </div>
		<div id="navbar"></div>
    </div>
</nav>

<div class="container">
    <div id="ajax_page1">{BODY}</div> 
    <div id="ajax_page2" style="display:none;"></div>
</div>

<div id="footer" class="navbar navbar-default box-shadow2 navbar-fixed-bottom">
    <div class="container">
    	<p>Копирайт © 2010 – {JCMS_YEAR} <a href="http://jensenstudio.net/" target="_blank">Jensen web-studio</a>. Все права защищены.<br><a href="http://jensenstudio.net/jensencms" target="_blank">Система управления сайтом Jensen CMS</a> <sup>{JENSENCMS_VERSION}</sup></p>
    </div>
</div>
<div id="loader-wrapper"></div><div id="loader-text" class="box-shadow">Обработка запроса...<br>Пожалуйста подождите...<br><span></span></div>
<script src="{HOME_URL}/admin/modules/core/js/jquery.min.js"></script>
<script src="{HOME_URL}/admin/modules/core/core.jcms.js"></script>
</body>
</html>