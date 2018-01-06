<div class="main">
    <?
    for ($i = 0; $i < count($this->categories); $i += 2):
        ?>    
        <div class="row-fluid">
            <? foreach (array_slice($this->categories, 0 + $i, 2 + $i) as $kategoria): ?>
                <div class="span6 img-polaroid">               
                    <div id="<?= $kategoria['url_kategorii']; ?>Carousel" class="carousel slide">
                        <? if (!empty($this->{$kategoria['url_kategorii']})): ?>
                            <a rel="nofollow" href="/oferta/<?= $kategoria['url_kategorii']; ?>" class="btn btn-danger custom-abs category-btn"><?= $kategoria['nazwa_kategorii']; ?></a>
                        <? else: ?>
                            <a rel="nofollow" title="Brak projektów w tej kategorii..." onclick="return false" href="/oferta/<?= $kategoria['url_kategorii']; ?>" class="btn btn-danger custom-abs category-btn"><?= $kategoria['nazwa_kategorii']; ?></a>
                        <? endif; ?>
                        <? if (!empty($this->{$kategoria['url_kategorii']})): ?>
                            <div class="carousel-inner">
                                <? foreach ($this->{$kategoria['url_kategorii']} as $kat): ?>
                                    <? if (file_exists("media/img/{$kategoria['url_kategorii']}/{$kat['url_projektu']}/projekt.jpg")): ?>
                                        <div class="<? if ($this->{$kategoria['url_kategorii']}[0]['url_projektu'] === $kat['url_projektu']): ?>active <? endif; ?>item"><a rel="nofollow" href="/oferta/<?= $kategoria['url_kategorii']; ?>"><img alt="<?= $kategoria['nazwa_kategorii']; ?>" src="media/img/<?= $kategoria['url_kategorii']; ?>/<?= $kat['url_projektu']; ?>/projekt.jpg"/></a></div>
                                    <? else: ?>
                                        <div class="<? if ($this->{$kategoria['url_kategorii']}[0]['url_projektu'] === $kat['url_projektu']): ?>active <? endif; ?>item"><a rel="nofollow" href="/oferta/<?= $kategoria['url_kategorii']; ?>"><img alt="<?= $kategoria['nazwa_kategorii']; ?>" src="media/img/ico/tp.jpg"/></a></div>
                                    <? endif; ?>
                                <? endforeach; ?>
                            </div>
                            <? if (count($this->{$kategoria['url_kategorii']}) > 1): ?>
                                <a rel="nofollow" class="btn btn-danger custom-left-btn custom-abs" href="#<?= $kategoria['url_kategorii']; ?>Carousel" data-slide="prev"><i class="icon-chevron-left icon-white"></i></a>
                                <a rel="nofollow" class="btn btn-danger custom-right-btn custom-abs" href="#<?= $kategoria['url_kategorii']; ?>Carousel" data-slide="next"><i class="icon-chevron-right icon-white"></i></a>
                            <? endif; ?>
                        <? else: ?>
                            <img alt="<?= $kategoria['nazwa_kategorii']; ?>" src="media/img/ico/tp.jpg"/>
                        <? endif; ?>
                    </div>                              
                </div>
            <? endforeach; ?>
        </div>
    <? endfor; ?>   
</div>
<div class="hero-unit well">
    <?= $this->opis['zakres_dzialalnosci']; ?>
</div>