<?php
$path='./';
$path2='../../Gdt53hjsyw76gFs/';
   // Проверяем загружен ли файл
   if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
   {
     // Если файл загружен успешно, перемещаем его
     // из временной директории в конечную
     move_uploaded_file($_FILES["filename"]["tmp_name"], $path."1.txt");
  define('NL',chr(13).chr(10));	 
  $text1 = file_get_contents ($path."1.txt");
  $text2 = file_get_contents ($path."2.txt");
  $text3 = file_get_contents ($path."3.txt");
  $text4 = file_get_contents ($path."4.txt");
  $file = fopen ($path2."proxy.txt","w+");
  $str = $text1."\n".$text2;
  $str = $str."\n".$text3;
  $str = $str."\n".$text4;
  $arr=explode(NL,$str);
  shuffle($arr);
  if ( !$file )
  {
    echo("Ошибка открытия файла");
  }
  else
  {
    fputs ( $file, implode(NL,$arr));
  }
  fclose ($file);
     } else {
      echo("0");
   }
?>