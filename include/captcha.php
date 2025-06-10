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

define("JENSENCMS2", true);
ob_start();
session_start();
/* Ширина изображения */
$width = 250;
/* Высота изображения */
$height = 80;
/* Длинна кода */
$sign = mt_rand(3,5);


/* Строим список доступных шрифтов из папки со шрифтами */
$fonts = scandir("./captcha_fonts");
unset($fonts[array_search('.',$fonts)], $fonts[array_search('..',$fonts)], $fonts[array_search('.htaccess',$fonts)]);
$fonts = array_values($fonts);

/* Символы, которые будут использованы в защитном коде */
/*$letters = array('а','б','в','г','д','е','ж','и','к','л','м','н','п','р','с','т','у','ф','х','ц','ы', 'ю', 'я',   'А','Б','В','Г','Д','Е','Ж','И','К','Л','М','П','П','Р','С','Т','У','Ф','Х','Ц','Ы', 'Ю', 'Я', '1', '2', '5', '6', '7', '8', '9');

if( !include("captcha_db.php") ){ exit("Извините, произошла внутренняя ошибка Jensen CMS."); }
if( $captcha_db ){
	$word = $captcha_db[rand(0, count($captcha_db))];
	$sign = mb_strlen($word, 'UTF-8');
}
*/

$letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'K', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 2,3,4,5,6,7,8,9);

/* Компоненты, используемые при создании для RGB-цвета */
$digital_data = array(44,55,66,77,88,99,111,122,133,144,155,166,177,188,199);

/* Белый фон изображения */ 
$img = imagecreatetruecolor($width, $height);
$fon = imagecolorallocate($img, 255, 255, 255); 
imagefill($img, 0, 0, $fon);
/* Ширина, отводимая под один символ */
$letter_Width = intval((0.9*$width)/$sign); 

/* Заливка фона случайными точками */
for($j=0; $j<$width; $j++) { 
	for($i=0; $i<($height*$width)/600; $i++) {
		/* Генерируем случайный цвет */
		$color = imagecolorallocatealpha($img,
		$digital_data[rand(0,count($digital_data)-1)],
		$digital_data[rand(0,count($digital_data)-1)],
		$digital_data[rand(0,count($digital_data)-1)],
		rand(10,30));
		/* Выводим случайную точку */
		imagesetpixel($img, rand(0,$width), rand(0,$height), $color);
	}
}

/* Накладываем защитный код */
for($i=0; $i<$sign; $i++) {
	$color = imagecolorallocatealpha($img,	$digital_data[rand(0,count($digital_data)-1)],	$digital_data[rand(0,count($digital_data)-1)],	$digital_data[rand(0,count($digital_data)-1)],rand(10,30));
	if( $word ){
		$letter = mb_substr($word, $i, 1, 'UTF-8');
	} else {
		/* Генерируем случайный символ */
		$letter = $letters[rand(0,sizeof($letters)-1)]; 
	}
	// Координаты вывода символа
	if(empty($x)) { 
		$x = intval($letter_Width*0.2);
	} else {
		if(rand(0,1)){
			$x = $x + $letter_Width + rand(0, intval($letter_Width*0.1));
		} else {
			$x = $x + $letter_Width - rand(0, intval($letter_Width*0.1));
		}
	}
	$y = rand( intval($height*0.7), intval($height*0.8) );	
	$size = rand(intval(0.4*$height), intval(0.5*$height));
	/* Задаем случайный угол поворота символа */
	$angle = rand(0, 50) - 25;
	$img_code .= $letter;
	/* Выбираем случайный шрифт из доступных шрифтов для каждого символа */
	shuffle($fonts);
	$font = $fonts[array_rand($fonts)];
	/* Рисуем сгенерированный символ на изображение */
	imagettftext($img, $size, $angle, $x, $y, $color, "captcha_fonts/".$font, $letter);
}

$_SESSION["JENSENCMS"]['captcha'] = mb_strtolower($img_code, 'UTF-8');
ob_end_clean();
header("Expires: Tue, 11 Jun 1985 05:00:00 GMT");
header("Last-Modified: " . gmdate( "D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: image/jpeg");
imagejpeg($img);

?>