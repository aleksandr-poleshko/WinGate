<?PHP
if( !defined("JENSENCMS2") ) { exit("Hacking attempt!"); }

// склоняет окончания для числа, например: " . numberEnd(5, array("1", "3", "5"));
function numberEnd($number, $titles) { $cases = array (2, 0, 1, 1, 1, 2); return $number.' '.$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ]; }

function mb_ucfirst($word){
	return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8').mb_strtolower(mb_substr($word, 1, mb_strlen($word), 'UTF-8'), 'UTF-8');
}

function ajaxOutput($resp){
	$resp['template'] = trim(ob_get_contents().$resp['template']);
	ob_end_clean();
	exit(json_encode($resp));	
}

function _rmdir($dir) { 
 $files = array_diff(scandir($dir), array('.','..')); 
  foreach ($files as $file) { 
	(is_dir("$dir/$file")) ? _rmdir("$dir/$file") : unlink("$dir/$file"); 
  } 
  return rmdir($dir); 
} 
  
function convertFromBytes($size){
    $unit=array('б','Кб','Мб','Гб','Тб','Пб');
    return round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

function convertToBytes( $value ) {
    if ( is_numeric( $value ) ) {
        return $value;
    } else {
        $value_length = strlen( $value );
        $qty = substr( $value, 0, $value_length - 1 );
        $unit = strtolower( substr( $value, $value_length - 1 ) );
        switch ( $unit ) {
            case 'k':
                $qty *= 1024;
                break;
            case 'm':
                $qty *= 1048576;
                break;
            case 'g':
                $qty *= 1073741824;
                break;
        }
        return $qty;
    }
}

function printr($t){
	echo "<pre>";
	var_dump($t);
	echo "</pre>";
}

function phpErrorDeprecatedHandler($output){
	global $_JCMS;
	if( !$_JCMS->show_php_errors ) return $output;
	$t = preg_match_all("/Deprecated: (.+) in (.+) on line (.+)/i", $output, $matches);
	$output = str_replace($matches[0], "", $output); 
	foreach($matches[0] as $key=>$val){
		$_JCMS->phpErrorHandler(E_DEPRECATED, $matches[1][$key], $matches[2][$key], $matches[3][$key]);
	}

	$t = preg_match_all("/Strict Standards: (.+) in (.+) on line (.+)/i", $output, $matches);
	$output = str_replace($matches[0], "", $output); 
	foreach($matches[0] as $key=>$val){
		$_JCMS->phpErrorHandler(E_STRICT, $matches[1][$key], $matches[2][$key], $matches[3][$key]);
	}

	return $output;
}

function array_trim($arr){
	if( !is_array($arr) ) return trim(strval($arr));
	foreach($arr as $key=>$val){
		if( is_array($val) ) $arr[$key] = array_trim($val); else $arr[$key] = trim(strval($val));
	}
	return $arr;
}

/* разбивает слова на строки оперделенной длины(без учета длины HTML тегов) */
function mb_htmlwordwrap($string, $length = 30, $wrapper = "&shy;"){ 
	$newstring = '';
	$word_length = 0; 
	$html = 0; 
	$string = $string;
	
	for($i=0;$i<mb_strlen($string, 'utf-8');$i+=1){ 
		$char = mb_substr($string, $i, 1, 'utf-8'); 
		if( $char == '<' ) $html = 1;
		elseif( $char == '>' ){$html = 0;}
		
		$newstring .= $char;
		
		if( !$html && $char != '>' ){
			$word_length++;
			$nchar = mb_substr($string, $i+1, 1, 'utf-8'); 
			if( $word_length >= $length-1 && $nchar != '<' ){
				$word_length = 0;
				if( $nchar != ' ' ) $newstring .= $wrapper;
			}						
		}
	}
	
	return $newstring; 
}

function translit($str) {
	$tr = array(
		"А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
		"Д"=>"d","Е"=>"e","Ё"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
		"Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
		"О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
		"У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
		"Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
		"Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
		"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"e","ж"=>"j",
		"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
		"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
		"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
		"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
		"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
		" "=> "_"
	);
	return mb_strtolower(preg_replace('/[^A-Za-z0-9_-]/', '', strtr($str,$tr)),'utf-8'); // удаляем ненужные символы
}
?>