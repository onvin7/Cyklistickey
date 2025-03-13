<div class="container mt-4">
    <h2 class="mb-4">➕ Přidat propagaci článku</h2>

    <!-- Navigace mezi stránkami propagace -->
    <nav class="nav nav-pills mb-4">
        <a class="nav-link" href="/admin/promotions">📢 Aktuální propagace</a>
        <a class="nav-link" href="/admin/promotions/upcoming">📅 Budoucí propagace</a>
        <a class="nav-link" href="/admin/promotions/history">📜 Historie propagací</a>
        <a class="nav-link active btn btn-success text-white" href="/admin/promotions/create">➕ Přidat propagaci</a>
    </nav>

    <div class="card">
        <div class="card-header">📌 Vyber článek a nastav propagaci</div>
        <div class="card-body">
            <!-- Filtr pro vyhledávání článků -->
            <div class="mb-3">
                <label class="form-label">🔍 Hledat článek:</label>
                <input type="text" class="form-control" id="articleFilter" placeholder="Začněte psát název článku...">
            </div>

            <form action="/admin/promotions/store" method="POST">
                <div class="mb-3">
                    <label class="form-label">Vyber článek:</label>
                    <select class="form-select" name="id_clanku" id="articleSelect" required>
                        <option value="null">Nevybráno</option>
                        <?php foreach ($articles as $article): ?>
                            <option value="<?= $article['id'] ?>"><?= htmlspecialchars($article['nazev']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">📆 Od kdy:</label>
                    <input type="datetime-local" class="form-control" name="zacatek" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">📆 Do kdy:</label>
                    <input type="datetime-local" class="form-control" name="konec" required>
                </div>

                <button type="submit" class="btn btn-primary">✅ Přidat propagaci</button>
            </form>
        </div>
    </div>
</div>

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