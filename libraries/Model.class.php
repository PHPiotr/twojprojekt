<?

defined('_PHPIOTR') or die('Odwieś się!');

class Model extends Database {

    public function __construct() {
        try {
            $this->db = new Database(HOST, DB, PORT, USERNAME, PASSWORD);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function categories() {
        $sth = $this->db->query("SELECT kategorie.* FROM kategorie 
        ORDER BY kategorie.sort");
        return $sth->fetchAll();
    }

    public function category($url) {
        return substr($url, 0, strpos($url, '_'));
        $url = htmlspecialchars($url);
        $sth = $this->db->prepare("SELECT kategorie.*, count(projekty.id) FROM kategorie 
        LEFT JOIN projekty ON projekty.id_kategorii = kategorie.id
        WHERE url_kategorii = :url_kategorii");
        $sth->bindParam(':url_kategorii', $url, PDO::PARAM_STR);
        $sth->execute();
        $result = $sth->fetch();
        return $result;
    }
    
    public function projektyKategorii(){
        $result = $this->db->query("SELECT kategorie.url_kategorii FROM projekty 
        LEFT JOIN kategorie ON kategorie.id = projekty.id_kategorii    
        GROUP BY kategorie.id")->fetchAll();
        $array = array();
        foreach($result as $notEmptyCategory){
            $array[] = $notEmptyCategory['url_kategorii'];
        }
        return $array;
    }

    public function opis() {
        return $this->db->query("SELECT * FROM opisy")->fetch();
    }

}