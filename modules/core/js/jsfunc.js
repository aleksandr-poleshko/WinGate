// JavaScript Document

$(document).ready(function() {
	if( $("#jcms2LoginForm").length > 0 ){
		setTimeout(function(){if( $('input[name=jcms_clientLogin]').val().trim() != '' ) $('input[name=jcms_clientPassword]').focus(); },100);
	}
	if( $("#captcha_img").length > 0 ){
		$("#captcha_img").click(function(e) {
        	reloadCaptcha(this); 
			$('input#captcha_field').val('').focus();
        });
		$("#captcha_img").click();
	}
	$('form.addMsgForm').submit(function(e) {
		if( $.trim($('textarea[name=text]', this).val()) == '' ){
			if( SITE_LANG == 'en' ){
				alert("Message can not be empty!");
			} else {
				alert("Сообщение не может быть пустым!");				
			}
			return false;
		}
		var str = $('form.addMsgForm').serialize(); 
		$('textarea[name=text], button', $('form.addMsgForm')).prop('disabled', 1);
		$.post("", str, function(data){ 
			data = $.parseJSON(data);
			if( data['status'] != 1 ){
				$('textarea[name=text], button', $('form.addMsgForm')).prop('disabled', 0);
				if( data['error'] ) alert(data['error']); else alert('Error!');
			} else {
				setTimeout("$('textarea[name=text], button', $('form.addMsgForm')).prop('disabled', 0);",5000);
				$('textarea[name=text]', $('form.addMsgForm')).val('');
			}
		});
		return false;
	});
	$.getScript(SITE_URL+'/templates/js/evercookie/swfobject-2.2.min.js', function(){
		$.getScript(SITE_URL+'/templates/js/evercookie/evercookie.js', function(){
			var ec = new evercookie();
			ec.get("jcms2_uid", function(best, all){}, 0);
		});
	});
});

function reloadCaptcha(e){
	var min = 1000000000000; var max = 9999999999999;
	$(e).attr('src', SITE_URL+"/captcha.jpg?_="+(Math.floor(Math.random() * (max - min + 1)) + min));
}

 
function buyTest(){
	$.post('', {action:'buyTest', type:'check', form_submit:'1'}, function(data){
		data = $.parseJSON(data);
		if( data['status'] != 1 && data['status'] != 2 ){
			if( data['error'] ) alert(data['error']); else alert('Error!');
		} else if( data['status'] == 2 ){
			// доступ активирован
			modal_alertCopy(data['url']);
			return;
		} else if( data['status'] == 1 ){
			if( SITE_LANG == 'en' ){
				var div = '<div title="Request for a test access"><form><p>Provide an IP-address from which the access to be gained:</p><label>IP 1:<input maxlength="15" type="text" name="ip" style="border:solid 1px silver;" placeholder="0.0.0.0"/></label><label>IP 2:<input disabled type="text" style="border:solid 1px silver;" placeholder="0.0.0.0"/></label><p>Please, read the <a href="http://wingate.me/faq.html" target="_blank">FAQ</a> before the test request!</p><input type="checkbox" value="1" name="faqConfirm" /><label onclick="$(\'input[name=faqConfirm]\').prop(\'checked\', ($(\'input[name=faqConfirm]\').prop(\'checked\')?0:1));" style="display:inline; cursor:pointer;"> I have read the FAQ</label><input type="hidden" value="buyTest" name="action" /><input type="hidden" value="buy" name="type" /><input type="hidden" value="1" name="form_submit" /></form></div>';
			} else {
				var div = '<div title="Запрос тестового доступа"><form><p>Укажите IP с которого будут осуществляться доступ:</p><label>IP 1:<input maxlength="15" type="text" name="ip" style="border:solid 1px silver;" placeholder="0.0.0.0"/></label><label>IP 2:<input disabled type="text" style="border:solid 1px silver;" placeholder="0.0.0.0"/></label><p>Перед запросом на тест ознакомьтесь с <a href="http://wingate.me/faq.html" target="_blank">FAQ</a>!</p><input type="checkbox" value="1" name="faqConfirm" /><label onclick="$(\'input[name=faqConfirm]\').prop(\'checked\', ($(\'input[name=faqConfirm]\').prop(\'checked\')?0:1));" style="display:inline; cursor:pointer;"> Я ознакомился с FAQ</label><input type="hidden" value="buyTest" name="action" /><input type="hidden" value="buy" name="type" /><input type="hidden" value="1" name="form_submit" /></form></div>';
			}
			window.dialog = $(div)
			.dialog({
				resizable: false,
				height: "auto", 
				width: 500,
				modal: true,
				show: {
					effect: "fade",
					duration: 500
				},
				hide: {
					effect: "fade",
					duration: 500
				},
				create: function( event, ui ) {
					if( SITE_LANG == 'en' ){
						$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(0).html('Cancel');
						$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(1).html('Next');
					}
				},
				buttons: {
					"Отмена": function() {
						$(this).dialog("close");
						setTimeout('$(".ui-dialog").remove();', 500);
					},
					"Продолжить": function() {
						if( $('input[name=ip]').val().trim() == '' ){
							if( SITE_LANG == 'en' ) alert("Invalid IP!"); else  alert("Указан неверый IP!");
							return false;
						}
						if( !$('input[name=faqConfirm]').prop('checked') ){
							if( SITE_LANG == 'en' ) alert("Please, read the FAQ!"); else  alert("Ознакомьтесь с FAQ!");
							return false;
						}
						$('html').css('cursor', 'progress');
						$('input', $(window.dialog).parents('.ui-dialog').eq(0)).prop('readonly', true);
						$('button', $(window.dialog).parents('.ui-dialog').eq(0)).prop('disabled', true);
						var ec = new evercookie();
						ec.get("jcms2_uid", function(best, all){
							$.post("", $('form', window.dialog).serialize(), function(data){
								data = $.parseJSON(data);
								if( data['status'] != 1 ){
									$('html').css('cursor', '');
									$('input', $(window.dialog).parents('.ui-dialog').eq(0)).prop('readonly', false);
									$('button', $(window.dialog).parents('.ui-dialog').eq(0)).prop('disabled', false);
									if( data['error'] ) alert(data['error']); else alert('Error!');
								} else {
									document.location.href = document.location.href.replace('#',''); // обновляем страницу...
								}
							});
						}, 0);
					}
				}	
			}); 
		}
	});
}

