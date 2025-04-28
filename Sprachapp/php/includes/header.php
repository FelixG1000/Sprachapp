<?php
// header.php - Header-Element für alle Seiten
require_once 'php/includes/config.php';
require_once 'php/includes/db.php';
require_once 'php/includes/functions.php';

// Falls der Benutzer eingeloggt ist, Streak aktualisieren
if (isLoggedIn()) {
    updateUserStreak($db, $_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Benutzerdefinierte Stile -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?= APP_NAME ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="units.php">Lerneinheiten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="flashcards.php">Karteikarten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="wrong_answers.php">Falsche Antworten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="leaderboard.php">Bestenliste</a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="php/admin/index.php">Admin-Bereich</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="bi bi-person-circle"></i>
                                <?= $_SESSION['username'] ?>
                                <?php if (isset($_SESSION['streak_days']) && $_SESSION['streak_days'] > 0): ?>
                                    <span class="badge bg-warning ms-1">
                                        <i class="bi bi-fire"></i> <?= $_SESSION['streak_days'] ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Abmelden</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Anmelden</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Registrieren</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php
        $flashMessage = getFlashMessage();
        if ($flashMessage): 
        ?>
        <div class="alert alert-<?= $flashMessage['type'] ?> alert-dismissible fade show" role="alert">
            <?= $flashMessage['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>