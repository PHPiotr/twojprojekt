<?php

defined('_PHPIOTR') or die('Restricted access');

class Tp extends Auth {

    function __construct() {
        parent::__construct();
        $this->checkSession('twojprojekt', false, '/zaloguj');
    }

    function index($args = false) {

        $this->model->prepareUrl($args);

        $this->view->category = $this->model->category();
       
        $this->view->projectUrl = $this->model->projectUrl;
        $this->view->projectId = $this->model->projectId();

        $this->view->dane = $this->model->dane();
        $this->view->wartosciDanych = $this->model->wartosciDanych();

        $this->view->projects = $this->model->projects();
        $this->view->project = $this->model->project();

        $this->view->materialowe = $this->model->materialowe();
        $this->view->wartosciMaterialowych = $this->model->wartosciMaterialowych();

        $this->view->pomieszczenia = $this->model->pomieszczenia();
        $this->view->aranzacje = $this->model->aranzacje();
        $this->view->powierzchnie = $this->model->powierzchnie();
        $this->view->elewacje = $this->model->elewacje();

        $this->view->projects = $this->model->projects();
        $this->view->project = $this->model->project();

        $this->view->typyProjektow = $this->model->typyProjektow();

        $this->view->zdjeciaRealizacji = $this->model->zdjeciaRealizacji();

        $this->view->pagination = $this->model->pagination();
        $this->view->currentPage = $this->model->currentPage;
        $this->view->pages = $this->model->pages;
        $this->view->total = $this->model->total;

        $this->view->render('tp');
    }

    function ustawienia() {
        $this->view->opis = $this->model->opis();
        $this->view->render('tp', 'ustawienia');
    }

    function zakresDzialalnosci() {
        $this->model->zakresDzialalnosci();
    }

    function duplicate() {
        $this->model->duplicate();
    }

    function editing() {
        $this->model->editing();
    }

    function pomieszczenie() {
        $this->model->pomieszczenie();
    }

    function iloscPomieszczen() {
        $this->model->iloscPomieszczen();
    }

    function iloscProjektowBackend() {
        $this->model->iloscProjektowBackend();
    }

    function iloscProjektowFrontend() {
        $this->model->iloscProjektowFrontend();
    }

    function aranzacja() {
        $this->model->aranzacja();
    }

    function sortowanie() {
        $this->model->sortowanie();
    }

    function podstawowa() {
        $this->model->podstawowa();
    }

    function materialowa() {
        $this->model->materialowa();
    }

    function logout() {
        $this->model->logout();
    }

    function add() {
        $this->model->add();
    }

    function edit() {
        $this->model->edit();
    }

    function delete() {
        $this->model->delete();
    }

}