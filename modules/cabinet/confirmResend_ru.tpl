<div class="block_pages block_lk" style="width:264px; margin:0 auto;">
    <div class="text" align="center">
        <h3 itemprop="name">Запрос на повторную отправку письма для активации</h3><br>
        	<b></b><br><br>

			<form class="jcms_clientForm" method="post">
                <input type="email" class="form-control top_stack" required name="jcms_clientEmail" placeholder="Ваш адрес эл. почты" style="width:250px;"><br />
                <img id="captcha_img" />
                <input name="jcms_clientCaptcha" id="captcha_field" type="text" required placeholder="текст с картинки" style="width:250px;"><br />
                <button class="btn btn-lg btn-primary btn-block" type="submit" style="width:150px; margin-left:51px;">Отправить</button>
                <input type="hidden"  name="client_form_submit" value="1" />
            </form>        <br><br>

        <p style="text-align:center; width:250px;">
            <a href="{SITE_URL}/login"><u>Авторизация</u></a>
             |  
            <a href="{SITE_URL}/register"><u>Регистрация</u></a>
        </p>  
    </div>
</div>