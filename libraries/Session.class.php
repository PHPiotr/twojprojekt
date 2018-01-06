<?

class Session {

//    private static $_sessionStarted = false;

    public static function init() {
//        if (self::$_sessionStarted == false) {
        session_start();
//        session_regenerate_id(true);
//            self::$_sessionStarted = true;
//        }
    }

    public static function set($key, $value, array $var = null) {
        if ($key == 'basket') {
            $_SESSION[$key][$value] = $var;
        } else {
            $_SESSION[$key] = $value;
        }
    }

    public static function get($key, $secondKey = false) {

        if ($secondKey == true) {
            if (isset($_SESSION[$key][$secondKey])) {
                return $_SESSION[$key][$secondKey];
            }
        } else {
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            }
        }
        return false;
    }

    public static function destroy($session, $key = false) {
        if(self::get($session)){
            if ($key == true) {
                unset($_SESSION[$session][$key]);
            } else {
                    unset($_SESSION[$session]);
//        session_unset();
//        session_destroy();
//        session_write_close();
                setcookie(session_name(), '', 0, '/');
                session_regenerate_id(true);
            }
        }
    }

    public static function counter($session, $count) {
        if (empty($_SESSION[$session])) {
            $_SESSION[$session] = $count;
        } else {
            $_SESSION[$session] += $count;
        }
    }

    public static function errorMsg($session, $message) {
        if (empty($_SESSION[$session])) {
            $_SESSION[$session] = $message;
        } else {
            self::destroy($session);
            $_SESSION[$session] = $message;
        }
    }

    public static function timeout($session, $destroy, $time) {


        if (!isset($_SESSION[$session])) {
            self::set($session, time() + $time);
        } else {
            if (self::get($session) < time()) {
                self::destroy($destroy);
                self::destroy($session);
            } else {
                self::set($session, time() + $time);
            }
        }
    }

}

