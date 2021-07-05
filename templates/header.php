<!DOCTYPE html>
<html lang="pl">
        <head>
                <meta charset="utf-8" />
                <?
                $project_title = isset($this->project[0]['nazwa_projektu']) && $this->project[0]['nazwa_projektu'] !== null ? $this->project[0]['nazwa_projektu'] . ' | ' : '';
                $category_title = $this->category == 'index' || $this->category === false || $this->category == 'zaloguj' || $this->category == 'error' ? '' : $this->category['nazwa_kategorii'] . ' | ';
                $title = $page_dir == 'tp' ? 'Administrator' : ($page_dir == 'zaloguj' ? 'Logowanie' : ($page_dir == 'error' ? 'Błąd' : 'Twój Projekt'));
                ?>
                <title><?= $project_title . $category_title . $title; ?></title>
                <meta name="keywords" content="architekt wodzisław, architekt rybnik, architekt powiat rybnicki, projektant rybnik, projektant wodzisław, projektant powiat rybnicki, projekty domów wodzisław, projekty domów rybnik, projekty domów powiat rybnicki, projekty domów powiat rybnicki, projekt domu wodzisław, projekt domu rybnik, tanie projekty wodzisław, tanie projekty rybnik, projekty typowe wodzisław, projekty typowe rybnik, projekty typowe powiat rybnicki, projekt wraz z pozwoleniem na budowę, projekt garażu rybnik, projekt garażu wodzisław, projekt garażu powiat rybnicki, projekty dla developera, oferta dla developera, projekty budynków mieszkalnych bliźniaczych, projety budynków wielorodzinnych, projekty budynków wielorodzinnych rybnik, projekty budynków wielorodzinnych wodzisław, projekty budynków usługowych, projekty budynków usługowych rybnik, projekty budynków usługowych wodzisław, projekt domu weselnego, projekt domu weselnego rybnik, projekt domu weselnego powiat rybnicki, projekt hali magazynowej, projekt hali magazynowej rybnik, projekt hali magazynowej wodzisław, projekt hali magazynowej powiat rybnicki, projekty na terenie śląska">
                <meta name="description" content="Twój Projekt | Projekty domów - budynki mieszkalne, domy jednorodzinne, garaże, budynki gospodarcze, Wodzisław Śląski, Rybnik, województwo śląskie. Zapraszamy Państwa do prezentacji projektów naszej pracowni projektowej. Wykonujemy: - projekty budynków mieszkalnych jednorodzinnych - projekty budynków mieszkalnych wielorodzinnych - projekty budynków usługowych - projekty budyków przemysłowych i magazynowych - projekty garaży, budynków gospodarczych, budynków inwentarskich - projekty przydomowych oczyszczalni ścieków">
                <? if ($page_dir === 'tp' || $page_dir === 'zaloguj' || $page_dir === 'error'): ?>
                        <meta name="robots" content="noindex, nofollow">
                <? else: ?>
                        <meta name="robots" content="index, follow">
                <? endif; ?>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta name="google-site-verification" content="BcheFTPm7ToF5gzbdotbDrPT1hdS95L2pdJwJXLrznM" />
                <link rel="shortcut icon" href="/media/img/ico/favicon.ico">
                <link rel="stylesheet" href="/media/js/shadowbox/shadowbox.css">
                <link rel="stylesheet" href="/media/css/bootstrap.min.css">
                <link rel="stylesheet" href="/media/css/bootstrap-responsive.min.css">
                <link rel="stylesheet" href="/media/css/custom.css">
        </head>
        <body>
                <div class="container-fluid">
                        <? if ($page_dir === 'tp' || $page_dir === 'zaloguj' || $page_dir === 'error'): ?>
                                <? if (Session::get('error')): ?>
                                        <div class="alert alert-block alert-error fade in">
                                                <button id="error" class="close" data-dismiss="alert" type="button">×</button>
                                                <h1 class="alert-heading">Błąd!</h1>
                                                <p><?= Session::get('error'); ?></p>
                                        </div>
                                <? endif; ?>
                                <? if (Session::get('success')): ?>
                                        <div class="alert alert-block alert-success fade in">
                                                <button id="success" class="close" data-dismiss="alert" type="button">×</button>
                                                <h1 class="alert-heading">Ok!</h1>
                                                <p><?= Session::get('success'); ?></p>
                                        </div>
                                <? endif; ?>
                        <? endif; ?>
                        <? if ($page_dir !== "zaloguj" && $page_dir !== "tp" && $page_dir !== 'error'): ?>
                                <div class="row-fluid">
                                        <div class="span5">
                                                <a href="http://www.twojprojekt.com.pl/" class="brand" title="Twój Projekt"><img style="margin-bottom:13px;" src="/media/img/ico/logo_twojprojekt.png" alt="Twój Projekt" /></a>
                                                <? if (Session::get('logged') && Session::get('twojprojekt')): ?>
                                                        <div>
                                                                <a rel="nofollow" href="/tp" target="_blank" title="Administrator"><i class="icon-user"></i></a>
                                                        </div>
                                                <? endif; ?>
                                        </div>
                                        <?php $kontakt = "<address><div>{$this->opis['osoba']}<br>{$this->opis['miasto']}<br>{$this->opis['ulica']}</div><br/><div>{$this->opis['telefon']}<br /><a href='mailto:{$this->opis['email']}'>{$this->opis['email']}</a></div></address>"; ?>
                                        <div class="span7 navbar">
                                                <div class="span12">
                                                        <div class="navbar-inner">
                                                                <div class="container">
                                                                        <a style="margin-bottom:5px;" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                                                                                <span class="icon-align-justify"></span>

                                                                        </a>

                                                                        <div class="nav-collapse collapse">
                                                                                <ul class="nav">
                                                                                        <? foreach ($this->categories as $id => $kategoria): ?>                                   
                                                                                                <li<? if (isset($this->category['url_kategorii']) && $this->category['url_kategorii'] === $kategoria['url_kategorii']): ?> class="active"<? endif; ?>>
                                                                                                        <? if (isset($kategoria['url_kategorii']) && in_array($kategoria['url_kategorii'], $this->projektyKategorii)): ?>
                                                                                                                <a href="/oferta/<?= $kategoria['url_kategorii'] ?><?= isset($this->category['url_kategorii']) && $this->category['url_kategorii'] === $kategoria['url_kategorii'] ? ''/* . $this->page */ : null; ?>" title="<?= $kategoria['nazwa_kategorii'] ?>"><?= $kategoria['skrocona_nazwa_kategorii'] ?></a>
                                                                                                        <? else: ?>
                                                                                                                <a title="Brak projektów w tej kategorii..." onclick="return false;" href="/oferta/<?= $kategoria['url_kategorii'] ?><?= isset($this->category['url_kategorii']) && $this->category['url_kategorii'] === $kategoria['url_kategorii'] ? ''/* . $this->page */ : null; ?>"><?= $kategoria['skrocona_nazwa_kategorii'] ?></a>
                                                                                                        <? endif; ?>
                                                                                                </li>
                                                                                        <? endforeach; ?>
                                                                                        <li>
                                                                                                <a class="contact" data-content="<?php echo $kontakt; ?>" data-toggle="popover" data-placement="bottom" data-original-title="<img src='/media/img/ico/logo-twojprojekt-f7f7f7.jpg' alt='Dane kontaktowe' />">Kontakt</a>
                                                                                        </li>
                                                                                </ul>
                                                                        </div>

                                                                </div>
                                                        </div>
                                                        <hr />
                                                </div>
                                        </div>
                                </div>
                        <? endif; ?>
                        <? if ($page_dir === 'tp'): ?>
                                <div class="row-fluid">
                                        <div class="span2">
                                                <a target="_blank" href="/" class="brand" rel="nofollow" title="Zobacz witrynę">
                                                        <img src="/media/img/ico/logo_twojprojekt.png" alt="Twój Projekt" />
                                                </a>
                                        </div>
                                        <div class="span10 navbar navbar-inverse">
                                                <div class="navbar-inner">
                                                        <div class="container">
                                                                <ul class="nav">
                                                                        <li<? if ($page === 'ustawienia'): ?> class="active"<? endif; ?>>
                                                                                <a href="/tp/ustawienia" rel="nofollow">Ustawienia</a>
                                                                        </li>
                                                                        <li>
                                                                                <span class="divider-vertical"></span>
                                                                        </li>

                                                                        <? foreach ($this->categories as $kategoria): ?>
                                                                                <li<? if ($this->category['url_kategorii'] === $kategoria['url_kategorii']): ?> class="active"<? endif; ?>>
                                                                                        <a rel="nofollow" href="/tp/<?= $kategoria['url_kategorii'] ?>"><?= $kategoria['skrocona_nazwa_kategorii'] ?></a>
                                                                                </li>
                                                                        <? endforeach; ?>
                                                                        <li>                                        
                                                                                <span class="divider-vertical"></span>
                                                                        </li>
                                                                        <li>
                                                                                <a rel="nofollow" href="/tp/logout"><i class=""></i>Wyloguj</a>
                                                                        </li>
                                                                </ul>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        <? endif; ?>
