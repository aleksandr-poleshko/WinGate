<div class="block_pages block_lk" style="width:280px; margin:0 auto;">
    <div class="text" align="center">
        <h3 itemprop="name">Password recovery</h3><br />
        <p align="left"><b>Enter the email you specified during the registration</b></p><br>
        <form class="jcms_clientForm" method="post">
            <input name="jcms_clientName" value="{jcms_clientName}" required type="text" autofocus class="form-control bottom_stack" placeholder="Your e-mail address" style="width:250px;"><br />
            <img id="captcha_img" />
            <input name="jcms_clientCaptcha" id="captcha_field" type="text" required placeholder="Text from the image" style="width:250px;"><br />
            <button class="btn btn-lg btn-primary btn-block" type="submit" style="width:150px;">Next</button>
            <input type="hidden"  name="client_form_submit" value="1" />
        </form>        <br><br>
        <p style="text-align:center; width:250px;">
            <a href="{SITE_URL}/login"><u>Authorization</u></a>
             |  
            <a href="{SITE_URL}/register"><u>Registration</u></a>
        </p>  
    </div>
</div>