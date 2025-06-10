<div class="box-shadow" style="width:350px; border-radius:10px; border:solid 1px #e7e7e7; margin:0 auto; background-color:#f7f7f7;">
    <form class="jcms_authForm" method="post">
        <h2 align="center" style="margin-top:0px;"><b>Авторизация</b></h2>
        <input name="jcms_authUsername" type="text" autofocus class="form-control bottom_stack" placeholder="Логин или e-mail">
        <input type="password" class="form-control top_stack" name="jcms_authPassword" placeholder="Пароль">

        <img src="" onclick="JCMS.reloadCaptcha(this);$('input[name=jcms_authCaptcha]').val('').focus();" class="bottom_stack" style="border:solid 1px silver; border-radius:4px; border-bottom-left-radius:0; border-bottom-right-radius:0; cursor:pointer;" id="captcha_img" />
        <input type="text" class="form-control top_stack" placeholder="Защитный код с картинки" rel="tooltip" title="Введите здесь защитный код нарисованный на картинке. Если код на картинке неразборчив, кликните по ней, для генерации нового кода." name="jcms_authCaptcha"> 

        <div class="checkbox" style="margin-left:10px;">
            <label>
                <input type="checkbox" value="1" name="jcms_authShortSession" checked="checked"> Короткая сессия
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit" style="width:150px; margin-left:51px;">ВОЙТИ</button>
        <input type="hidden" name="jcms_authToken" /> 
        <input type="hidden"  name="load_interface" value="1" />
        <input type="hidden"  name="form_submit" value="1" />
        <input type="hidden"  name="form_auth" value="1" />
    </form>
</div>

<style>
.jcms_authForm {
  max-width: 282px;
  padding: 15px;
  margin: 0 auto;
}

.jcms_authForm .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.jcms_authForm .form-control:focus {
  z-index: 2;
}
.jcms_authForm .bottom_stack {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.jcms_authForm .top_stack {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>
<script type="text/javascript">
$('.tooltip').bs_tooltip();
</script>