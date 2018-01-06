<?

defined('_PHPIOTR') or die('Restricted access');

class Zaloguj_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    function authorization() {
        $error = array();
        if (empty($_POST['twojlog']) || empty($_POST['twojpas'])) {
            $error[] = '<i class="icon-warning-sign"></i> Żadne z pól nie może pozostać puste.';
            return false;
        } else {
            try {
                $login = $_POST['twojlog'];
                $password = sha1('tkeprojjotw' . md5(sha1(md5($_POST['twojpas']))));
                $sql = "SELECT login, active FROM user WHERE login = :twojlog AND password = :twojpas";
                $sth = $this->db->prepare($sql);
                $sth->bindParam(':twojlog', $login, PDO::PARAM_STR);
                $sth->bindParam(':twojpas', $password, PDO::PARAM_STR);
                $sth->execute();
                $result = $sth->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $error[] = '<i class="icon-warning-sign"></i> ' . $e->getMessage();
                return false;
            }
            if ($result['active'] == 2) {
//                Session::set('success', 'Witaj, ' . ucfirst($_POST['twojlog']) . '!');
                Session::set('twojprojekt', true);
                Session::set('logged', true);
                Session::destroy('error');
                Session::destroy('counter');
                try {
                    $this->db->prepare("UPDATE user SET failed = 0")->execute();
                } catch (Exception $e) {
                    $error[] = '<i class="icon-warning-sign"></i> ' . $e->getMessage();
                }
                return true;
            } else if ($result['active'] == 1) {
                Session::set('success', 'Witaj, ' . ucfirst($_POST['twojlog']) . '!');
                Session::set('logged', true);
                Session::destroy('error');
                Session::destroy('counter');
                return true;
            } else if (!empty($result) && $result['active'] == 0) {
                $error[] = '<i class="icon-warning-sign"></i> Twoje konto jest nieaktywne.';
                return false;
            } else {
                Session::counter('counter', 1);
                try {
                    $this->db->prepare("UPDATE user SET failed = failed + 1")->execute();
                } catch (Exception $e) {
                    $error[] = '<i class="icon-warning-sign"></i> ' . $e->getMessage();
                }
                try {
                    $result = $this->db->query("SELECT failed FROM user")->fetch();
                } catch (Exception $e) {
                    $error[] = '<i class="icon-warning-sign"></i> ' . $e->getMessage();
                }
                if (Session::get('counter') > 9 || (int) $result['failed'] > 9) {
                    try {
                        $this->db->prepare("UPDATE user SET active = 0")->execute();
                    } catch (Exception $e) {
                        $error[] = '<i class="icon-warning-sign"></i> ' . $e->getMessage();
                    }
                    $error[] = '<i class="icon-warning-sign"></i> Konto zostało zdezaktywowane';
                } else {
                    $error[] = '<i class="icon-warning-sign"></i> Niewłaściwa kombinacja loginu z hasłem <strong>(' . Session::get('counter') . ' próba)</strong>. Przy dziesiątej nieudanej próbie konto zostanie zdezaktywowane.';
                }
                Session::set('error', implode('<br />', $error));
                return false;
            }
        }
    }

}