<div class="block_pages block_lk" style="width:264px; margin:0 auto;">
    <div class="text" align="center">
        <h3 itemprop="name">Активация аккаунта</h3><br>
        	<b>Для активации аккаунта введите текущий пароль:</b><br><br>

			<form class="jcms_clientForm" method="post">
                <input type="password" class="form-control top_stack" required name="jcms_clientPassword" placeholder="Пароль" style="width:250px;"><br />
                <img id="captcha_img" />
                <input name="jcms_clientCaptcha" id="captcha_field" type="text" required placeholder="текст с картинки" style="width:250px;"><br />
                <button class="btn btn-lg btn-primary btn-block" type="submit" style="width:150px; margin-left:51px;">Продолжить</button>
                <input type="hidden"  name="client_form_submit" value="1" />
            </form>        <br><br>

        <p style="text-align:center; width:250px;">
            <a href="{SITE_URL}/login"><u>Авторизация</u></a>
             |  
            <a href="{SITE_URL}/lost_password"><u>Забыли пароль?</u></a>
        </p>  
    </div>
</div>