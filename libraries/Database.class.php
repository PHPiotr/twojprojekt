<?

defined('_PHPIOTR') or die('Restricted area');

class Database extends PDO {

    public static function exception_handler($e) {
        die($e->getMessage());
    }

    public function __construct($host, $dbname, $port, $username, $password) {
        set_exception_handler(array(__CLASS__, 'exception_handler'));
        parent::__construct("mysql:host=$host;dbname=$dbname;port=$port", $username, $password, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
        restore_exception_handler();
    }

}