<?php
require __DIR__.'/vendor/autoload.php';

use \Exception as Exception;
use \Symfony\Component\DomCrawler\Crawler as Crawler;
use \ZipArchive as ZipArchive; 

$image_exts = array("jpg", "jpeg", "gif", "png", "bmp");
$html_exts = array("html", "htm", "shtml");
$filelist = array();
$image_list = array();
$folider_to_extract = "";
$unique_foldername = "";
$count_changed_files = 0;

function ru2lat($str)
{
    $tr = array(
        "А"=>"a", "Б"=>"b", "В"=>"v", "Г"=>"g", "Д"=>"d",
        "Е"=>"e", "Ё"=>"yo", "Ж"=>"zh", "З"=>"z", "И"=>"i", 
        "Й"=>"j", "К"=>"k", "Л"=>"l", "М"=>"m", "Н"=>"n", 
        "О"=>"o", "П"=>"p", "Р"=>"r", "С"=>"s", "Т"=>"t", 
        "У"=>"u", "Ф"=>"f", "Х"=>"kh", "Ц"=>"ts", "Ч"=>"ch", 
        "Ш"=>"sh", "Щ"=>"sch", "Ъ"=>"", "Ы"=>"y", "Ь"=>"", 
        "Э"=>"e", "Ю"=>"yu", "Я"=>"ya", "а"=>"a", "б"=>"b", 
        "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "ё"=>"yo", 
        "ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"j", "к"=>"k", 
        "л"=>"l", "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p", 
        "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u", "ф"=>"f", 
        "х"=>"kh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"sch", 
        "ъ"=>"", "ы"=>"y", "ь"=>"", "э"=>"e", "ю"=>"yu", 
        "я"=>"ya"
    );
    return strtr($str,$tr);
}

function search_file($folderName, $extensions)
{
    global $filelist;
    // открываем текущую папку 
    $dir = opendir($folderName); 
    // перебираем папку 
    while (($file = readdir($dir)) !== false) { // перебираем пока есть файлы
        if ($file != "." && $file != "..") { // если это не папка
            if (is_file($folderName."/".$file)) { // если файл проверяем имя
                // если имя файла нужное, то вернем путь до него
                $path_info = pathinfo($folderName."/".$file);
                if (in_array($path_info['extension'], $extensions)) {
                    $filelist[] = $path_info;
                }
            } 
            // если папка, то рекурсивно вызываем search_file
            if (is_dir($folderName."/".$file)) {
                search_file($folderName."/".$file, $extensions);
            }
        } 
    }
    // закрываем папку
    closedir($dir);
}

function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
      return false;
    }
   
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
      return false;
    }
   
    $source = str_replace('\\', '/', realpath($source));
   
    if (is_dir($source) === true) {
      $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
   
      foreach ($files as $file) {
          $file = str_replace('\\', '/', $file);
   
          // Ignore "." and ".." folders
          if(in_array(substr($file, strrpos($file, '/')+1), array('.', '..')))
              continue;
   
          $file = realpath($file);
          $file = str_replace('\\', '/', $file);
           
          if (is_dir($file) === true) {
              $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
          }else if (is_file($file) === true) {
              $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
          }
      }
    }else if (is_file($source) === true) {
      $zip->addFromString(basename($source), file_get_contents($source));
    }
    return $zip->close();
}


$uploaddir = getcwd()."/uploads/";
// проверка на существование папки uploads, в Windows ругается
if (!file_exists($uploaddir)) {
    mkdir($uploaddir);
}
$uploadfile = $uploaddir . basename($_FILES['file']['name']);
$uploadfile1 = $uploadfile;
$i = 1;
while (file_exists($uploadfile1)) {
    $uploadfile1 = $uploadfile . '_' . (string)$i;
    $i++;
}
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
    $path_info = pathinfo($uploadfile);
    if ($path_info['extension'] !== 'zip')
        die("Ошибка: выбранный файл не является zip-архивом");
    echo "Файл корректен и был успешно загружен.\n<br />";
    $zip = new ZipArchive;
    $res = $zip->open($uploadfile);
    if ($res === TRUE) {
        do {
            $unique_foldername = md5(microtime() . rand(0, 9999));
            $folder_to_extract = $uploaddir . $unique_foldername;
        } while (file_exists($folder_to_extract));
        for ($i=0; $i<$zip->numFiles; $i++) {
            $zip_filename = $zip->getNameIndex($i);
            $filenames[] = $zip->getNameIndex ($i, 64);
            $zip->extractTo($folder_to_extract, $zip_filename);
            if ($filenames[$i] === mb_convert_encoding(
                mb_convert_encoding($filenames[$i], "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) ; 
            else {
                $filenames[$i] = mb_convert_encoding ($filenames[$i],'UTF-8','CP866');
                rename($folder_to_extract . '/' . $zip_filename, $folder_to_extract . '/' . $filenames[$i]);
            }
        }
        $zip->close();
        echo "Файл успешно разархивирован. Начат поиск русских символах в именах графических файлов\n<br />";
        search_file($folder_to_extract, $image_exts);
        foreach ($filelist as $path_info) {
            if (preg_match('/[А-Яа-яЁё]+/', $path_info['filename'])) {
                $path_info['new_name'] = ru2lat($path_info['filename']);
                $new_name_patch = '';
                $i = 1;
                while (file_exists($path_info['dirname'].'/'.$path_info['new_name'].$new_name_patch.'.'.$path_info['extension'])) {
                    $new_name_patch = '_'.(string)$i;
                    $i++;
                }
                $path_info['new_name'] = $path_info['new_name'].$new_name_patch;
                $image_list[] = $path_info;
                // файл переименовываю
                $new_filename = $path_info['dirname'].'/'.$path_info['new_name'].'.'.$path_info['extension'];
                rename($path_info['dirname'].'/'.$path_info['basename'], $new_filename);
            }
        }
        
        echo "Найдено и переименовано ".count($filelist)." файлов изображений с русскими символами\n<br />";
        $filelist = array();
        search_file($folder_to_extract, $html_exts);
        foreach ($filelist as $html_file) {
            $filename = $html_file['dirname'].'/'.$html_file['basename'];
            $html = file_get_contents($filename);
            try {
                $crawler = new Crawler($html);
                $file_changed = false;
                $imgs = $crawler->filter('img');
                foreach ($imgs as $img) {
                    $src = $img->getAttribute('src');
                    $src_info = pathinfo($src);
                    foreach ($image_list as $imagefile_info) {
                        if ($src_info['filename'] == $imagefile_info['filename']) {
                            $pos = strrpos($src, $imagefile_info['filename']);
                            if ($pos !== false) {
                                $src_changed = substr_replace($src, ru2lat($imagefile_info['basename']), $pos);
                                $img->setAttribute('src', $src_changed);
                                $count_changed_files++;
                                $file_changed = true;
                            }
                        }
                    }
                }
            }
            catch (Exception $ex) {
                //Выводим сообщение об исключении.
                echo $ex->getMessage();
            }	        
            if ($file_changed) {
                $html = $crawler->html();
                file_put_contents($filename, $html);
            }
        }
        echo "Произведено ".$count_changed_files." замен в файлах html(htm, shtml)\n<br />";
        $filepath = $folder_to_extract.'/../'.$unique_foldername.'.zip';
        if (Zip($folder_to_extract, $filepath)) {
            echo '<a href="/uploads/'.$unique_foldername.'.zip" download>Скачать сконвертированный архив</a><br />';
        }
    }
    else {
        echo "Ошибка: файл не удалось разархивировать\n";
    }
} else {
    echo "Возможная атака с помощью файловой загрузки!\n";
}
