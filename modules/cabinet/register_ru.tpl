<div class="block_pages block_lk" style="width:280px; margin:0 auto;">
    <div class="text" align="center">
        <h3 itemprop="name">Регистрация</h3><br />
        <form class="jcms_clientForm" method="post">
            <input name="jcms_clientEmail" value="{jcms_clientEmail}" required type="email" autofocus placeholder="Ваш адрес эл. почты" style="width:250px;"><br />
            <input name="jcms_clientLogin" value="{jcms_clientLogin}" autofocus required type="text" autofocus placeholder="желаемый логин" style="width:250px;"><br />
            <input name="jcms_clientPassw1" type="password" required placeholder="желаемый пароль" style="width:250px;"><br />
            <input name="jcms_clientPassw2" type="password" required placeholder="желаемый пароль еще раз" style="width:250px;"><br />
            <img id="captcha_img" />
            <input name="jcms_clientCaptcha" id="captcha_field" type="text" required placeholder="текст с картинки" style="width:250px;"><br />
            <button class="btn btn-lg btn-primary btn-block" type="submit" style="width:150px;">Продолжить</button>
            <input type="hidden"  name="client_form_submit" value="1" />
        </form><br><br>        
        <p style="text-align:center; width:250px;">
            <a href="{SITE_URL}/login"><u>Авторизация</u></a>
             |  
            <a href="{SITE_URL}/lost_password"><u>Забыли пароль?</u></a><br>

            <a href="{SITE_URL}/confirmResend"><u>Запрос на повторную активацию</u></a>
        </p>  
    </div>
</div>