<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login-SprachApp</title>
    <style>
        :root {
            --primary: #4a6bdf;
            --primary-hover: #3a56b7;
            --background: #f5f7ff;
            --box-shadow: rgba(74, 107, 223, 0.15);
            --text-color: #333;
            --input-bg: #f9faff;
            --input-border: #e1e5f5;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: var(--background);
            color: var(--text-color);
        }
        
        .container {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px var(--box-shadow);
            width: 100%;
            max-width: 380px;
            transition: all 0.3s ease;
        }
        
        .container h2 {
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            color: var(--primary);
            text-align: center;
            font-weight: 600;
        }
        
        .input-group {
            margin-bottom: 1.25rem;
            position: relative;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            background-color: var(--input-bg);
            font-size: 1rem;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 107, 223, 0.2);
        }
        
        button {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 0.75rem;
        }
        
        button:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .toggle-form {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.95rem;
        }
        
        .toggle-form a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }
        
        .toggle-form a:hover {
            text-decoration: underline;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .form-slide {
            transition: transform 0.4s ease, opacity 0.3s ease;
        }
        
        .slide-out {
            transform: translateX(-20px);
            opacity: 0;
        }
        
        .slide-in {
            transform: translateX(0);
            opacity: 1;
        }
        
        .forgot-password {
            text-align: right;
            margin-bottom: 1rem;
        }
        
        .forgot-password a {
            color: #666;
            font-size: 0.85rem;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            color: var(--primary);
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">
        <span>Sprach</span><span style="color: #333;">App</span>
    </div>
    
    <div id="login-container" class="form-slide slide-in">
        <h2>Willkommen zurück</h2>
        <form id="login-form">
            <div class="input-group">
                <label for="login-email">E-Mail</label>
                <input type="email" id="login-email" required>
            </div>
            <div class="input-group">
                <label for="login-password">Passwort</label>
                <input type="password" id="login-password" required>
            </div>
            <div class="forgot-password">
                <a href="#">Passwort vergessen?</a>
            </div>
            <button type="submit">Anmelden</button>
        </form>
        <div class="toggle-form">
            Noch kein Konto? <a href="#" id="toggle-register">Registrieren</a>
        </div>
    </div>

    <div id="register-container" class="form-slide" style="display:none;">
        <h2>Konto erstellen</h2>
        <form id="register-form">
            <div class="input-group">
                <label for="register-username">Benutzername</label>
                <input type="text" id="register-username" required>
            </div>
            <div class="input-group">
                <label for="register-email">E-Mail</label>
                <input type="email" id="register-email" required>
            </div>
            <div class="input-group">
                <label for="register-password">Passwort</label>
                <input type="password" id="register-password" required>
            </div>
            <button type="submit">Konto erstellen</button>
        </form>
        <div class="toggle-form">
            Bereits ein Konto? <a href="#" id="toggle-login">Anmelden</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-register').addEventListener('click', function(event) {
        event.preventDefault();
        const loginContainer = document.getElementById('login-container');
        const registerContainer = document.getElementById('register-container');
        
        loginContainer.classList.add('slide-out');
        
        setTimeout(() => {
            loginContainer.style.display = 'none';
            registerContainer.style.display = 'block';
            setTimeout(() => {
                registerContainer.classList.add('slide-in');
            }, 50);
        }, 300);
    });

    document.getElementById('toggle-login').addEventListener('click', function(event) {
        event.preventDefault();
        const loginContainer = document.getElementById('login-container');
        const registerContainer = document.getElementById('register-container');
        
        registerContainer.classList.remove('slide-in');
        
        setTimeout(() => {
            registerContainer.style.display = 'none';
            loginContainer.style.display = 'block';
            setTimeout(() => {
                loginContainer.classList.remove('slide-out');
            }, 50);
        }, 300);
    });

    document.getElementById('login-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        // Hier serverseitige Verarbeitung einfügen
        console.log('Login:', { email, password });
    });

    document.getElementById('register-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const username = document.getElementById('register-username').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        // Hier serverseitige Verarbeitung einfügen
        console.log('Registrieren:', { username, email, password });
    });
</script>

</body>
</html>