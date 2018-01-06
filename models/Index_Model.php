<?

defined('_PHPIOTR') or die('Restricted access');

class Index_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function jednorodzinne() {
        $sth = $this->db->query("SELECT url_projektu FROM projekty WHERE id_kategorii = 1 ORDER BY id DESC LIMIT 5");
        $sth->execute();
        return $sth->fetchAll();
    }
    
    public function deweloper() {
        $sth = $this->db->query("SELECT url_projektu FROM projekty WHERE id_kategorii = 2 ORDER BY id DESC LIMIT 5");
        $sth->execute();
        return $sth->fetchAll();
    }       
    
    public function gospodarcze() {
        $sth = $this->db->query("SELECT url_projektu FROM projekty WHERE id_kategorii = 3 ORDER BY id DESC LIMIT 5");
        $sth->execute();
        return $sth->fetchAll();
    }       
    
    public function realizacje() {
        $sth = $this->db->query("SELECT url_projektu FROM projekty WHERE id_kategorii = 4 ORDER BY id DESC LIMIT 5");
        $sth->execute();
        return $sth->fetchAll();
    }       

}