<?php
// functions.php - Hilfsfunktionen für die Sprachapp

// Session-basierte Flashmessages
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Sicherheitsfunktionen
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length));
}

// Benutzerauthentifizierung
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlashMessage('warning', 'Bitte melde dich an, um auf diese Seite zuzugreifen.');
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setFlashMessage('danger', 'Du hast keine Berechtigung, auf diese Seite zuzugreifen.');
        header('Location: dashboard.php');
        exit;
    }
}

// Streak-Verwaltung
function updateUserStreak($db, $userId) {
    $today = date('Y-m-d');
    
    // Aktuellen Streak-Status abrufen
    $user = $db->selectOne("SELECT streak_days, streak_last_date FROM users WHERE user_id = :user_id", 
        ['user_id' => $userId]);
    
    if (!$user) return false;
    
    $lastDate = $user['streak_last_date'];
    $streakDays = $user['streak_days'];
    
    // Wenn noch nie eingeloggt oder letzter Login war vor mehr als 2 Tagen, Streak auf 1 setzen
    if (!$lastDate || (strtotime($today) - strtotime($lastDate) > 86400 * 2)) {
        $streakDays = 1;
    } 
    // Wenn letzter Login gestern war, Streak erhöhen
    else if (strtotime($today) - strtotime($lastDate) > 86400) {
        $streakDays++;
    }
    
    // Streak und letztes Login-Datum aktualisieren
    $db->update('users', 
        ['streak_days' => $streakDays, 'streak_last_date' => $today], 
        'user_id = :user_id', 
        ['user_id' => $userId]
    );
    
    // Eintrag in der Statistik für heute erstellen oder aktualisieren
    $existingStat = $db->selectOne(
        "SELECT stat_id FROM user_stats WHERE user_id = :user_id AND date = :date",
        ['user_id' => $userId, 'date' => $today]
    );
    
    if (!$existingStat) {
        $db->insert('user_stats', [
            'user_id' => $userId,
            'date' => $today,
            'cards_learned' => 0,
            'units_learned' => 0
        ]);
    }
    
    return $streakDays;
}

// Funktion zum Aktualisieren der Lernstatistik
function updateLearningStats($db, $userId, $cards = 1, $units = 0) {
    $today = date('Y-m-d');
    
    $existingStat = $db->selectOne(
        "SELECT stat_id, cards_learned, units_learned FROM user_stats WHERE user_id = :user_id AND date = :date",
        ['user_id' => $userId, 'date' => $today]
    );
    
    if ($existingStat) {
        $db->update('user_stats', 
            [
                'cards_learned' => $existingStat['cards_learned'] + $cards,
                'units_learned' => $existingStat['units_learned'] + $units
            ], 
            'stat_id = :stat_id', 
            ['stat_id' => $existingStat['stat_id']]
        );
    } else {
        $db->insert('user_stats', [
            'user_id' => $userId,
            'date' => $today,
            'cards_learned' => $cards,
            'units_learned' => $units
        ]);
    }
}

// Funktion zur Berechnung des nächsten Wiederholungsdatums
function calculateNextReviewDate($wasCorrect) {
    $now = new DateTime();
    
    if ($wasCorrect) {
        $now->add(new DateInterval('P' . REVIEW_DAYS_CORRECT . 'D'));
    }
    
    return $now->format('Y-m-d H:i:s');
}

// Funktion zum Abrufen der Bestenliste
function getLeaderboard($db, $limit = 10) {
    $sql = "SELECT u.username, u.streak_days, 
           (SELECT SUM(cards_learned) FROM user_stats WHERE user_id = u.user_id) as total_cards,
           (SELECT SUM(units_learned) FROM user_stats WHERE user_id = u.user_id) as total_units
           FROM users u
           ORDER BY streak_days DESC, total_cards DESC
           LIMIT :limit";
    
    return $db->select($sql, ['limit' => $limit]);
}

// Text-to-Speech-Funktion (simuliert - in der Praxis würdest du eine TTS-API verwenden)
function getAudioUrl($text, $language = 'de') {
    // In einer echten Implementierung würdest du hier eine TTS-API anbinden
    // Diese vereinfachte Funktion gibt einfach eine URL zu einer Audiodatei zurück
    $audioFile = urlencode($text) . '_' . $language . '.mp3';
    return APP_URL . '/audio/' . $audioFile;
}