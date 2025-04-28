-- Datenbank erstellen
CREATE DATABASE IF NOT EXISTS sprachapp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sprachapp_db;

-- Tabelle für Benutzer
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    streak_days INT DEFAULT 0,
    streak_last_date DATE NULL
);

-- Tabelle für Units/Kategorien
CREATE TABLE IF NOT EXISTS units (
    unit_id INT AUTO_INCREMENT PRIMARY KEY,
    unit_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Tabelle für Karteikarten/Vokabeln
CREATE TABLE IF NOT EXISTS flashcards (
    card_id INT AUTO_INCREMENT PRIMARY KEY,
    german_word VARCHAR(255) NOT NULL,
    english_word VARCHAR(255) NOT NULL,
    audio_file_de VARCHAR(255),
    audio_file_en VARCHAR(255),
    unit_id INT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL,
    UNIQUE KEY (german_word, english_word, unit_id)
);

-- Tabelle für Benutzer-Lernfortschritt
CREATE TABLE IF NOT EXISTS user_progress (
    progress_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_id INT NOT NULL,
    correct_count INT DEFAULT 0,
    wrong_count INT DEFAULT 0,
    last_practiced TIMESTAMP NULL,
    next_review_date TIMESTAMP NULL,
    is_favorite BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (card_id) REFERENCES flashcards(card_id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, card_id)
);

-- Tabelle für Benutzer-Unit-Favoriten
CREATE TABLE IF NOT EXISTS user_favorites (
    favorite_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    unit_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, unit_id)
);

-- Tabelle für Benutzer-Statistiken
CREATE TABLE IF NOT EXISTS user_stats (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    cards_learned INT DEFAULT 0,
    units_learned INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, date)
);

-- Beispieldaten für Admin-Benutzer
INSERT INTO users (username, email, password, is_admin) 
VALUES ('admin', 'admin@example.com', '$2y$10$xUMz9VHSjOwNAMlH3Vty6.A77S5kY1QnJE6TfBAMFNFGNT5KsOOhq', 1);
-- Das Passwort ist 'admin123' (gehasht mit bcrypt)

-- Beispiel-Units
INSERT INTO units (unit_name, description, created_by) VALUES 
('Grundwortschatz', 'Einfache Alltagswörter für Anfänger', 1),
('Reisen', 'Vokabeln rund um das Reisen', 1),
('Geschäftsenglisch', 'Wichtige Begriffe für die Geschäftswelt', 1);

-- Beispiel-Karteikarten
INSERT INTO flashcards (german_word, english_word, unit_id, created_by) VALUES 
('Hallo', 'Hello', 1, 1),
('Auf Wiedersehen', 'Goodbye', 1, 1),
('Danke', 'Thank you', 1, 1),
('Flughafen', 'Airport', 2, 1),
('Hotel', 'Hotel', 2, 1),
('Besprechung', 'Meeting', 3, 1),
('Vertrag', 'Contract', 3, 1);