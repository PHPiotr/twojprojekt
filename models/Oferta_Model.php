<?

defined('_PHPIOTR') or die('Restricted access');

class Oferta_Model extends Model {

    public $categoryUrl;
    public $categoryId;
    public $currentPage = 1;
    public $projectUrl;
    public $projectId;
    public $per_page = 9;
    public $total = 1;
    public $pages = 1;
    public $projects;
    public $projectsCountRows;
    public $projectsArrayPerRow;
    public $span;
    public $rows = 1;
    public $amountOfScannedFiles;

    /**
     * Array for elevations - there are always 4 of them
     */
    public $elewacje = array('front', 'bok1', 'tyl', 'bok2');

    /**
     * All fields in form without areas in arrangements
     */
    public $posted = array('id_kategorii', 'nazwa_projektu', 'url_projektu', 'opis_projektu', 'link_pod_zdjeciem', 'opis_linku_pod_zdjeciem');

    /**
     * Areas records array helper for inserting / updating arrangements
     */
    public $powierzchnie = array('id_pomieszczenia', 'id_aranzacji', 'id_projektu', 'nr_pomieszczenia', 'pow_pomieszczenia');

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all records from kategorie table as an array - where url_kategorii equals...
     * @return array Array of recoords from table 'kategorie'.
     */
    public function category() {
        if ($this->categoryUrl !== 'ustawienia') {
            $sth = $this->db->prepare("SELECT * FROM kategorie WHERE url_kategorii = :url_kategorii");
            $sth->bindParam(':url_kategorii', $this->categoryUrl, PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetch();
            $this->per_page = $result['ilosc_projektow_frontend'];
            $this->per_page = $this->per_page <= 0 ? 1 : $this->per_page;
            $this->spanner = $this->per_page < 4 ? 12 / $this->per_page : 4;
            return $result;
        } else {
            return;
        }
    }

    public function pagination() {
        return Pagination::create($this->pages, $this->currentPage, "/oferta/$this->categoryUrl");
    }

    public function projectsRows() {
        return ceil(count($this->projects) / 3);
    }

    public function scan() {
        if (is_writable("media/img/{$this->categoryUrl}/{$this->projectUrl}/rzut")) {
            $scan = array_slice(scandir("media/img/{$this->categoryUrl}/{$this->projectUrl}/rzut"), 2);

            if (($key = array_search("Thumbs.db", $scan)) !== false) {
                unset($scan[$key]);
            }
            $this->amountOfScannedFiles = count($scan) == 0 ? 1 : count($scan);
            $this->span = (int) floor(12 / $this->amountOfScannedFiles);
            return $scan;
        } else {
            return;
        }
    }

    public function dane() {
//        if($this->projectId){
//            $sql = "SELECT podstawowe_dane.*,wartosci_podst_danych.wartosc_danej FROM wartosci_podst_danych RIGHT JOIN podst_dane ON podstawowe_dane.id = wartosci_podst_danych.id_projektu WHERE wartosci_podst_danych.id_projektu = {$this->projectId} ORDER BY podstawowe_dane.sort";
//            $sth = $this->db->query($sql);
//            return $sth->fetchAll();
//            
//        }else{
        return $this->db->query("SELECT podstawowe_dane.* FROM podstawowe_dane ORDER BY sort")->fetchAll();
//        }
    }

    /**
     * Show values in 'Podstawowe dane' area.
     * @return array    Array of records from table 'wartosci_podst_danych' of edited project
     */
    public function wartosciDanych() {
        if ($this->projectId !== null) {
            return $this->db->query("SELECT wartosci_podst_danych.*, podstawowe_dane.nazwa_danej 
                    FROM wartosci_podst_danych 
                    LEFT JOIN podstawowe_dane ON podstawowe_dane.id = wartosci_podst_danych.id_danej
                    WHERE id_projektu = {$this->projectId} 
                    ORDER BY podstawowe_dane.sort")->fetchAll();
        } else {
            return array();
        }
    }

    /**
     * Show values in 'Podstawowe dane' area.
     * @return array    Array of records from table 'wartosci_podst_danych' of edited project
     */
    public function wartosciMaterialowych() {
        if ($this->projectId !== null) {
            return $this->db->query("SELECT wartosci_danych_materialowych.*, dane_materialowe.nazwa_danej_materialowej 
                    FROM wartosci_danych_materialowych 
                    LEFT JOIN dane_materialowe ON dane_materialowe.id = wartosci_danych_materialowych.id_danej_materialowej 
                    WHERE id_projektu = {$this->projectId}
                    AND wartosc_danej_materialowej != ''
                    ORDER BY dane_materialowe.sort")->fetchAll();
        } else {
            return array();
        }
    }

    public function categories() {
        return $this->db->query("SELECT * FROM kategorie ORDER BY sort")->fetchAll();
    }

    /**
     * Get project id.
     * @return int 
     */
    public function projectId() {
        if ($this->categoryUrl !== 'ustawienia' && $this->projectUrl !== null) {
            $sth = $this->db->query("SELECT id FROM projekty WHERE url_projektu = '$this->projectUrl'");
            $result = $sth->fetch();
            $this->projectId = $result['id'];
            return $result['id'];
        } else {
            return;
        }
    }

    /**
     * Prepare url routes.
     */
    public function prepareUrl($args) {

        if ($args == false) {
            header('Location:/');
        }
        $arg = explode(',', $args);
        $arg0 = htmlspecialchars($arg[0]);
        $sth = $this->db->prepare("SELECT COUNT(projekty.id) AS count_projects, kategorie.id, kategorie.ilosc_projektow_frontend FROM kategorie 
                LEFT JOIN projekty ON projekty.id_kategorii = kategorie.id
                WHERE url_kategorii = :url_kategorii");
        $sth->bindParam(":url_kategorii", $arg0, PDO::PARAM_STR);
        $sth->execute();
        $result = $sth->fetch();
        if ($result['id'] !== null) {
            $this->categoryUrl = $arg[0];
        } else {
            header('Location:/error');
        }
        if (!empty($arg[1])) {

            if (!empty($arg[2])) {
                $this->projectUrl = (string) $arg[2];
            }
            $max = ceil($result['count_projects'] / $result['ilosc_projektow_frontend']);
            $this->currentPage = (int) $arg[1] <= 0 ? 1 : (((int) $arg[1] > $max) ? header('Location:/error') : (int) $arg[1]);
//            $this->currentPage = (int) $arg[1];
        }
    }

    public function sortowanie() {
        if ($_POST) {

            Session::destroy('search');
            Session::destroy('search-success');
            Session::destroy('pageForEachProject');
            Session::destroy('highlight');
            Session::destroy('znalezione');

            $url = htmlspecialchars($_POST['url_kategorii']);
            $currentPage = (int) $_POST['page'];

            switch ((int) $_POST['sort']) {
                case 1://nazwa a-z
                    Session::destroy('sortFrontWhere');
                    Session::set('sortFrontOrder', 'projekty.url_projektu ASC');
                    Session::set('sorteFrontOrder', 'projekty.url_projektu ASC');
                    Session::set('sortFront', '1');
                    break;
                case 2://nazwa z-a
                    Session::destroy('sortFrontWhere');
                    Session::set('sortFrontOrder', 'projekty.url_projektu DESC');
                    Session::set('sorteFrontOrder', 'projekty.url_projektu DESC');
                    Session::set('sortFront', '2');
                    break;
                case 3://powierzchnia w górę
                    Session::destroy('sortFrontWhere');
                    Session::set('sortFrontOrder', 'wartosci_podst_danych.wartosc_danej ASC');
                    Session::set('sortFront', '3');
                    break;
                case 4://powierzchnia w dół
                    Session::destroy('sortFrontWhere');
                    Session::set('sortFrontOrder', 'wartosci_podst_danych.wartosc_danej DESC');
                    Session::set('sortFront', '4');
                    break;
                case 5:// tylko parterowe
                    Session::set('sortFrontWhere', 'AND (projekty.id_typu_projektu = 1 OR projekty.id_typu_projektu = 3) ');
                    Session::set('sortFront', '5');
                    break;
                case 6://tylko podpiwniczone
                    Session::set('sortFrontWhere', 'AND (projekty.id_typu_projektu = 2 OR projekty.id_typu_projektu = 4) ');
                    Session::set('sortFront', '6');
                    break;
                case 7://z poddaszem użytkowym
                    Session::set('sortFrontWhere', 'AND (projekty.id_typu_projektu = 3 OR projekty.id_typu_projektu = 4) ');
                    Session::set('sortFront', '7');
                    break;
            }

            header("Location: /oferta/{$url}/{$currentPage}");
        } else {
            header('Location:/error');
        }
    }

    public function projects() {

        if (!Session::get('sortFrontOrder')) {
            Session::set('sortFrontOrder', 'wartosci_podst_danych.wartosc_danej ASC');
        }
        if (!Session::get('sorteFrontOrder')) {
            Session::set('sorteFrontOrder', 'url_projektu ASC');
        }

        if ($this->categoryUrl !== 'realizacje') {
            $order = Session::get('sortFrontOrder');
            $where = Session::get('sortFrontWhere') ? Session::get('sortFrontWhere') : '';
        } else {
            $order = Session::get('sorteFrontOrder');
            $where = '';
        }

        $start = ($this->currentPage - 1) * $this->per_page;

        if ($this->categoryUrl != 'realizacje') {
            $sql = "SELECT SQL_CALC_FOUND_ROWS
                projekty.id,
                projekty.id_kategorii,
                projekty.nazwa_projektu,
                kategorie.id AS kat_id,
                projekty.url_projektu,
                wartosci_podst_danych.wartosc_danej                
            FROM projekty
            LEFT JOIN kategorie ON projekty.id_kategorii = kategorie.id
            LEFT JOIN wartosci_podst_danych ON projekty.id = wartosci_podst_danych.id_projektu
            WHERE kategorie.url_kategorii = '$this->categoryUrl'
                AND wartosci_podst_danych.id_danej = 2 {$where}ORDER BY {$order} LIMIT $start, $this->per_page
            ";
        } else {
            $sql = "SELECT SQL_CALC_FOUND_ROWS
                projekty.id,
                projekty.id_kategorii,
                projekty.nazwa_projektu,
                kategorie.id AS kat_id,
                projekty.url_projektu,
                realizacje.id_kategorii_realizacji
            FROM projekty
            LEFT JOIN kategorie ON projekty.id_kategorii = kategorie.id
            LEFT JOIN realizacje ON realizacje.id_realizacji = projekty.id
            WHERE kategorie.url_kategorii = '$this->categoryUrl' 
                    {$where}ORDER BY {$order} LIMIT $start, $this->per_page
            ";
        }

        $sth = $this->db->query($sql);
        $total = $this->db->query('SELECT FOUND_ROWS()')->fetch(PDO::FETCH_COLUMN);

        $this->total = (int) $total;
        $this->pages = (int) ceil((int) $this->total / (int) $this->per_page);

        if ($sth->rowCount() > 0) {
            $result = $sth->fetchAll();
            $rowCount = $sth->rowCount();
            $this->rows = ceil($rowCount / 3);
            $this->projectsCountRows = ceil($rowCount / 3) < 3 ? 3 : ceil($rowCount / 3);
            $this->projectsArrayPerRow = array_chunk($result, $this->projectsCountRows);
            return $result;
        } else {
            return null;
        }
    }

    public function zdjeciaRealizacji() {
        if (is_writable("media/img/realizacje/$this->projectUrl/realizacja")) {
            if ($this->projectUrl !== null) {
                $scandir = array_slice(scandir("media/img/realizacje/$this->projectUrl/realizacja"), 2);
                $this->count = count($scandir);
                $this->rows = ceil($this->count / 4);
                return array_chunk($scandir, 4);
            } else {
                return;
            }
        } else {
            return;
        }
    }

    public function typyProjektow() {
        return $this->db->query("SELECT * FROM typy_projektow ORDER BY id")->fetchall();
    }

    public function pomieszczenia() {
        $category = $this->category();
        $categoryId = $category['id'];
        $sth = $this->db->query("SELECT id, nazwa_pomieszczenia FROM pomieszczenia WHERE id_kategorii = {$categoryId} ORDER BY nazwa_pomieszczenia ASC");
        $sth->execute();
        return $sth->fetchAll();
    }

    public function aranzacje() {
        $sth = $this->db->query("SELECT * FROM aranzacje WHERE id_kategorii = (SELECT id FROM kategorie WHERE url_kategorii = '$this->categoryUrl')");
        $sth->execute();
        return $sth->fetchAll();
    }

    public function getAranzacje() {
        $sth = $this->db->query("SELECT * FROM aranzacje ORDER BY id");
        $sth->execute();
        return $sth->fetchAll();
    }

    public function elewacje() {
        $sth = $this->db->query("SELECT * FROM elewacje ORDER BY id");
        $sth->execute();
        return $sth->fetchAll();
    }

    public function powierzchnie() {
        $sql = "SELECT powierzchnie.* FROM pomieszczenia LEFT JOIN projekty ON powierzchnie.id_projektu = projekty.id WHERE url_projektu = '$this->projectUrl'";
        if(!$this->projectId){
        $sql = "SELECT
            pomieszczenia.nazwa_pomieszczenia,
            powierzchnie.nr_pomieszczenia,
            powierzchnie.id_pomieszczenia,
            powierzchnie.id_projektu,
            powierzchnie.id_aranzacji,
            powierzchnie.pow_pomieszczenia,
            aranzacje.nazwa_aranzacji,
            aranzacje.url_aranzacji
            FROM powierzchnie
            LEFT JOIN pomieszczenia ON powierzchnie.id_pomieszczenia = pomieszczenia.id
            LEFT JOIN aranzacje ON powierzchnie.id_aranzacji = aranzacje.id
            WHERE powierzchnie.id_projektu = (SELECT projekty.id FROM projekty WHERE url_projektu = '$this->projectUrl')
            ";
        }else{
          $sql = "SELECT
            pomieszczenia.nazwa_pomieszczenia,
            powierzchnie.nr_pomieszczenia,
            powierzchnie.id_pomieszczenia,
            powierzchnie.id_projektu,
            powierzchnie.id_aranzacji,
            powierzchnie.pow_pomieszczenia,
            aranzacje.nazwa_aranzacji,
            aranzacje.url_aranzacji
            FROM powierzchnie
            LEFT JOIN pomieszczenia ON powierzchnie.id_pomieszczenia = pomieszczenia.id
            LEFT JOIN aranzacje ON powierzchnie.id_aranzacji = aranzacje.id
            WHERE powierzchnie.id_projektu = $this->projectId
            ";  
        }
        $sth = $this->db->query($sql);
        $sth->execute();
        return $sth->fetchAll();
    }

    public function project() {
        if ($this->projectUrl === null) {
            return;
        } else {
//            $sql = "SELECT projekty.* FROM projekty  
//            WHERE projekty.url_projektu = :url_projektu  
//            ORDER BY nazwa_projektu";
            if ($this->categoryUrl !== 'realizacje') {
                $sql = "SELECT projekty.*,wartosci_podst_danych.wartosc_danej FROM projekty 
                LEFT JOIN wartosci_podst_danych ON projekty.id = wartosci_podst_danych.id_projektu 
                WHERE projekty.url_projektu = :url_projektu 
                AND wartosci_podst_danych.id_danej = 2 
                ORDER BY nazwa_projektu";
            } else {
                $sql = "SELECT projekty.* FROM projekty  
                WHERE projekty.url_projektu = :url_projektu 
                ORDER BY nazwa_projektu";
            }
            $sth = $this->db->prepare($sql);
            $sth->bindParam(':url_projektu', $this->projectUrl, PDO::PARAM_STR);
            $sth->execute();
            if ($sth->rowCount() > 0) {
                return $sth->fetchAll();
            } else {
                return;
            }
        }
    }

    public function materialowe() {
        return $this->db->query("SELECT * FROM dane_materialowe ORDER BY sort")->fetchAll();
    }

    public function znaleziono() {
        if (isset($_POST['search-input'])) {

            $odOutput = '';
            $doOutput = '';

            if ((empty($_POST['search-input']) || trim($_POST['search-input']) == '') && (!isset($_POST['od']) || trim($_POST['od']) == '') && (!isset($_POST['do']) || trim($_POST['do']) == '')) {
                Session::destroy('search-success');
                Session::destroy('znalezione');
                Session::destroy('highlight');
                Session::set('search', 'Wprowadź szukaną frazę i/lub zakres powierzchni.');
            } else {

                $session_for_order = Session::get('sortFrontOrder') ? Session::get('sortFrontOrder') : 'projekty.url_projektu';
                //get all sorted projects from jednorodzinne
                $sth = $this->db->query("SELECT url_projektu FROM projekty 
                        LEFT JOIN wartosci_podst_danych ON wartosci_podst_danych.id_projektu = projekty.id
                        WHERE id_kategorii = 1 
                        AND wartosci_podst_danych.id_danej = 2 ORDER BY $session_for_order");
                $jednorodzinne = $sth->fetchAll();
                $sortedJednorodzinne = array();
                foreach ($jednorodzinne as $val) {
                    $sortedJednorodzinne[] = $val['url_projektu'];
                }

                //get all sorted projects from deweloper
                $sth = $this->db->query("SELECT url_projektu FROM projekty 
                        LEFT JOIN wartosci_podst_danych ON wartosci_podst_danych.id_projektu = projekty.id
                        WHERE id_kategorii = 2 
                        AND wartosci_podst_danych.id_danej = 2 ORDER BY $session_for_order");
                $deweloper = $sth->fetchAll();

                $sortedDeweloper = array();
                foreach ($deweloper as $val) {
                    $sortedDeweloper[] = $val['url_projektu'];
                }

                //get all sorted projects from gospodarcze
                $sth = $this->db->query("SELECT url_projektu FROM projekty 
                        LEFT JOIN wartosci_podst_danych ON wartosci_podst_danych.id_projektu = projekty.id
                        WHERE id_kategorii = 3 
                        AND wartosci_podst_danych.id_danej = 2 ORDER BY $session_for_order");
                $gospodarcze = $sth->fetchAll();

                $sortedGospodarcze = array();
                foreach ($gospodarcze as $val) {
                    $sortedGospodarcze[] = $val['url_projektu'];
                }

                Session::destroy('search');
                Session::destroy('highlight');
                Session::destroy('znalezione');
                $search_input = trim($_POST['search-input']);
                $search_input = htmlspecialchars($search_input);

                if (!empty($_POST['search-input']) || trim($_POST['search-input']) != '') {
                    $wgFrazy = "według frazy [ <strong>{$search_input}</strong> ]";
                } else {
                    $wgFrazy = '';
                }

                if (isset($_POST['od'])) {
                    if (!empty($_POST['od']) || trim($_POST['od']) !== '') {
                        $od = " AND wartosci_podst_danych.wartosc_danej >= :od";
                    } else {
                        $od = '';
                    }
                } else {
                    $od = '';
                }

                if (isset($_POST['do'])) {
                    if (!empty($_POST['do']) || trim($_POST['do']) !== '') {
                        $do = " AND wartosci_podst_danych.wartosc_danej <= :do";
                    } else {
                        $do = '';
                    }
                } else {
                    $do = '';
                }

                try {
                    if (!empty($_POST['search-input']) || trim($_POST['search-input']) != '') {
                        $sql = "SELECT
                        projekty.id,
                        projekty.nazwa_projektu,
                        projekty.url_projektu,
                        projekty.opis_projektu,
                        wartosci_podst_danych.wartosc_danej as powierzchnia,
                        format(wartosci_podst_danych.wartosc_danej/100,2,'de_DE') as sformatowana_powierzchnia,
                        kategorie.url_kategorii,
                        kategorie.nazwa_kategorii
                        FROM projekty
                        LEFT JOIN wartosci_podst_danych ON wartosci_podst_danych.id_projektu = projekty.id
                        LEFT JOIN kategorie ON kategorie.id = projekty.id_kategorii
                        WHERE wartosci_podst_danych.id_danej = 2
                        AND (projekty.nazwa_projektu LIKE :nazwa_projektu OR projekty.opis_projektu LIKE :opis_projektu)
                        {$od}{$do}
                        GROUP BY projekty.url_projektu 
                        ORDER BY $session_for_order";

                        $sth = $this->db->prepare($sql);

                        $search_term = '%' . $search_input . '%';

                        $sth->bindParam(':nazwa_projektu', $search_term, PDO::PARAM_STR);
                        $sth->bindParam(':opis_projektu', $search_term, PDO::PARAM_STR);
                    } else {
                        $sql = "SELECT
                        projekty.id,
                        projekty.nazwa_projektu,
                        projekty.url_projektu,
                        projekty.opis_projektu,
                        wartosci_podst_danych.wartosc_danej as powierzchnia,
                        format(wartosci_podst_danych.wartosc_danej/100,2,'de_DE') as sformatowana_powierzchnia,
                        kategorie.url_kategorii,
                        kategorie.nazwa_kategorii
                        FROM projekty
                        LEFT JOIN wartosci_podst_danych ON wartosci_podst_danych.id_projektu = projekty.id
                        LEFT JOIN kategorie ON kategorie.id = projekty.id_kategorii
                        WHERE (wartosci_podst_danych.id_danej = 2)
                        {$od}{$do}
                        GROUP BY projekty.url_projektu 
                        ORDER BY $session_for_order";
                        $sth = $this->db->prepare($sql);
                    }

                    if (isset($_POST['od'])) {
                        if (!empty($_POST['od']) || trim($_POST['od']) !== '') {
                            $odOutput = ' od ' . (int) $_POST['od'] . ' m<sup>2</sup>';
                            $odPrepared = ((int) $_POST['od']) * 100;
                            $sth->bindParam(':od', $odPrepared, PDO::PARAM_INT);
                        }
                    } else {
                        $odOutput = '';
                    }

                    if (isset($_POST['do'])) {
                        if (!empty($_POST['do']) || trim($_POST['do']) !== '') {
                            $doOutput = ' do ' . (int) $_POST['do'] . ' m<sup>2</sup>';
                            $doPrepared = ((int) $_POST['do']) * 100;
                            $sth->bindParam(':do', $doPrepared, PDO::PARAM_INT);
                        }
                    } else {
                        $doOutput = '';
                    }

                    $sth->execute();
                } catch (Exception $e) {
                    Session::set('search', $e->getMessage());
                    header("Location: /oferta/{$_POST['url_kategorii']}/{$_POST['current_page']}/{$_POST['url_projektu']}");
                }

                $rowCount = $sth->rowCount();
                if ($rowCount > 0) {
                    $result = $sth->fetchAll();

                    $pageForEachProject = array();

                    foreach ($result as $val) {
                        if ($val['url_kategorii'] === 'jednorodzinne') {
                            $pageForEachProject[] = ceil((array_search($val['url_projektu'], $sortedJednorodzinne) + 1) / ((int) $_POST['total'] / (int) $_POST['pages']));
                        }
                        if ($val['url_kategorii'] === 'deweloper') {
                            $pageForEachProject[] = ceil((array_search($val['url_projektu'], $sortedDeweloper) + 1) / ((int) $_POST['total'] / (int) $_POST['pages']));
                        }
                        if ($val['url_kategorii'] === 'gospodarcze') {
                            $pageForEachProject[] = ceil((array_search($val['url_projektu'], $sortedGospodarcze) + 1) / ((int) $_POST['total'] / (int) $_POST['pages']));
                        }
                    }
                    Session::set('pageForEachProject', $pageForEachProject);
                    Session::set('znalezione', $result);
                    if (!empty($_POST['search-input']) || trim($_POST['search-input']) != '') {
                        Session::set('highlight', $search_input);
                    }
                    Session::set('search-success', "Znaleziono projekty: [ <strong>{$sth->rowCount()}</strong> ] {$wgFrazy}{$odOutput}{$doOutput}");
                } else {
                    Session::destroy('pageForEachProject');
                    Session::destroy('znalezione');
                    Session::destroy('highlight');
                    Session::destroy('search-success');
                    Session::set('search', "Wyszukiwanie projektów {$wgFrazy}{$odOutput}{$doOutput} nie dało rezultatów...");
                }
            }
            $url_projektu = isset($_POST['url_projektu']) ? $_POST['url_projektu'] : null;
            header("Location: /oferta/{$_POST['url_kategorii']}/{$_POST['current_page']}/{$url_projektu}");
        }
    }

}