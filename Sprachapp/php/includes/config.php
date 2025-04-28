<?php
// config.php - Konfigurationsdatei für die Sprachapp

// Datenbank-Konfiguration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sprachapp_db');

// Applikations-Konfiguration
define('APP_NAME', 'Sprachapp');
define('APP_URL', 'http://localhost/Sprachapp');

// Session-Konfiguration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zeitzone setzen
date_default_timezone_set('Europe/Berlin');

// Konstanten für Lernalgorithmus
define('REVIEW_DAYS_CORRECT', 3); // Anzahl der Tage, bis eine korrekt beantwortete Karte wieder angezeigt wird