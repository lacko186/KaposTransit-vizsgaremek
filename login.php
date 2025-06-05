<?php
session_start();

$host ='localhost';
$dbname='kkzrt';
$username='root';
$password='';

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



// reset password felépítése 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $email = $_POST['resetEmail'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Reset token adatbázisba
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $conn->prepare("UPDATE users SET reset_token = :token, reset_expiry = :expiry WHERE email = :email");
            $stmt->execute([
                'token' => $token,
                'expiry' => $expiry,
                'email' => $email
            ]);
            
            // átirányítás reset password php-ra token segítségével
            header("Location: reset-password.php?token=$token");
            exit; 
            
        } else {
            $_SESSION['error'] = "A megadott email cím nem található!";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Rendszerhiba történt!";
    }
}

//  Login felépítés
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Helytelen email vagy jelszó!";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Rendszerhiba történt!";
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
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
        
        .login-container {
            background: linear-gradient(to right, #303639,#b30000);
            max-width: 400px;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 30px 90px rgba(0,0,0,1);
        }
        
        form
        {
            text-align: center;
            color: white;
            font-size: 120%;
        }

        .form-control {
            border-radius: 12px !important;
            padding: 0.8rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--kkzrt-blue);
            box-shadow: 0 0 0 0.2rem rgba(0,75,147,0.15);
        }
        
        .btn-kkzrt {
            background: linear-gradient(to right, #000000, #FF0000);
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 12px !important;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 100%;
        }
        
        .btn-kkzrt:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,75,147,0.2);
        }
        
        .forgot-password-link, .register-link {
            color: white;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 0.5rem;
            font-weight: 500;
        }
        
        .forgot-password-link:hover, .register-link:hover {
            color: #0066cc;
            transform: translateX(3px);
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
        }
        
        .modal-header {
            border-radius: 20px 20px 0 0;
            background: linear-gradient(to right, #303639,#b30000);
            color: white;
        }
        
        .modal-footer {
            border-radius: 0 0 20px 20px;
        }
        
        .alert {
            border-radius: 12px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="icon-container text-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" style="max-width: 200px;" viewBox="0 0 576 512"><path fill="#ffffff" d="M288 0C422.4 0 512 35.2 512 80l0 16 0 32c17.7 0 32 14.3 32 32l0 64c0 17.7-14.3 32-32 32l0 160c0 17.7-14.3 32-32 32l0 32c0 17.7-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32l0-32-192 0 0 32c0 17.7-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32l0-32c-17.7 0-32-14.3-32-32l0-160c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32c0 0 0 0 0 0l0-32s0 0 0 0l0-16C64 35.2 153.6 0 288 0zM128 160l0 96c0 17.7 14.3 32 32 32l112 0 0-160-112 0c-17.7 0-32 14.3-32 32zM304 288l112 0c17.7 0 32-14.3 32-32l0-96c0-17.7-14.3-32-32-32l-112 0 0 160zM144 400a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm288 0a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM384 80c0-8.8-7.2-16-16-16L208 64c-8.8 0-16 7.2-16 16s7.2 16 16 16l160 0c8.8 0 16-7.2 16-16z"/></svg>

            </div>
            
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>
<!------------------------------------------------------------------------------ Bejelentkezés form -------------------------------------------------------------------------------->     
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label style="font-weight: bold;" for="loginEmail" class="form-label">Email cím:</label>
                    <input style="font-weight: bold;" type="email" id="loginEmail" name="loginEmail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label style="font-weight: bold;" for="loginPassword" class="form-label">Jelszó:</label>
                    <input  type="password" id="loginPassword" name="loginPassword" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-kkzrt mb-3">Bejelentkezés</button>
            </form>
            
            <div class="text-center">
                <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" class="forgot-password-link">
                    <i class="fas fa-key me-1"></i>Elfelejtettem a jelszavam
                </a>
                <div class="mt-2">
                    <a href="register.php" class="register-link">
                        <i class="fas fa-user-plus me-1"></i>Regisztrálj!
                    </a>
                </div>
            </div>
        </div>
    </div>

<!-------------------------------------------------------------------------------- Elfelejtett Jelszó -------------------------------------------------------------------------------->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Jelszó visszaállítása</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label for="resetEmail" class="form-label">Add meg az email címed:</label>
                            <input type="email" class="form-control" id="resetEmail" name="resetEmail" required>
                        </div>
                        <button type="submit" name="reset_password" class="btn btn-kkzrt w-100">Jelszó visszaállítása</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>