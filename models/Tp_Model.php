<?

error_reporting(E_ALL);

defined('_PHPIOTR') or die('Restricted access');

class Tp_Model extends Model {

	public $categoryUrl;
	public $categoryId;
	public $iloscPomieszczenKategorii;
	public $currentPage = 1;
	public $projectUrl;
	public $projectId;
	public $per_page = 9;
	public $total = 1;
	public $pages = 1;

	/**
	 * Array for elevations - there are always 4 of them
	 */
	public $elewacje = array('front', 'bok1', 'tyl', 'bok2');

	/**
	 * All fields in form without areas in arrangements
	 */
	public $posted = array('id_kategorii', 'nazwa_projektu', 'url_projektu', 'opis_projektu', 'id_typu_projektu', 'link_pod_zdjeciem', 'opis_linku_pod_zdjeciem');

	/**
	 * Areas records array helper for inserting / updating arrangements
	 */
	public $powierzchnie = array('id_pomieszczenia', 'id_aranzacji', 'id_projektu', 'nr_pomieszczenia', 'pow_pomieszczenia');

	public function __construct() {
		parent::__construct();
	}

	public function editing() {
		if ($_POST) {
			Session::destroy('duplication');
			header("Location:/tp/{$_POST['editing_category_url']}/{$_POST['editing_current_page']}/{$_POST['editing_project']}");
		}
	}

	public function duplicate() {
		if ($_POST) {
			Session::set('duplication', "1");
			header("Location:/tp/{$_POST['duplicate_category_url']}/{$_POST['duplicate_current_page']}/{$_POST['duplicate_project']}");
		}
	}

