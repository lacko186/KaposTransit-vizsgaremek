<?php
session_start();
require_once 'config.php';

// token ellenőrzés
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        // token lekérdezés
        $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_expiry > NOW()");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $_SESSION['error'] = "Érvénytelen vagy lejárt token!";
            header("Location: login.php");
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Rendszerhiba történt!";
        header("Location: login.php");
        exit();
    }
}

// jelszó mentés
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    if ($password !== $password_confirm) {
        $_SESSION['error'] = "A jelszavak nem egyeznek!";
    } else {
        try {
            // jelszó frissítés
            $stmt = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_expiry = NULL WHERE reset_token = :token AND reset_expiry > NOW()");
            $stmt->execute([
                'password' => $password,
                'token' => $token
            ]);
            
            $_SESSION['success'] = "A jelszó sikeresen megváltoztatva!";
            header("Location: login.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Rendszerhiba történt!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelszó visszaállítása</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
       :root {
            --kkzrt-blue: #004b93;
            --kkzrt-yellow: #ffd800;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to TOP, #211717,#b30000, #FFFFFF);
            background-size: cover;
            background-repeat: no-repeat;
            color: #000;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .reset-container {
            max-width: 400px;
            width: 100%;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
        }
        
        .form-control {
            border-radius: 12px !important;
            padding: 0.8rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--volan-blue);
            box-shadow: 0 0 0 0.2rem rgba(0,75,147,0.15);
        }
        
        .btn-reset {
            background: linear-gradient(to right, #000000, #FF0000);
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 12px !important;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 100%;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,75,147,0.2);
        }
        
        .alert {
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        
        .back-to-login {
            color: var(--volan-blue);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 1rem;
            font-weight: 500;
        }
        
        .back-to-login:hover {
            color: #0066cc;
            transform: translateX(-3px);
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
        }
        
        .icon-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .icon-container svg {
            max-width: 40%;
            height: auto;
            fill: var(--volan-blue);
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0S160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17v80c0 13.3 10.7 24 24 24h80c13.3 0 24-10.7 24-24v-40h40c13.3 0 24-10.7 24-24v-40h40c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zm40-176c-22.1 0-40-17.9-40-40s17.9-40 40-40s40 17.9 40 40s-17.9 40-40 40z"/>
            </svg>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <h2 class="text-center mb-4">Új jelszó beállítása</h2>
        
        <form method="POST" action="reset-password.php" id="resetForm">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="mb-3">
                <label for="password" class="form-label">Új jelszó:</label>
                <input type="password" class="form-control" id="password" name="password" required 
                       minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                <div class="password-requirements">
                    
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password_confirm" class="form-label">Jelszó megerősítése:</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>
            
            <button type="submit" name="new_password" class="btn btn-reset">
                <i class="fas fa-key me-2"></i>Jelszó megváltoztatása
            </button>
        </form>
        
        <div class="text-center">
            <a href="login.php" class="back-to-login">
                <i class="fas fa-arrow-left me-2"></i>Vissza a bejelentkezéshez
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // jelszó ellenőrzés
        document.getElementById('resetForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (password !== passwordConfirm) {
                event.preventDefault();
                alert('A két jelszó nem egyezik!');
            }
      
        });
    </script>
</body>
</html>