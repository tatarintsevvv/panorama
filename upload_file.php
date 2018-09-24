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

$uploaddir = getcwd()."/uploads";
// проверка на существование папки uploads, в Windows ругается
if (!file_exists($uploaddir)) {
    mkdir($uploaddir);
}
$uploadfile = $uploaddir . '/' . basename($_FILES['file']['name']);
$uploadfile = generateFilenameIfExist($uploadfile);

if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
    $path_info = pathinfo($uploadfile);
    if ($path_info['extension'] !== 'zip') {
        die("Ошибка: выбранный файл не является zip-архивом");
    }
    echo "Файл корректен и был успешно загружен.\n<br />";
    $zip = new ZipArchive;
    $res = $zip->open($uploadfile);
    if ($res === TRUE) {
        $unique_foldername = generateUniqueFoldername($uploaddir);
        $folder_to_extract = $uploaddir . '/' . $unique_foldername;
        for ($i=0; $i < $zip->numFiles; $i++) {
            $zip_filename = $zip->getNameIndex($i);
            $filenames[$i] = $zip->getNameIndex ($i, 64);
            $zip->extractTo($folder_to_extract, $zip_filename);
            if ($filenames[$i] === mb_convert_encoding(
                mb_convert_encoding($filenames[$i], "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) ; 
            else {
                $filenames[$i] = mb_convert_encoding ($filenames[$i],'UTF-8','CP866');
                checkAndRenameCyrillicFilenames(
                    $folder_to_extract, 
                    $image_exts,
                    $zip_filename,
                    $filenames[$i]
                );
            }
        }
        $zip->close();
        echo "Файл успешно разархивирован.<br />";
        echo "Найдено и переименовано ".count($image_list)." файлов изображений с русскими символами\n<br />";

        $filelist = array();
        searchFile($folder_to_extract, $html_exts, $filelist);

        foreach ($filelist as $html_file) {
            findAndReplaceHtmlAttributes(
                $html_file['dirname'].'/'.$html_file['basename'],
                'img',
                'src',
                $image_list,
                $count_changed_files
            );
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

// функция генерирует случайное название каталога и проверяет на то, что он не должен существовать.
// возвращает результат
function generateUniqueFoldername($dir) 
{
    do {
        $unique_foldername = md5(microtime() . rand(0, 9999));
    } while (file_exists($dir . '/' . $unique_foldername));
    return $unique_foldername;
}

// функция конвертирует русские символы в транслит и возвращает итоговую строку.
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

// функция ищет в папке $folderName файлы с расширениями из массива $extensions, найденные файлы 
// записываются в массив $filelist
function searchFile($folderName, $extensions, &$filelist)
{
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
            // если папка, то рекурсивно вызываем searchFile
            if (is_dir($folderName."/".$file)) {
                searchFile($folderName."/".$file, $extensions, $filelist);
            }
        } 
    }
    // закрываем папку
    closedir($dir);
    return $filelist;
}


// функция создает zip-архив с названием $destination из каталога $source, включая вложенные папки 
// и их содержимое
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
    } else if (is_file($source) === true) {
      $zip->addFromString(basename($source), file_get_contents($source));
    }
    return $zip->close();
}

// функция проверяет имя файла $filename в папке $folder_to_extract на наличие русских символов, заменяет
// их на транслит и переименовывает файл $old_filename из этой папки на новое название. Проверка и 
// замена осуществляется для файлов, расширения которых перечислены в $extensions. Переименованные 
// файлы добавляются в массив $image_list
function checkAndRenameCyrillicFilenames(
    $folder_to_extract, 
    $extensions, 
    $old_filename, 
    $new_filename
) {
    global $image_list;

    $old_filename = $folder_to_extract . '/' . $old_filename;
    $real_filename = $folder_to_extract . '/' . $new_filename;
    $real_path_info = pathinfo($real_filename);
    if (in_array($real_path_info['extension'], $extensions) && preg_match('/[А-Яа-яЁё]+/', $real_path_info['filename'])) {
        $real_path_info['new_name'] = ru2lat($real_path_info['filename']);
        $new_name_patch = '';
        $i1 = 1;
        while (file_exists($real_path_info['dirname'].'/'.$real_path_info['new_name'].$new_name_patch.'.'.$real_path_info['extension'])) {
            $new_name_patch = '_'.(string)$i1;
            $i1++;
        }
        $real_path_info['new_name'] = $real_path_info['new_name'].$new_name_patch;
        $image_list[] = $real_path_info;
        // файл переименовываю
        $filename = $real_path_info['dirname'].'/'.$real_path_info['new_name'].'.'.$real_path_info['extension'];
        if (file_exists($old_filename)) {
            rename($old_filename, $filename);
        }
    }
}


// функция ищет и заменяет значения аттрибутов $attr тэгов $tags в html-файле $filename, если значение 
// аттрибута содержит имя файла из массива $files. Замена осуществляется на транслит имени файла, при котором
// русские символы заменяются латинскими
function findAndReplaceHtmlAttributes(
    $filename,
    $tags,
    $attr,
    $files,
    &$count_changed_files
) {
    $html = file_get_contents($filename);
    try {
        $crawler = new Crawler($html);
        $file_changed = false;
        $imgs = $crawler->filter($tags);
        foreach ($imgs as $img) {
            $src = $img->getAttribute($attr);
            $src_info = pathinfo($src);
            foreach ($files as $file_info) {
                if ($src_info['filename'] == $file_info['filename']) {
                    $pos = strrpos($src, $file_info['filename']);
                    if ($pos !== false) {

                        $src_changed = substr_replace($src, $file_info['new_name'] . '.' . $file_info['extension'], $pos);
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

// функция осуществляет проверку на существовании файла и добавляет к его имени подчеркивание и порядковый 
// номер с инкрементом до тех пор, пока не будет найдено несуществующее название файла. 
// Возвращается результат.
function generateFilenameIfExist($filename)
{
    $new_filename = $filename;
    $path_info = pathinfo($filename);
    $i = 1;
    while (file_exists($new_filename)) {
        $new_filename = $path_info['dirname']. '/' . $path_info['filename']. '_' . (string)$i . '.' . $path_info['extension'];
        $i++;
    }
    return $new_filename;
}

?>
