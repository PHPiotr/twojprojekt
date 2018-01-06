<?

defined('_PHPIOTR') or die('Restricted access.');

class Bootstrap {

    /**
     * @var type array
     */
    private $_url = array();

    /**
     * @return type array 
     */
    private function _getUrl() {
        $url = isset($_GET['get']) ? $_GET['get'] : null;
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        return $url;
    }

    /**
     * http authorization 
     */
    public function httpAuth() {

        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || ($_SERVER['PHP_AUTH_USER'] !== 'twoj' && $_SERVER['PHP_AUTH_PW'] !== 'projekt')) {
            header('WWW-Authenticate: Basic realm="dev"');
            header('HTTP/1.0 401 Unauthorized');
            echo '<!DOCTYPE html><html><head><title>401 Authorization Required</title></head><body><h1>Authorization Required</h1><p>This server could not verify that you are authorized to access the document requested.  Either you supplied the wrong credentials (e.g., bad password), or your browser doesn\'t understand how to supply the credentials required.</p><p>Additionally, a 401 Authorization Required error was encountered while trying to use an Error Document to handle the request.</p></body></html>';
            exit;
        }
    }

    /**
     * Run the app. 
     */
    public function run() {
        $this->_url = $this->_getUrl();
        $name = (empty($this->_url[0])) ? 'Index' : (is_file(CONTROLLER . ucfirst($this->_url[0]) . '.php') ? ucfirst($this->_url[0]) : 'Error');
        require_once "controllers/{$name}.php";
        $controller = new $name();
//        $controller->loadModel("{$name}_Model");
//        $controller->loadView();
        $controller->load("{$name}_Model");
        $method = (!isset($this->_url[1])) ? 'index' : $this->_url[1];
        $args_array = array_slice($this->_url, 1);
        $args = implode(',', $args_array);
        $args = ($args == null) ? null : $args;
        method_exists($controller, $method) ? $controller->$method($args) : $controller->index($args);
    }

}