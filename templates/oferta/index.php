<?php
defined('_PHPIOTR') || die('Restricted access');

if ($this->projectId == null):
    ?>
    <div class="row-fluid">
        <div class="span3">
            <div>
                <form action="/oferta/sortowanie" class="form-inline" method="post">
                    <fieldset>
                        <select class="input-medium" id="sort" name="sort">
                            <option value="1"<? if ((Session::get('sortFront') == '1' && $this->category['url_kategorii'] !== 'realizacje') || (Session::get('sorteFront') == '1' && $this->category['url_kategorii'] === 'realizacje')): ?> selected="selected"<? endif; ?>>Nazwa &and;</option>
                            <option value="2"<? if ((Session::get('sortFront') == '2' && $this->category['url_kategorii'] !== 'realizacje') || (Session::get('sorteFront') == '2' && $this->category['url_kategorii'] === 'realizacje')): ?> selected="selected"<? endif; ?>>Nazwa &or;</option>
                            <? if ($this->category['url_kategorii'] !== 'realizacje'): ?>
                                <option value="3"<? if (Session::get('sortFront') == '3' || !Session::get('sortFront')): ?> selected="selected"<? endif; ?>>Powierzchnia &and;</option>
                                <option value="4"<? if (Session::get('sortFront') == '4'): ?> selected="selected"<? endif; ?>>Powierzchnia &or;</option>
                                <option value="5"<? if (Session::get('sortFront') == '5'): ?> selected="selected"<? endif; ?>>Parterowe</option>
                                <option value="6"<? if (Session::get('sortFront') == '6'): ?> selected="selected"<? endif; ?>>Podpiwniczone</option>
                                <option value="7"<? if (Session::get('sortFront') == '7'): ?> selected="selected"<? endif; ?>>Z poddaszem użytkowym</option>
                            <? endif; ?>
                        </select>
                        <input name="id_kategorii" type="hidden" value="<?= $this->category['id']; ?>" />
                        <input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
                        <input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
                        <? if ($this->projectUrl !== null): ?>
                            <input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
                        <? endif; ?>
                        <button type="submit" class="btn pull-right">Sortuj</button>
                    </fieldset>
                </form>
            </div>
            <div>
                <ul class="nav nav-tabs nav-stacked">
                    <?
                    if ($this->projects):
                        foreach ($this->projects as $project):
                            $area = $this->category['url_kategorii'] !== 'realizacje' ? ' ' . number_format($project['wartosc_danej'] / 100, 2, ',', ' ') . ' m<sup>2</sup>' : '';
                            ?>
                            <li><a href="/oferta/<?= $this->category['url_kategorii']; ?>/<?= $this->currentPage ?>/<?= $project['url_projektu']; ?>"><?= '<strong>' . strtoupper($project['nazwa_projektu']) . '</strong>' . $area; ?></a></li>
                            <?
                        endforeach;
                    endif;
                    ?>
                </ul>
            </div>
        </div>
        <div class="span9">
            <div class="span12">
                <? if ($this->pagination): ?>
                    <div class="pagination pagination-small pull-left">
                        <?= $this->pagination; ?>
                    </div>
                <? endif; ?>
                <form action="/oferta/znaleziono" class="form-search pull-right" method="post">
                    <input id="search-input" name="search-input" placeholder="Znajdź projekt..." type="text" class="input-medium search-query">
                    <input type="hidden" name="url_kategorii" value="<?= $this->category['url_kategorii']; ?>" />
                    <input type="hidden" name="current_page" value="<?= $this->currentPage ?>" />
                    <input type="hidden" name="pages" value="<?= $this->pages ?>" />
                    <input type="hidden" name="total" value="<?= $this->total ?>" />
                    <div class="btn-group">
                        <button id="search-btn" type="submit" class="btn">Szukaj</button>
                        <? $search = "<div class='well well-small'><input type='text' class='input-small' placeholder='od' id='search_od' name='od'/></div><div style='margin-bottom: 0;' class='well well-small'><input type='text' class='input-small' placeholder='do' id='search_do' name='do'/></div>"; ?>
                        <span class="advanced_search btn dropdown-toggle" data-content="<?= $search; ?>" data-toggle="popover" data-placement="bottom" data-original-title="wg powierzchni m<sup>2</sup>">
                            <span class="caret"></span>
                        </span>
                    </div>
                </form>
            </div>
            <? if (Session::get('search')): ?>
                <div class="span12 alert alert-block alert-error fade in" style="margin-left:0">
                    <button id="search" class="close" data-dismiss="alert" type="button">×</button>
                    <h2 style="font-weight:normal" class="alert-heading"><?= Session::get('search'); ?></h2>
                </div>
            <? endif; ?>
            <? if (Session::get('search-success')): ?>
                <div class="span12 alert alert-block alert-warning fade in" style="margin-left:0">
                    <button id="search-success" class="close" data-dismiss="alert" type="button">×</button>
                    <h2 style="font-weight:normal" class="alert-heading"><?= Session::get('search-success'); ?></h2>
                    <div class="well-small well">
                        <div class="accordion" id="accordion2">
                            <?
                            $word = Session::get('highlight') ? Session::get('highlight') : null;
                            $i = 0;
                            foreach (Session::get('znalezione') as $znaleziony):
                                ?>

                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle text-warning" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?= $znaleziony['url_projektu'] ?>"><?= strtoupper(preg_replace("%$word%i", '<strong>' . $word . '</strong>', $znaleziony['nazwa_projektu'])); ?> <strong><?= $znaleziony['sformatowana_powierzchnia']; ?> m<sup>2</sup></strong>
                                            <span class="found-category pull-right"><small><?= $znaleziony['nazwa_kategorii']; ?></small></span>
                                        </a>
                                    </div>
                                    <div id="collapse<?= $znaleziony['url_projektu'] ?>" class="accordion-body collapse">
                                        <div class="accordion-inner">

                                            <div class="row-fluid">
                                                <div class="span3">
                                                    <? if (is_file("media/img/{$znaleziony['url_kategorii']}/{$znaleziony['url_projektu']}/projekt.jpg")): ?>

                                                        <a title="<?= $znaleziony['nazwa_projektu']; ?>" rel="shadowbox" href="/media/img/<?= $znaleziony['url_kategorii'] ?>/<?= $znaleziony['url_projektu'] ?>/projekt.jpg">
                                                            <img alt="<?= $znaleziony['nazwa_projektu']; ?>" src="/media/img/<?= $znaleziony['url_kategorii'] ?>/<?= $znaleziony['url_projektu'] ?>/projekt.jpg" />
                                                        </a>

                                                        <?
                                                    endif;
                                                    $page = Session::get('pageForEachProject');
                                                    $page = (int) $page[$i];
                                                    ?>
                                                    <div>
                                                        <a href="/oferta/<?= $znaleziony['url_kategorii']; ?>/<?= $page; ?>/<?= $znaleziony['url_projektu'] ?>"><strong>Zobacz projekt</strong></a>
                                                        <br />
                                                        <a href="/oferta/<?= $znaleziony['url_kategorii']; ?>/<?= $page; ?>"><span><small>Zobacz kategorię</small></span></a>
                                                    </div>
                                                </div>
                                                <div class="span9">
                                                    <?
                                                    if (!Session::get('highlight')) {
                                                        echo $znaleziony['opis_projektu'];
                                                    } else {
                                                        echo preg_replace("/$word/", "<strong class='text-error'>$word</strong>", $znaleziony['opis_projektu']);
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?
                                $i++;
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            <? endif; ?>
            <div class="clearfix"></div>
            <? for ($i = 0; $i < $this->rows; $i++): ?>            
                <ul class="thumbnails">

                    <? for ($x = 0; $x < 3; $x++): ?>
                        <? if (!empty($this->projectsInOneRow[$i][$x])): ?>
                            <li class="span<?= $this->spanner; ?>">
                                <a title="<?= mb_strtoupper($this->projectsInOneRow[$i][$x]['nazwa_projektu'], 'utf-8'); ?>" href="/oferta/<?= $this->category['url_kategorii']; ?>/<?= $this->currentPage . '/' . $this->projectsInOneRow[$i][$x]['url_projektu']; ?>" class="thumbnail custom-rel">
                                    <? if (file_exists("media/img/{$this->category['url_kategorii']}/{$this->projectsInOneRow[$i][$x]['url_projektu']}/projekt.jpg")): ?>
                                        <img alt="<?= mb_strtoupper($this->projectsInOneRow[$i][$x]['nazwa_projektu'], 'utf-8'); ?>" src="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->projectsInOneRow[$i][$x]['url_projektu']; ?>/projekt.jpg" />
                                    <? else: ?>
                                        <img alt="<?= mb_strtoupper($this->projectsInOneRow[$i][$x]['nazwa_projektu'], 'utf-8'); ?>" src="/media/img/ico/tp.jpg"/>
                                    <?
                                    endif;
                                    $area = $this->category['url_kategorii'] !== 'realizacje' ? ' ' . number_format($this->projectsInOneRow[$i][$x]['wartosc_danej'] / 100, 2, ',', ' ') . ' m<sup>2</sup>' : '';
                                    $cat = $this->category['url_kategorii'] !== 'realizacje' ? '' : "<span style=\"right:0\" class=\"btn btn-mini btn-warning right-btn custom-abs\">{$this->categories[$this->projectsInOneRow[$i][$x]['id_kategorii_realizacji'] - 1]['nazwa_kategorii']}</span>";
                                    ?>
                                    <span class="btn btn-mini btn-danger left-btn custom-abs"><?= '<strong>' . mb_strtoupper($this->projectsInOneRow[$i][$x]['nazwa_projektu'], 'utf-8') . '</strong>' . $area; ?></span>
                                    <?= $cat; ?>
                                </a>
                            </li>
                        <? endif; ?>
                    <? endfor; ?>
                </ul>
            <? endfor; ?>
        </div>
    </div>
<? else: ?>
    <? if ($this->category['url_kategorii'] !== 'realizacje'): ?>
        <div class="row-fluid">
            <div class="span4">
                <h2>
                    <? if (file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/{$this->project[0]['url_projektu']}.pdf")): ?>
                        <a data-placement="right" data-toggle="tooltip" title="Wersja PDF" class="text-success" target="_blank" href="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu'] ?>/<?= $this->project[0]['url_projektu'] ?>.pdf"><?= strtoupper($this->project[0]['nazwa_projektu']) . ' ' . number_format((int) $this->project[0]['wartosc_danej'] / 100, 2, ',', ' '); ?> m<sup>2</sup></a>
                    <? else: ?>
                        <a href="#" data-placement="right" data-toggle="tooltip" title="Wersja PDF niedostępna" class="text-success"><?= strtoupper($this->project[0]['nazwa_projektu']) . ' ' . number_format((int) $this->project[0]['wartosc_danej'] / 100, 2, ',', ' '); ?> m<sup>2</sup></span></a>
                    <? endif; ?>
                </h2>

                <? if (file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/projekt.jpg")): ?>
                        <!--                <a href="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu'] ?>/projekt.jpg" title="<?= $this->project[0]['nazwa_projektu'] ?> 127,65 m2">        -->
                    <a rel="shadowbox[projekt]" href="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu'] ?>/projekt.jpg" title="<?= $this->project[0]['nazwa_projektu'] ?> 127,65 m2">        
                        <img alt="<?= $this->project[0]['nazwa_projektu'] . ' ' . number_format((int) $this->project[0]['wartosc_danej'] / 100, 2, ',', ' '); ?> m2" src="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu'] ?>/projekt.jpg">
                    </a>
                <? else: ?>
                    <img alt="<?= $this->project[0]['nazwa_projektu'] . ' ' . number_format((int) $this->project[0]['wartosc_danej'] / 100, 2, ',', ' '); ?> m2" src="/media/img/ico/tp.jpg"/>
                <? endif; ?>

                <? if ($this->project[0]['link_pod_zdjeciem']): ?>
                    <div>
                        <a href="http://<?= str_replace('http://', '', $this->project[0]['link_pod_zdjeciem']); ?>" target="_blank"<? if ($this->project[0]['opis_linku_pod_zdjeciem']): ?>title="<?= $this->project[0]['opis_linku_pod_zdjeciem']; ?>"<? endif; ?>><?= $this->project[0]['opis_linku_pod_zdjeciem'] ? $this->project[0]['opis_linku_pod_zdjeciem'] : str_replace('http://','',$this->project[0]['link_pod_zdjeciem']) ?></a>
                    </div>
                <? endif; ?>
            </div>
            <div class="span8">
                <div class="span12">
                    <? if ($this->pagination): ?>
                        <div class="pagination pagination-small pull-left">
                            <?= $this->pagination; ?>
                        </div>
                    <? endif; ?>

                    <form action="/oferta/znaleziono" class="form-search pull-right" method="post">
                        <input id="search-input" name="search-input" placeholder="Znajdź projekt..." type="text" class="input-medium search-query">
                        <input type="hidden" name="url_kategorii" value="<?= $this->category['url_kategorii']; ?>" />
                        <input type="hidden" name="url_projektu" value="<?= $this->projectUrl; ?>" />
                        <input type="hidden" name="current_page" value="<?= $this->currentPage ?>" />
                        <input type="hidden" name="pages" value="<?= $this->pages ?>" />
                        <input type="hidden" name="total" value="<?= $this->total ?>" />
                        <div class="btn-group">
                            <button id="search-btn" type="submit" class="btn">Szukaj</button>
                            <? $search = "<div class='well well-small'><input type='text' class='input-small' placeholder='od' id='search_od' name='od'/></div><div style='margin-bottom: 0;' class='well well-small'><input type='text' class='input-small' placeholder='do' id='search_do' name='do'/></div>"; ?>
                            <span class="advanced_search btn dropdown-toggle" data-content="<?= $search; ?>" data-toggle="popover" data-placement="bottom" data-original-title="wg powierzchni m<sup>2</sup>">
                                <span class="caret"></span>
                            </span>
                        </div>
                    </form>
                </div>
                <? if (Session::get('search')): ?>
                    <div class="span12 alert alert-block alert-error fade in" style="margin-left:0">
                        <button id="search" class="close" data-dismiss="alert" type="button">×</button>
                        <h2 style="font-weight:normal" class="alert-heading"><?= Session::get('search'); ?></h2>
                    </div>
                <? endif; ?>
                <? if (Session::get('search-success')): ?>
                    <div class="span12 alert alert-block alert-warning fade in" style="margin-left:0">
                        <button id="search-success" class="close" data-dismiss="alert" type="button">×</button>
                        <h2 style="font-weight:normal" class="alert-heading"><?= Session::get('search-success'); ?></h2>
                        <div class="well-small well">
                            <div class="accordion" id="accordion2">
                                <?
                                $word = Session::get('highlight') ? Session::get('highlight') : null;
                                $i = 0;
                                foreach (Session::get('znalezione') as $znaleziony):
                                    ?>

                                    <div class="accordion-group">
                                        <div class="accordion-heading">
                                            <a class="accordion-toggle text-warning" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?= $znaleziony['url_projektu'] ?>"><?= strtoupper(preg_replace("%$word%i", '<strong>' . $word . '</strong>', $znaleziony['nazwa_projektu'])); ?> <strong><?= $znaleziony['sformatowana_powierzchnia']; ?> m<sup>2</sup></strong>
                                                <span class="found-category pull-right"><small><?= $znaleziony['nazwa_kategorii']; ?></small></span>
                                            </a>
                                        </div>
                                        <div id="collapse<?= $znaleziony['url_projektu'] ?>" class="accordion-body collapse">
                                            <div class="accordion-inner">

                                                <div class="row-fluid">
                                                    <div class="span3">
                                                        <? if (is_file("media/img/{$znaleziony['url_kategorii']}/{$znaleziony['url_projektu']}/projekt.jpg")): ?>

                                                            <a title="<?= $znaleziony['nazwa_projektu']; ?>" rel="shadowbox" href="/media/img/<?= $znaleziony['url_kategorii'] ?>/<?= $znaleziony['url_projektu'] ?>/projekt.jpg">
                                                                <img alt="<?= $znaleziony['nazwa_projektu']; ?>" src="/media/img/<?= $znaleziony['url_kategorii'] ?>/<?= $znaleziony['url_projektu'] ?>/projekt.jpg" />
                                                            </a>

                                                            <?
                                                        endif;
                                                        $page = Session::get('pageForEachProject');
                                                        $page = (int) $page[$i];
                                                        ?>
                                                        <div>
                                                            <a href="/oferta/<?= $znaleziony['url_kategorii']; ?>/<?= $page; ?>/<?= $znaleziony['url_projektu'] ?>"><strong>Zobacz projekt</strong></a>
                                                            <br />
                                                            <a href="/oferta/<?= $znaleziony['url_kategorii']; ?>/<?= $page; ?>"><span><small>Zobacz kategorię</small></span></a>
                                                        </div>
                                                    </div>
                                                    <div class="span9">
                                                        <?
                                                        if (!Session::get('highlight')) {
                                                            echo $znaleziony['opis_projektu'];
                                                        } else {
                                                            echo preg_replace("/$word/", "<strong class='text-error'>$word</strong>", $znaleziony['opis_projektu']);
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                    $i++;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
                <div class="clearfix"></div>
                <p style="text-align:justify">
                    <small><?= $this->project[0]['opis_projektu']; ?></small>
                </p>
                <div class="span7" style="margin-left:0">
                    <? if ($this->wartosciDanych): ?>
                        <table class="table table-condensed table-hover table-bordered">
                            <thead>
                                <tr class="text-error">
                                    <th colspan="2">Podstawowe dane</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach ($this->wartosciDanych as $rubryka): ?>
                                    <? if ($rubryka['wartosc_danej'] > 0): ?>
                                        <tr>
                                            <td><small><?= $rubryka['nazwa_danej']; ?></small</td>
                                            <? if ($rubryka['id_danej'] != 8): ?>
                                                <td><small><?= number_format((int) $rubryka['wartosc_danej'] / 100, 2, ',', ' '); ?> m<sup><?= ($rubryka['id_danej'] == 6) ? null : ($rubryka['id_danej'] == 5 ? 3 : 2); ?></sup></small></td>
                                            <? else: ?>
                                                <td><small><?= (int) $rubryka['wartosc_danej']; ?>°</small></td>
                                            <? endif; ?>
                                        </tr>
                                    <? endif; ?>
                                <? endforeach; ?>
                            </tbody>
                        </table>
                    <? endif; ?>
                </div>
                <div class="span5">
                    <? if ($this->wartosciMaterialowych): ?>
                        <table class="table table-condensed table-hover table-bordered">
                            <thead>
                                <tr class="text-error">
                                    <th colspan="2">Podstawowe dane materiałowe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach ($this->wartosciMaterialowych as $rubryka): ?>
                                    <? if (trim($rubryka['wartosc_danej_materialowej']) != ""): ?>
                                        <tr>
                                            <td><small><?= $rubryka['nazwa_danej_materialowej'] ?></small></td>
                                            <td><small><?= $rubryka['wartosc_danej_materialowej'] ?></small></td>
                                        </tr>
                                    <? endif; ?>
                                <? endforeach; ?>
                            </tbody>
                        </table>
                    <? endif; ?>
                </div>
            </div>
        </div>

        <? if (!empty($this->scan)): ?>

            <div class="row-fluid" style="margin-top:20px;">            

                <? foreach ($this->aranzacje as $a): ?>
                    <? if (in_array("{$a['url_aranzacji']}.jpg", $this->scan)): ?>
                        <div class="span<?= $this->span ?>">
                            <div id="<?= $a['url_aranzacji'] ?>" style="display:none;">
                                <? if (file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/rzut/{$a['url_aranzacji']}.jpg")): ?>
                                    <img alt="<?= $a['nazwa_aranzacji'] ?>" src="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu']; ?>/rzut/<?= $a['url_aranzacji'] ?>.jpg" />
                                <? else: ?>
                                    <img alt="<?= $a['nazwa_aranzacji'] ?>" src="/media/img/ico/tp.jpg" />
                                <? endif; ?>
                                <? if (!empty($this->powierzchnie)): ?>
                                    <table class="table table-condensed table-hover table-bordered">
                                        <thead>
                                            <tr class="text-error">
                                                <th colspan="2">Powierzchnia użytkowa</th>
                                                <th>m<sup>2</sup></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <? $razem = 0; ?>
                                            <? foreach ($this->powierzchnie as $p): ?>

                                                <? if ($a['id'] === $p['id_aranzacji']): ?>
                                                    <tr>
                                                        <td><?= $p['nr_pomieszczenia']; ?></td>
                                                        <td><?= $p['nazwa_pomieszczenia']; ?></td>
                                                        <td><?= number_format((float) $p['pow_pomieszczenia'] / 100, 2, ',', ' '); ?></td>
                                                        <? $razem += $p['pow_pomieszczenia']; ?>
                                                    </tr>
                                                <? endif; ?>

                                            <? endforeach; ?>
                                        <td colspan="2">RAZEM</td>
                                        <td><?= number_format((float) $razem / 100, 2, ',', ' '); ?></td>
                                        </tbody>
                                    </table>
                                <? endif; ?>
                            </div>
                            <a title="<?= $a['nazwa_aranzacji'] ?>" rel="shadowbox[projekt];height=600;width=800" href="#<?= $a['url_aranzacji'] ?>">
                                <? if (file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/rzut/{$a['url_aranzacji']}.jpg")): ?>
                                    <img alt="<?= $a['nazwa_aranzacji'] ?>" src="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu']; ?>/rzut/<?= $a['url_aranzacji'] ?>.jpg" />
                                <? else: ?>
                                    <img alt="<?= $a['nazwa_aranzacji'] ?>" src="/media/img/ico/tp.jpg" />
                                <? endif; ?>
                            </a>
                        </div>
                    <? endif; ?>
                <? endforeach; ?>

                <div class="span<?= $this->span ?>">

                    <? if (file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/rzut/przekroj.jpg")): ?>
                        <a title="Przekrój" rel="shadowbox[projekt]" href="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu']; ?>/rzut/przekroj.jpg">        
                            <img alt="Przekrój" src="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu']; ?>/rzut/przekroj.jpg" />
                        </a>
                    <? else: ?>
                        <img alt="<?= $a['nazwa_aranzacji'] ?>" src="/media/img/ico/tp.jpg" />
                    <? endif; ?>

                </div>

            </div>

        <? endif; ?>

        <div class="row-fluid" style="margin-top:20px;">
            <? foreach ($this->elewacje as $elewacja) : ?>
                <div class="span3">

                    <? if (file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/elewacja/{$elewacja['skrot_elewacji']}.jpg")): ?>
                        <a title="<?= $elewacja['nazwa_elewacji'] ?>" rel="shadowbox[projekt]" href="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu']; ?>/elewacja/<?= $elewacja['skrot_elewacji'] ?>.jpg">        
                            <img alt="<?= $elewacja['nazwa_elewacji'] ?>" src="/media/img/<?= $this->category['url_kategorii']; ?>/<?= $this->project[0]['url_projektu']; ?>/elewacja/<?= $elewacja['skrot_elewacji'] ?>.jpg" />
                        </a>
                    <? else: ?>
                        <img alt="<?= $elewacja['nazwa_elewacji'] ?>" src="/media/img/ico/tp.jpg" />
                    <? endif; ?>

                </div>
            <? endforeach; ?>
        </div>
    <? else: ?>
        <div class="row-fluid">
            <div class="span4">
                <h2 class="text-success" style="text-decoration:underline"><?= strtoupper($this->project[0]['nazwa_projektu']); ?></h2>
				<? if ($this->project[0]['link_pod_zdjeciem']): ?>
                    <div>
                        <a href="http://<?= str_replace('http://', '', $this->project[0]['link_pod_zdjeciem']); ?>" target="_blank"<? if ($this->project[0]['opis_linku_pod_zdjeciem']): ?>title="<?= $this->project[0]['opis_linku_pod_zdjeciem']; ?>"<? endif; ?>><?= $this->project[0]['opis_linku_pod_zdjeciem'] ? $this->project[0]['opis_linku_pod_zdjeciem'] : str_replace('http://','',$this->project[0]['link_pod_zdjeciem']) ?></a>
                    </div>
                <? endif; ?>
            </div>
            <div class="span8">
                <div class="span12">
                    <? if ($this->pagination): ?>
                        <div class="pagination pagination-small pull-left">
                            <?= $this->pagination; ?>
                        </div>
                    <? endif; ?>
                    <form action="/oferta/znaleziono" class="form-search pull-right" method="post">
                        <input id="search-input" name="search-input" placeholder="Znajdź projekt..." type="text" class="input-medium search-query">
                        <input type="hidden" name="url_kategorii" value="<?= $this->category['url_kategorii']; ?>" />
                        <input type="hidden" name="url_projektu" value="<?= $this->projectUrl; ?>" />
                        <input type="hidden" name="current_page" value="<?= $this->currentPage ?>" />
                        <input type="hidden" name="pages" value="<?= $this->pages ?>" />
                        <input type="hidden" name="total" value="<?= $this->total ?>" />
                        <div class="btn-group">
                            <button id="search-btn" type="submit" class="btn">Szukaj</button>
                            <? $search = "<div class='well well-small'><input type='text' class='input-small' placeholder='od' id='search_od' name='od'/></div><div style='margin-bottom: 0;' class='well well-small'><input type='text' class='input-small' placeholder='do' id='search_do' name='do'/></div>"; ?>
                            <span class="advanced_search btn dropdown-toggle" data-content="<?= $search; ?>" data-toggle="popover" data-placement="bottom" data-original-title="wg powierzchni m<sup>2</sup>">
                                <span class="caret"></span>
                            </span>
                        </div>
                    </form>
                </div>
                <? if (Session::get('search')): ?>
                    <div class="span12 alert alert-block alert-error fade in" style="margin-left:0">
                        <button id="search" class="close" data-dismiss="alert" type="button">×</button>
                        <h2 style="font-weight:normal" class="alert-heading"><?= Session::get('search'); ?></h2>
                    </div>
                <? endif; ?>
                <? if (Session::get('search-success')): ?>
                    <div class="span12 alert alert-block alert-warning fade in" style="margin-left:0">
                        <button id="search-success" class="close" data-dismiss="alert" type="button">×</button>
                        <h2 style="font-weight:normal" class="alert-heading"><?= Session::get('search-success'); ?></h2>
                        <div class="well-small well">
                            <div class="accordion" id="accordion2">
                                <?
                                $word = Session::get('highlight') ? Session::get('highlight') : null;
                                $i = 0;
                                foreach (Session::get('znalezione') as $znaleziony):
                                    ?>

                                    <div class="accordion-group">
                                        <div class="accordion-heading">
                                            <a class="accordion-toggle text-warning" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?= $znaleziony['url_projektu'] ?>"><?= strtoupper(preg_replace("%$word%i", '<strong>' . $word . '</strong>', $znaleziony['nazwa_projektu'])); ?> <strong><?= $znaleziony['sformatowana_powierzchnia']; ?> m<sup>2</sup></strong>
                                                <span class="found-category pull-right"><small><?= $znaleziony['nazwa_kategorii']; ?></small></span>
                                            </a>
                                        </div>
                                        <div id="collapse<?= $znaleziony['url_projektu'] ?>" class="accordion-body collapse">
                                            <div class="accordion-inner">

                                                <div class="row-fluid">
                                                    <div class="span3">
                                                        <? if (is_file("media/img/{$znaleziony['url_kategorii']}/{$znaleziony['url_projektu']}/projekt.jpg")): ?>

                                                            <a title="<?= $znaleziony['nazwa_projektu']; ?>" rel="shadowbox" href="/media/img/<?= $znaleziony['url_kategorii'] ?>/<?= $znaleziony['url_projektu'] ?>/projekt.jpg">
                                                                <img alt="<?= $znaleziony['nazwa_projektu']; ?>" src="/media/img/<?= $znaleziony['url_kategorii'] ?>/<?= $znaleziony['url_projektu'] ?>/projekt.jpg" />
                                                            </a>

                                                            <?
                                                        endif;
                                                        $page = Session::get('pageForEachProject');
                                                        $page = (int) $page[$i];
                                                        ?>
                                                        <div>
                                                            <a href="/oferta/<?= $znaleziony['url_kategorii']; ?>/<?= $page; ?>/<?= $znaleziony['url_projektu'] ?>"><strong>Zobacz projekt</strong></a>
                                                            <br />
                                                            <a href="/oferta/<?= $znaleziony['url_kategorii']; ?>/<?= $page; ?>"><span><small>Zobacz kategorię</small></span></a>
                                                        </div>
                                                    </div>
                                                    <div class="span9">
                                                        <?
                                                        if (!Session::get('highlight')) {
                                                            echo $znaleziony['opis_projektu'];
                                                        } else {
                                                            echo preg_replace("/$word/", "<strong class='text-error'>$word</strong>", $znaleziony['opis_projektu']);
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                    $i++;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
                <div class="clearfix"></div>
            </div>
        </div>
        <? foreach ($this->zdjeciaRealizacji as $rows): ?>
            <div id="gallery" class="row-fluid" style="margin-bottom:10px;">
                <? for ($i = 0; $i < 4; $i++): ?>
                    <div class="span3 custom-rel">
                        <? if (isset($rows[$i])): ?>
                            <a class="thumbnail" rel="shadowbox[gallery]" href="/media/img/realizacje/<?= $this->project[0]['url_projektu'] ?>/realizacja/<?= $rows[$i] ?>">
                                <img alt="<?= $rows[$i] ?>" src="/media/img/realizacje/<?= $this->project[0]['url_projektu'] ?>/realizacja/<?= $rows[$i] ?>" />
                            </a>
                        <? endif; ?>
                    </div>
                <? endfor; ?>
            </div>
        <? endforeach; ?>    
    <? endif; ?>
<? endif; ?>
<? if ($this->pagination): ?>

    <div class="pagination pagination-small">
        <?= $this->pagination; ?>

    </div>

<? endif; ?>