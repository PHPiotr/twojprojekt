<?

defined('_PHPIOTR') or die('Odwieś się!');

class Zaloguj extends Auth {

    public function __construct() {
        parent::__construct();
        $this->checkSession('logged', true, '/error');
    }

    public function index() {
        $this->view->render('zaloguj');
    }

    public function authorization() {
        if ($_POST) {
            if ($this->model->authorization() == true) {
                if (Session::get('twojprojekt') && Session::get('logged')) {
                    $this->view->sendHeader('/tp/ustawienia');
                } else {
                    $this->view->sendHeader('/error');
                }
            } else {
                $this->view->sendHeader('/zaloguj');
            }
        } else {
            $this->view->sendHeader('/zaloguj');
        }
    }

}