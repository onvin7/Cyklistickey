<section class="content-section">
    <div class="section-header">
        <h2>Přidat propagaci článku</h2>
        <div>
            <a href="/admin/promotions" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Zpět na propagace
            </a>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="/admin/promotions">Aktuální propagace</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/admin/promotions/upcoming">Budoucí propagace</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/admin/promotions/history">Historie propagací</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="/admin/promotions/create">Přidat propagaci</a>
        </li>
    </ul>

    <div class="row">
        <div class="col-md-8">
            <form action="/admin/promotions/store" method="POST">
                <div class="mb-4">
                    <label class="form-label">Hledat článek:</label>
                    <input type="text" class="form-control" id="articleFilter" placeholder="Začněte psát název článku...">
                </div>

                <div class="mb-4">
                    <label class="form-label">Vyber článek:</label>
                    <select class="form-select" name="id_clanku" id="articleSelect" required>
                        <option value="null">Nevybráno</option>
                        <?php foreach ($articles as $article): ?>
                            <option value="<?= $article['id'] ?>"><?= htmlspecialchars($article['nazev']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label">Datum začátku:</label>
                            <input type="datetime-local" class="form-control" name="zacatek" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label">Datum konce:</label>
                            <input type="datetime-local" class="form-control" name="konec" required>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-action">
                        <i class="fas fa-save me-1"></i> Uložit propagaci
                    </button>
                    <a href="/admin/promotions" class="btn btn-secondary">Zrušit</a>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Nápověda</div>
                <div class="card-body">
                    <p>
                        <strong>Výběr článku:</strong> Vyberte článek, který chcete propagovat z dostupných článků.
                    </p>
                    <p>
                        <strong>Datum začátku:</strong> Datum a čas, od kdy se má článek začít propagovat.
                    </p>
                    <p>
                        <strong>Datum konce:</strong> Datum a čas, kdy propagace skončí.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('articleFilter').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let options = document.getElementById('articleSelect').options;

        for (let i = 0; i < options.length; i++) {
            let text = options[i].text.toLowerCase();
            options[i].style.display = text.includes(filter) ? '' : 'none';
        }
    });
</script>