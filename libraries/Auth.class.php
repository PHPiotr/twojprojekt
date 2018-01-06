<?

defined('_PHPIOTR') or die('Odwieś się!');

class Auth extends Controller {

    public function checkSession($sess, $bool, $url) {
        if (Session::get($sess) == $bool) {
            header("Location: $url");
        }
    }

}