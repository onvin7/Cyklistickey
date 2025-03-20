<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template">
                <h1 class="display-1 text-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </h1>
                <h2 class="display-4">Přístup zamítnut</h2>
                <div class="error-details my-4">
                    <?php if (isset($error_message)): ?>
                        <p class="lead"><?php echo htmlspecialchars($error_message); ?></p>
                    <?php else: ?>
                        <p class="lead">Nemáte dostatečná oprávnění pro přístup na tuto stránku.</p>
                    <?php endif; ?>
                </div>
                <div class="error-actions">
                    <a href="/admin" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-house me-2"></i>Zpět na hlavní stránku
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-template {
    padding: 40px 15px;
}
.error-template i {
    font-size: 100px;
}
.error-details {
    color: #666;
}
.error-actions {
    margin-top: 30px;
}
</style> 