<?php
require_once 'php/includes/header.php';

// Wenn Benutzer bereits eingeloggt ist, weiterleiten
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Formularverarbeitung
$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eingaben validieren
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validierung
    if (empty($username)) {
        $errors[] = 'Bitte gib einen Benutzernamen ein.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Der Benutzername muss zwischen 3 und 50 Zeichen lang sein.';
    }
    
    if (empty($email)) {
        $errors[] = 'Bitte gib eine E-Mail-Adresse ein.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
    }
    
    if (empty($password)) {
        $errors[] = 'Bitte gib ein Passwort ein.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    }
    
    if ($password !== $password_confirm) {
        $errors[] = 'Die Passwörter stimmen nicht überein.';
    }
    
    // Überprüfen, ob Benutzername oder E-Mail bereits existieren
    $existingUser = $db->selectOne(
        "SELECT user_id FROM users WHERE username = :username OR email = :email",
        ['username' => $username, 'email' => $email]
    );
    
    if ($existingUser) {
        $errors[] = 'Benutzername oder E-Mail-Adresse sind bereits vergeben.';
    }
    
    // Wenn keine Fehler, Benutzer erstellen
    if (empty($errors)) {
        // Passwort hashen
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Benutzer in Datenbank speichern
        $userId = $db->insert('users', [
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'created_at' => date('Y-m-d H:i:s'),
            'streak_days' => 0
        ]);
        
        if ($userId) {
            // Benutzer einloggen
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = false;
            $_SESSION['streak_days'] = 0;
            
            // Erfolgsmeldung anzeigen und weiterleiten
            setFlashMessage('success', 'Registrierung erfolgreich! Willkommen bei ' . APP_NAME . '.');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Bei der Registrierung ist ein Fehler aufgetreten. Bitte versuche es später erneut.';
        }
    }
}
?>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="auth-form mt-5">
            <h2 class="text-center mb-4">Registrierung</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Benutzername</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= $username ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">E-Mail-Adresse</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Passwort</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Das Passwort muss mindestens 8 Zeichen lang sein.</div>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Passwort bestätigen</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Registrieren</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p>Bereits registriert? <a href="login.php">Hier anmelden</a></p>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'php/includes/footer.php';
?>