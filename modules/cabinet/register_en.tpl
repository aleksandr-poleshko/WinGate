<div class="block_pages block_lk" style="width:280px; margin:0 auto;">
    <div class="text" align="center">
        <h3 itemprop="name">Registration</h3><br />
        <form class="jcms_clientForm" method="post">
            <input name="jcms_clientEmail" value="{jcms_clientEmail}" required type="email" autofocus placeholder="Your e-mail address" style="width:250px;"><br />
            <input name="jcms_clientLogin" value="{jcms_clientLogin}" autofocus required type="text" autofocus placeholder="Enter your login here" style="width:250px;"><br />
            <input name="jcms_clientPassw1" type="password" required placeholder="Enter your password here" style="width:250px;"><br />
            <input name="jcms_clientPassw2" type="password" required placeholder="Enter your password here again" style="width:250px;"><br />
            <img id="captcha_img" />
            <input name="jcms_clientCaptcha" id="captcha_field" type="text" required placeholder="Text from the image" style="width:250px;"><br />
            <button class="btn btn-lg btn-primary btn-block" type="submit" style="width:150px;">Next</button>
            <input type="hidden"  name="client_form_submit" value="1" />
        </form><br><br>        
        <p style="text-align:center; width:250px;">
            <a href="{SITE_URL}/login"><u>Authentication</u></a>
             |  
            <a href="{SITE_URL}/lost_password"><u>Forgot your password?</u></a><br>

            <a href="{SITE_URL}/confirmResend"><u>Request for re-activation</u></a>
        </p>  
    </div>
</div>