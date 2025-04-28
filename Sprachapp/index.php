<?php
require_once 'php/includes/header.php';
?>

<div class="row">
    <div class="col-md-6 offset-md-3 text-center py-5">
        <h1 class="display-4">Willkommen bei <?= APP_NAME ?></h1>
        <p class="lead">Lerne Sprachen effektiv mit Karteikarten, Audio-Unterstützung und intelligenten Lernalgorithmen</p>
        
        <?php if (!isLoggedIn()): ?>
            <div class="mt-4">
                <a href="register.php" class="btn btn-primary btn-lg me-2">Jetzt registrieren</a>
                <a href="login.php" class="btn btn-outline-secondary btn-lg">Anmelden</a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <a href="dashboard.php" class="btn btn-primary btn-lg">Zum Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-card-list display-4 text-primary mb-3"></i>
                <h3>Karteikarten</h3>
                <p>Lerne mit interaktiven Karteikarten für verschiedene Themen und Schwierigkeitsstufen.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-volume-up display-4 text-primary mb-3"></i>
                <h3>Audio-Unterstützung</h3>
                <p>Verbessere deine Aussprache mit der integrierten Audioausgabe für alle Vokabeln.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-graph-up display-4 text-primary mb-3"></i>
                <h3>Intelligentes Lernen</h3>
                <p>Unser System merkt sich deine Fortschritte und wiederholt gezielt Vokabeln, die du noch nicht beherrschst.</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-check2-square display-4 text-primary mb-3"></i>
                <h3>Mini-Tests</h3>
                <p>Überprüfe dein Wissen mit interaktiven Tests, in denen du die Übersetzungen selbst eingeben musst.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-list-check display-4 text-primary mb-3"></i>
                <h3>Multiple Choice</h3>
                <p>Teste dein Wissen im unterhaltsamen Multiple-Choice-Format, ähnlich wie bei Kahoot.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-fire display-4 text-primary mb-3"></i>
                <h3>Lernstreak</h3>
                <p>Bleibe motiviert mit deinem persönlichen Lernstreak und verdiene Belohnungen für regelmäßiges Lernen.</p>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'php/includes/footer.php';
?>