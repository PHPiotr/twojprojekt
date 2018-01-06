<?php
defined('_PHPIOTR') || die('Restricted access');
?>
<div class="row-fluid">
    <div class="span4">
		<? if ($this->pagination): ?>
			<div class="pagination pagination-mini">
				<?= $this->pagination; ?>
			</div>
		<? endif; ?>
		<? if ($this->projects !== null): ?>
			<div class="span12">
				<h2 class="text-success"><?= $this->category['url_kategorii'] !== 'realizacje' ? 'Projekty' : 'Realizacje' ?></h2>
				<hr />
				<!--projekty-->
				<table class="table table-bordered table-striped table-condensed table-hover">
					<thead><tr>
							<th></th>
							<th></th>
							<th>Nazwa</th>
							<? if ($this->category['url_kategorii'] !== 'realizacje'): ?>
								<th>m<sup>2</sup></th>
								<th></th>
							<? endif; ?>
						</tr>
					</thead>
					<tbody>
						<? $i = 1; ?>
						<? foreach ($this->projects as $project): ?>
							<tr>              
								<td><?= $i; ?>.</td>
								<td>
									<form action="/tp/delete" method="post" style="margin-bottom:0">
										<input id="project_id" name="project_id" type="hidden" value="<?= $project['id']; ?>"/>
										<input id="delete_project" name="delete_project" type="hidden" value="<?= $project['url_projektu']; ?>"/>
										<input id="delete_category_id" name="delete_category_id" type="hidden" value="<?= $project['id_kategorii']; ?>"/>
										<input id="delete_category_url" name="delete_category_url" type="hidden" value="<?= $this->category['url_kategorii']; ?>"/>
										<button class="btn btn-mini btn-trash" title="<?= ucfirst($project['nazwa_projektu']); ?>" type="submit"><i class="icon-trash"></i></button>
									</form>
								</td>
								<td>
									<form action="/tp/editing" method="post" style="margin-bottom:0">
										<input id="editing_current_page" name="editing_current_page" type="hidden" value="<?= $this->currentPage ?>"/>
										<input id="editing_project" name="editing_project" type="hidden" value="<?= $project['url_projektu']; ?>"/>
										<input id="editing_category_url" name="editing_category_url" type="hidden" value="<?= $this->category['url_kategorii']; ?>"/>
										<button class="btn-block btn btn-mini<? if ($project['url_projektu'] === $this->projectUrl): ?> btn-success<? endif; ?>" title="Edytuj projekt <?= ucfirst($project['nazwa_projektu']); ?>" type="submit"><small><?= ucfirst($project['nazwa_projektu']); ?></small></button>
									</form>
								</td>
								<? if ($this->category['url_kategorii'] !== 'realizacje'): ?>
									<td><small><?= number_format($project['wartosc_danej'] / 100, 2, ',', ' '); ?></small></td>
									<td>
										<form action="/tp/duplicate" method="post" style="margin-bottom:0">
											<input id="duplicate_current_page" name="duplicate_current_page" type="hidden" value="<?= $this->currentPage ?>"/>
											<input id="duplicate_project" name="duplicate_project" type="hidden" value="<?= $project['url_projektu']; ?>"/>
											<input id="duplicate_category_url" name="duplicate_category_url" type="hidden" value="<?= $this->category['url_kategorii']; ?>"/>
											<button class="btn btn-mini<? if ($project['url_projektu'] === $this->projectUrl && Session::get('duplication')): ?> btn-success<? endif; ?>" title="Duplikuj projekt <?= ucfirst($project['nazwa_projektu']); ?>" type="submit"><i class="icon-plus-sign"></i></button>
										</form>
									</td>
								<? endif; ?>                          
							</tr>
							<?
							$i++;
						endforeach;
						?>
					</tbody>
				</table>
			</div>
			<!--sortowanie projektów-->
			<? if ($this->project === null): ?>
				<div class="span12 well well-small">
					<form action="/tp/sortowanie" class="form-inline" method="post">
						<fieldset>
							<label>Aktualna metoda sortowania</label>
							<select class="span8" id="sort" name="sort">
								<option value="url_projektu"<? if ((Session::get('sort') === 'url_projektu' && (isset($this->categoryUrl) && $this->categoryUrl != 'realizacje')) || (Session::get('sorte') === 'url_projektu' && (isset($this->categoryUrl) && $this->categoryUrl === 'realizacje'))): ?> selected="true"<? endif; ?>>Nazwa &and;</option>
								<option value="url_projektu DESC"<? if (Session::get('sort') === 'url_projektu DESC' || Session::get('sorte') === 'url_projektu DESC'): ?> selected="true"<? endif; ?>>Nazwa &or;</option>
								<? if ($this->category['url_kategorii'] !== 'realizacje'): ?>
									<option value="wartosci_podst_danych.wartosc_danej"<? if (Session::get('sort') === 'wartosci_podst_danych.wartosc_danej'): ?> selected="true"<? endif; ?>>Powierzchnia &and;</option>
									<option value="wartosci_podst_danych.wartosc_danej DESC"<? if (Session::get('sort') === 'wartosci_podst_danych.wartosc_danej DESC'): ?> selected="true"<? endif; ?>>Powierzchnia &or;</option>
								<? endif; ?>
							</select>
							<input name="id_kategorii" type="hidden" value="<?= $this->category['id']; ?>" />
							<input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
							<input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
							<? if ($this->projectUrl !== null): ?>
								<input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
							<? endif; ?>
							<button type="submit" class="btn">Sortuj</button>
						</fieldset>
					</form>
					<form action="/tp/iloscProjektowBackend" class="form-inline" method="post">
						<label>Ilość projektów - zaplecze</label>
						<input class="input-small" id="ilosc_projektow_backend" name="ilosc_projektow_backend" placeholder="Ilość" type="text" value="<?= $this->category['ilosc_projektow_backend']; ?>" >
						<input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
						<input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
						<? if ($this->projectUrl !== null): ?>
							<input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
						<? endif; ?>
						<button type="submit" class="btn">Aktualizuj</button>
					</form>
					<form action="/tp/iloscProjektowFrontend" class="form-inline" method="post">
						<label>Ilość projektów - witryna</label>
						<input class="input-small" id="ilosc_projektow_frontend" name="ilosc_projektow_frontend" placeholder="Ilość" type="text" value="<?= $this->category['ilosc_projektow_frontend']; ?>" >
						<input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
						<input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
						<? if ($this->projectUrl !== null): ?>
							<input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
						<? endif; ?>
						<button type="submit" class="btn">Aktualizuj</button>
					</form>
				</div>
			<? endif; ?>
		<? endif; ?>
		<? if ($this->category['url_kategorii'] !== 'realizacje'): ?>
			<!--pomieszczenia-->
			<div class="span12 well well-small">
				<form action="/tp/pomieszczenie" method="post">
					<fieldset>
						<legend class="text-error">Pomieszczenia</legend>
						<label>Usuń istniejące pomieszczenie</label>
						<select class="span11" id="starePom" name="starePom">
							<option value="">Wybierz do usunięcia</option>
							<? foreach ($this->pomieszczenia as $p): ?>
								<option value="<?= $p['id']; ?>"><?= $p['nazwa_pomieszczenia'] ?></option>
							<? endforeach; ?>
						</select>
						<label>Dodaj nowe pomieszczenie</label>
						<input class="span11" id="nowePom" name="nowePom" type="text" placeholder="Nazwa pomieszczenia">
						<input name="id_kategorii" type="hidden" value="<?= $this->category['id']; ?>" />
						<input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
						<input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
						<? if ($this->projectUrl !== null): ?>
							<input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
						<? endif; ?>
						<button type="submit" class="btn">Dodaj <i class="icon-retweet"></i> Usuń</button>
					</fieldset>
				</form>
				<form action="/tp/iloscPomieszczen" class="form-inline" method="post">
					<label>Wyświetlane pomieszczenia</label>
					<input class="input-small" id="ilosc_pomieszczen" name="ilosc_pomieszczen" placeholder="Ilość" type="text" value="<?= $this->category['ilosc_pomieszczen']; ?>" >
					<input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
					<input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
					<? if ($this->projectUrl !== null): ?>
						<input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
					<? endif; ?>
					<button type="submit" class="btn">Aktualizuj</button>
				</form>
			</div>
			<!--kondygnacje-->
			<div class="span12 well well-small">
				<form action="/tp/aranzacja" method="post">
					<fieldset>
						<legend class="text-error">Kondygnacje</legend>
						<label>Usuń istniejącą kondygnację</label>
						<select class="span11" id="staraKond" name="staraKond">
							<option value="">Wybierz do usunięcia</option>
							<? foreach ($this->aranzacje as $p): ?>
								<option value="<?= $p['id']; ?>"><?= $p['nazwa_aranzacji'] ?></option>
							<? endforeach; ?>
						</select>
						<label>Dodaj nową kondygnację</label>
						<input class="span11" id="nowaKond" name="nowaKond" type="text" placeholder="Nazwa nowej konygnacji">
						<input name="id_kategorii" type="hidden" value="<?= $this->category['id']; ?>" />
						<input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
						<input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
						<? if ($this->projectUrl !== null): ?>
							<input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
						<? endif; ?>
						<button type="submit" class="btn">Dodaj <i class="icon-retweet"></i> Usuń</button>
					</fieldset>
				</form>
			</div>
			<!--podstawowe dane-->
			<div class="span12 well well-small">
				<form action="/tp/podstawowa" method="post">
					<fieldset>
						<legend class="text-error">Podstawowe dane</legend>
						<label>Usuń istniejącą</label>
						<select class="span11" id="staraPodst" name="staraPodst">
							<option value="">Wybierz do usunięcia</option>
							<? foreach ($this->dane as $p): ?>
								<option value="<?= $p['id']; ?>"><?= $p['nazwa_danej'] ?></option>
							<? endforeach; ?>
						</select>
						<label>Dodaj nową</label>
						<input class="span11" id="nowaPodst" name="nowaPodst" type="text" placeholder="Nazwa podstawowej danej">
						<input name="id_kategorii" type="hidden" value="<?= $this->category['id']; ?>" />
						<input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
						<input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
						<? if ($this->projectUrl !== null): ?>
							<input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
						<? endif; ?>
						<button type="submit" class="btn">Dodaj <i class="icon-retweet"></i> Usuń</button>
					</fieldset>
				</form>
			</div>
			<!--dane materiałowe-->
			<div class="span12 well well-small">
				<form action="/tp/materialowa" method="post">
					<fieldset>
						<legend class="text-error">Dane materiałowe</legend>
						<label>Usuń istniejącą</label>
						<select class="span11" id="staraMaterial" name="staraMaterial">
							<option value="">Wybierz do usunięcia</option>
							<? foreach ($this->materialowe as $p): ?>
								<option value="<?= $p['id']; ?>"><?= $p['nazwa_danej_materialowej'] ?></option>
							<? endforeach; ?>
						</select>
						<label>Dodaj nową</label>
						<input class="span11" id="nowaMaterial" name="nowaMaterial" type="text" placeholder="Nazwa danej materiałowej">
						<input name="id_kategorii" type="hidden" value="<?= $this->category['id']; ?>" />
						<input name="url_kategorii" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
						<input name="page" type="hidden" value="<?= $this->currentPage; ?>" />
						<? if ($this->projectUrl !== null): ?>
							<input name="project" type="hidden" value="<?= $this->projectUrl ?>" />
						<? endif; ?>
						<button type="submit" class="btn">Dodaj <i class="icon-retweet"></i> Usuń</button>
					</fieldset>
				</form>
			</div>
		<? endif; ?>
    </div>
    <div class="span8 well well-small">
        <div class="span12">
            <form action="/tp/<?= $this->projectUrl && !Session::get('duplication') ? 'edit' : 'add'; ?>" class="form-horizontal" enctype="multipart/form-data" method="post">
                <fieldset>
                    <legend class="text-error"><?= $this->projectUrl ? (Session::get('duplication') ? 'Duplikowanie' : 'Edytowanie') : 'Dodawanie'; ?> <?= $this->category['url_kategorii'] !== 'realizacje' ? 'projektu' : 'realizacji'; ?></legend>
                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="btn btn-danger">Zapisz <?= $this->category['url_kategorii'] !== 'realizacje' ? 'projekt' : 'realizację'; ?></button>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="nazwa_projektu">Nazwa <?= $this->category['url_kategorii'] !== 'realizacje' ? 'projektu' : 'realizacji'; ?></label>
                        <div class="controls">
                            <input <? if ($this->category['url_kategorii'] === 'realizacje'): ?>class="input-block-level"<? endif; ?>id="nazwa_projektu" name="nazwa_projektu" placeholder="Nazwa <?= $this->category['url_kategorii'] !== 'realizacje' ? 'projektu' : 'realizacji'; ?>" type="text" value="<?= $this->projectId != null ? $this->project[0]['nazwa_projektu'] : Session::get('nazwa_projektu'); ?>" />
                        </div>
                    </div>
					<? if ($this->category['url_kategorii'] === 'realizacje'): ?>
						<div class="control-group">
							<label class="control-label" for="kategoria_realizacji">Kategoria realizacji</label>
							<div class="controls">
								<select id="kategoria_realizacji" name="kategoria_realizacji">
									<option value="0">Wybierz kategorię</option>
									<? foreach ($this->categories as $kategoria): ?>
										<? if ($kategoria['url_kategorii'] != 'realizacje'): ?>
											<option <? if (Session::get('kategoria_realizacji') == $kategoria['id'] || $this->project[0]['id_kategorii_realizacji'] == $kategoria['id']): ?>selected="selected" <? endif; ?>value="<?= $kategoria['id'] ?>"><?= $kategoria['skrocona_nazwa_kategorii'] ?></option>
										<? endif; ?>
									<? endforeach; ?>
								</select>
							</div>
						</div>

						<!--link pod zdjęciem-->
						<div class="control-group">
							<label class="control-label" for="link_pod_zdjeciem">Link pod zdjęciem</label>
							<div class="controls">
								<input class="input-xlarge" id="link_pod_zdjeciem" name="link_pod_zdjeciem" type="text" value="<?= $this->project !== null ? $this->project[0]['link_pod_zdjeciem'] : Session::get('link_pod_zdjeciem'); ?>" />
							</div>
						</div>
						<!--opis linku pod zdjęciem-->
						<div class="control-group">
							<label class="control-label" for="opis_linku_pod_zdjeciem">Opis linku pod zdjęciem</label>
							<div class="controls">
								<input class="input-xlarge" id="opis_linku_pod_zdjeciem" name="opis_linku_pod_zdjeciem" type="text" value="<?= $this->project !== null ? $this->project[0]['opis_linku_pod_zdjeciem'] : Session::get('opis_linku_pod_zdjeciem'); ?>" />
							</div>
						</div> 

						<!--zdjęcie główne-->
						<div class="control-group">
							<label class="control-label text-error" for="front">Zdjęcie główne realizacji</label>
							<div class="controls">
								<input id="projekt" name="projekt" type="file" />
								<? if ($this->project !== null && file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/projekt.jpg")): ?>
									<div id="glowne" class="modal hide fade">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h3>Zdjęcie główne realizacji</h3>                    
										</div>
										<div class="modal-body">
											<img alt="Zdjęcie główne realizacji" src="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/projekt.jpg" />
										</div>
									</div>
									<a rel="nofollow" href="#glowne" data-toggle="modal">                        
										<img width="100" src="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/projekt.jpg">
									</a>
								<? endif; ?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="realizacja">Dodawanie zdjęć (~.jpg)</label>
							<div class="controls">
								<input id="realizacja" name="realizacja[]" type="file" multiple="true" />
							</div>
						</div>

						<? if ($this->zdjeciaRealizacji): ?>
							<div class="control-group">
								<label class="control-label">Usuwanie zdjęć:</label>
							</div>
						<? endif; ?>
						<?
						if ($this->zdjeciaRealizacji):
							foreach ($this->zdjeciaRealizacji as $rows):
								?>
								<div class="row-fluid">
									<? for ($i = 0; $i < 3; $i++): ?>
										<? if (isset($rows[$i])): ?>
											<div class="span4 custom-rel">

												<label for="usun_zdjecie"><?= $rows[$i]; ?></label>
												<input type="checkbox" class="custom-abs" id="usun_zdjecie" name="usun_zdjecie[]" value="<?= $rows[$i]; ?>" />                                         
												<a rel="shadowbox[edit] nofollow" title="<?= $rows[$i]; ?>" href="/media/img/realizacje/<?= $this->project[0]['url_projektu'] ?>/realizacja/<?= $rows[$i] ?>">
													<img width="600" alt="<?= $rows[$i] ?>" src="/media/img/realizacje/<?= $this->project[0]['url_projektu'] ?>/realizacja/<?= $rows[$i] ?>" />
												</a>

											</div>                                                                     
										<? endif; ?>
									<? endfor; ?>
								</div>
								<?
							endforeach;
						endif;
						?>
					<? else: ?>
						<!------------------------------------------------------------------------------------>                    
						<!--opis projektu-->
						<div class="control-group">
							<label class="control-label" for="opis_projektu">Opis projektu</label>
							<div class="controls">
								<textarea class="span12" id="opis_projektu" name="opis_projektu" placeholder="Opis projektu"  rows="6"><?= $this->projectId !== null ? $this->project[0]['opis_projektu'] : Session::get('opis_projektu'); ?></textarea>
							</div>
						</div>
						<!--typ projektu-->
						<div class="control-group">
							<label class="control-label" for="typ_projektu">Typ projektu</label>
							<div class="controls">
								<select id="typ_projektu" name="typ_projektu">
									<option value="">Wybierz typ</option>
									<? foreach ($this->typyProjektow as $typ): ?>
										<option<? if (($this->projectId === null && (Session::get('typ_projektu') == $typ['id'])) || $this->project !== null && ($this->project[0]['id_typu_projektu'] == $typ['id'])): ?> selected="true"<? endif; ?> value="<?= $typ['id']; ?>"><?= ucfirst($typ['nazwa_typu_projektu']); ?></option>
									<? endforeach; ?>
								</select>
							</div>
						</div>
						<span class="text-error">Podstawowe dane</span>
						<hr />
						<? foreach ($this->dane as $dana): ?>
							<div class="control-group">
								<label class="control-label" for="<?= $dana['post_danej'] ?>"><?= $dana['nazwa_danej'] ?></label>
								<div class="controls input-append">
									<? if (!empty($this->wartosciDanych)): ?>
										<? foreach ($this->wartosciDanych as $wartosc): ?>
											<? if ($dana['id'] == $wartosc['id_danej']): ?>
												<? $value = $wartosc['wartosc_danej'] ?>                                          -->
											<? endif; ?>
										<? endforeach; ?>
										<input class="input-mini" id="<?= $dana['post_danej'] ?>" name="<?= $dana['post_danej'] ?>" type="text" value="<?= $this->project !== null ? $value : Session::get("{$dana['post_danej']}"); ?>" />
										<input type="hidden" name="id_danej<?= $dana['id'] ?>" value="<?= $dana['id'] ?>" />                                     
									<? else: ?>
										<input class="input-mini" id="<?= $dana['post_danej'] ?>" name="<?= $dana['post_danej'] ?>" type="text" value="<?= $this->project !== null ? $value : Session::get("{$dana['post_danej']}"); ?>" />
										<input type="hidden" name="id_danej<?= $dana['id'] ?>" value="<?= $dana['id'] ?>" />
									<? endif; ?>
									<? if ($dana['id'] != 8): ?> 
										<span class="add-on">cm<sup><?= ($dana['id'] == 6) ? null : ($dana['id'] == 5 ? 3 : 2); ?></sup></span>
									<? else: ?>
										<span class="add-on">°</span>
									<? endif; ?>
								</div>
							</div>                    
						<? endforeach; ?>
						<span class="text-error">Podstawowe dane materiałowe</span>
						<hr />
						<? foreach ($this->materialowe as $dana): ?>
							<div class="control-group">
								<label class="control-label" for="<?= $dana['post_danej_materialowej'] ?>"><?= $dana['nazwa_danej_materialowej'] ?></label>
								<div class="controls input-append">
									<? if (!empty($this->wartosciMaterialowych)): ?>
										<? foreach ($this->wartosciMaterialowych as $wartosc): ?>
											<? if ($dana['id'] == $wartosc['id_danej_materialowej']): ?>
												<? $value = $wartosc['wartosc_danej_materialowej']; ?>                                          -->
											<? endif; ?>
										<? endforeach; ?>
										<input class="input-xlarge" id="<?= $dana['post_danej_materialowej'] ?>" name="<?= $dana['post_danej_materialowej'] ?>" type="text" value="<?= $this->projectId !== null ? $value : Session::get($dana['post_danej_materialowej']); ?>" />
										<input type="hidden" name="id_danej_materialowej<?= $dana['id'] ?>" value="<?= $dana['id'] ?>" />
									<? else: ?>
										<input class="input-xlarge" id="<?= $dana['post_danej_materialowej'] ?>" name="<?= $dana['post_danej_materialowej'] ?>" type="text" value="<?= $this->projectId !== null ? $value : Session::get($dana['post_danej_materialowej']); ?>" />
										<input type="hidden" name="id_danej_materialowej<?= $dana['id'] ?>" value="<?= $dana['id'] ?>" />
									<? endif; ?>
								</div>
							</div> 
						<? endforeach; ?>
						<hr />
						<!--pdf-->
						<div class="control-group">
							<label class="control-label" for="pdf">Plik PDF projektu</label>
							<div class="controls">
								<input id="pdf" name="pdf" type="file" />
								<? if ($this->project !== null && file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/{$this->project[0]['url_projektu']}.pdf")): ?>  
									<a rel="nofollow" title="Zobacz PDF" target="_blank" href="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/<?= $this->project[0]['url_projektu']; ?>.pdf">
										<i class="icon-file"></i>
									</a>
								<? endif; ?>
							</div>
						</div>

						<!--zdjęcie główne-->
						<div class="control-group">
							<label class="control-label text-error" for="front">Zdjęcie główne projektu</label>
							<div class="controls">
								<input id="projekt" name="projekt" type="file" />
								<? if ($this->project !== null && file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/projekt.jpg")): ?>
									<a rel="nofollow" rel="shadowbox[admin]" href="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/projekt.jpg">                        
										<img width="100" src="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/projekt.jpg">
									</a>
								<? endif; ?>
							</div>
						</div>

						<!--link pod zdjęciem-->
						<div class="control-group">
							<label class="control-label" for="link_pod_zdjeciem">Link pod zdjęciem</label>
							<div class="controls">
								<input class="input-xlarge" id="link_pod_zdjeciem" name="link_pod_zdjeciem" type="text" value="<?= $this->project !== null ? $this->project[0]['link_pod_zdjeciem'] : Session::get('link_pod_zdjeciem'); ?>" />
							</div>
						</div>
						<!--opis linku pod zdjęciem-->
						<div class="control-group">
							<label class="control-label" for="opis_linku_pod_zdjeciem">Opis linku pod zdjęciem</label>
							<div class="controls">
								<input class="input-xlarge" id="opis_linku_pod_zdjeciem" name="opis_linku_pod_zdjeciem" type="text" value="<?= $this->project !== null ? $this->project[0]['opis_linku_pod_zdjeciem'] : Session::get('opis_linku_pod_zdjeciem'); ?>" />
							</div>
						</div>                    
						<span class="text-error">Elewacje</span>
						<hr />
						<!--elewacje-->
						<? foreach ($this->elewacje as $elewacja): ?>
							<div class="control-group">
								<label class="control-label" for="<?= $elewacja['skrot_elewacji']; ?>"><?= $elewacja['nazwa_elewacji']; ?></label>
								<div class="controls">
									<input id="<?= $elewacja['skrot_elewacji']; ?>" name="<?= $elewacja['skrot_elewacji']; ?>" type="file" />
									<? if ($this->project !== null && file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/elewacja/{$elewacja['skrot_elewacji']}.jpg")): ?>
										<a rel="nofollow" title="<?= $elewacja['nazwa_elewacji']; ?>" rel="shadowbox[admin]" href="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu'] ?>/elewacja/<?= $elewacja['skrot_elewacji']; ?>.jpg">                        
											<img width="100" src="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu'] ?>/elewacja/<?= $elewacja['skrot_elewacji']; ?>.jpg">
										</a>
									<? endif; ?>
								</div>
							</div>  
						<? endforeach; ?>
						<span class="text-error">Rzuty</span>
						<hr />
						<!--przekrój-->
						<div class="control-group">
							<label class="control-label" for="przekroj">Przekrój</label>
							<div class="controls">
								<input id="przekroj" name="przekroj" type="file" />
								<? if ($this->project !== null && file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/rzut/przekroj.jpg")): ?>
									<a rel="nofollow" title="Przekrój" rel="shadowbox[admin]" href="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/rzut/przekroj.jpg">
										<img width="100" alt="Przekrój" src="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/rzut/przekroj.jpg" />
									</a>
								<? endif; ?>
							</div>
						</div>
						<? foreach ($this->aranzacje as $aranzacja): ?>
							<!--kondygnacje-->
							<div class="control-group">
								<label class="control-label" for="<?= Url::create($aranzacja['nazwa_aranzacji']); ?>"><?= $aranzacja['nazwa_aranzacji'] ?></label>
								<div class="controls">
									<input id="<?= Url::create($aranzacja['nazwa_aranzacji']); ?>" name="<?= Url::createPostValue($aranzacja['nazwa_aranzacji']); ?>" type="file" />
									<? if ($this->project !== null && file_exists("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/rzut/{$aranzacja['url_aranzacji']}.jpg")): ?>
										<? if (is_file("media/img/{$this->category['url_kategorii']}/{$this->project[0]['url_projektu']}/rzut/{$aranzacja['url_aranzacji']}.jpg")): ?>
											<a rel="nofollow" title="<?= $aranzacja['nazwa_aranzacji'] ?>" rel="shadowbox[admin]" href="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/rzut/<?= $aranzacja['url_aranzacji'] ?>.jpg">
												<img width="100" alt="<?= $aranzacja['nazwa_aranzacji'] ?>" src="/media/img/<?= $this->category['url_kategorii'] ?>/<?= $this->project[0]['url_projektu']; ?>/rzut/<?= $aranzacja['url_aranzacji'] ?>.jpg" />
											</a>
										<? endif; ?>
									<? endif; ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="<?= $aranzacja['url_aranzacji'] ?>_prefix_dla_wszystkich">Dla wszystkich</label>
								<div class="controls">
									<div class="input-append">
										<select class="input-mini prefix" id="<?= $aranzacja['url_aranzacji'] ?>_prefix_dla_wszystkich" name="<?= $aranzacja['url_aranzacji'] ?>_prefix_dla_wszystkich">
											<option value=""></option>
											<? for ($i = 0; $i < 10; $i++): ?>
												<option value="<?= $i ?>"><?= $i ?></option>
											<? endfor; ?>
										</select>
										<span class="add-on">.1</span>
									</div>
								</div>
							</div>
							<?
							//default available rooms when no arrangement
							$roomsAvailableNr = $this->category['ilosc_pomieszczen'];
							//count all fields in each arangement
							$roomsOfArrangementNr = 0;
							$lp = 1;
							$prefix = 1;
							for ($x = 0; $x < count($this->powierzchnie); $x++):
								if ($aranzacja['id'] === $this->powierzchnie[$x]['id_aranzacji']):
									$prefix = (int) ($this->powierzchnie[$x]['nr_pomieszczenia'] / 10);
									?>
									<div class="control-group"> 
										<label class="control-label text-success" for="<?= $aranzacja['url_aranzacji']; ?>_prefix_<?= $lp ?>">Pomieszczenie</label>
										<div class="controls">
											<div class="input-append">
												<select class="input-mini" id="<?= $aranzacja['url_aranzacji']; ?>_prefix_<?= $lp ?>" name="<?= $aranzacja['url_aranzacji']; ?>_prefix_<?= $lp ?>">
													<?
													for ($i = 0; $i < 20; $i++):
														$selected = (((int) $this->powierzchnie[$x]['nr_pomieszczenia'][0] == $i && $this->project !== null) || (Session::get("{$aranzacja['url_aranzacji']}_prefix_{$lp}") == $i && $this->project === null)) ? ' selected="true"' : null;
														?>
														<option<?= $selected; ?> value="<?= $i ?>"><?= $i; ?></option>
													<? endfor; ?>
												</select>
												<span class="add-on">.<?= $lp; ?></span>
											</div>
											<select class="span3" id="<?= $aranzacja['url_aranzacji']; ?>_pomieszczenie_<?= $lp ?>" name="<?= $aranzacja['url_aranzacji']; ?>_pomieszczenie_<?= $lp ?>">
												<?
												for ($i = 0; $i < count($this->pomieszczenia); $i++):
													$selected = ($this->pomieszczenia[$i]['id'] === $this->powierzchnie[$x]['id_pomieszczenia'] || (Session::get("{$aranzacja['url_aranzacji']}_pomieszczenie_{$lp}") == $this->powierzchnie[$x]['id_pomieszczenia']) && $this->project === null) ? " selected='true'" : null;
													echo "<option{$selected} value='{$this->pomieszczenia[$i]['id']}'>{$this->pomieszczenia[$i]['nazwa_pomieszczenia']}</option>";
												endfor;
												?>
											</select>                        
											<div class="input-append">
												<input class="input-mini" id="<?= $aranzacja['url_aranzacji']; ?>_powierzchnia_<?= $lp ?>" name="<?= $aranzacja['url_aranzacji']; ?>_powierzchnia_<?= $lp ?>" type="text" value="<?= $this->project !== null ? $this->powierzchnie[$x]['pow_pomieszczenia'] : Session::get("{$aranzacja['url_aranzacji']}_powierzchnia_{$lp}"); ?>" />
												<span class="add-on">cm<sup>2</sup></span>
											</div>
										</div>
									</div>
									<?
									$roomsOfArrangementNr++;
									$lp++;
								endif;
							endfor;
							//how many fields left available
							$roomsAvailableNr = $roomsAvailableNr - $roomsOfArrangementNr;
							for ($y = 0; $y < $roomsAvailableNr; $y++):
								?>
								<div class="control-group">
									<label class="control-label text-error" for="<?= $aranzacja['url_aranzacji']; ?>_prefix_<?= $lp ?>">Pomieszczenie</label>
									<div class="controls">

										<div class="input-append">
											<select class="input-mini" id="<?= $aranzacja['url_aranzacji']; ?>_prefix_<?= $lp ?>" name="<?= $aranzacja['url_aranzacji']; ?>_prefix_<?= $lp; ?>">
												<?
												for ($i = 0; $i < 20; $i++):
													$selected = ($this->project === null && Session::get("{$aranzacja['url_aranzacji']}_prefix_{$lp}") == $i) ? ' selected="true"' : null;
													?>
													<option class="opcja-test"<?= $selected; ?> value="<?= $i ?>"><?= $i; ?></option>
												<? endfor; ?>
											</select>
											<span class="add-on">.<?= $lp; ?></span>
										</div>

										<select class="span3" id="<?= $aranzacja['url_aranzacji']; ?>_pomieszczenie_<?= $lp ?>" name="<?= $aranzacja['url_aranzacji']; ?>_pomieszczenie_<?= $lp ?>">
											<option value="0">Nie wybrano</option>
											<? foreach ($this->pomieszczenia as $pomieszcz): ?>
												<option<? if ($this->project === null && Session::get("{$aranzacja['url_aranzacji']}_pomieszczenie_{$lp}") == $pomieszcz['id']): ?> selected="true"<? endif; ?> value='<?= $pomieszcz['id'] ?>'><?= $pomieszcz['nazwa_pomieszczenia'] ?></option>
											<? endforeach; ?>
										</select>
										<div class="input-append">
											<input class="input-mini" id="<?= $aranzacja['url_aranzacji']; ?>_powierzchnia_<?= $lp ?>" name="<?= $aranzacja['url_aranzacji']; ?>_powierzchnia_<?= $lp ?>" type="text" value="<?= $this->project === null ? Session::get("{$aranzacja['url_aranzacji']}_powierzchnia_{$lp}") : null; ?>" />
											<span class="add-on">cm<sup>2</sup></span>
										</div>
									</div>
								</div>
								<?
								$lp++;
							endfor;
							?>
						<? endforeach; ?>
						<!------------------------------------------------------------------------------------>
					<? endif; ?>
                    <div class="control-group">
                        <div class="controls">
                            <a rel="nofollow" href="#" class="btn btn-danger scrollup right-btn" style="display:none;position:fixed;bottom:20px;right:0">Do góry</a>
                        </div>
                    </div>
					<? if ($this->projectId !== null): ?>
						<input name="url_starego_projektu" type="hidden" value="<?= $this->projectUrl; ?>" />
						<input name="id_starego_projektu" type="hidden" value="<?= $this->projectId; ?>" />
					<? endif; ?>
                    <input name="kategoria" type="hidden" value="<?= $this->category['url_kategorii']; ?>" />
                    <input name="id_kategorii" type="hidden" value="<?= $this->category['id']; ?>" />
					<? if ($this->category['url_kategorii'] !== 'realizacje'): ?>
						<input name="ilosc_pomieszczen" type="hidden" value="<?= $this->category['ilosc_pomieszczen'] ?>" />
					<? endif; ?>
                </fieldset>
            </form>
        </div>
    </div>
</div>