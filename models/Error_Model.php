<?

defined('_PHPIOTR') or die('Restricted access');

class Error_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function dismissSession() {
        Session::destroy($_POST['sess']);
    }

}