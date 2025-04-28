<?php
require_once 'php/includes/header.php';

// Wenn Benutzer bereits eingeloggt ist, weiterleiten
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Formularverarbeitung
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    // Validierung
    if (empty($email) || empty($password)) {
        $errors[] = 'Bitte gib E-Mail-Adresse und Passwort ein.';
    } else {
        // Benutzer in Datenbank suchen
        $user = $db->selectOne(
            "SELECT user_id, username, password, is_admin, streak_days FROM users WHERE email = :email",
            ['email' => $email]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            // Login erfolgreich
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
            $_SESSION['streak_days'] = $user['streak_days'];
            
            // Streak aktualisieren
            $streakDays = updateUserStreak($db, $user['user_id']);
            $_SESSION['streak_days'] = $streakDays;
            
            // Last Login aktualisieren
            $db->update('users', 
                ['last_login' => date('Y-m-d H:i:s')], 
                'user_id = :user_id', 
                ['user_id' => $user['user_id']]
            );
            
            // Weiterleiten
            $redirectTo = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'dashboard.php';
            unset($_SESSION['redirect_after_login']);
            
            header("Location: $redirectTo");
            exit;
        } else {
            $errors[] = 'E-Mail-Adresse oder Passwort ist falsch.';
        }
    }
}
?>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="auth-form mt-5">
            <h2 class="text-center mb-4">Anmelden</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="email" class="form-label">E-Mail-Adresse</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Passwort</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Anmelden</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p>Noch kein Konto? <a href="register.php">Jetzt registrieren</a></p>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'php/includes/footer.php';
?>