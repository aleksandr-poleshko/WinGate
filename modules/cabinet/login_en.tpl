<div class="block_pages block_lk" style="width:264px; margin:0 auto;">
    <div class="text" align="center">
        <h3 itemprop="name">Authentication</h3><br />
        <div class="text">
			<form class="jcms_clientForm" id="jcms2LoginForm" method="post">
                <input name="jcms_clientLogin" type="text" required autofocus class="form-control bottom_stack" placeholder="Login or password" value="{CABINET_LOGIN}" style="width:250px;"><br />
                <input type="password" class="form-control top_stack" name="jcms_clientPassword" required placeholder="Password" style="width:250px;">
           		<img id="captcha_img" />
                <input name="jcms_clientCaptcha" id="captcha_field" type="text" required placeholder="Text from the image" style="width:250px;"><br />
                <button class="btn btn-lg btn-primary btn-block" type="submit" style="width:150px;">Enter</button>
                <input type="hidden" name="jcms_clientToken" value="{CABINET_TOKEN}" /> 
                <input type="hidden"  name="client_form_submit" value="1" />
            </form>    
            <br>
<br>

<p style="text-align:center; width:250px;">
	<a href="{SITE_URL}/register"><u>Registration</u></a>
     |  
	<a href="{SITE_URL}/lost_password"><u>Forgot your password?</u></a><br>

            <a href="{SITE_URL}/confirmResend"><u>Request for re-activation</u></a>
</p>    
        </div>
    </div>
</div>