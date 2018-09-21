<?php
require __DIR__.'/vendor/autoload.php';

use \Exception as Exception;
use \Symfony\Component\DomCrawler\Crawler as Crawler;
use \ZipArchive as ZipArchive; 


$image_exts = array("jpg", "jpeg", "gif", "png", "bmp");
$html_exts = array("html", "htm", "shtml");
$filelist = array();
$image_list = array();
$folder_to_extract = "";
$unique_foldername = "";
$count_changed_files = 0;



$uploaddir = getcwd()."/uploads/";
// проверка на существование папки uploads, в Windows нужно создавать до создания файла в несуществующем каталоге
if (!file_exists($uploaddir)) {
    mkdir($uploaddir);
}

$uploadfile = $uploaddir . basename($_FILES['file']['name']);
$uploadfile = generate_non_existing_filename($uploadfile);

if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
    $path_info = pathinfo($uploadfile);
    if ($path_info['extension'] !== 'zip')
        die("Ошибка: выбранный файл не является zip-архивом");
    echo "Файл корректен и был успешно загружен.\n<br />";

    $folder_to_extract = zip_extract_with_conversion($uploadfile, $uploaddir, "CP866");
    if ($folder_to_extract) {
        echo "Файл успешно разархивирован. Начат поиск русских символах в именах графических файлов\n<br />";

       
        $image_list = convert_rus_to_translit_names($folder_to_extract, $image_exts);
        echo "Найдено и переименовано ".count($image_list)." файлов изображений с русскими символами\n<br />";

        replace_converted_files_for_links(
            $folder_to_extract, 
            $html_exts, 
            $image_list,
            $count_changed_files, 
            'img', 
            'src'
        );
        echo "Произведено ".$count_changed_files." замен в файлах html(htm, shtml)\n<br />";
        $filepath = $folder_to_extract . '/../' . $unique_foldername . '.zip';
        if (Zip($folder_to_extract, $filepath)) {
            echo '<a href="/uploads/' . $unique_foldername . '.zip" download>Скачать сконвертированный архив</a><br />';
        }
    }
    else {
        echo "Ошибка: файл не удалось разархивировать\n";
    }
} else {
    echo "Возможная атака с помощью файловой загрузки!\n";
}

// функция переводит русские символы строки в транслит и возвращает результат
function ru2lat($str) {
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

// функция рекурсивно ищет в папке $folderName файлы с раширениями, указанными в массиве $extensions
// и возвращает результат
function search_file($folderName, $extensions, &$filelist) {
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
                search_file($folderName."/".$file, $extensions, $filelist);
            }
        } 
    }
    // закрываем папку
    closedir($dir);
}

// функция создает архив $destination из папки $source
function Zip($source, $destination) {
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
          if (in_array(substr($file, strrpos($file, '/')+1), array('.', '..')))
              continue;
   
          $file = realpath($file);
          $file = str_replace('\\', '/', $file);
           
          if (is_dir($file) === true) {
              $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
          } else if (is_file($file) === true) {
              $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
          }
      }
    } elseif (is_file($source) === true) {
      $zip->addFromString(basename($source), file_get_contents($source));
    }
    return $zip->close();
}

// функция генерирует уникальное название 
function createUniqueFolder($dir) {
    $folder_to_extract = $dir;
    do {
        $unique_foldername = md5(microtime() . rand(0, 9999));
        $folder_to_extract = $dir . $unique_foldername;
    } while (file_exists($folder_to_extract));
    return $folder_to_extract;
}

// функция распаковывает архив $zip_archive в папку $folder_to, создавая в ней папку с уникальным 
// названием и конвертирует 
function zip_extract_with_conversion($zip_archive, $folder_to, $codepage = "CP866") {
    $folder_to_extract = "";
    $zip = new ZipArchive;
    $res = $zip->open($zip_archive);
    if ($res === TRUE) {
        $folder_to_extract = createUniqueFolder($folder_to);
        for ($i=0; $i<$zip->numFiles; $i++) {
            $zip_filename = $zip->getNameIndex($i);
            $real_filename = $zip->getNameIndex ($i, 64);
            $zip->extractTo($folder_to_extract, $zip_filename);
            if ($real_filename === mb_convert_encoding(mb_convert_encoding($real_filename, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) ; 
            else {
                $real_filename = mb_convert_encoding ($real_filename,'UTF-8',$codepage);
                rename($folder_to_extract . '/' . $zip_filename, $folder_to_extract . '/' . $real_filename);
            }
        }
        $zip->close();
    }
    return $folder_to_extract;
}

// функция просматривает в каталоге $folder файлы с расширениями из массива $extensions и проводит транслит
// для тех из них, в которых есть русские символы,
// возвращает массив названий файлов (basename, dirname, extension, filename, new_name)
function convert_rus_to_translit_names($folder, $extensions) {
    $found_files = array();
    $image_list = array();
    $filelist = array();
    search_file($folder, $extensions, $filelist);
    foreach ($filelist as $path_info) {
        if (preg_match('/[А-Яа-яЁё]+/', $path_info['filename'])) {
            $path_info['new_name'] = ru2lat($path_info['filename']);
            $new_name_patch = '';
            $i = 1;
            while(file_exists($path_info['dirname'] . '/' . $path_info['new_name'] . $new_name_patch . '.' . $path_info['extension'])) {
                $new_name_patch = '_' . (string)$i;
                $i++;
            }
            $path_info['new_name'] = $path_info['new_name'] . $new_name_patch;
            $image_list[] = $path_info;
            // файл переименовываю
            $new_filename = $path_info['dirname'] . '/' . $path_info['new_name'] . '.' . $path_info['extension'];
            rename($path_info['dirname'] . '/' . $path_info['basename'], $new_filename);
        }
    }
    return $image_list;
} 

function replace_converted_files_for_links(
    $folder,
    $extensions, 
    $converted_files,
    &$count_changed_files, 
    $dom_element = "img",
    $dom_attribute = "src"
) {
    $filelist = array();
    search_file($folder, $extensions, $filelist);
    foreach ($filelist as $html_file) {
        $filename = $html_file['dirname'] . '/' . $html_file['basename'];
        $html = file_get_contents($filename);
        try {
            $crawler = new Crawler($html);
            $file_changed = false;
            $imgs = $crawler->filter($dom_element);
            foreach ($imgs as $img) {
                $src = $img->getAttribute($dom_attribute);
                $src_info = pathinfo($src);
                foreach ($converted_files as $item) {
                    if ($src_info['filename'] == $item['filename']) {
                        $pos = strrpos($src, $item['filename']);
                        if ($pos !== false) {
                            $src_changed = substr_replace($src, ru2lat($item['basename']), $pos);
                            $img->setAttribute($dom_attribute, $src_changed);
                            $count_changed_files++;
                            $file_changed = true;
                        }
                    }
                }
            }
        }
        catch (Exception $ex) {
            echo $ex->getMessage();
        }	        
        if ($file_changed) {
            $html = $crawler->html();
            file_put_contents($filename, $html);
        }
    }
}

// функция создает создает несуществующее имя на основе 
function generate_non_existing_filename($filename) {
    $path_info = pathinfo($filename);
    $new_filename = $filename;
    $i = 1;
    while (file_exists($new_filename)) {
        $new_filename = $path_info['dirname'] . '/' . $path_info['filename'] . '_' . (string)$i . '.' . $path_info['extension'];
        $i++;
    }
    return $new_filename;
}

