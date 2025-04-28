<?php
require_once 'php/includes/header.php';

// Benötigte Zugriffsberechtigung prüfen
requireLogin();

// Parameter verarbeiten
$unitId = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
$direction = isset($_POST['direction']) ? $_POST['direction'] : (isset($_GET['direction']) ? $_GET['direction'] : 'de_en');
$userId = $_SESSION['user_id'];

// Wenn keine Unit ID angegeben wurde und keine bestimmte Lernmethode, zeige alle Units an
if ($unitId === 0) {
    // Alle Units abrufen
    $units = $db->select("SELECT * FROM units ORDER BY unit_name");
    
    // Favoriten des Benutzers abrufen
    $favorites = $db->select(
        "SELECT unit_id FROM user_favorites WHERE user_id = :user_id",
        ['user_id' => $userId]
    );
    
    $favoriteIds = array_column($favorites, 'unit_id');
?>

<div class="row mb-4">
    <div class="col-12">
        <h1>Lerneinheiten</h1>
        <p class="lead">Wähle eine Einheit aus, um mit dem Lernen zu beginnen.</p>
    </div>
</div>

<div class="row">
    <?php foreach ($units as $unit): ?>
        <div class="col-md-4 mb-4">
            <div class="card unit-card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($unit['unit_name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($unit['description']) ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <a href="flashcards.php?unit_id=<?= $unit['unit_id'] ?>" class="btn btn-primary">Lernen</a>
                        <button class="favorite-btn <?= in_array($unit['unit_id'], $favoriteIds) ? 'active' : '' ?>" 
                                data-id="<?= $unit['unit_id'] ?>" 
                                data-type="unit">
                            <i class="bi <?= in_array($unit['unit_id'], $favoriteIds) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
} else {
    // Spezifische Unit abrufen
    $unit = $db->selectOne(
        "SELECT * FROM units WHERE unit_id = :unit_id",
        ['unit_id' => $unitId]
    );
    
    if (!$unit) {
        setFlashMessage('danger', 'Die angeforderte Lerneinheit wurde nicht gefunden.');
        header('Location: flashcards.php');
        exit;
    }
    
    // Karteikarten der Unit abrufen, unter Berücksichtigung des nächsten Review-Datums
    $currentDate = date('Y-m-d H:i:s');
    
    $sql = "
        SELECT f.*, 
               COALESCE(up.next_review_date, '1970-01-01 00:00:00') as next_review_date,
               COALESCE(up.is_favorite, 0) as is_favorite,
               COALESCE(up.correct_count, 0) as correct_count,
               COALESCE(up.wrong_count, 0) as wrong_count
        FROM flashcards f
        LEFT JOIN user_progress up ON f.card_id = up.card_id AND up.user_id = :user_id
        WHERE f.unit_id = :unit_id 
        AND (up.next_review_date IS NULL OR up.next_review_date <= :current_date)
        ORDER BY up.is_favorite DESC, RAND()
    ";
    
    $flashcards = $db->select($sql, [
        'user_id' => $userId,
        'unit_id' => $unitId,
        'current_date' => $currentDate
    ]);
    
    // Statistiken für diese Unit
    $totalCards = $db->selectOne(
        "SELECT COUNT(*) as count FROM flashcards WHERE unit_id = :unit_id",
        ['unit_id' => $unitId]
    );
    
    $learnedCards = $db->selectOne(
        "SELECT COUNT(*) as count FROM flashcards f
         JOIN user_progress up ON f.card_id = up.card_id
         WHERE f.unit_id = :unit_id AND up.user_id = :user_id",
        ['unit_id' => $unitId, 'user_id' => $userId]
    );
    
    $correctPercentage = 0;
    if ($learnedCards && $learnedCards['count'] > 0) {
        $correctStats = $db->selectOne(
            "SELECT SUM(correct_count) as correct, SUM(correct_count + wrong_count) as total
             FROM flashcards f
             JOIN user_progress up ON f.card_id = up.card_id
             WHERE f.unit_id = :unit_id AND up.user_id = :user_id",
            ['unit_id' => $unitId, 'user_id' => $userId]
        );
        
        if ($correctStats && $correctStats['total'] > 0) {
            $correctPercentage = round(($correctStats['correct'] / $correctStats['total']) * 100);
        }
    }
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><?= htmlspecialchars($unit['unit_name']) ?></h1>
            <div class="btn-group">
                <a href="mini_test.php?unit_id=<?= $unitId ?>" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-square"></i> Mini-Test
                </a>
                <a href="quiz.php?unit_id=<?= $unitId ?>" class="btn btn-outline-success">
                    <i class="bi bi-question-circle"></i> Quiz
                </a>
                <a href="all_flashcards.php?unit_id=<?= $unitId ?>" class="btn btn-outline-info">
                    <i class="bi bi-table"></i> Alle anzeigen
                </a>
            </div>
        </div>
        <p class="text-muted"><?= htmlspecialchars($unit['description']) ?></p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="badge bg-primary"><?= $learnedCards ? $learnedCards['count'] : 0 ?> / <?= $totalCards ? $totalCards['count'] : 0 ?> Karten gelernt</span>
                        <?php if ($correctPercentage > 0): ?>
                            <span class="badge bg-success ms-2"><?= $correctPercentage ?>% korrekt</span>
                        <?php endif; ?>
                    </div>
                    <div class="language-switch">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="language-switch" <?= $direction === 'en_de' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="language-switch">
                                <?= $direction === 'de_en' ? 'DE → EN' : 'EN → DE' ?>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="progress-container">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: <?= $correctPercentage ?>%;" 
                             aria-valuenow="<?= $correctPercentage ?>" aria-valuemin="0" aria-valuemax="100">
                            <?= $correctPercentage ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($flashcards)): ?>
    <div class="alert alert-info">
        <h4 class="alert-heading">Alles gelernt!</h4>
        <p>Du hast alle Vokabeln in dieser Einheit bereits gelernt. Die nächsten Karten werden bald wieder für dich verfügbar sein.</p>
        <hr>
        <p class="mb-0">
            <a href="flashcards.php" class="btn btn-primary btn-sm">Zurück zu den Einheiten</a>
            <a href="wrong_answers.php?unit_id=<?= $unitId ?>" class="btn btn-danger btn-sm">Falsche Antworten üben</a>
        </p>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <?php foreach ($flashcards as $index => $card): ?>
                <div class="flashcard mb-4 <?= $index > 0 ? 'd-none' : '' ?>" data-card-id="<?= $card['card_id'] ?>">
                    <div class="flashcard-inner">
                        <div class="flashcard-front">
                            <div class="d-flex justify-content-between w-100">
                                <button class="favorite-btn <?= $card['is_favorite'] ? 'active' : '' ?>" 
                                        data-id="<?= $card['card_id'] ?>" 
                                        data-type="card">
                                    <i class="bi <?= $card['is_favorite'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                </button>
                                <div class="card-counter"><?= $index + 1 ?> / <?= count($flashcards) ?></div>
                            </div>
                            <h2 class="my-3">
                                <?= htmlspecialchars($direction === 'de_en' ? $card['german_word'] : $card['english_word']) ?>
                            </h2>
                            <button class="audio-btn" data-audio="<?= getAudioUrl($direction === 'de_en' ? $card['german_word'] : $card['english_word'], $direction === 'de_en' ? 'de' : 'en') ?>">
                                <i class="bi bi-volume-up"></i>
                            </button>
                            <div class="mt-3">
                                <small class="text-muted">Klicke auf die Karte, um sie umzudrehen</small>
                            </div>
                        </div>
                        <div class="flashcard-back">
                            <h2 class="my-3">
                                <?= htmlspecialchars($direction === 'de_en' ? $card['english_word'] : $card['german_word']) ?>
                            </h2>
                            <button class="audio-btn" data-audio="<?= getAudioUrl($direction === 'de_en' ? $card['english_word'] : $card['german_word'], $direction === 'de_en' ? 'en' : 'de') ?>">
                                <i class="bi bi-volume-up"></i>
                            </button>
                            <div class="mt-4">
                                <button class="btn btn-success answer-btn me-2" data-answer="correct">
                                    <i class="bi bi-check-lg"></i> Gewusst
                                </button>
                                <button class="btn btn-danger answer-btn" data-answer="wrong">
                                    <i class="bi bi-x-lg"></i> Nicht gewusst
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php
}
require_once 'php/includes/footer.php';
?>