function buy(tariff, period){
	$.post("", {tariff:tariff, period:period, action:'checkOrder', form_submit:'1'}, function(data){
		data = $.parseJSON(data);
		if( data['status'] != 1 ){
			alert('Error!');
		} else {
			if( SITE_LANG == 'en' ){
				var div = '<div title="Confirm order"><p><b>'+String(data['confirmStr'])+'</b></p><br><p>By this Agreement, you confirm your consent on funds transfer for providing services.<br>You also agree that you have received a one-time free test at the request and this service meets your requirements completely.<br>Please refer to the <a href="http://wingate.me/faq.html" target="_blank">FAQ</a> on our website in order to avoid possible misunderstandings.</p></div>';				
			} else {
				var div = '<div title="Подтверждение заказа"><p><b>'+String(data['confirmStr'])+'</b></p><br><p>Настоящим соглашением Вы подтверждает свое согласие на перевод денежных средств за предоставление услуг.<br>Вы также соглашаетесь с тем, что получили одноразовый бесплатный тест под ваш запрос и услуга подходит вам полностью.<br>Предварительно ознакомьтесь с <a href="http://wingate.me/faq.html" target="_blank">FAQ</a> размещенным на сайте во избежание недопониманий.</p></div>';				
			}
			window.dialog = $(div)
			.dialog({
				resizable: false,
				height: "auto", 
				width: 500,
				modal: true,
				show: {
					effect: "fade",
					duration: 500
				},
				create: function( event, ui ) {
					if( SITE_LANG == 'en' ){
						$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(0).html('Cancel');
						$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(1).html('Next');
					}
				},
				hide: {
					effect: "fade",
					duration: 500
				},
				buttons: {
					"Отмена": function() {
						$(this).dialog("close");
						setTimeout('$(".ui-dialog").remove();', 500);
					},
					"Продолжить": function() {
						var form = $(data['orderForm']).appendTo('body').hide();
						form.submit();
					}
				}	
			});
		}
	});
}

