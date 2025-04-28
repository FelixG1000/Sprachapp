<?php
require_once 'php/includes/header.php';

// Benötigte Zugriffsberechtigung prüfen
requireLogin();

// Benutzerdaten laden
$userId = $_SESSION['user_id'];
$user = $db->selectOne("SELECT * FROM users WHERE user_id = :user_id", ['user_id' => $userId]);

// Statistiken laden
$totalCards = $db->selectOne("
    SELECT COUNT(progress_id) as count 
    FROM user_progress 
    WHERE user_id = :user_id", 
    ['user_id' => $userId]
);

$learnedToday = $db->selectOne("
    SELECT cards_learned 
    FROM user_stats 
    WHERE user_id = :user_id AND date = CURDATE()", 
    ['user_id' => $userId]
);

$favoritedUnits = $db->selectOne("
    SELECT COUNT(favorite_id) as count 
    FROM user_favorites 
    WHERE user_id = :user_id", 
    ['user_id' => $userId]
);

$wrongAnswers = $db->selectOne("
    SELECT COUNT(progress_id) as count 
    FROM user_progress 
    WHERE user_id = :user_id AND wrong_count > 0", 
    ['user_id' => $userId]
);

// Kürzlich gelernte Einheiten
$recentUnits = $db->select("
    SELECT u.*, COUNT(up.progress_id) as cards_practiced
    FROM units u
    JOIN flashcards f ON u.unit_id = f.unit_id
    JOIN user_progress up ON f.card_id = up.card_id
    WHERE up.user_id = :user_id
    GROUP BY u.unit_id
    ORDER BY MAX(up.last_practiced) DESC
    LIMIT 3", 
    ['user_id' => $userId]
);

// Favorisierte Einheiten
$favoriteUnits = $db->select("
    SELECT u.*
    FROM units u
    JOIN user_favorites uf ON u.unit_id = uf.unit_id
    WHERE uf.user_id = :user_id
    ORDER BY uf.added_at DESC", 
    ['user_id' => $userId]
);
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5">Willkommen zurück, <?= htmlspecialchars($user['username']) ?>!</h1>
        <p class="lead">
            <?php if ($user['streak_days'] > 0): ?>
                <span class="streak-badge">
                    <i class="bi bi-fire"></i> <?= $user['streak_days'] ?> Tage Streak
                </span>
            <?php endif; ?>
            Heute ist ein guter Tag zum Lernen!
        </p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <i class="bi bi-card-list"></i>
                <div class="stats-value"><?= $totalCards ? $totalCards['count'] : 0 ?></div>
                <div class="stats-label">Gelernte Karteikarten</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <i class="bi bi-calendar-check"></i>
                <div class="stats-value"><?= $learnedToday ? $learnedToday['cards_learned'] : 0 ?></div>
                <div class="stats-label">Heute gelernt</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <i class="bi bi-star-fill"></i>
                <div class="stats-value"><?= $favoritedUnits ? $favoritedUnits['count'] : 0 ?></div>
                <div class="stats-label">Favorisierte Einheiten</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <i class="bi bi-x-circle"></i>
                <div class="stats-value"><?= $wrongAnswers ? $wrongAnswers['count'] : 0 ?></div>
                <div class="stats-label">Fehlerhafte Antworten</div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Kürzlich gelernte Einheiten
            </div>
            <div class="card-body">
                <?php if ($recentUnits): ?>
                    <div class="list-group">
                        <?php foreach ($recentUnits as $unit): ?>
                            <a href="flashcards.php?unit_id=<?= $unit['unit_id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($unit['unit_name']) ?>
                                <span class="badge bg-primary rounded-pill"><?= $unit['cards_practiced'] ?> Karten</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Du hast noch keine Einheiten gelernt. Starte jetzt!</p>
                <?php endif; ?>
                <div class="mt-3">
                    <a href="units.php" class="btn btn-outline-primary">Alle Einheiten anzeigen</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-star"></i> Deine Favoriten
            </div>
            <div class="card-body">
                <?php if ($favoriteUnits): ?>
                    <div class="list-group">
                        <?php foreach ($favoriteUnits as $unit): ?>
                            <a href="flashcards.php?unit_id=<?= $unit['unit_id'] ?>" class="list-group-item list-group-item-action">
                                <?= htmlspecialchars($unit['unit_name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Du hast noch keine Einheiten favorisiert.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning-charge"></i> Schneller Zugriff
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="flashcards.php" class="btn btn-primary d-block">
                            <i class="bi bi-card-text"></i> Karteikarten lernen
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="mini_test.php" class="btn btn-success d-block">
                            <i class="bi bi-pencil-square"></i> Mini-Test starten
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="quiz.php" class="btn btn-warning d-block text-dark">
                            <i class="bi bi-question-circle"></i> Quiz spielen
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="wrong_answers.php" class="btn btn-danger d-block">
                            <i class="bi bi-x-circle"></i> Fehlerhafte Antworten üben
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="all_flashcards.php" class="btn btn-info d-block text-white">
                            <i class="bi bi-collection"></i> Alle Vokabeln anzeigen
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="leaderboard.php" class="btn btn-secondary d-block">
                            <i class="bi bi-trophy"></i> Bestenliste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'php/includes/footer.php';
?>