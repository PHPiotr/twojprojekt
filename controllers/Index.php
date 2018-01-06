<?

defined('_PHPIOTR') or die('Restricted access');

class Index extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($args = false) {
        if ($args) {
            $this->view->render('error');
        } else {
            $this->view->jednorodzinne = $this->model->jednorodzinne();
            $this->view->deweloper = $this->model->deweloper();
            $this->view->gospodarcze = $this->model->gospodarcze();
            $this->view->realizacje = $this->model->realizacje();
            $this->view->render('index');
        }
    }

}