	public function pagination() {
		return Pagination::create($this->pages, $this->currentPage, "/tp/$this->categoryUrl");
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
			$this->per_page = $result['ilosc_projektow_backend'];
			return $result;
		} else {
			return;
		}
	}

	public function typyProjektow() {
		return $this->db->query("SELECT * FROM typy_projektow ORDER BY id")->fetchAll();
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
			return $this->db->query("SELECT * FROM wartosci_podst_danych WHERE id_projektu = {$this->projectId}")->fetchAll();
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
			return $this->db->query("SELECT * FROM wartosci_danych_materialowych WHERE id_projektu = {$this->projectId}")->fetchAll();
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
		if (/* $this->categoryUrl !== 'ustawienia' && */$this->projectUrl !== null) {
			$sth = $this->db->query("SELECT id FROM projekty WHERE url_projektu = '$this->projectUrl' AND projekty.id_kategorii = (SELECT id FROM kategorie WHERE url_kategorii = '{$this->categoryUrl}')");
			$result = $sth->fetch();
			$this->projectId = $result['id'];
			return $result['id'];
		} //else {
		//return;
		//}
	}

	/**
	 * Prepare url routes.
	 */
	public function prepareUrl($args) {
		if ($args == false) {
			header('Location:/tp/ustawienia');
		} else {
			$arg = explode(',', $args);

			if (!is_numeric($arg[0])) {
				$arg0 = htmlspecialchars($arg[0]);
//                $sth = $this->db->prepare("SELECT id FROM kategorie WHERE url_kategorii = :url_kategorii");
				$sth = $this->db->prepare("SELECT COUNT(projekty.id) AS count_projects, kategorie.id, kategorie.ilosc_projektow_backend FROM kategorie 
                LEFT JOIN projekty ON projekty.id_kategorii = kategorie.id
                WHERE url_kategorii = :url_kategorii");
				$sth->bindParam(":url_kategorii", $arg0, PDO::PARAM_STR);
				$sth->execute();
				$result = $sth->fetch();
				if ($sth->rowCount() > 0) {
					$this->categoryUrl = $arg[0];
				} else {
					header('Location:/tp/ustawienia');
				}
			} else {
				header('Location:/tp/ustawienia');
			}
			if (!empty($arg[1])) {

				if (!empty($arg[2])) {
					$this->projectUrl = (string) $arg[2];
				}
				$max = ceil($result['count_projects'] / $result['ilosc_projektow_backend']);
				$this->currentPage = (int) $arg[1] <= 0 ? 1 : (((int) $arg[1] > $max) ? header('Location:/error') : (int) $arg[1]);
//            $this->currentPage = (int) $arg[1];
			}
//            if (!empty($arg[1])) {
//                if (empty($arg[2])) {
//                    if ((int) $arg[1] < 1 && is_numeric($arg[1])) {
//                        $this->projectUrl = (string) $arg[1];
//                    } else {
//                        $this->currentPage = (int) $arg[1];
//                    }
//                } else {
//                    $this->projectUrl = (string) $arg[2];
//                    if ((int) $arg[1] > 0 && is_numeric($arg[1])) {
//                        $this->currentPage = (int) $arg[1];
//                    } else {
//                        $this->projectUrl = (string) $arg[1];
//                    }
//                }
//            }
		}
	}

	public function sortowanie() {
		if ($_POST) {
			$url = htmlspecialchars($_POST['url_kategorii']);
			$currentPage = (int) $_POST['page'];
			$project = isset($_POST['project']) ? '/' . htmlspecialchars($_POST['project']) : null;
			if ($url === 'realizacje') {
				Session::set('sorte', $_POST['sort']);
			} else {
				Session::set('sort', $_POST['sort']);
			}

			header("Location: /tp/{$url}/{$currentPage}{$project}");
		} else {
			header('Location:/error');
		}
	}

	public function projects() {

		$order = Session::get('sort') ? Session::get('sort') : 'url_projektu';
		$order_real = Session::get('sorte') ? Session::get('sorte') : 'url_projektu';

		$start = ($this->currentPage - 1) * $this->per_page;

		if ($this->categoryUrl != 'realizacje') {
			$sql = "
            SELECT SQL_CALC_FOUND_ROWS
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
                AND wartosci_podst_danych.id_danej = 2 ORDER BY {$order} LIMIT $start, $this->per_page
            ";
		} else {
			$sql = "
            SELECT SQL_CALC_FOUND_ROWS projekty.id, 
        projekty.id_kategorii, 
        projekty.nazwa_projektu,
        projekty.url_projektu,
        kategorie.id AS kat_id 
        FROM projekty 
        LEFT JOIN kategorie ON projekty.id_kategorii = kategorie.id 
        WHERE kategorie.url_kategorii = '{$this->categoryUrl}' 
                    ORDER BY projekty.$order_real LIMIT $start, $this->per_page
                ";
		}

		$sth = $this->db->query($sql);
		$total = $this->db->query('SELECT FOUND_ROWS();')->fetch(PDO::FETCH_COLUMN);
		$this->total = (int) $total;
		$this->pages = (int) ceil((int) $this->total / (int) $this->per_page);

		if ($sth->rowCount() > 0) {
			return $sth->fetchAll();
		} else {
			return null;
		}
	}

	public function pomieszczenia() {
		$category = $this->category();
		$categoryId = $category['id'];
		$sth = $this->db->query("SELECT id, nazwa_pomieszczenia FROM pomieszczenia WHERE id_kategorii = {$categoryId} ORDER BY nazwa_pomieszczenia ASC");
		$sth->execute();
		return $sth->fetchAll();
	}

	public function pomieszczenie() {
		if ($_POST) {
			$error = array();
			$success = array();
			$stare = (int) $_POST['starePom'];
			$nowe = trim($_POST['nowePom']);
			$url = htmlspecialchars($_POST['url_kategorii']);
			$currentPage = (int) $_POST['page'];
			$project = isset($_POST['project']) ? '/' . htmlspecialchars($_POST['project']) : null;
			if (empty($nowe) && $stare == null) {
				$error[] = "<i class='icon-warning-sign'></i> Ani nie zostało wybrane pomieszczenie do usunięcia, ani nie została wprowadzona nazwa nowego pomieszczenia do wstawienia.";
			}
			Session::set('error', implode('<br />', $error));
			if (empty($error)) {
				try {
					if ($stare !== null) {
						try {
							$sth = $this->db->query("SELECT powierzchnie.id, pomieszczenia.nazwa_pomieszczenia AS nazwa FROM powierzchnie RIGHT JOIN pomieszczenia ON pomieszczenia.id = powierzchnie.id_pomieszczenia WHERE id_pomieszczenia = $stare");
							$result = $sth->fetch();
							$nazwa = $result['nazwa'];
							if ($sth->rowCount() > 0) {
								Session::set('error', '<i class="icon-warning-sign"></i> <strong>' . ucfirst($nazwa) . '</strong> jest aktualnie w użyciu. Nie do usunięcia.');
							} else {
								$sth = $this->db->prepare("DELETE FROM pomieszczenia WHERE id = $stare");
								$sth->execute();
								if ($sth->rowCount() > 0) {
									$success[] = "<i class='icon-ok'></i> Usunięcie pomieszczenia powiodło się.";
								}
							}
						} catch (Exception $e) {
							Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
						}
					}
					if (!empty($nowe)) {
						$nowe = htmlspecialchars($nowe);
						$id = (int) $_POST['id_kategorii'];
						$sth = $this->db->query("SELECT id FROM pomieszczenia WHERE id_kategorii = $id AND nazwa_pomieszczenia = '$nowe'");
						$sth->fetchAll();
						if ($sth->rowCount() > 0) {
							Session::set('error', '<i class="icon-warning-sign"></i>' . "Pomieszczenie o nazwie <strong>$nowe</strong> już istnieje w tej kategorii.");
						} else {
							$this->db->query("INSERT INTO pomieszczenia (id_kategorii, nazwa_pomieszczenia) VALUES ($id, '$nowe')");
							$success[] = "<i class='icon-ok'></i> Pomieszczenie o nazwie <strong>$nowe</strong> zostało dodane.";
						}
					}
				} catch (Exception $e) {
					Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
				}
				Session::set('success', implode('<br />', $success));
			}
			header("Location: /tp/{$url}/{$currentPage}{$project}");
		} else {
			header('Location:/error');
		}
	}

	public function aranzacje() {
		$sth = $this->db->prepare("SELECT * FROM aranzacje WHERE id_kategorii = (SELECT id FROM kategorie WHERE url_kategorii = '$this->categoryUrl')");
		$sth->execute();
		return $sth->fetchAll();
	}

	public function elewacje() {
		$sth = $this->db->prepare("SELECT * FROM elewacje ORDER BY id");
		$sth->execute();
		return $sth->fetchAll();
	}

	public function aranzacja() {
		if ($_POST) {
			$error = array();
			$success = array();
			$stare = (int) $_POST['staraKond'];
			$nowe = trim($_POST['nowaKond']);
			$url = htmlspecialchars($_POST['url_kategorii']);
			$id = (int) $_POST['id_kategorii'];
			$currentPage = (int) $_POST['page'];
			$project = isset($_POST['project']) ? '/' . htmlspecialchars($_POST['project']) : null;
			if (empty($nowe) && $stare == null) {
				$error[] = "<i class='icon-warning-sign'></i> Ani nie została wybrana kondygnacja do usunięcia, ani nie została wprowadzona nazwa nowej kondygnacji do wstawienia.";
			}
			Session::set('error', implode('<br />', $error));
			if (empty($error)) {
				try {
					if ($stare !== null) {
						try {
							$sth = $this->db->query("SELECT powierzchnie.id, aranzacje.nazwa_aranzacji AS nazwa FROM powierzchnie RIGHT JOIN aranzacje ON aranzacje.id = powierzchnie.id_aranzacji WHERE id_aranzacji = $stare");
							$result = $sth->fetch();
							$nazwa = $result['nazwa'];
							if ($sth->rowCount() > 0) {
								Session::set('error', '<i class="icon-warning-sign"></i> <strong>' . ucfirst($nazwa) . '</strong> jest aktualnie w użyciu. Nie do usunięcia.');
							} else {
								$sth = $this->db->prepare("DELETE FROM aranzacje WHERE id = $stare");
								$sth->execute();
								if ($sth->rowCount() > 0) {
									$success[] = "<i class='icon-ok'></i> Usunięcie kondygnacji powiodło się.";
								}
							}
						} catch (Exception $e) {
							Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
						}
					}
					if (!empty($nowe)) {
						$nowe = htmlspecialchars($nowe);
						$postValue = Url::createPostValue($nowe);

						$sth = $this->db->query("SELECT id FROM aranzacje WHERE id_kategorii = $id AND nazwa_aranzacji = '$nowe'");
						$sth->fetchAll();
						if ($sth->rowCount() > 0) {
							Session::set('error', '<i class="icon-warning-sign"></i>' . "Kondygnacja o nazwie <strong>$nowe</strong> już istnieje w tej kategorii.");
						} else {
							$this->db->query("INSERT INTO aranzacje (id_kategorii, nazwa_aranzacji, url_aranzacji) VALUES ($id, '$nowe', '$postValue')");
							$success[] = "<i class='icon-ok'></i> Kondygnacja o nazwie <strong>$nowe</strong> została dodana do tej kategorii.";
						}
					}
				} catch (Exception $e) {
					Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
				}
				Session::set('success', implode('<br />', $success));
			}
			header("Location: /tp/{$url}/{$currentPage}{$project}");
		} else {
			header('Location:/error');
		}
	}

	public function podstawowa() {
		if ($_POST) {
			$error = array();
			$success = array();
			$stare = (int) $_POST['staraPodst'];
			$nowe = trim($_POST['nowaPodst']);
			$url = htmlspecialchars($_POST['url_kategorii']);
			$currentPage = (int) $_POST['page'];
			$project = isset($_POST['project']) ? '/' . htmlspecialchars($_POST['project']) : null;
			if (empty($nowe) && $stare == null) {
				$error[] = "<i class='icon-warning-sign'></i> Ani nie została wybrana dana podstawowa do usunięcia, ani nie została wprowadzona nazwa nowej danej podstawowej do wstawienia.";
			}
			Session::set('error', implode('<br />', $error));
			if (empty($error)) {
				try {
					if ($stare !== null) {
						try {
							$sth = $this->db->query("SELECT wartosci_podst_danych.id_danej, podstawowe_dane.nazwa_danej AS nazwa FROM wartosci_podst_danych RIGHT JOIN podstawowe_dane ON podstawowe_dane.id = wartosci_podst_danych.id_danej WHERE id_danej = $stare");
							$result = $sth->fetch();
							$nazwa = $result['nazwa'];
							if ($sth->rowCount() > 0) {
								Session::set('error', '<i class="icon-warning-sign"></i> <strong>' . ucfirst($nazwa) . '</strong> jest aktualnie w użyciu. Nie do usunięcia.');
							} else {
								$sth = $this->db->prepare("DELETE FROM podstawowe_dane WHERE id = $stare");
								$sth->execute();
								if ($sth->rowCount() > 0) {
									$success[] = "<i class='icon-ok'></i> Usunięcie danej podstawowej powiodło się.";
								}
							}
						} catch (Exception $e) {
							Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
						}
					}
					if (!empty($nowe)) {
						$nowe = htmlspecialchars($nowe);
						$postValue = Url::createPostValue($nowe);
						$sth = $this->db->query("SELECT id FROM podstawowe_dane WHERE nazwa_danej = '$nowe'");
						$sth->fetchAll();
						if ($sth->rowCount() > 0) {
							Session::set('error', '<i class="icon-warning-sign"></i>' . "Dana o nazwie <strong>$nowe</strong> już istnieje.");
						} else {
							$this->db->query("INSERT INTO podstawowe_dane (nazwa_danej, post_danej) VALUES ('$nowe', '$postValue')");
							$success[] = "<i class='icon-ok'></i> Dana podstawowa o nazwie <strong>$nowe</strong> została dodana.";
						}
					}
				} catch (Exception $e) {
					Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
				}
				Session::set('success', implode('<br />', $success));
			}
			header("Location: /tp/{$url}/{$currentPage}{$project}");
		} else {
			header('Location:/error');
		}
	}

	public function materialowa() {
		if ($_POST) {
			$error = array();
			$success = array();
			$stare = (int) $_POST['staraMaterial'];
			$nowe = trim($_POST['nowaMaterial']);
			$url = htmlspecialchars($_POST['url_kategorii']);
			$currentPage = (int) $_POST['page'];
			$project = isset($_POST['project']) ? '/' . htmlspecialchars($_POST['project']) : null;
			if (empty($nowe) && $stare == null) {
				$error[] = "<i class='icon-warning-sign'></i> Ani nie została wybrana dana materiałowa do usunięcia, ani nie została wprowadzona nazwa nowej danej materiałowej do wstawienia.";
			}
			Session::set('error', implode('<br />', $error));
			if (empty($error)) {
				try {
					if ($stare !== null) {
						try {
							$sth = $this->db->query("SELECT wartosci_danych_materialowych.id_danej_materialowej, dane_materialowe.nazwa_danej_materialowej AS nazwa FROM wartosci_danych_materialowych RIGHT JOIN dane_materialowe ON dane_materialowe.id = wartosci_danych_materialowych.id_danej_materialowej WHERE id_danej_materialowej = $stare");
							$result = $sth->fetch();
							$nazwa = $result['nazwa'];
							if ($sth->rowCount() > 0) {
								Session::set('error', '<i class="icon-warning-sign"></i> <strong>' . ucfirst($nazwa) . '</strong> jest aktualnie w użyciu. Nie do usunięcia.');
							} else {
								$sth = $this->db->prepare("DELETE FROM dane_materialowe WHERE id = $stare");
								$sth->execute();
								if ($sth->rowCount() > 0) {
									$success[] = "<i class='icon-ok'></i> Usunięcie danej materiałowej powiodło się.";
								}
							}
						} catch (Exception $e) {
							Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
						}
					}
					if (!empty($nowe)) {
						$nowe = htmlspecialchars($nowe);
						$postValue = Url::createPostValue($nowe);
						$sth = $this->db->query("SELECT id FROM dane_materialowe WHERE nazwa_danej_materialowej = '$nowe'");
						$sth->fetchAll();
						if ($sth->rowCount() > 0) {
							Session::set('error', '<i class="icon-warning-sign"></i>' . "Dana materiałowa o nazwie <strong>$nowe</strong> już istnieje.");
						} else {
							$this->db->query("INSERT INTO dane_materialowe (nazwa_danej_materialowej, post_danej_materialowej) VALUES ('$nowe', '$postValue')");
							$success[] = "<i class='icon-ok'></i> Dana materiałowa o nazwie <strong>$nowe</strong> została dodana.";
						}
					}
				} catch (Exception $e) {
					Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
				}
				Session::set('success', implode('<br />', $success));
			}
			header("Location: /tp/{$url}/{$currentPage}{$project}");
		} else {
			header('Location:/error');
		}
	}

	public function powierzchnie() {
		$sql = "SELECT powierzchnie.* FROM pomieszczenia LEFT JOIN projekty ON powierzchnie.id_projektu = projekty.id WHERE url_projektu = '$this->projectUrl'";
		if ($this->projectId) {
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
		} else {
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
		}
		$sth = $this->db->query($sql);
		$sth->execute();
		return $sth->fetchAll();
	}

	public function zdjeciaRealizacji() {
		if (is_writable("media/img/realizacje/$this->projectUrl/realizacja")) {
			if ($this->projectUrl !== null) {
				$scandir = array_slice(scandir("media/img/realizacje/$this->projectUrl/realizacja"), 2);
				$this->count = count($scandir);
				$this->rows = ceil($this->count / 3);
				return array_chunk($scandir, 3);
			} else {
				return;
			}
		} else {
			return;
		}
	}

	public function project() {
		if ($this->projectUrl === null) {
			return;
		} else {
			if ($this->categoryUrl == 'realizacje') {
				$sql = "SELECT projekty.*,realizacje.id_kategorii_realizacji FROM projekty 
                LEFT JOIN realizacje ON realizacje.id_realizacji = projekty.id
                LEFT JOIN kategorie ON kategorie.id = projekty.id_kategorii
                WHERE projekty.id_kategorii = 4 AND projekty.url_projektu = :url_projektu 
                LIMIT 1";
			} else {
				$sql = "SELECT projekty.*,wartosci_podst_danych.wartosc_danej FROM projekty 
                LEFT JOIN wartosci_podst_danych ON projekty.id = wartosci_podst_danych.id_projektu 
                LEFT JOIN kategorie ON kategorie.id = projekty.id_kategorii
                WHERE projekty.url_projektu = :url_projektu AND projekty.id_kategorii = (SELECT id FROM kategorie WHERE url_kategorii = '$this->categoryUrl') 
                AND wartosci_podst_danych.id_danej = 2";
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

	public function iloscPomieszczen() {
		if ($_POST) {
			$url = htmlspecialchars($_POST['url_kategorii']);
			$currentPage = (int) $_POST['page'];
			$project = isset($_POST['project']) ? '/' . htmlspecialchars($_POST['project']) : null;
			$ilosc_pomieszczen = (int) $_POST['ilosc_pomieszczen'] > 0 ? (int) $_POST['ilosc_pomieszczen'] : 1;
			try {
				$this->db->prepare("UPDATE kategorie SET ilosc_pomieszczen = $ilosc_pomieszczen WHERE url_kategorii = '$url'")->execute();
				Session::set('success', '<i class="icon-ok"></i> Ilość wyświetlanych pomieszczeń dla poszczególnej kondygnacji została zaktualizowana. Aktualnie wynosi ona: <strong>' . $ilosc_pomieszczen . '</strong>.');
				header("Location: /tp/{$url}/{$currentPage}{$project}");
				exit();
			} catch (Exception $e) {
				Session::set('error', '<i class=icon-warning-sign></i> ' . $e->getMessage());
			}
		} else {
			header("Location: /error");
		}
	}

	public function iloscProjektowBackend() {
		if ($_POST) {
			$url = htmlspecialchars($_POST['url_kategorii']);
			$currentPage = (int) $_POST['page'];
			$project = isset($_POST['project']) ? '/' . htmlspecialchars($_POST['project']) : null;
			$ilosc_projektow_backend = (int) $_POST['ilosc_projektow_backend'] > 0 ? (int) $_POST['ilosc_projektow_backend'] : 1;
			try {
				$this->db->prepare("UPDATE kategorie SET ilosc_projektow_backend = $ilosc_projektow_backend WHERE url_kategorii = '$url'")->execute();
				Session::set('success', '<i class="icon-ok"></i> Ilość wyświetlanych projektów na zapleczu w tej kategorii została zaktualizowana. Aktualnie wynosi ona: <strong>' . $ilosc_projektow_backend . '</strong>.');
//                header("Location: /tp/{$url}/{$currentPage}{$project}"); //current page doesn't work if we've had before more pages than after and we were on higher page than the amount of pages after
				header("Location: /tp/{$url}/1");
				exit();
			} catch (Exception $e) {
				Session::set('error', '<i class=icon-warning-sign></i> ' . $e->getMessage());
			}
		} else {
			header("Location: /error");
		}
	}

	public function iloscProjektowFrontend() {
		if ($_POST) {
			$url = htmlspecialchars($_POST['url_kategorii']);
			$currentPage = (int) $_POST['page'];
			$project = isset($_POST['project']) ? '/' . htmlspecialchars($_POST['project']) : null;
			$ilosc_projektow_frontend = (int) $_POST['ilosc_projektow_frontend'] > 0 ? (int) $_POST['ilosc_projektow_frontend'] : 1;
			try {
//                $this->db->prepare("UPDATE kategorie SET ilosc_projektow_frontend = $ilosc_projektow_frontend WHERE url_kategorii = '$url'")->execute();
				$this->db->prepare("UPDATE kategorie SET ilosc_projektow_frontend = $ilosc_projektow_frontend")->execute();
//                Session::set('success', '<i class="icon-ok"></i> Ilość wyświetlanych projektów w witrynie w tej kategorii została zaktualizowana. Aktualnie wynosi ona: <strong>' . $ilosc_projektow_frontend . '</strong>.');
				Session::set('success', '<i class="icon-ok"></i> Ilość wyświetlanych projektów w witrynie we wszystkich kategoriach została zaktualizowana. Aktualnie wynosi ona: <strong>' . $ilosc_projektow_frontend . '</strong>.');
				header("Location: /tp/{$url}/{$currentPage}{$project}");
				exit();
			} catch (Exception $e) {
				Session::set('error', '<i class=icon-warning-sign></i> ' . $e->getMessage());
			}
		} else {
			header("Location: /error");
		}
	}

	public function add() {

		if ($_POST || $_FILES) {

			$error = array();

			$url_kategorii = htmlspecialchars($_POST['kategoria']);
			$nazwa_projektu = htmlspecialchars($_POST['nazwa_projektu']);
			$url_projektu = Url::create($nazwa_projektu);
			$id_kategorii = (int) $_POST['id_kategorii'];

			$nazwa = $url_kategorii == 'realizacje' ? 'realizacji' : 'projektu';

			$link_pod_zdjeciem = htmlspecialchars($_POST['link_pod_zdjeciem']);
			$opis_linku_pod_zdjeciem = htmlspecialchars($_POST['opis_linku_pod_zdjeciem']);

			//jeśli nie mamy do czynienia z realizacją
			if ($url_kategorii !== 'realizacje') {

				//projekt lub realizacja
				foreach ($this->posted as $post) {
					if (isset($POST[$post])) {
						Session::set("$post", "$_POST[$post]");
					}
				}

				//podstawowe dane
				foreach ($this->dane() as $dana) {
					Session::set($dana['post_danej'], $_POST[$dana['post_danej']]);
				}
				//podstawowe dane materialowe
				foreach ($this->materialowe() as $dana) {
					Session::set($dana['post_danej_materialowej'], $_POST[$dana['post_danej_materialowej']]);
				}

				$opis_projektu = htmlspecialchars($_POST['opis_projektu']);
				$ilosc_pomieszczen = (int) $_POST['ilosc_pomieszczen'];
			} else {
				$kategoria_realizacji = (int) $_POST['kategoria_realizacji'];
			}
			//sprawdzenie czy projekt / realizacja już istnieje w danej kategorii
			try {
				if ($url_kategorii !== 'realizacje') {
					$sth = $this->db->query("SELECT id FROM projekty WHERE url_projektu = '{$url_projektu}' AND id_kategorii = $id_kategorii");
				} else {
					$sth = $this->db->query("SELECT id FROM realizacje WHERE id_realizacji = (SELECT id FROM projekty WHERE url_projektu = '{$url_projektu}') AND id_kategorii_realizacji = $kategoria_realizacji");
				}
				$sth->fetchAll();
				if ($sth->rowCount() > 0) {
					$error[] = "<i class=\"icon-warning-sign\"></i> Nazwa $nazwa <strong>$nazwa_projektu</strong> istnieje już w tej kategorii. Nazwy wewnątrz kategorii nie mogą sie powtarzać.";
				}
			} catch (Exception $e) {
				$error[] = '<i class="icon-warning-sign"></i>' . $e->getMessage();
			}

			if (empty($nazwa_projektu) || trim($nazwa_projektu) == '') {
				$error[] = "<i class=\"icon-warning-sign\"></i> Nazwa $nazwa nie może być pusta.";
			}

			if ($url_kategorii == 'realizacje') {
				if ($kategoria_realizacji === 0) {
					$error[] = '<i class="icon-warning-sign"></i> Musisz wybrać kategorię realizacji.';
				}
				if (empty($_FILES['realizacja']['name'][0])) {
					$error[] = '<i class="icon-warning-sign"></i> Nie dodałeś żadnych zdjęć.';
				} else {
					foreach ($_FILES['realizacja']['name'] as $key => $name) {
						if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'jpg') {
							$error[] = "W przypadku pliku <strong>$name</strong>  rozszerzenie ~.jpg byłoby bardziej na miejscu.";
						}
					}
				}
				//zdjęcie główne
				if (!empty($_FILES['projekt']['name']) && Photo::extension($_FILES['projekt']['name']) != 'jpg') {
					$error[] = "<i class='icon-warning-sign'></i> Plik zdjęcia głównego <strong>{$_FILES['projekt']['name']}</strong> powinien mieć rozszerzenie jpg.";
				}
				Session::set('nazwa_projektu', $nazwa_projektu);
				Session::set('kategoria_realizacji', $kategoria_realizacji);
			}

			if ($url_kategorii !== 'realizacje') {

				if (empty($opis_projektu) || trim($opis_projektu) == '') {
					$error[] = '<i class="icon-warning-sign"></i> Opis projektu nie może być pusty.';
				}

				if ((int) $_POST['powuzczmieszkalnej'] === 0) {
					$error[] = '<i class="icon-warning-sign"></i> Jeśłi chodzi o dane podstawowe, to musisz wpisać przynajmniej powierzchnię użytkową części mieszkalnej.';
				}

				if ((bool) $_POST['typ_projektu'] === false) {
					$error[] = '<i class="icon-warning-sign"></i> Typ projektu musi być wybrany.';
				}

				//plik pdf
				if (!empty($_FILES['pdf']['name']) && Photo::extension($_FILES['pdf']['name']) != strtolower('pdf')) {
					$error[] = "<i class='icon-warning-sign'></i> Plik PDF <strong>{$_FILES['pdf']['name']}</strong> powinien mieć rozszerzenie jpg.";
				}

				//zdjęcie główne
				if (!empty($_FILES['projekt']['name']) && Photo::extension($_FILES['projekt']['name']) != 'jpg') {
					$error[] = "<i class='icon-warning-sign'></i> Plik zdjęcia głównego <strong>{$_FILES['projekt']['name']}</strong> powinien mieć rozszerzenie jpg.";
				}

				//przekrój
				if (!empty($_FILES['przekroj']['name']) && Photo::extension($_FILES['przekroj']['name']) != strtolower('jpg')) {
					$error[] = "<i class='icon-warning-sign'></i> Plik przekroju <strong>{$_FILES['przekroj']['name']}</strong> powinien mieć rozszerzenie jpg.";
				}

				//elewacje
				foreach ($this->elewacje as $elewacja) {
					if (!empty($_FILES[$elewacja]['name']) && Photo::extension($_FILES[$elewacja]['name']) != strtolower('jpg')) {
						$error[] = "<i class='icon-warning-sign'></i> Plik elewacyjny ($elewacja) <strong>{$_FILES[$elewacja]['name']}</strong> powinien mieć rozszerzenie jpg.";
					}
				}

				//rzuty
				$kondygnacje = $this->db->query("SELECT * FROM aranzacje WHERE id_kategorii = {$_POST['id_kategorii']}")->fetchAll();

				foreach ($kondygnacje as $aranzacja) {
					if (!empty($_FILES["{$aranzacja['url_aranzacji']}"]['name']) && Photo::extension($_FILES["{$aranzacja['url_aranzacji']}"]['name']) != strtolower('jpg')) {
						$error[] = "<i class='icon-warning-sign'></i> Plik aranżacji ({$aranzacja['nazwa_aranzacji']}) <strong>{$_POST[$aranzacja['url_aranzacji']]['name']}</strong> powinien mieć rozszerzenie jpg.";
					}
				}
				//sesje dla kondygnacji
				foreach ($kondygnacje as $aranzacja) {
					for ($i = 1; $i <= $ilosc_pomieszczen; $i++) {
						$id_pomieszczenia = (int) $_POST[$aranzacja['url_aranzacji'] . '_pomieszczenie_' . $i];
						Session::set($aranzacja['url_aranzacji'] . '_pomieszczenie_' . $i, $id_pomieszczenia);
						$nr = $_POST[$aranzacja['url_aranzacji'] . '_prefix_dla_wszystkich'];
						Session::set($aranzacja['url_aranzacji'] . '_prefix_dla_wszystkich', $nr);

						if ($id_pomieszczenia > 0) {
							$pow_pomieszczenia = (int) $_POST["{$aranzacja['url_aranzacji']}_powierzchnia_{$i}"];
							Session::set("{$aranzacja['url_aranzacji']}_powierzchnia_{$i}", $pow_pomieszczenia);
							if ($nr === '') {
								Session::destroy($aranzacja['url_aranzacji'] . '_prefix_dla_wszystkich');
								$nr_pomieszczenia = (string) $_POST["{$aranzacja['url_aranzacji']}_prefix_{$i}"] . '.' . $i;
								Session::set("{$aranzacja['url_aranzacji']}_prefix_{$i}", $_POST["{$aranzacja['url_aranzacji']}_prefix_{$i}"]);
							}
						} else {
							Session::destroy("{$aranzacja['url_aranzacji']}_powierzchnia_{$i}");
							Session::destroy("{$aranzacja['url_aranzacji']}_prefix_{$i}");
						}
					}
				}
			}

			if (empty($error)) {

				try {

					$this->db->beginTransaction();

					if ($url_kategorii !== 'realizacje') {

						$columns = implode(', ', $this->posted);

						$placeholders = ':' . implode(', :', $this->posted);

						$sql = "INSERT INTO projekty ($columns) VALUES ($placeholders)";

						$sth = $this->db->prepare($sql);

						$sth->bindParam(':id_kategorii', $id_kategorii, PDO::PARAM_INT);
						$sth->bindParam(':nazwa_projektu', $nazwa_projektu, PDO::PARAM_STR);
						$sth->bindParam(':url_projektu', $url_projektu, PDO::PARAM_STR);
						$sth->bindParam(':opis_projektu', $opis_projektu, PDO::PARAM_STR);
						$sth->bindParam(':id_typu_projektu', $_POST['typ_projektu'], PDO::PARAM_INT);
						$sth->bindParam(':link_pod_zdjeciem', $link_pod_zdjeciem, PDO::PARAM_STR);
						$sth->bindParam(':opis_linku_pod_zdjeciem', $opis_linku_pod_zdjeciem, PDO::PARAM_STR);
						$sth->execute();

						$id_projektu = $this->db->lastInsertId();

						//podstawowe dane
						$podstawowe_dane = array();
						foreach ($this->dane() as $dana) {
							$podstawowe_dane[] = '(' . $id_projektu . ', ' . $_POST['id_danej' . $dana['id']] . ', ' . (int) $_POST[$dana['post_danej']] . ')';
						}

						$podstawowe_dane = (implode(',', $podstawowe_dane));
						$sql = "INSERT INTO wartosci_podst_danych (id_projektu, id_danej, wartosc_danej) VALUES $podstawowe_dane";
						$this->db->prepare($sql)->execute();

						//podstawowe dane materialowe
						$podstawowe_dane_materialowe = array();
						foreach ($this->materialowe() as $dana) {
							$podstawowe_dane_materialowe[] = '(' . $id_projektu . ', ' . $_POST['id_danej_materialowej' . $dana['id']] . ', \'' . htmlspecialchars(preg_replace('/,/', ' ', $_POST[$dana['post_danej_materialowej']])) . '\')';
						}

						$podstawowe_dane_materialowe = (implode(',', $podstawowe_dane_materialowe));
						$sql = "INSERT INTO wartosci_danych_materialowych (id_projektu, id_danej_materialowej, wartosc_danej_materialowej) VALUES $podstawowe_dane_materialowe";
						$this->db->prepare($sql)->execute();

						//aranżacje 
						$values = array();
						foreach ($kondygnacje as $aranzacja) {
							for ($i = 1; $i <= $ilosc_pomieszczen; $i++) {
								$id_pomieszczenia = (int) $_POST[$aranzacja['url_aranzacji'] . '_pomieszczenie_' . $i];
								$nr = $_POST[$aranzacja['url_aranzacji'] . '_prefix_dla_wszystkich'];
								if ($nr !== '') {
									$nr_pomieszczenia = (string) ($nr . '.' . $i);
								}

								if ($id_pomieszczenia > 0) {
									$pow_pomieszczenia = (int) $_POST["{$aranzacja['url_aranzacji']}_powierzchnia_{$i}"];
									if ($nr === '') {
										$nr_pomieszczenia = (string) $_POST["{$aranzacja['url_aranzacji']}_prefix_{$i}"] . '.' . $i;
									}
									if ($pow_pomieszczenia > 0) {
										$values[] = "({$id_pomieszczenia}, {$aranzacja['id']}, $id_projektu, '{$nr_pomieszczenia}', {$pow_pomieszczenia})";
									}
								}
							}
						}

						if (!empty($values)) {
							$values = implode(',', $values);
							$sql = "INSERT INTO powierzchnie (id_pomieszczenia, id_aranzacji, id_projektu, nr_pomieszczenia, pow_pomieszczenia) VALUES $values";
							$sth = $this->db->query($sql);
						}

						//podstawowe dane
						foreach ($this->dane() as $dana) {
							Session::destroy($dana['post_danej']);
						}

						//podstawowe dane materialowe
						foreach ($this->materialowe() as $dana) {
							Session::destroy($dana['post_danej_materialowej']);
						}

						foreach ($kondygnacje as $aranzacja) {
							for ($i = 1; $i <= $ilosc_pomieszczen; $i++) {
								$id_pomieszczenia = (int) $_POST[$aranzacja['url_aranzacji'] . '_pomieszczenie_' . $i];
								Session::destroy($aranzacja['url_aranzacji'] . '_pomieszczenie_' . $i);
								$nr = $_POST[$aranzacja['url_aranzacji'] . '_prefix_dla_wszystkich'];
								Session::destroy($aranzacja['url_aranzacji'] . '_prefix_dla_wszystkich', $nr);

								if ($id_pomieszczenia > 0) {
									$pow_pomieszczenia = (int) $_POST["{$aranzacja['url_aranzacji']}_powierzchnia_{$i}"];
									Session::destroy("{$aranzacja['url_aranzacji']}_powierzchnia_{$i}");
									if ($nr === '') {
										$nr_pomieszczenia = (string) $_POST["{$aranzacja['url_aranzacji']}_prefix_{$i}"] . '.' . $i;
										Session::destroy("{$aranzacja['url_aranzacji']}_prefix_{$i}");
									}
								}
							}
						}
					} else {

						$sql = "INSERT INTO projekty (id_kategorii,nazwa_projektu,url_projektu,link_pod_zdjeciem,opis_linku_pod_zdjeciem) VALUES (:id_kategorii,:nazwa_projektu,:url_projektu,:link_pod_zdjeciem,:opis_linku_pod_zdjeciem)";

						$sth = $this->db->prepare($sql);

						$sth->bindParam(':id_kategorii', $id_kategorii, PDO::PARAM_INT);
						$sth->bindParam(':nazwa_projektu', $nazwa_projektu, PDO::PARAM_STR);
						$sth->bindParam(':url_projektu', $url_projektu, PDO::PARAM_STR);
						$sth->bindParam(':link_pod_zdjeciem', $link_pod_zdjeciem, PDO::PARAM_STR);
						$sth->bindParam(':opis_linku_pod_zdjeciem', $opis_linku_pod_zdjeciem, PDO::PARAM_STR);

						$sth->execute();

						$id_projektu = $this->db->lastInsertId();

						$sql = "INSERT INTO realizacje (id_realizacji, id_kategorii_realizacji) VALUES (:id_realizacji,:id_kategorii_realizacji)";
						$sth = $this->db->prepare($sql);
						$sth->bindParam(':id_realizacji', $id_projektu, PDO::PARAM_INT);
						$sth->bindParam(':id_kategorii_realizacji', $kategoria_realizacji, PDO::PARAM_INT);

						$sth->execute();
						Session::destroy('kategoria_realizacji');
					}

					foreach ($this->posted as $post) {
						Session::destroy($post);
					}

					Session::destroy('error');
					Session::set('success', "<i class=\"icon-ok\"></i> Dodanie $nazwa o nazwie <strong>{$nazwa_projektu}</strong> powiodło się.");

					$this->db->commit();
				} catch (Exception $e) {

					if ($url_kategorii !== 'realizacje') {

						foreach ($this->posted as $post) {
							Session::set($post, $_POST[$post]);
						}
						//podstawowe dane
						foreach ($this->dane() as $dana) {
							Session::set($dana['post_danej'], $_POST[$dana['post_danej']]);
						}
						//podstawowe dane materialowe
						foreach ($this->materialowe() as $dana) {
							Session::set($dana['post_danej_materialowej'], $_POST[$dana['post_danej_materialowej']]);
						}
					} else {
						Session::set('nazwa_projektu', $nazwa_projektu);
						Session::set('kategoria_realizacji', $kategoria_realizacji);
					}

					Session::set('error', $e->getMessage());

					$this->db->rollBack();

					header("Location:/tp/{$url_kategorii}");
				}

				mkdir("media/img/{$url_kategorii}/{$url_projektu}");

				if ($url_kategorii !== 'realizacje') {

					mkdir("media/img/{$url_kategorii}/{$url_projektu}/elewacja");
					mkdir("media/img/{$url_kategorii}/{$url_projektu}/rzut");

					foreach ($this->elewacje as $elewacja) {
						Photo::uploadRename($elewacja, "media/img/{$url_kategorii}/{$url_projektu}/elewacja/");
					}

					foreach ($kondygnacje as $aranzacja) {
						Photo::uploadRename($aranzacja['url_aranzacji'], "media/img/{$url_kategorii}/{$url_projektu}/rzut/");
					}

					Photo::uploadRename('przekroj', "media/img/{$url_kategorii}/{$url_projektu}/rzut/");

					Photo::uploadRename('projekt', "media/img/{$url_kategorii}/{$url_projektu}/");

					Photo::uploadRename('pdf', "media/img/{$url_kategorii}/{$url_projektu}/", $url_projektu);
				} else {
					mkdir("media/img/{$url_kategorii}/{$url_projektu}/realizacja");
					Photo::uploadRename('projekt', "media/img/{$url_kategorii}/{$url_projektu}/");
					Photo::uploadMultipleRename('realizacja', "media/img/{$url_kategorii}/{$url_projektu}/realizacja/");
				}
			} else {
				Session::set('error', implode('<br/>', $error));
			}
		}

		header("Location:/tp/{$url_kategorii}");
	}

	public function edit() {

		$error = array();

		$url_kategorii = htmlspecialchars($_POST['kategoria']);

		$nazwa_projektu = htmlspecialchars($_POST['nazwa_projektu']);

		$url_projektu = Url::create($nazwa_projektu);

		$nazwa = $url_kategorii !== 'realizacje' ? 'Projekt' : 'Realizacja';
		$nazwy = $url_kategorii !== 'realizacje' ? 'projektu' : 'realizacji';

		$url_starego_projektu = Url::create($_POST['url_starego_projektu']);
		$id_starego_projektu = Url::create($_POST['id_starego_projektu']);

		try {
			$sth = $this->db->query("SELECT id FROM projekty WHERE url_projektu = '{$url_projektu}' AND url_projektu != '{$url_starego_projektu}' AND id_kategorii = (SELECT id FROM kategorie WHERE url_kategorii = '{$url_kategorii}')");
			$sth->fetchAll();
			if ($sth->rowCount() > 0) {
				$error[] = "<i class=\"icon-warning-sign\"></i> $nazwa o nazwie <strong>$nazwa_projektu</strong> istnieje już w tej kategorii. Nazwy wewnątrz kategorii nie mogą sie powtarzać.";
			}
		} catch (Exception $e) {
			$error[] = '<i class="icon-warning-sign"></i>' . $e->getMessage();
		}

		if (empty($nazwa_projektu)) {
			$error[] = "<i class='icon-warning-sign'></i> Nazwa $nazwy nie może być pusta.";
		}

		if ($url_kategorii !== 'realizacje') {

			$opis_projektu = $_POST['opis_projektu'];
			$ilosc_pomieszczen = (int) $_POST['ilosc_pomieszczen'];

			if (empty($opis_projektu)) {
				$error[] = "<i class='icon-warning-sign'></i> Opis projektu nie może być pusty.";
			}

			if ((int) $_POST['powuzczmieszkalnej'] === 0) {
				$error[] = '<i class="icon-warning-sign"></i> Jeśłi chodzi o dane podstawowe, to musisz wpisać przynajmniej powierzchnię użytkową części mieszkalnej.';
			}

			if ((bool) $_POST['typ_projektu'] === false) {
				$error[] = '<i class="icon-warning-sign"></i> Typ projektu musi być wybrany.';
			}

			//pdf
			if (!empty($_FILES['pdf']['name']) && Photo::extension($_FILES['pdf']['name']) != strtolower('pdf')) {
				$error[] = "<i class='icon-warning-sign'></i> Plik PDF <strong>{$_FILES['pdf']['name']}</strong> powinien mieć rozszerzenie jpg.";
			}

			//przekrój
			if (!empty($_FILES['przekroj']['name']) && Photo::extension($_FILES['przekroj']['name']) != strtolower('jpg')) {
				$error[] = "<i class='icon-warning-sign'></i> Plik przekroju <strong>{$_FILES['przekroj']['name']}</strong> powinien mieć rozszerzenie jpg.";
			}

			//elewacje
			foreach ($this->elewacje as $elewacja) {
				if (!empty($_FILES[$elewacja]['name']) && Photo::extension($_FILES[$elewacja]['name']) != strtolower('jpg')) {
					$error[] = "<i class='icon-warning-sign'></i> Plik elewacyjny ($elewacja) <strong>{$_FILES[$elewacja]['name']}</strong> powinien mieć rozszerzenie jpg.";
				}
			}

			$kondygnacje = $this->db->query("SELECT * FROM aranzacje WHERE id_kategorii = {$_POST['id_kategorii']}")->fetchAll();
			//rzuty
			foreach ($kondygnacje as $aranzacja) {
				if (!empty($_FILES["{$aranzacja['url_aranzacji']}"]['name']) && Photo::extension($_FILES["{$aranzacja['url_aranzacji']}"]['name']) != strtolower('jpg')) {
					$error[] = "<i class='icon-warning-sign'></i> Plik kondygnacji ({$aranzacja['nazwa_aranzacji']}) <strong>{$_FILES[$aranzacja['url_aranzacji']]['name']}</strong> powinien mieć rozszerzenie jpg.";
				}
			}
		} else {
			$kategoria_realizacji = (int) $_POST['kategoria_realizacji'];
		}

		//zdjęcie główne
		if (!empty($_FILES['projekt']['name']) && Photo::extension($_FILES['projekt']['name']) != strtolower('jpg')) {
			$error[] = "<i class='icon-warning-sign'></i> Plik zdjęcia głównego <strong>{$_FILES['projekt']['name']}</strong> powinien mieć rozszerzenie jpg.";
		}

//        try {
//            //id edytowanego projektu
//            $sth = $this->db->prepare("SELECT id FROM projekty WHERE url_projektu = :url_starego_projektu");
//            $sth->bindParam(":url_starego_projektu", $url_starego_projektu, PDO::PARAM_STR);
//            $sth->execute();
//            $result = $sth->fetch();
//            $id_starego_projektu = (int) $result['id'];
//        } catch (Exception $e) {
//            $error[] = '<i class="icon-warning-sign"><i>' . $e->getMessage();
//        }

		foreach ($this->posted as $post) {
			if (isset($_POST[$post])) {
				Session::set($post, $_POST[$post]);
			}
		}

		if (empty($error)) {

			try {

				$this->db->beginTransaction();

				if ($url_kategorii !== 'realizacje') {

					$set = "";
					foreach ($this->posted as $posted) {
						$set .= "{$posted} = :{$posted}, ";
					}
					$set = rtrim($set, ', ');

					$sql = "UPDATE projekty SET $set WHERE url_projektu = '{$url_starego_projektu}'";

					$sth = $this->db->prepare($sql);
					$sth->bindParam(':id_kategorii', $_POST['id_kategorii'], PDO::PARAM_INT);
					$sth->bindParam(':nazwa_projektu', $nazwa_projektu, PDO::PARAM_STR);
					$sth->bindParam(':url_projektu', $url_projektu, PDO::PARAM_STR);
					$sth->bindParam(':opis_projektu', $opis_projektu, PDO::PARAM_STR);
					$sth->bindParam(':id_typu_projektu', $_POST['typ_projektu'], PDO::PARAM_INT);
					$sth->bindParam(':link_pod_zdjeciem', $_POST['link_pod_zdjeciem'], PDO::PARAM_STR);
					$sth->bindParam(':opis_linku_pod_zdjeciem', $_POST['opis_linku_pod_zdjeciem'], PDO::PARAM_STR);

					$sth->execute();

					$this->db->query("DELETE FROM wartosci_podst_danych WHERE id_projektu = $id_starego_projektu");

					$this->db->query("DELETE FROM wartosci_danych_materialowych WHERE id_projektu = $id_starego_projektu");

					//podstawowe dane
					$podstawowe_dane = array();
					foreach ($this->dane() as $dana) {
						$podstawowe_dane[] = '(' . $id_starego_projektu . ', ' . $_POST['id_danej' . $dana['id']] . ', ' . (int) $_POST[$dana['post_danej']] . ')';
					}

					$podstawowe_dane = (implode(',', $podstawowe_dane));
					$sql = "INSERT INTO wartosci_podst_danych (id_projektu, id_danej, wartosc_danej) VALUES $podstawowe_dane";
					$this->db->prepare($sql)->execute();

					//podstawowe dane materialowe
					$podstawowe_dane_materialowe = array();
					foreach ($this->materialowe() as $dana) {
						$podstawowe_dane_materialowe[] = '(' . $id_starego_projektu . ', ' . $_POST['id_danej_materialowej' . $dana['id']] . ', \'' . htmlspecialchars(preg_replace('/,/', ' ', $_POST[$dana['post_danej_materialowej']])) . '\')';
					}

					$podstawowe_dane_materialowe = (implode(',', $podstawowe_dane_materialowe));
					$sql = "INSERT INTO wartosci_danych_materialowych (id_projektu, id_danej_materialowej, wartosc_danej_materialowej) VALUES $podstawowe_dane_materialowe";
					$this->db->prepare($sql)->execute();

					//powierzchnie według aranżacji 
					$values = array();

					foreach ($kondygnacje as $aranzacja) {
						for ($i = 1; $i <= $ilosc_pomieszczen; $i++) {
							$id_pomieszczenia = (int) $_POST[$aranzacja['url_aranzacji'] . '_pomieszczenie_' . $i];
							$nr = $_POST[$aranzacja['url_aranzacji'] . '_prefix_dla_wszystkich'];
							if ($nr !== '') {
								$nr_pomieszczenia = (string) ($nr . '.' . $i);
							}

							if ($id_pomieszczenia > 0) {
								$pow_pomieszczenia = (int) $_POST["{$aranzacja['url_aranzacji']}_powierzchnia_{$i}"];
								if ($nr === '') {
									$nr_pomieszczenia = (string) $_POST["{$aranzacja['url_aranzacji']}_prefix_{$i}"] . '.' . $i;
								}
								if ($pow_pomieszczenia > 0) {
									$values[] = "({$id_pomieszczenia}, {$aranzacja['id']}, $id_starego_projektu, '{$nr_pomieszczenia}', {$pow_pomieszczenia})";
								}
							}
						}
					}

					$values = implode(',', $values);

					//firstly delete all records from powierzchnie where id_projektu = ...
					$sth = $this->db->prepare("DELETE FROM powierzchnie WHERE id_projektu = :id_starego_projektu");
					$sth->bindParam(":id_starego_projektu", $id_starego_projektu, PDO::PARAM_INT);
					$sth->execute();
					//secondly insert new records where id_produktu = ...
					if (!empty($values)) {
						$sql = "INSERT INTO powierzchnie (id_pomieszczenia, id_aranzacji, id_projektu, nr_pomieszczenia, pow_pomieszczenia) VALUES $values";
						$sth = $this->db->query($sql);
					}

					foreach ($this->posted as $post) {
						Session::destroy($post);
					}
				} else {

					$sql = "UPDATE realizacje SET id_kategorii_realizacji = $kategoria_realizacji WHERE id_realizacji = $id_starego_projektu";
					$sth = $this->db->query($sql);

					$sql = "UPDATE projekty SET nazwa_projektu = :nazwa_projektu, url_projektu = :url_projektu, link_pod_zdjeciem = :link_pod_zdjeciem, opis_linku_pod_zdjeciem = :opis_linku_pod_zdjeciem WHERE url_projektu = '{$url_starego_projektu}'";
					$sth = $this->db->prepare($sql);
					$sth->bindParam(':nazwa_projektu', $nazwa_projektu, PDO::PARAM_STR);
					$sth->bindParam(':url_projektu', $url_projektu, PDO::PARAM_STR);
					$sth->bindParam(':link_pod_zdjeciem', $_POST['link_pod_zdjeciem'], PDO::PARAM_STR);
					$sth->bindParam(':opis_linku_pod_zdjeciem', $_POST['opis_linku_pod_zdjeciem'], PDO::PARAM_STR);
					$sth->execute();

					Session::destroy('nazwa_projektu');
					Session::destroy('kategoria_realizacji');
				}

				Session::destroy('error');
				Session::set('success', "<i class='icon-ok-circle'></i> Modyfikacja $nazwy <strong>{$nazwa_projektu}</strong> dokonana pomyślnie.");

				$this->db->commit();
			} catch (Exception $e) {

				$url_projektu = $url_starego_projektu;
				Session::set('error', "<i class='icon-warning-sign'></i> " . $e->getMessage());

				$this->db->rollBack();
				header("Location:/tp/{$url_kategorii}/{$this->currentPage}/{$url_projektu}");
			}

			//rename directory name if name of project was modified
			if ($url_starego_projektu !== $url_projektu) {
				rename("media/img/{$url_kategorii}/{$url_starego_projektu}", "media/img/{$url_kategorii}/{$url_projektu}");
			}

			if ($url_kategorii !== 'realizacje') {

				//upload new files if they were chosen
				foreach ($this->elewacje as $elewacja) {
					if (!empty($_FILES[$elewacja]['name'])) {
						if (is_file("media/img/{$url_kategorii}/{$url_projektu}/rzut/{$elewacja}.jpg")) {
							unlink("media/img/{$url_kategorii}/{$url_projektu}/elewacja/{$elewacja}.jpg");
						}
						Photo::uploadRename($elewacja, "media/img/{$url_kategorii}/{$url_projektu}/elewacja/");
					}
				}

				//upload new arrangements if they were chosen
				foreach ($kondygnacje as $aranzacja) {
					if (!empty($_FILES[$aranzacja['url_aranzacji']]['name'])) {
						if (is_file("media/img/{$url_kategorii}/{$url_projektu}/rzut/{$aranzacja['url_aranzacji']}.jpg")) {
							unlink("media/img/{$url_kategorii}/{$url_projektu}/rzut/{$aranzacja['url_aranzacji']}.jpg");
						}
						Photo::uploadRename($aranzacja['url_aranzacji'], "media/img/{$url_kategorii}/{$url_projektu}/rzut/");
					}
				}

				//upload przekroj.jpg
				if (!empty($_FILES['przekroj']['name'])) {
					if (is_file("media/img/{$url_kategorii}/{$url_projektu}/przekroj.jpg")) {
						unlink("media/img/{$url_kategorii}/{$url_projektu}/przekroj.jpg");
					}
					Photo::uploadRename('przekroj', "media/img/{$url_kategorii}/{$url_projektu}/rzut/");
				}

				//upload pdf file
				if (!empty($_FILES['pdf']['name'])) {
					if (is_file("media/img/{$url_kategorii}/{$url_projektu}/{$url_projektu}.jpg")) {
						unlink("media/img/{$url_kategorii}/{$url_projektu}/{$url_projektu}.jpg");
					}
					Photo::uploadRename('pdf', "media/img/{$url_kategorii}/{$url_projektu}/", $url_projektu);
				}
			} else {

				if (isset($_POST['usun_zdjecie'])) {
					foreach ($_POST['usun_zdjecie'] as $zdjecie) {
						unlink("media/img/realizacje/$url_projektu/realizacja/$zdjecie");
					}
				}

				Photo::uploadMultipleRename('realizacja', "media/img/{$url_kategorii}/{$url_projektu}/realizacja/");
			}

			//upload projekt.jpg
			if (!empty($_FILES['projekt']['name'])) {
				if (is_file("media/img/{$url_kategorii}/{$url_projektu}/projekt.jpg")) {
					unlink("media/img/{$url_kategorii}/{$url_projektu}/projekt.jpg");
				}
				Photo::uploadRename('projekt', "media/img/{$url_kategorii}/{$url_projektu}/");
			}
		} else {

			foreach ($this->posted as $post) {
				Session::set("$post", "$_POST[$post]");
			}

			$url_projektu = $url_starego_projektu;
			Session::set('error', implode('<br/>', $error));
		}

		header("Location:/tp/{$url_kategorii}/{$this->currentPage}/{$url_projektu}");
	}

	public function delete() {

		if ($_POST) {

			$projectUrl = htmlspecialchars($_POST['delete_project']);

			$projectId = (int) ($_POST['project_id']);

			$categoryId = (int) $_POST['delete_category_id'];

			$categoryUrl = htmlspecialchars($_POST['delete_category_url']);

			try {

				$this->db->beginTransaction();

				$this->db->query("DELETE FROM projekty WHERE url_projektu = '{$projectUrl}' AND id_kategorii = {$categoryId}");
				if ($categoryUrl !== 'realizacje') {
					$this->db->query("DELETE FROM powierzchnie WHERE id_projektu = $projectId");
					$this->db->query("DELETE FROM wartosci_podst_danych WHERE id_projektu = $projectId");
					$this->db->query("DELETE FROM wartosci_danych_materialowych WHERE id_projektu = $projectId");
				} else {
					$this->db->query("DELETE FROM realizacje WHERE id_realizacji = $projectId");
				}
				$this->db->commit();
			} catch (Exception $e) {

				$this->db->rollBack();

				Session::set('error', '<i class="icon-warning-sign"></i>' . $e->getMessage());
			}

			Photo::removeDir("media/img/$categoryUrl/$projectUrl");
			Session::set('success', "<i class='icon-ok'></i> Projekt został usunięty.");
			header("Location:/tp/{$categoryUrl}");
		} else {

			header('Location:/error');
		}
	}

	public function logout() {
		Session::destroy('success');
		Session::destroy('error');
		Session::destroy('twojprojekt');
		Session::destroy('logged');
		header('Location:/zaloguj');
	}

	public function materialowe() {
		return $this->db->query("SELECT * FROM dane_materialowe ORDER BY sort")->fetchAll();
	}

	public function opis() {
		return $this->db->query("SELECT * FROM opisy")->fetch();
	}

	public function zakresDzialalnosci() {
		if ($_POST) {
			try {
				$sth = $this->db->prepare("UPDATE opisy SET zakres_dzialalnosci = :zakres_dzialalnosci, miasto = :miasto, ulica = :ulica, telefon = :telefon, email = :email WHERE id = 1");
				$sth->bindParam(':zakres_dzialalnosci', $_POST['zakres_dzialalnosci'], PDO::PARAM_STR);
				$sth->bindParam(':miasto', $_POST['miasto'], PDO::PARAM_STR);
				$sth->bindParam(':ulica', $_POST['ulica'], PDO::PARAM_STR);
				$sth->bindParam(':telefon', $_POST['telefon'], PDO::PARAM_STR);
				$sth->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
				$sth->execute();
			} catch (Exception $e) {
				Session::set('error', '<i class="icon-warning-sign"></i> ' . $e->getMessage());
			}
		} else {
			header('Location: /error');
		}
		session::set('success', '<i class="icon-ok"></i> Opisy zmieniono pomyślnie.');
		header('Location: /tp');
	}

}