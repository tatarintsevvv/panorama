<?php

namespace Panorama\Controllers;

use Panorama\Controller as Controller;

class Home extends Controller {

    /**
     * @param array $params
     *
     * @return bool
     */
    public function initialize(array $params = array()) {
        if (!empty($_REQUEST['q'])) {
            $this->redirect('/');
        }
        return true;
    }


    /**
     * @return string
     */
    public function run() {
        return $this->template('home', array(
            'title' => 'Главная страница',
            'pagetitle' => 'Конвертация zip-архивов',
            'content' => 'Конвертация zip-архивов это тестовое задание для ИД Панорама, выполненное Татаринцевым Виталием.',
        ), $this);
    }

}

?>