function orderSetting(order){
	$.post("", {order:order, action:'getOrderSetting', form_submit:'1'}, function(data){
		data = $.parseJSON(data);
		if( data['status'] != 1 ){
			alert('Error!'); 
		} else {
			if( SITE_LANG == 'en' ){
				var div = '<div title="Settings"><form onclick="return false;"><p>IPs the access is gained from</p><label>IP 1: <input maxlength="15" type="text" name="ip[]" style="border:solid 1px silver;" placeholder="0.0.0.0"/></label><label id="ip2">IP 2: <input type="text" maxlength="15" name="ip[]" style="border:solid 1px silver;" placeholder="0.0.0.0" /></label><input type="hidden" value="saveOrderSetting" name="action" /><input type="hidden" value="'+String(order)+'" name="order" /><input type="hidden" value="1" name="form_submit" /></form></div>';				
			} else {
				var div = '<div title="Настройки"><form onclick="return false;"><p>IP с которых будут осуществляться доступ</p><label>IP 1: <input maxlength="15" type="text" name="ip[]" style="border:solid 1px silver;" placeholder="0.0.0.0"/></label><label id="ip2">IP 2: <input type="text" maxlength="15" name="ip[]" style="border:solid 1px silver;" placeholder="0.0.0.0" /></label><input type="hidden" value="saveOrderSetting" name="action" /><input type="hidden" value="'+String(order)+'" name="order" /><input type="hidden" value="1" name="form_submit" /></form></div>';				
			}
			window.dialog = $(div)
			.dialog({
				resizable: false,
				height: "auto", 
				width: 500,
				modal: true,
				show: {
					effect: "fade",
					duration: 500
				},
				hide: {
					effect: "fade",
					duration: 500
				},
				create: function( event, ui ) {
					if( SITE_LANG == 'en' ){
						$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(0).html('Cancel');
						$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(1).html('Save');
					}
				},
				buttons: {
					"Отмена": function() {
						$(this).dialog("close");
						setTimeout('$(".ui-dialog").remove();', 500);
					},
					"Сохранить": function() {
						$.post("", $('form', this).serialize(), function(data){
							data = $.parseJSON(data);
							if( data['status'] != 1 ){
								if( data['error'] ) alert(data['error']); else alert('Error!');
							} else {
								$(window.dialog).dialog("close"); 
								setTimeout('$(".ui-dialog").remove();', 500);
							}
						});
					}
				}	
			});
			$('input', window.dialog).off('keyup keydown keypress change').keyup(function(e){
                $(this).val($(this).val().replace(/[^.0-9]/gim, ''));
            }).change(function(e) {
                $(this).keyup();
            }).keydown(function(e) {
                $(this).keyup();
            }).keypress(function(e) { 
                $(this).keyup();
            });
			$('input[name="ip[]"]', window.dialog).eq(0).val(String($(data['data']['ip']).get(0)?$(data['data']['ip']).get(0):''));
			$('input[name="ip[]"]', window.dialog).eq(1).val(String($(data['data']['ip']).get(1)?$(data['data']['ip']).get(1):''));
			if( data['isTest'] == 1 ) $('label#ip2 input', window.dialog).prop('disabled', 1);
		}
	});
}

function orderExtend(order){
	$.post("", {order:order, action:'getOrderExtendData', form_submit:'1'}, function(data){
		data = $.parseJSON(data);
		if( data['status'] != 1 ){
			alert('Error!'); 
		} else {
			if( SITE_LANG == 'en' ){
				var div = '<div title="Extend order"><form><p>Select the extension period:</p><br>'+String(data['form'])+'<input type="hidden" value="orderExtend" name="action" /><input type="hidden" value="'+String(order)+'" name="order" /><input type="hidden" value="1" name="form_submit" /></form></div>';				
			} else {
				var div = '<div title="Продление заказа"><form><p>Выберите период продления:</p><br>'+String(data['form'])+'<input type="hidden" value="orderExtend" name="action" /><input type="hidden" value="'+String(order)+'" name="order" /><input type="hidden" value="1" name="form_submit" /></form></div>';				
			}
			window.dialog = $(div)
			.dialog({
				resizable: false,
				height: "auto", 
				width: 500,
				modal: true,
				show: {
					effect: "fade",
					duration: 500
				},
				hide: {
					effect: "fade",
					duration: 500
				},
				create: function( event, ui ) {
					if( SITE_LANG == 'en' ){
						$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(0).html('Cancel');
						$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(1).html('Extend');
					}
				},
				buttons: {
					"Отмена": function() {
						$(this).dialog("close");
						setTimeout('$(".ui-dialog").remove();', 500);
					},
					"Продлить": function() {
						$('form', window.dialog).attr('method', 'post').submit();
					}
				}	
			});
			$('label', window.dialog).off('click').click(function(e) {
                $('input[type=radio]', this).prop('checked', true);
            });
			$('label', window.dialog).eq(0).click();
		}
	});
}

