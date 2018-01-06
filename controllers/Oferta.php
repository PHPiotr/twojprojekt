<?php

defined('_PHPIOTR') or die('Restricted access');

class Oferta extends Controller {

    function __construct() {
        parent::__construct();
    }

    function index($args = false) {
        
        $this->model->prepareUrl($args);

        $this->view->category = $this->model->category();

        $this->view->projectUrl = $this->model->projectUrl;
        $this->view->projectId = $this->model->projectId();

        $this->view->dane = $this->model->dane();
        $this->view->wartosciDanych = $this->model->wartosciDanych();

        $this->view->materialowe = $this->model->materialowe();
        $this->view->wartosciMaterialowych = $this->model->wartosciMaterialowych();

        $this->view->pomieszczenia = $this->model->pomieszczenia();
        $this->view->aranzacje = $this->model->aranzacje();
        $this->view->powierzchnie = $this->model->powierzchnie();
        $this->view->elewacje = $this->model->elewacje();

        $this->view->projects = $this->model->projects();
        $this->view->typyProjektow = $this->model->typyProjektow();
        
        $this->view->project = $this->model->project();       

        $this->view->rows = $this->model->rows;
        $this->view->projectsInOneRow = $this->model->projectsArrayPerRow;
        $this->view->pagination = $this->model->pagination($page = false);
        $this->view->currentPage = $this->model->currentPage;
        $this->view->pages = $this->model->pages;
        $this->view->total = $this->model->total;

        $this->view->scan = $this->model->scan();
        $this->view->amountOfScannedFiles = $this->model->amountOfScannedFiles;
        $this->view->span = $this->model->span;
        $this->view->spanner = $this->model->spanner;
        
        $this->view->zdjeciaRealizacji = $this->model->zdjeciaRealizacji();

        $this->view->render('oferta');
    }

    function sortowanie() {
        $this->model->sortowanie();
    }
    
    function znaleziono(){
        $this->model->znaleziono();
//        $this->view->render('znaleziono');
    }

}