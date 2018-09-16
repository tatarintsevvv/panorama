<?php
namespace Panorama;

use \Fenom as Fenom;
use \Exception as Exception;


class Core {
    public $config = array();
    /** @var Fenom $fenom */
    public $fenom;


    /**
     * Конструктор класса
     *
     * @param string $config Имя файла с конфигом
     */
    function __construct($config = 'config') {
        if (is_string($config)) {
            $config = dirname(__FILE__) . "/Config/{$config}.inc.php";
            if (file_exists($config)) {
                require $config;
            }
            else {
                exit('Не могу загрузить файл конфигурации');
            }
        }
        else {
            exit('Неправильное имя файла конфигурации');
        }

    }


    /**
     * Обработка входящего запроса
     *
     * @param $uri
     */
    public function handleRequest($uri) {
        $request = explode('/', $uri);

        $className = '\Panorama\Controllers\\' . ucfirst(array_shift($request));
        /** @var Controller $controller */
        if (!class_exists($className)) {
            $controller = new Controllers\Home($this);
        }
        else {
            $controller = new $className($this);
        }

        $controller->isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        $initialize = $controller->initialize($request);
        if ($initialize === true) {
            $response = $controller->run();
        }
        elseif (is_string($initialize)) {
            $response = $initialize;
        }
        else {
            $response = 'Возникла ошибка при загрузке страницы';
        }

        if ($controller->isAjax) {
            $this->ajaxResponse(false, 'Не могу обработать ajax запрос');
        }
        else {
            echo $response;
        }
    }


    /**
     * Получение экземпляра класса Fenom
     *
     * @return bool|Fenom
     */
    public function getFenom() {
        if (!$this->fenom) {
            try {
                if (!file_exists(PROJECT_CACHE_PATH)) {
                    mkdir(PROJECT_CACHE_PATH);
                }
                $this->fenom = Fenom::factory(PROJECT_TEMPLATES_PATH, PROJECT_CACHE_PATH, PROJECT_FENOM_OPTIONS);
            }
            catch (Exception $e) {
                $this->log($e->getMessage());
                return false;
            }
        }

        return $this->fenom;
    }


    /**
     * Получение парсера текстов
     *
     * @return Parsedown
     */
    public function getParser() {
        if (!$this->parser) {
            $this->parser = new Parsedown();
        }

        return $this->parser;
    }


    /**
     * Метод удаления директории с кэшем
     *
     */
    public function clearCache() {
        Core::rmDir(PROJECT_CACHE_PATH);
        mkdir(PROJECT_CACHE_PATH);
    }


    /**
     * Логирование. Пока просто выводит ошибку на экран.
     *
     * @param $message
     * @param $level
     */
    public function log($message, $level = E_USER_ERROR) {
        if (!is_scalar($message)) {
            $message = print_r($message, true);
        }
        trigger_error($message, $level);
    }


    /**
     * Удаление ненужных файлов в пакетах, установленных через Composer
     *
     * @param mixed $base
     */
    public static function cleanPackages($base = '') {
        if (!is_string($base)) {
            $base = dirname(dirname(__FILE__)) . '/vendor/';
        }
        if ($dirs = @scandir($base)) {
            foreach ($dirs as $dir) {
                if (in_array($dir, array('.', '..'))) {
                    continue;
                }
                $path = $base . $dir;
                if (is_dir($path)) {
                    if (in_array($dir, array('tests', 'test', 'docs', 'gui', 'sandbox', 'examples', '.git'))) {
                        Core::rmDir($path);
                    }
                    else {
                        Core::cleanPackages($path . '/');
                    }
                }
                elseif (pathinfo($path, PATHINFO_EXTENSION) != 'php') {
                    unlink($path);
                }
            }
        }
    }


    /**
     * Рекурсивное удаление директорий
     *
     * @param $dir
     */
    public static function rmDir($dir) {
        $dir = rtrim($dir, '/');
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir . '/' . $object)) {
                        Core::rmDir($dir . '/' . $object);
                    }
                    else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }


    /**
     * Вывод ответа в установленном формате для всех Ajax запросов
     *
     * @param bool|true $success
     * @param string $message
     * @param array $data
     */
    public function ajaxResponse($success = true, $message = '', array $data = array()) {
        $response = array(
            'success' => $success,
            'message' => $message,
            'data' => $data,
        );

        exit(json_encode($response));
    }

}