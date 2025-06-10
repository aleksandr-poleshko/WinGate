<?PHP
/*  =================================  ##
##              Jensen CMS 2           ##
##  =================================  ##
##          Copyright (c) 2015         ##
##         www.JensenStudio.net        ##
##  =================================  ##
##   WWW: www.JensenStudio.net         ##
##   EMAIL: support@JensenStudio.net   ##
##  =================================  */

if( !defined("JENSENCMS2") ) { header("HTTP/1.1 403"); exit("Hacking attempt!"); }

class NEWS implements JCMS_MODULE{
	/* инициаизация модуля */
	function setup(){
		$setup["title"] = "Модуль &laquo;Новостные страницы&raquo;";
		$setup["descr"] = "";
		$cat = "modules"; 
		$setup["nav"][$cat]['title'] = "Модули"; 
		$setup["nav"][$cat]['items'][] = array(
			"title" => "Управление новостями",
			"href" => "module/news"
		);
		$setup["perm"] = array(
			"view"=>"Просмотр списка новостных страниц и их контента",
			"add"=>"Создание новых новостных страниц",
			"edit"=>"Редактирование новостных страниц",
			"delete"=>"Удаление новостных страниц",
		);
		
		return $setup;
	}
	
	/* воркер, обрабатывающий запрос к этому модулю */
	function work(){
		global $_JCMS;
		$json = array();
		$_perm = $_JCMS->auth->getProfile(); $_perm = $_perm['permission']; if( $_perm[$_JCMS->query[0].'/'.$_JCMS->query[1]] ){ $_perm = $_perm[$_JCMS->query[0].'/'.$_JCMS->query[1]]; } else { $_perm = array(); }

		// создание страницы
		if( $_JCMS->query[2] == 'add' ){
			if( !in_array('add', $_perm) ){
				$json['result'] = 1; $_JCMS->message('У вас недостаточно прав для выполнения этого запроса!', 'Доступ ограничен групповой политикой безопасности.', 'error');
				return $json;
			}
			$json = $this->showPage_add();
			return $json;
		}		
		// создание страницы
		if( $_JCMS->query[2] == 'edit' ){
			if( !in_array('edit', $_perm) ){
				$json['result'] = 1; $_JCMS->message('У вас недостаточно прав для выполнения этого запроса!', 'Доступ ограничен групповой политикой безопасности.', 'error');
				return $json;
			}
			if( $_POST['action'] == 'delete' ){
				if( !in_array('delete', $_perm) ){
					$json['result'] = 1; $_JCMS->message('У вас недостаточно прав для выполнения этого запроса!', 'Доступ ограничен групповой политикой безопасности.', 'error');
					return $json;
				}
				$json = $this->actionDelete();
			} else {
				$json = $this->showPage_edit();
			}
			return $json;
		}		

		if( !in_array('view', $_perm) ){
			$json['result'] = 1; $_JCMS->message('У вас недостаточно прав для выполнения этого запроса!', 'Доступ ограничен групповой политикой безопасности.', 'error');
			return $json;
		}
		
		// данные для таблицы (AJAX)
		if( $_GET['action'] == 'getTableData' ){
			$res = $this->getTableDataAjax($_POST);
			$res['template'] = trim(ob_get_contents().$res['template']);
			ob_end_clean();
			exit(json_encode($res));
		}

		if( $_SESSION['JENSENCMS']['mod_news']['add_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_news']['add_success'];
			$_JCMS->message('Создание новостной страницы #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_news']['add_success']);
		}
		if( $_SESSION['JENSENCMS']['mod_news']['edit_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_news']['edit_success'];
			$_JCMS->message('Редактирование новостной страницы #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_news']['edit_success']);
		}
		if( $_SESSION['JENSENCMS']['mod_news']['delete_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_news']['delete_success'];
			$_JCMS->message('Удаление новостной страницы #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_news']['delete_success']);
		}
		
		$_JCMS->tpl->load('news/newsView.tpl');
		$_JCMS->tpl->tag("{HOME_URL}", $_JCMS->getConfig('site_url'));
		$json['result'] = 1;
		$json['module_title'] = 'Управление новостными страницами';
		$json['template'] = $_JCMS->tpl->compile();;
		$json['load_module'] = 'news/news';
		$json['callback'] = 'JCMS.modules.news.showMainPage';
		
		return $json;
	}
	
	function showPage_add(){
		global $_JCMS;
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.news.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['news_title']) ){ $_JCMS->message("Ошибка: поле &laquo;Заголовок&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( empty($data['news_sysname']) ){ $_JCMS->message("Ошибка: поле &laquo;ЧПУ заголовок&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( !in_array($data['news_type'], array(1,2,3,4)) ){ $_JCMS->message("Ошибка: в поле &laquo;Режим полной новости&raquo; ничего не выбрано!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( strip_tags($data['news_shortText']) == '' ){ $_JCMS->message("Ошибка: поле &laquo;Текст новостной страницы&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( $data['news_type'] == 1 && strip_tags($data['news_fullText']) == '' ){ $_JCMS->message("Ошибка: поле &laquo;Полный текст новостной страницы&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( $data['news_type'] == 2 && (empty($data['news_import']) || !filter_var($data['news_import'], FILTER_VALIDATE_URL)) ){ $_JCMS->message("Ошибка: поле &laquo;URL адрес для импорта&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; } elseif( $data['news_type'] == 2 ){ $data['news_fullText'] = $data['news_import']; }
#			if( $data['news_type'] == 3 ){ $data['news_fullText'] = ""; }
#			if( $data['news_type'] == 4 && (empty($data['news_exturl']) || !filter_var($data['news_exturl'], FILTER_VALIDATE_URL)) ){ $_JCMS->message("Ошибка: поле &laquo;URL адрес ссылки&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; } elseif( $data['news_type'] == 4 ){ $data['news_fullText'] = $data['news_exturl']; }
			if( !in_array($data['news_status'], array('0','1'/*,'2'*/))  ){ $_JCMS->message("Ошибка: в поле &laquo;Состояние&raquo; ничего не выбрано!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( $data['news_status'] == 2 && (empty($data['news_datePublic']) || !strtotime($data['news_datePublic'])) > 0 ){ $_JCMS->message("Ошибка: поле &laquo;Дата публикации&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; } else
#			if( $data['news_status'] == 2 ){
#				if( strtotime($data['news_datePublic']) > time() ){
#					$data['news_datePublic'] = date("Y-m-d H:i:s", strtotime($data['news_datePublic']));					
#				} else {
#					// указанная дата публикации уже прошла... ставим статус на включено
#					$data['news_datePublic'] = '0000-00-00 00:00:00';
#					$data['news_status'] = 1;
#				}
#			} elseif( $data['news_status'] != 2 ){
				$data['news_datePublic'] = '0000-00-00 00:00:00';
#			}
#			$data['news_meta'] = $_JCMS->db->escape_string(json_encode($data['news_meta']));
			
			$profile = $_JCMS->auth->getProfile();
			$data['admin_id'] = $profile['admin_id'];
			
			$sql_code = "INSERT INTO `jcms2_moduleNews`(`news_sysname`, `news_title`, `news_type`, `news_shortText`, `news_fullText`, `news_meta`, `news_dateCreate`, `news_dateChange`, `news_datePublic`, `admin_id`, `news_status`) VALUES ('{$data[news_sysname]}', '{$data[news_title]}', '{$data[news_type]}', '{$data[news_shortText]}', '{$data[news_fullText]}', '{$data[news_meta]}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '{$data[news_datePublic]}', '{$data[admin_id]}', '{$data[news_status]}')";
			if( !$res = $_JCMS->db->query($sql_code) ){
				if( preg_match("/Duplicate entry '.+' for key 'news_sysname'/", $_JCMS->db->error) ){
					$_JCMS->message("Ошибка: новостная страница с таким &laquo;ЧПУ заголовком&raquo; уже существует!", "", 'error');					
				} else {
					$_JCMS->message("Ошибка базы данных! [php/core.news#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				}
				return $json;
			}
			$json['add'] = 'success';
			$_SESSION['JENSENCMS']['mod_news']['add_success'] = $_JCMS->db->insert_id;
			
			return $json;
		} // end if

		$json['result'] = 1;
		$json['module_title'] = 'Создание новой новостной страницы / Управление новостными страницами';
		$_JCMS->tpl->load("news/newsAdd.tpl");
		$_JCMS->tpl->tag("{HOME_URL}", $_JCMS->getConfig('site_url'));
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'news/news';
		$json['callback'] = 'JCMS.modules.news.showPage_add';
		return $json;
	}

	function showPage_edit(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_GET['id']));
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.news.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['news_title']) ){ $_JCMS->message("Ошибка: поле &laquo;Заголовок&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( empty($data['news_sysname']) ){ $_JCMS->message("Ошибка: поле &laquo;ЧПУ заголовок&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( !in_array($data['news_type'], array(1,2,3,4)) ){ $_JCMS->message("Ошибка: в поле &laquo;Режим полной новости&raquo; ничего не выбрано!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			if( strip_tags($data['news_shortText']) == '' ){ $_JCMS->message("Ошибка: поле &laquo;Текст новостной страницы&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( $data['news_type'] == 1 && strip_tags($data['news_fullText']) == '' ){ $_JCMS->message("Ошибка: поле &laquo;Полный текст новостной страницы&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( $data['news_type'] == 2 && (empty($data['news_import']) || !filter_var($data['news_import'], FILTER_VALIDATE_URL)) ){ $_JCMS->message("Ошибка: поле &laquo;URL адрес для импорта&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; } elseif( $data['news_type'] == 2 ){ $data['news_fullText'] = $data['news_import']; }
#			if( $data['news_type'] == 3 ){ $data['news_fullText'] = ""; }
			if( !in_array($data['news_status'], array('0','1'/*,'2'*/))  ){ $_JCMS->message("Ошибка: в поле &laquo;Состояние&raquo; ничего не выбрано!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
#			if( $data['news_type'] == 4 && (empty($data['news_exturl']) || !filter_var($data['news_exturl'], FILTER_VALIDATE_URL)) ){ $_JCMS->message("Ошибка: поле &laquo;URL адрес ссылки&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; } elseif( $data['news_type'] == 4 ){ $data['news_fullText'] = $data['news_exturl']; }
#			if( $data['news_status'] == 2 && (empty($data['news_datePublic']) || !strtotime($data['news_datePublic'])) > 0 ){ $_JCMS->message("Ошибка: поле &laquo;Дата публикации&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; } else
#			if( $data['news_status'] == 2 ){
#				if( strtotime($data['news_datePublic']) > time() ){
#					$data['news_datePublic'] = date("Y-m-d H:i:s", strtotime($data['news_datePublic']));					
#				} else {
#					// указанная дата публикации уже прошла... ставим статус на включено
#					$data['news_datePublic'] = '0000-00-00 00:00:00';
#					$data['news_status'] = 1;
#				}
#			} elseif( $data['news_status'] != 2 ){
#				$data['news_datePublic'] = '0000-00-00 00:00:00';
#			}
#			$data['news_meta'] = $_JCMS->db->escape_string(json_encode($data['news_meta']));

			$sql_code = "UPDATE `jcms2_moduleNews` SET `news_sysname`='{$data[news_sysname]}', `news_title`='{$data[news_title]}', `news_type`='{$data[news_type]}', `news_fullText`='{$data[news_fullText]}', `news_shortText`='{$data[news_shortText]}', `news_meta`='{$data[news_meta]}', `news_status`='{$data[news_status]}', `news_dateChange` = CURRENT_TIMESTAMP, `news_datePublic` = '{$data[news_datePublic]}' WHERE `news_id` = '{$id}' LIMIT 1";
			if( !$res = $_JCMS->db->query($sql_code) ){
				if( preg_match("/Duplicate entry '.+' for key 'news_sysname'/", $_JCMS->db->error) ){
					$_JCMS->message("Ошибка: новостная страница с таким &laquo;ЧПУ заголовком&raquo; уже существует!", "", 'error');					
				} else {
					$_JCMS->message("Ошибка базы данных! [php/core.news#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				}
				return $json;
			}
			$json['edit'] = 'success';
			$_SESSION['JENSENCMS']['mod_news']['edit_success'] = $id;
			
			return $json;
		} // end if
		
		$sql_code = "SELECT * FROM `jcms2_moduleNews` WHERE `news_id` = '{$id}' LIMIT 1";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.news#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res->num_rows !== 1 ){
			$_JCMS->message("Ошибка: запрошенная новостная страница не найдена! [php/core.news#".__LINE__."]", "", 'error'); return $json;
		}
		$data = $res->fetch_assoc();
		$admin_name = '<span class="_tooltip" rel="tooltip" title="<b>ID:</b> '.$data['admin_id'].'<br /><b>Login:</b> '.$data['admin_login'].'">'.mb_ucfirst($data['admin_name']?$data['admin_name']:$data['admin_login']).'</span>';
		$news_dateChange = date("d.m.Y H:i:s",strtotime($data['news_dateChange']));
	

		$json['result'] = 1;
		$json['module_title'] = 'Редактирование новостной страницы #'.$id.' / Управление новостными страницами';
		$_JCMS->tpl->load("news/newsEdit.tpl");
		$_JCMS->tpl->tag("{HOME_URL}", $_JCMS->getConfig('site_url'));
		$data['meta'] = json_decode($data['news_meta'],1);
		foreach($data as $key=>$val){ $_JCMS->tpl->tag("{".strtoupper($key)."}", $val); }
		$_JCMS->tpl->tag("{NEWS_DATECREATE}", date('d.m.Y H:i:s', strtotime($data['news_dateCreate'])));
		$_JCMS->tpl->tag("{NEWS_DATEPUBLIC}", $data['news_datePublic']!='0000-00-00 00:00:00'?date('d.m.Y H:i:s', strtotime($data['news_datePublic'])):'');
		$_JCMS->tpl->tag("{META_DESCR}", $data['meta']['descr']);
		$_JCMS->tpl->tag("{META_KEYWORDS}", $data['meta']['keywords']);
		$_JCMS->tpl->tag("{NEWS_FULLTEXT}", $data['news_type']==1?$data['news_fullText']:'');
		$_JCMS->tpl->tag("{NEWS_IMPORT}", $data['news_type']==2?$data['news_fullText']:''); 
		$_JCMS->tpl->tag("{NEWS_EXTURL}", $data['news_type']==4?$data['news_fullText']:'');
		$_JCMS->tpl->tag("{NEWS_LINK}", $_JCMS->getConfig('site_url').'/news/'.($data['news_sysname']?$data['news_sysname']:$data['news_id']));
		$json['template'] = $_JCMS->tpl->compile();
		$json['load_module'] = 'news/news';
		$json['callback'] = 'JCMS.modules.news.showPage_edit';
		$json['data']['news_status'] = $data['news_status'];
		$json['data']['news_type'] = $data['news_type'];
		return $json;
	}
	
	function actionDelete(){
		global $_JCMS;
		$json = array();
		$json['result'] = 1;
		$json['callback'] = 'JCMS.modules.news.result';
		$id = $_JCMS->db->escape_string(intval($_GET['id']));
		
		$sql_code = "DELETE FROM `jcms2_moduleNews` WHERE `news_id` = '{$id}' LIMIT 1";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.news#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		} else {
			$json['delete'] = 'success';
			$_SESSION['JENSENCMS']['mod_news']['delete_success'] = $id;
		}
		
		return $json;
	}
	
	/* выбирает из базы строки, подходящие запросу юзера(с учетом поиска и сортировки) и формирует ответ в формате, подходящем для JS плагина DataTables */
	function getTableDataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('news_id', 'news_sysname', 'news_title', 'news_dateChange', array('jcms2_admins`.`admin_id','jcms2_admins`.`admin_name','jcms2_admins`.`admin_login'));

		$_POST['search']['value'] = trim(strval($_POST['search']['value']));
		if( !empty($_POST['search']['value']) ){ $search = $_JCMS->db->escape_string($_POST['search']['value']);; } else { $search = false; }
		
		$sql_search = $sql_order = "";
		foreach($columns as $key=>$val){
			if( $search ){
				// SQL фрагмент запроса на поиск
				if( !empty($sql_search) ){ $sql_search .= "OR"; }
				if( is_array($val) ){
					$t = '';
					foreach($val as $val2){
						if( !empty($t) ){ $t .= "OR"; }
						$t .= " `".$val2."` LIKE '%".$search."%' ";
					}
					$sql_search .= $t; unset($t);
				} else {
					$sql_search .= " `".$val."` LIKE '%".$search."%' ";					
				}
			}
			if( is_array($_POST['order']) ){
				foreach($_POST['order'] as $arr){
					if( $arr['column'] == $key ){
						// SQL фрагмент запроса на сортировку
						if( !empty($sql_order) ){ $sql_order .= ","; } else { $sql_order = "ORDER BY"; }
						if( is_array($val) ){
							foreach($val as $arr2){
								if( $sql_order != 'ORDER BY' ){ $sql_order .= ","; }
								$sql_order .= " `".$arr2."` ".strtoupper($_JCMS->db->escape_string($arr['dir']))." ";
							}
						} else {
							$sql_order .= " `".$val."` ".strtoupper($_JCMS->db->escape_string($arr['dir']))." ";
						}
					}
					unset($arr);
				}
			}
		}
		$sql_where = NULL;
		if( !empty($sql_search) ){ $sql_where = !$sql_where?"AND (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "SELECT SQL_CALC_FOUND_ROWS `jcms2_moduleNews`.*, `jcms2_admins`.`admin_name`, `jcms2_admins`.`admin_login` FROM `jcms2_moduleNews`, `jcms2_admins` WHERE `jcms2_admins`.`admin_id` = `jcms2_moduleNews`.`admin_id` {$sql_where} {$sql_order} {$sql_limit}";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.news#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				$admin_name = '<span class="_tooltip" rel="tooltip" title="<b>ID:</b> '.$data['admin_id'].'<br /><b>Login:</b> '.$data['admin_login'].'">'.mb_ucfirst($data['admin_name']?$data['admin_name']:$data['admin_login']).'</span>';
				$news_dateChange = date("d.m.Y H:i:s",strtotime($data['news_dateChange']));
				$before_title .= !$before_title&&$data['news_type']==2?'<span class="glyphicon glyphicon-share" rel="tooltip" title="<b>Тип:</b> &laquo;Иморт URL&raquo;"></span> ':'';
				$before_title .= $data['news_status']==0?'<span class="glyphicon glyphicon-eye-close" rel="tooltip" title="Новостная страница отключена"></span> ':'';
				$before_title .= $data['news_status']==2?'<span class="glyphicon glyphicon-time" rel="tooltip" title="<b>Отложенная публикация.</b><br />Новость будет опубликована<br />'.date("d.m.Y H:i:s", strtotime($data['news_datePublic'])).'"></span> ':'';
				$table_data[] = array($data['news_id'], /*mb_htmlwordwrap($data['news_sysname'],30),*/ mb_htmlwordwrap($before_title.$data['news_title'],30), $news_dateChange, $admin_name);
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.news#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; } else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(`news_id`) as total FROM `jcms2_moduleNews`, `jcms2_admins` WHERE `jcms2_admins`.`admin_id` = `jcms2_moduleNews`.`admin_id`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.news#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			$data = $res->fetch_assoc();
			$total = $data['total'];
		}
		
		$resp['recordsTotal'] = $total;
		$resp['recordsFiltered'] = $total_filtered;		
		$resp['data'] = $table_data; unset($table_data);
		
		return $resp;
	}
	
	/* получает страницу конфигурации модуля */
	function getConfigPage(){
		global $_JCMS;
		$modes = '<option value="1"'.($_JCMS->getConfig('news_mode')=='1'?' selected':'').'>ДА</option><option value="0"'.($_JCMS->getConfig('news_mode')!='1'?' selected':'').'>НЕТ</option>';
		$_JCMS->tpl->load('news/news.config.tpl');
		$_JCMS->tpl->tag("{NEWS_MODES}", $modes);
		return $_JCMS->tpl->compile();
	}
	
	/* проверка конфигурации модуля(если нужно), перед сохранением */
	function checkConfig(&$errors){
		// $_POST['key'] = 'val'; // элемент конфигурации
		// $errors[] = 'текст ошибки'; // вернуть ошибку (конфигурация не будет сохранена, пока есть хотя бы одна ошибка)
	}
}

// END.
?>