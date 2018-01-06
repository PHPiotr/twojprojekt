<div class="row-fluid">
    <div class="hero-unit">
        <form class="form-inline" action="/tp/zakresDzialalnosci" method="post">
            <fieldset>
                <legend>Zmień dane opisowe</legend>
                <label for="zakres_dzialalnosci">Zakres działalności</label>
                <textarea class="input-block-level" id="zakres_dzialalnosci" name="zakres_dzialalnosci" rows="8"><?= $this->opis['zakres_dzialalnosci'] ?></textarea>
                <input class="input-medium" type="text" name="miasto" id="miasto" value="<?= $this->opis['miasto'] ?>" />
                <input class="input-medium" type="text" name="ulica" id="ulica" value="<?= $this->opis['ulica'] ?>" />
                <input class="input-medium" type="text" name="telefon" id="telefon" value="<?= $this->opis['telefon'] ?>" />
                <input type="text" name="email" id="email" value="<?= $this->opis['email'] ?>" />
                <button type="submit" class="btn pull-right">Zmień</button>
            </fieldset>
        </form>
    </div>
</div>