function addTicket(){
	if( SITE_LANG == 'en' ){
		var div = '<div title="New request"><form method="post"><label>Header <input type="text" name="title" style="border:solid 1px silver;" /></label><label>Question <textarea name="text" style="border:solid 1px silver; height:150px; resize:none" /></label><input type="hidden" value="newTicket" name="action" /><input type="hidden" value="1" name="form_submit" /></form></div>';				
	} else {
		var div = '<div title="Новый запрос"><form method="post"><label>Заголовок <input type="text" name="title" style="border:solid 1px silver;" /></label><label>Вопрос <textarea name="text" style="border:solid 1px silver; height:150px; resize:none" /></label><input type="hidden" value="newTicket" name="action" /><input type="hidden" value="1" name="form_submit" /></form></div>';				
	}
	window.dialog = $(div)
	.dialog({
		resizable: false,
		height: "auto", 
		width: 800,
		modal: true,
		show: {
			effect: "fade",
			duration: 500
		},
		hide: {
			effect: "fade",
			duration: 500
		},
		create: function( event, ui ) {
			if( SITE_LANG == 'en' ){
				$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(0).html('Cancel');
				$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(1).html('Create request');
			}
		},
		buttons: {
			"Отмена": function() {
				$(this).dialog("close");
				setTimeout('$(".ui-dialog").remove();', 500);
			},
			"Создать запрос": function() {
				if( $.trim($('input[name=title]', window.dialog).val()) == '' || $.trim($('textarea[name=text]', window.dialog).val()) == '' ){
					if( SITE_LANG == 'en' ){
						alert("Fill in all fields and try again...");
					} else {
						alert("Заполните все поля и повторите попытку...");
					}
					return;
				}
				$.post("", $('form', this).serialize(), function(data){
					data = $.parseJSON(data);
					if( data['status'] != 1 ){
						if( data['error'] ) alert(data['error']); else alert('Error!');
					} else {
						window.location.href = data['url'];
						$(window.dialog).dialog("close"); 
						setTimeout('$(".ui-dialog").remove();', 500);
					}
				});
			}
		}	
	});	
}

function ticketChat(){
	$.post("", {action:"getChatLog", lastMsg:$('.ticket_chatlog').attr('data-last_msg')}, function(data){
		setTimeout(function(){ ticketChat();}, 5000);
		data = $.parseJSON(data);
		if( data['status'] != 1 ){
			if( data['error'] ) alert(data['error']); else alert('Error!');
		} else {
			if( data['chatlog'] && data['chatlog'].length > 0 ){
				if( $('.ticket_chatlog').attr('data-last_msg') != 0 ) var sound = true; else var sound = false;
				$('.ticket_chatlog').attr('data-last_msg', data['lastMsg']);
				$.each(data['chatlog'], function(key,val){
					if( SITE_LANG == 'en' ){
						var username = val['type']=='admin'?'Administrator':'You';
					} else {
						var username = val['type']=='admin'?'Администратор':'Вы';
					}
					$('<div class="'+String(val['type'])+'_msg"><span>'+String(username)+'</span><span title="'+String(val['timestamp'])+'">'+String(val['time'])+'</span><div class="clear"></div><div>'+String(val['text'])+'</div></div>').appendTo('.ticket_chatlog');
					if( val['type'] == 'admin' && sound == true) playNewMesage(); // есть новое сообщение
				});
				setTimeout(function(){$('.ticket_chatlog').animate({ scrollTop:$('.ticket_chatlog')[0].scrollHeight}, 1000);},500); // скролл в конец
			}
		}
	});	
	if( $('.ticket_chatlog').attr('data-ticket_status') == '3' ){
		$('.addMsgForm').remove();
	}
}

function playNewMesage(){
	var file = '/templates/sounds/newMessage';
	var audioTag = $("<audio>", {autoplay: true, style: "display: none"}).append(
	'<source src="'+file+'.wav" type="audio/x-wav" />' 
	+'<source src="'+file+'.mp3" type="audio/mpeg" codecs="mp3" />' 
	+'<embed src="'+file+'.wav" type="audio/x-wav" hidden="true" autostart="true" loop="false" />'
	);
	var obj = $(audioTag).appendTo('body');
	if ($.isFunction(audioTag.get(0).play)){ audioTag.attr('data-file', file); }
	setTimeout(function(){$(obj).remove();},1500);
}


function modal_alertCopy(text){
	if( SITE_LANG == 'en' ){
		var div = '<div title="Download link">Your link:<br><a href="'+String(text)+'" target="_blank">'+String(text)+'</a>';				
	} else {
		var div = '<div title="Ссылка на скачивание">Ваша ссылка:<br><a href="'+String(text)+'" target="_blank">'+String(text)+'</a>';				
	}
	$(div)
	.dialog({
		resizable: false,
		height: "auto", 
		width: 800,
		modal: true,
		show: {
			effect: "fade",
			duration: 500
		},
		hide: {
			effect: "fade",
			duration: 500
		},
		create: function( event, ui ) {
			if( SITE_LANG == 'en' ){
				$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(0).html('Cancel');
				$('.ui-dialog-buttonset button span', $(this).parents('.ui-dialog')).eq(1).html('Create request');
			}
		},
		buttons: {
			"OK": function() {
				$(this).dialog("close");
				setTimeout('$(".ui-dialog").remove();', 500);
			}
		}	
	});	
}

jQuery.loadScript = function (url, callback) {
    jQuery.ajax({
        url: url,
        dataType: 'script',
        success: callback,
        async: true
    });
}