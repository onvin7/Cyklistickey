<div class="access-denied-container">
    <div class="access-denied-content">
        <div class="access-denied-icon">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        
        <h1 class="access-denied-title">Přístup zamítnut</h1>
        
        <div class="access-denied-message">
            <?php if (isset($error_message)): ?>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            <?php else: ?>
                <p>Nemáte dostatečná oprávnění pro přístup na tuto stránku.</p>
            <?php endif; ?>
        </div>
        
        <div class="access-denied-info">
            <div class="info-box">
                <i class="fa-solid fa-info-circle"></i>
                <p>Pokud si myslíte, že by jste měli mít přístup, kontaktujte prosím administrátora.</p>
            </div>
        </div>
        
        <div class="access-denied-actions">
            <a href="/admin" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                Zpět na hlavní stránku
            </a>
        </div>
    </div>
</div>

<style>
.access-denied-container {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: -20px;
}

.access-denied-content {
    background: white;
    border-radius: 20px;
    padding: 3rem 2rem;
    max-width: 600px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    text-align: center;
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.access-denied-icon {
    margin-bottom: 2rem;
}

.access-denied-icon i {
    font-size: 120px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.access-denied-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
}

.access-denied-message {
    font-size: 1.1rem;
    color: #4a5568;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.access-denied-info {
    margin: 2rem 0;
}

.info-box {
    background: #f7fafc;
    border-left: 4px solid #667eea;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    text-align: left;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.info-box i {
    color: #667eea;
    font-size: 1.5rem;
    margin-top: 0.2rem;
    flex-shrink: 0;
}

.info-box p {
    margin: 0;
    color: #4a5568;
    font-size: 0.95rem;
    line-height: 1.5;
}

.access-denied-actions {
    margin-top: 2rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    color: white;
    text-decoration: none;
}

.btn-back i {
    font-size: 1.1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .access-denied-container {
        padding: 1rem;
    }
    
    .access-denied-content {
        padding: 2rem 1.5rem;
    }
    
    .access-denied-icon i {
        font-size: 80px;
    }
    
    .access-denied-title {
        font-size: 2rem;
    }
    
    .access-denied-message {
        font-size: 1rem;
    }
    
    .btn-back {
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
    }
}
</style> 