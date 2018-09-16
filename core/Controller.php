<?php

namespace Panorama;

use \Exception as Exception;


class Controller {
    /** @var Core $core */
    public $core;
    /** @var string $name */
    public $name = 'home';
    /** @var bool $isAjax */
    public $isAjax = false;


    /**
     * Конструктор класса, требует передачи Core
     *
     * @param Core $core
     */
    function __construct(Core $core) {
        $this->core = $core;
    }


    /**
     * @param array $params
     *
     * @return bool
     */
    public function initialize(array $params = array()) {

        return true;
    }


    /**
     * Основной рабочий метод
     *
     * @return string
     */
    public function run() {
        return "Система контроля задач";
    }


    /**
     * Шаблонизация
     *
     * @param string $tpl Имя шаблона
     * @param array $data Массив данных для подстановки
     * @param Controller|null $controller Контроллер для передачи в шаблон
     *
     * @return mixed|string
     */
    public function template($tpl, array $data = array(), $controller = null) {
        $output = '';
        if (!preg_match('#\.tpl$#', $tpl)) {
            $tpl .= '.tpl';
        }
        if ($fenom = $this->core->getFenom()) {
            try {
                $data['_core'] = $this->core;
                $data['_controller'] = !empty($controller) && $controller instanceof Controller
                    ? $controller
                    : $this;
                $output = $fenom->fetch($tpl, $data);
            }
            catch (Exception $e) {
                $this->core->log($e->getMessage());
            }
        }

        return $output;
    }


    /**
     * Возвращает пункты меню сайта
     *
     * @return array
     */
    public function getMenu() {
        return array(
            'home' => array(
                'title' => 'Главная',
                'link' => '/',
            ),
            'news' => array(
                'title' => 'Задачи',
                'link' => '/?q=task',
            ),
            'test' => array(
                'title' => 'База данных',
                'link' => '/test/',
            )
        );
    }


    /**
     * Возвращает массив с постраничной навигацией
     *
     * @param $totalItems
     * @param int $currentPage
     * @param int $itemsPerPage
     * @param int $neighbours
     *
     * @return array
     */
    public function getPagination($totalItems, $currentPage = 1, $itemsPerPage = 10, $neighbours = 2) {
        $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);

        return $pagination->build();
    }


    /**
     * Редирект на указанный адрес
     *
     * @param string $url
     */
    public function redirect($url = '/') {
        if ($this->isAjax) {
            $this->core->ajaxResponse(false, 'Редирект на другой адрес', array('redirect' => $url));
        }
        else {
            header("Location: {$url}");
            exit();
        }
    }

}