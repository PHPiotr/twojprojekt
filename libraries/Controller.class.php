<?

defined('_PHPIOTR') or die('Odwieś się!');

class Controller {
    
    public $name;

    public function __construct() {
        Session::init();
    }
    
    public function load($name){
        if (is_file("models/{$name}.php")) {
            require_once "models/{$name}.php";
            $this->model = new $name();
        }
        $this->name = $name;
        $this->view = new View();
        $this->view->categories = $this->model->categories();
        $this->view->category = $this->model->category(strtolower($this->name));
        $this->view->projektyKategorii = $this->model->projektyKategorii();
        $this->view->opis = $this->model->opis();
    }

}