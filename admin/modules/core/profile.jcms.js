// JavaScript Document

if( !JCMS.modules ) JCMS.modules = {};

JCMS.modules.profile = {
	showPage: function(data){
		this.showPage(data['template'], function(data){
			this.navigator('/core/profile',1);
			self.tooltipInit();	
			$('form#changePassword').unbind('submit').submit(function(e) {
				JCMS.ajaxForm(this, function(data){
					JCMS.preloader(0);
					if( data['action_result'] != 1 ){
						if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);this.preloader(0);}
					}				
				});
				
				return false;
			});
			$("#passwGenerate").click(function(e) {
					JCMS.modules.profile.genPassw($("input[name=user_newpassw1], input[name=user_newpassw2]"), 10);                    
                });
			$('#changeUsername>u').click(function(e) {
				oldname = $(this).text();
				$(this).hide();
                $('<form><div class="input-group"><input name="user_newname" type="text" class="form-control" placeholder="введите новое имя пользователя" value="'+String(oldname)+'" /><span class="input-group-btn"><button type="submit" class="btn btn-success" id="passwGenerate">Сохранить</button></span></div><input type="hidden" name="action" value="changeUsername" /><input type="hidden" name="load_interface" value="1" /></form>').appendTo($('#changeUsername'));

				$('#changeUsername form').unbind('submit').submit(function(e) {
					JCMS.interfaceLoad = false;
					JCMS.ajaxForm(this, function(data){
						if( data['template'] != undefined && $.trim(data['template']) != '' ){ JCMS.getCurPageShowed().prepend(data['template']);this.preloader(0);}			
						if( data['changeUsername'] == 'success' ){
							var newname = $('input[name=user_newname]').val();
							$('#changeUsername form').remove();
							$('#changeUsername>u').html(newname).show();
						}
					});
					return false;
				});
				
            });
			
			JCMS.preloader(0);
		}, data);
	},
	
	genPassw: function(obj, len){
		if( window.genPasswInterval != undefined ) clearInterval(window.genPasswInterval);
		if( !len || len == undefined ) len = 8;
		var result = '';
        var words = 'abcdefghkmnpqastuvwxyzABCDEFGHKMNPQASTUVWXYZ23456789';
		window.genPasswInterval = setInterval(function(){
			position = Math.floor ( Math.random() * words.length-1);
			result = result + words.substring(position, position+1);
			if( result.length >= len ) clearInterval(window.genPasswInterval);
			$(obj).val(result+JCMS.modules.profile.genStr(len-result.length-1));
			$(obj).attr('type', 'text');
		},50);
	},
	
	genStr: function(len){
		if( len == undefined ) len = 10;
		var result = '';
		var words = 'abcdefghkmnpqastuvwxyzABCDEFGHKMNPQASTUVWXYZ23456789';
		for(i=0; i<len; ++i) {
			position = Math.floor ( Math.random() * words.length - 1 );
			result = result + words.substring(position, position + 1);
		}
        return result;
	}
}