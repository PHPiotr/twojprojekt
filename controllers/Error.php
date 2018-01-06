<?

defined('_PHPIOTR') or die('Restricted access');

class Error extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->view->render('error');
    }

    public function dismissSession() {
        $this->model->dismissSession();
    }

}