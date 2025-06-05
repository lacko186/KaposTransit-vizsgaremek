<?php
session_start();

$isApiRequest = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

// API fejléc beállítások
if ($isApiRequest) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Alapbeállítások
$host = 'localhost';
$db_user = 'root';
$db_password = ''; 
$db_name = 'kaposvar';
$port = 3306;
$admin_username = "KaposTransit";
$admin_password = "KaposTransitAdmin997.@";

// Bejelentkezés
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isApiRequest && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = true;
    }
}

// Kijelentkezés
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// DB kapcsolat
try {
    $conn = new mysqli($host, $db_user, $db_password, $db_name, $port);
    
    if ($conn->connect_error) {
        throw new Exception("Kapcsolódási hiba: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    $db_connection_success = true;
} catch (Exception $e) {
    $db_connection_success = false;
    $dbError = "Adatbázis hiba: " . $e->getMessage();
    
    if ($isApiRequest) {
        http_response_code(500);
        echo json_encode(['error' => $dbError]);
        exit;
    }
}

// API kérések
if ($isApiRequest) {
    if (!$db_connection_success) {
        http_response_code(500);
        echo json_encode(['error' => $dbError]);
        exit;
    }
    
    processApiRequest($conn);
    exit;
}

// Admin panel
displayAdminPanel($conn);
$conn->close();

// API kérés feldolgozás
function processApiRequest($conn) {
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestType = isset($_GET['type']) ? $_GET['type'] : 'delays';
    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
    $input = json_decode(file_get_contents('php://input'), TRUE);

    switch ($requestType) {
        case 'news':
            handleNewsRequest($conn, $requestMethod, $id, $input);
            break;
        
        case 'delays':
        default:
            handleDelaysRequest($conn, $requestMethod, $id, $input);
            break;
    }
}

// Hírek kezelése
function handleNewsRequest($conn, $requestMethod, $id, $input) {
    switch ($requestMethod) {
        case 'GET':
            $id ? getNewsById($conn, $id) : getNews($conn);
            break;
        case 'POST':
            createNews($conn, $input);
            break;
        case 'PUT':
            if (!$id) {
                sendError(400, "Hiányzó azonosító");
            }
            updateNews($conn, $id, $input);
            break;
        case 'DELETE':
            if (!$id) {
                sendError(400, "Hiányzó azonosító");
            }
            deleteNews($conn, $id);
            break;
        default:
            sendError(405, "Nem támogatott művelet");
            break;
    }
}

// Késések kezelése
function handleDelaysRequest($conn, $requestMethod, $id, $input) {
    switch ($requestMethod) {
        case 'GET':
            $id ? getDelayById($conn, $id) : getAllDelays($conn);
            break;
        case 'POST':
            createDelay($conn, $input);
            break;
        case 'PUT':
            if (!$id) {
                sendError(400, "Hiányzó azonosító");
            }
            updateDelay($conn, $id, $input);
            break;
        case 'DELETE':
            if (!$id) {
                sendError(400, "Hiányzó azonosító");
            }
            deleteDelay($conn, $id);
            break;
        default:
            sendError(405, "Nem támogatott művelet");
            break;
    }
}

// Hiba küldés
function sendError($code, $message) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// Hírek lekérése
function getNews($conn) {
    $sql = "SELECT h.*, (SELECT COUNT(*) FROM kepek WHERE news_id = h.id) AS image_count 
            FROM hirek h ORDER BY date DESC";
    $result = $conn->query($sql);
    
    if ($result === false) {
        sendError(500, "DB hiba: " . $conn->error);
    }
    
    $news = [];
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
    
    echo json_encode($news);
    exit;
}

// Egy hír lekérése
function getNewsById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM hirek WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendError(404, "Nem található hír");
    }
    
    $news = $result->fetch_assoc();
    
    // Képek lekérése
    $stmt = $conn->prepare("SELECT * FROM kepek WHERE news_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $imageResult = $stmt->get_result();
    
    $images = [];
    while ($row = $imageResult->fetch_assoc()) {
        $images[] = $row;
    }
    
    echo json_encode([
        'news' => $news,
        'images' => $images
    ]);
    exit;
}

// Új hír létrehozás
function createNews($conn, $data) {
    // Adatok ellenőrzése
    if (!isset($data['title']) || !isset($data['details']) || !isset($data['date'])) {
        sendError(400, "Hiányzó mezők");
    }
    
    $conn->begin_transaction();
    
    try {
        // Hír beszúrása
        $stmt = $conn->prepare("INSERT INTO hirek (title, details, date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['title'], $data['details'], $data['date']);
        
        if (!$stmt->execute()) {
            throw new Exception("Hír létrehozási hiba: " . $stmt->error);
        }
        
        $newsId = $conn->insert_id;
        
        // Képek mentése
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                $stmt = $conn->prepare("INSERT INTO kepek (news_id, image_url) VALUES (?, ?)");
                $stmt->bind_param("is", $newsId, $image['image_url']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Kép hiba: " . $stmt->error);
                }
            }
        }
        
        $conn->commit();
        
        // Új adatok lekérése
        $stmt = $conn->prepare("SELECT * FROM hirek WHERE id = ?");
        $stmt->bind_param("i", $newsId);
        $stmt->execute();
        $result = $stmt->get_result();
        $newNews = $result->fetch_assoc();
        
        // Új képek lekérése
        $stmt = $conn->prepare("SELECT * FROM kepek WHERE news_id = ?");
        $stmt->bind_param("i", $newsId);
        $stmt->execute();
        $imageResult = $stmt->get_result();
        
        $images = [];
        while ($row = $imageResult->fetch_assoc()) {
            $images[] = $row;
        }
        
        echo json_encode([
            'news' => $newNews,
            'images' => $images
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        sendError(500, $e->getMessage());
    }
}

// Hír frissítés
function updateNews($conn, $id, $data) {
    // Adatok ellenőrzése
    if (!isset($data['title']) || !isset($data['details']) || !isset($data['date'])) {
        sendError(400, "Hiányzó mezők");
    }
    
    // Hír létezik?
    $checkStmt = $conn->prepare("SELECT id FROM hirek WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        sendError(404, "Nem található hír");
    }
    
    $conn->begin_transaction();
    
    try {
        // Hír frissítése
        $stmt = $conn->prepare("UPDATE hirek SET title = ?, details = ?, date = ? WHERE id = ?");
        $stmt->bind_param("sssi", $data['title'], $data['details'], $data['date'], $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Frissítési hiba: " . $stmt->error);
        }
        
        // Képek kezelése
        if (isset($data['images']) && is_array($data['images'])) {
            // Régi törlése
            $stmt = $conn->prepare("DELETE FROM kepek WHERE news_id = ?");
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Képtörlési hiba: " . $stmt->error);
            }
            
            // Új képek
            foreach ($data['images'] as $image) {
                $stmt = $conn->prepare("INSERT INTO kepek (news_id, image_url) VALUES (?, ?)");
                $stmt->bind_param("is", $id, $image['image_url']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Kép hiba: " . $stmt->error);
                }
            }
        }
        
        $conn->commit();
        
        // Frissített adatok
        $stmt = $conn->prepare("SELECT * FROM hirek WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedNews = $result->fetch_assoc();
        
        // Képek lekérése
        $stmt = $conn->prepare("SELECT * FROM kepek WHERE news_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $imageResult = $stmt->get_result();
        
        $images = [];
        while ($row = $imageResult->fetch_assoc()) {
            $images[] = $row;
        }
        
        echo json_encode([
            'news' => $updatedNews,
            'images' => $images
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        sendError(500, $e->getMessage());
    }
}

// Hír törlése
function deleteNews($conn, $id) {
    // Hír létezik?
    $checkStmt = $conn->prepare("SELECT id FROM hirek WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        sendError(404, "Nem található hír");
    }
    
    $conn->begin_transaction();
    
    try {
        // Képek törlése
        $stmt = $conn->prepare("DELETE FROM kepek WHERE news_id = ?");
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Képtörlési hiba: " . $stmt->error);
        }
        
        // Hír törlése
        $stmt = $conn->prepare("DELETE FROM hirek WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Törlési hiba: " . $stmt->error);
        }
        
        $conn->commit();
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        $conn->rollback();
        sendError(500, $e->getMessage());
    }
}

// Admin panel
function displayAdminPanel($conn) {
    global $db_connection_success, $dbError, $login_error;
    
    $is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    
    $delays = [];
    $stats = [
        'totalDelays' => 0,
        'todayDelays' => 0,
        'avgDelay' => 0
    ];
    
    if ($is_logged_in && $db_connection_success) {
        // Késések lekérése
        $sql = "SELECT * FROM keses ORDER BY datum DESC, ido DESC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $delays[] = $row;
            }
        }
        
        // Statisztikák
        $totalDelays = count($delays);
        $todayDelays = 0;
        $totalMinutes = 0;
        
        foreach ($delays as $delay) {
            $totalMinutes += $delay['keses_perc'];
            
            // Mai késések
            $delayDate = new DateTime($delay['datum']);
            $today = new DateTime('today');
            
            if ($delayDate->format('Y-m-d') === $today->format('Y-m-d')) {
                $todayDelays++;
            }
        }
        
        $avgDelay = $totalDelays > 0 ? round($totalMinutes / $totalDelays) : 0;
        
        $stats = [
            'totalDelays' => $totalDelays,
            'todayDelays' => $todayDelays,
            'avgDelay' => $avgDelay
        ];
    }
    
    // Adatok JSON-ba
    $delaysJson = json_encode($delays);
    $statsJson = json_encode($stats);
    $isLoggedInJson = json_encode($is_logged_in);
    $hasDbErrorJson = json_encode(!$db_connection_success);
    $dbErrorJson = json_encode($dbError ?? '');
    $hasLoginErrorJson = json_encode(isset($login_error) && $login_error);
    
    // HTML kiírás
    outputHtml($delaysJson, $statsJson, $isLoggedInJson, $hasDbErrorJson, $dbErrorJson, $hasLoginErrorJson);
}

// Késések lekérése
function getAllDelays($conn) {
    $sql = "SELECT * FROM keses ORDER BY datum DESC, ido DESC";
    $result = $conn->query($sql);
    
    if ($result === false) {
        sendError(500, "DB hiba: " . $conn->error);
    }
    
    $delays = [];
    while ($row = $result->fetch_assoc()) {
        $delays[] = $row;
    }
    
    echo json_encode($delays);
    exit;
}

// Egy késés lekérése
function getDelayById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM keses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendError(404, "Nem található késés");
    }
    
    $delay = $result->fetch_assoc();
    echo json_encode($delay);
    exit;
}

// Új késés létrehozása
function createDelay($conn, $data) {
    // Adatok ellenőrzése
    if (!isset($data['route_name']) || !isset($data['datum']) || !isset($data['ido']) || !isset($data['keses_perc'])) {
        sendError(400, "Hiányzó mezők");
    }
    
    $stmt = $conn->prepare("INSERT INTO keses (route_name, datum, ido, keses_perc) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $data['route_name'], $data['datum'], $data['ido'], $data['keses_perc']);
    
    if (!$stmt->execute()) {
        sendError(500, "Létrehozási hiba: " . $stmt->error);
    }
    
    $id = $conn->insert_id;
    
    // Új adat lekérése
    $stmt = $conn->prepare("SELECT * FROM keses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $newDelay = $result->fetch_assoc();
    
    echo json_encode($newDelay);
    exit;
}

// Késés frissítése
function updateDelay($conn, $id, $data) {
    // Adatok ellenőrzése
    if (!isset($data['route_name']) || !isset($data['datum']) || !isset($data['ido']) || !isset($data['keses_perc'])) {
        sendError(400, "Hiányzó mezők");
    }
    
    // Késés létezik?
    $checkStmt = $conn->prepare("SELECT id FROM keses WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        sendError(404, "Nem található késés");
    }
    
    // Frissítés
    $stmt = $conn->prepare("UPDATE keses SET route_name = ?, datum = ?, ido = ?, keses_perc = ? WHERE id = ?");
    $stmt->bind_param("sssii", $data['route_name'], $data['datum'], $data['ido'], $data['keses_perc'], $id);
    
    if (!$stmt->execute()) {
        sendError(500, "Frissítési hiba: " . $stmt->error);
    }
    
    // Frissített adat
    $stmt = $conn->prepare("SELECT * FROM keses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $updatedDelay = $result->fetch_assoc();
    
    echo json_encode($updatedDelay);
    exit;
}

// Késés törlése
function deleteDelay($conn, $id) {
    // Késés létezik?
    $checkStmt = $conn->prepare("SELECT id FROM keses WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        sendError(404, "Nem található késés");
    }
    
    // Törlés
    $stmt = $conn->prepare("DELETE FROM keses WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        sendError(500, "Törlési hiba: " . $stmt->error);
    }
    
    echo json_encode(['success' => true]);
    exit;
}

// HTML kiírás
function outputHtml($delaysJson, $statsJson, $isLoggedInJson, $hasDbErrorJson, $dbErrorJson, $hasLoginErrorJson) {
    ?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaposTransit Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-red: #8B0000;
            --dark-gray: #333333;
            --light-gray: #f5f5f5;
        }
        
        body {
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background-color: #000;
            color: white;
            min-height: 100vh;
            border-right: 3px solid var(--dark-red);
        }
        
        .sidebar .nav-link {
            color: white;
            margin: 5px 0;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--dark-red);
            padding-left: 25px;
        }
        
        .sidebar .nav-link.active {
            background-color: var(--dark-red);
            font-weight: bold;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .logo-wrapper {
            padding: 20px 0;
            text-align: center;
            border-bottom: 1px solid var(--dark-gray);
            margin-bottom: 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        
        .logo span {
            color: var(--dark-red);
        }
        
        .content-header {
            background-color: white;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .main-content {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin: 20px;
        }
        
        .card {
            border: none;
            border-radius: 5px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: var(--dark-red);
            color: white;
            font-weight: 600;
        }

        .btn-dark-red {
            background-color: var(--dark-red);
            color: white;
            border: none;
        }
        
        .btn-dark-red:hover {
            background-color: #6b0000;
            color: white;
        }
        
        .table th {
            background-color: var(--dark-gray);
            color: white;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,0.02);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--dark-red);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .action-buttons .btn {
            margin-right: 5px;
        }
        
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header .logo {
            margin-bottom: 15px;
            color: black;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--dark-red);
            border-color: var(--dark-red);
        }
        
        .pagination .page-link {
            color: var(--dark-red);
        }
        
        .alert {
            margin-bottom: 15px;
        }
        
        .modal-header.bg-dark-red {
            background-color: var(--dark-red);
            color: white;
        }
        
        #images-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 10px;
            margin-bottom: 10px;
        }
        
        .image-input-group {
            margin-bottom: 10px;
        }
        
        .preview-image-btn:hover {
            color: #0056b3;
        }
        
        .remove-image-btn:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Login Form -->
    <div id="loginForm" class="login-container">
        <div class="login-header">
            <div class="logo">Kapos<span>Transit</span></div>
            <h4>Admin Bejelentkezés</h4>
        </div>
        <div id="login-alert" class="alert alert-danger d-none" role="alert">
            Hibás felhasználónév vagy jelszó!
        </div>
        <div id="db-error-alert" class="alert alert-danger d-none" role="alert">
            
        </div>
        <form id="loginFormSubmit" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="action" value="login">
            <div class="mb-3">
                <label for="username" class="form-label">Felhasználónév</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Adja meg felhasználónevét">
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Jelszó</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Adja meg jelszavát">
                </div>
            </div>
            <button type="submit" class="btn btn-dark-red w-100">Bejelentkezés</button>
        </form>
    </div>

<!------------------------------------------------------------------------------ admin Panel ------------------------------------------------------------------------------>
    <div id="adminPanel" class="d-none">
        <div class="container-fluid">
            <div class="row">
<!-------------------------------------------------------------------------------- oldalsáv -------------------------------------------------------------------------------->
                <div class="col-md-3 col-lg-2 sidebar">
                    <div class="logo-wrapper">
                        <div class="logo">Kapos<span>Transit</span></div>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" id="dashboard-link">
                                <i class="fas fa-tachometer-alt"></i> Irányítópult
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="delays-link">
                                <i class="fas fa-clock"></i> Késések kezelése
                            </a>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="news-link">
                                <i class="fas fa-newspaper"></i> Hírek kezelése
                            </a>
                        </li>
                        

                        <div class="col-md-9 col-lg-10 ms-sm-auto px-0">
    
                        <li class="nav-item mt-5">
                            <a class="nav-link" href="<?php echo $_SERVER['PHP_SELF']; ?>?action=logout" id="logout-link">
                                <i class="fas fa-sign-out-alt"></i> Kijelentkezés
                            </a>
                        </li>
                        
                    </ul>
                    
                </div>
                
<!------------------------------------------------------------------------------ fő tartalom ------------------------------------------------------------------------------>
                <div class="col-md-9 col-lg-10 ms-sm-auto px-0">
                    <!-- Fejléc -->
                    <div class="content-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0" id="page-title">Irányítópult</h4>
                        </div>
                        <div class="user-info">
                            <div class="user-avatar">A</div>
                            <div>Admin</div>
                        </div>
                    </div>
                    
<!------------------------------------------------------------------------------ Irányítópult ------------------------------------------------------------------------------>
                    <div id="dashboard-view">
                        <div class="main-content">
                            <div id="dashboard-alert" class="alert alert-danger d-none" role="alert">
                                Adatok betöltése sikertelen. Ellenőrizze az adatbázis kapcsolatot.
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h1 class="display-4 mb-2" id="total-delays">0</h1>
                                            <p class="mb-0 text-muted">Összes késés</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h1 class="display-4 mb-2" id="today-delays">0</h1>
                                            <p class="mb-0 text-muted">Mai késések</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h1 class="display-4 mb-2" id="avg-delay">0 perc</h1>
                                            <p class="mb-0 text-muted">Átlagos késés</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    Legutóbbi késések
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="recent-delays-table">
                                            <thead>
                                                <tr>
                                                    <th>Járat</th>
                                                    <th>Dátum</th>
                                                    <th>Idő</th>
                                                    <th>Késés (perc)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
<!-------------------------------------------------------------------------------- késések kezelése nézet -------------------------------------------------------------------------------->
                    <div id="delays-view" class="d-none">
                        <div class="main-content">
                            <div id="delays-alert" class="alert d-none" role="alert">

                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Késések listája</h5>
                                <button class="btn btn-dark-red" data-bs-toggle="modal" data-bs-target="#addDelayModal">
                                    <i class="fas fa-plus"></i> Új késés rögzítése
                                </button>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="delays-table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Járat</th>
                                                    <th>Dátum</th>
                                                    <th>Idő</th>
                                                    <th>Késés (perc)</th>
                                                    <th>Műveletek</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<!-------------------------------------------------------------------------------- hírek kezelése nézet-------------------------------------------------------------------------------->
<div id="news-view" class="d-none">
    <div class="main-content">
        <div id="news-alert" class="alert d-none" role="alert">
        
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Hírek listája</h5>
            <button class="btn btn-dark-red" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                <i class="fas fa-plus"></i> Új hír rögzítése
            </button>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="news-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cím</th>
                                <th>Dátum</th>
                                <th>Képek</th>
                                <th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!------------------------------------------------------------------------------ új hír, hírek szerkesztése ------------------------------------------------------------------------------>
                    <div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-dark-red text-white">
                                    <h5 class="modal-title" id="addNewsModalLabel">Új hír rögzítése</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="news-modal-alert" class="alert alert-danger d-none" role="alert">

                                    </div>
                                    <form id="newsForm">
                                        <input type="hidden" id="news-id" value="">
                                        <div class="mb-3">
                                            <label for="news-title" class="form-label">Cím</label>
                                            <input type="text" class="form-control" id="news-title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="news-date" class="form-label">Dátum</label>
                                            <input type="date" class="form-control" id="news-date" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="news-details" class="form-label">Leírás</label>
                                            <textarea class="form-control" id="news-details" rows="4" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Képek</label>
                                            <div id="images-container" class="mb-2">
<!------------------------------------------------------------------------------képek konténer, kép hozzáadás ------------------------------------------------------------------------------>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="add-image-btn">
                                                <i class="fas fa-plus"></i> Kép hozzáadása
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                                    <button type="button" class="btn btn-dark-red" id="saveNewsBtn">Mentés</button>
                                </div>
                            </div>
                        </div>
                    </div>
<!-------------------------------------------------------------------------------- hír törlés  -------------------------------------------------------------------------------->
                    <div class="modal fade" id="deleteNewsModal" tabindex="-1" aria-labelledby="deleteNewsModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="deleteNewsModalLabel">Hír törlése</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Biztosan törölni szeretné ezt a hírt? Ez a művelet nem visszavonható.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteNewsBtn">Törlés</button>
                                </div>
                            </div>
                        </div>
                    </div>
<!------------------------------------------------------------------------------ kép előnézet -------------------------------------------------------------------------------->
                    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="imagePreviewModalLabel">Kép előnézet</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="preview-image" src="" alt="Előnézet" class="img-fluid">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<!-------------------------------------------------------------------------------- Új késés hozzáadás, szerkesztés-------------------------------------------------------------------------------->
<div class="modal fade" id="addDelayModal" tabindex="-1" aria-labelledby="addDelayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDelayModalLabel">Új késés rögzítése</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
            </div>
            <div class="modal-body">
                <div id="modal-alert" class="alert alert-danger d-none" role="alert">

                </div>
                <form id="delayForm">
                    <input type="hidden" id="delay-id" value="">
                    <div class="mb-3">
                        <label for="route-name" class="form-label">Járat száma</label>
                        <input type="text" class="form-control" id="route-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="delay-date" class="form-label">Dátum</label>
                        <input type="date" class="form-control" id="delay-date" required>
                    </div>
                    <div class="mb-3">
                        <label for="delay-time" class="form-label">Idő</label>
                        <input type="time" class="form-control" id="delay-time" required>
                    </div>
                    <div class="mb-3">
                        <label for="delay-minutes" class="form-label">Késés (perc)</label>
                        <input type="number" class="form-control" id="delay-minutes" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                <button type="button" class="btn btn-dark-red" id="saveDelayBtn">Mentés</button>
            </div>
        </div>
    </div>
</div>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
const API_URL = window.location.href;
let news = []; 
let currentNewsId = null; 

// betöltéskor
$(document).ready(function() {
    // Adatok inicializálása PHP-ból
    const isLoggedIn = <?php echo $isLoggedInJson; ?>;
    const hasDbError = <?php echo $hasDbErrorJson; ?>;
    const dbError = <?php echo $dbErrorJson; ?>;
    const hasLoginError = <?php echo $hasLoginErrorJson; ?>;
    const delays = <?php echo $delaysJson; ?>;
    const stats = <?php echo $statsJson; ?>;
    // Admin panel 
    initAdminPanel(isLoggedIn, hasDbError, dbError, hasLoginError, delays, stats);
    
    // menü eseménykezelők
    $("#dashboard-link").on("click", e => { e.preventDefault(); showView("dashboard"); });
    $("#delays-link").on("click", e => { e.preventDefault(); showView("delays"); });
    $("#news-link").on("click", e => { 
        e.preventDefault(); 
        showView("news"); 
        loadNews(); 
    });
    
    // gombok eseménykezelői
    $("#saveDelayBtn").on("click", saveDelay);
    $("#saveNewsBtn").on("click", saveNews);
    $("#confirmDeleteNewsBtn").on("click", () => {
        if(currentNewsId) deleteNews(currentNewsId);
    });
    
    // új kép hozzáadás gomb
    $("#add-image-btn").on("click", addImageField);
    
    // új hír gomb 
    $("button[data-bs-target='#addNewsModal']").on("click", () => {
        resetNewsForm();
        $("#addNewsModalLabel").text("Új hír rögzítése");
    });
});

 // admin panel inicializálása
function initAdminPanel(isLoggedIn, hasDbError, dbError, hasLoginError, delays, stats) {
    if (isLoggedIn) {
        // Bejelentkezett
        $("#loginForm").addClass("d-none");
        $("#adminPanel").removeClass("d-none");
        populateDashboard(delays, stats);
        populateDelaysTable(delays);
    } else {
        // Bejelentkezési
        $("#loginForm").removeClass("d-none");
        $("#adminPanel").addClass("d-none");
        
        if (hasLoginError) {
            $("#login-alert").removeClass("d-none");
        }
    }
    
    // adatbázis hiba kezelése
    if (hasDbError) {
        $("#db-error-alert").removeClass("d-none").text(dbError);
        $("#dashboard-alert").removeClass("d-none");
    }
}

//nézetek közötti váltás
function showView(view) {
    // nézetek elrejtése
    $("#dashboard-view, #delays-view, #news-view").addClass("d-none");
    $(".nav-link").removeClass("active");
    
    // kiválasztott nézet 
    const viewConfig = {
        "dashboard": { title: "Irányítópult" },
        "delays": { title: "Késések kezelése" },
        "news": { title: "Hírek kezelése" }
    };
    
    if (viewConfig[view]) {
        $(`#${view}-view`).removeClass("d-none");
        $(`#${view}-link`).addClass("active");
        $("#page-title").text(viewConfig[view].title);
    }
}

 // irányítópult feltöltése adatokkaé
function populateDashboard(delays, stats) {
    // statisztika frissítése
    $("#total-delays").text(stats.totalDelays);
    $("#today-delays").text(stats.todayDelays);
    $("#avg-delay").text(stats.avgDelay + " perc");
    
    // legutóbbi késések 
    const recentDelaysTable = $("#recent-delays-table tbody");
    recentDelaysTable.empty();
    
    if (delays.length === 0) {
        recentDelaysTable.append('<tr><td colspan="4" class="text-center">Nincs rögzített késés</td></tr>');
    } else {
        // első 5 késés megjelenítése
        delays.slice(0, 5).forEach(delay => {
            recentDelaysTable.append(`
                <tr>
                    <td>${delay.route_name}</td>
                    <td>${formatDate(delay.datum)}</td>
                    <td>${delay.ido}</td>
                    <td>${delay.keses_perc}</td>
                </tr>
            `);
        });
    }
}

//dátum formázása
function formatDate(dateString) {
    const parts = dateString.split('-');
    return parts.length === 3 ? `${parts[2]}.${parts[1]}.${parts[0]}` : dateString;
}

//késések kezelése, táblázat feltölt
function populateDelaysTable(delays) {
    const delaysTable = $("#delays-table tbody");
    delaysTable.empty();
    
    if (delays.length === 0) {
        delaysTable.append('<tr><td colspan="6" class="text-center">Nincs rögzített késés</td></tr>');
        return;
    }
    
    delays.forEach(delay => {
        delaysTable.append(`
            <tr>
                <td>${delay.id}</td>
                <td>${delay.route_name}</td>
                <td>${formatDate(delay.datum)}</td>
                <td>${delay.ido}</td>
                <td>${delay.keses_perc}</td>
                <td class="action-buttons">
                    <button class="btn btn-sm btn-primary edit-delay-btn" data-id="${delay.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-delay-btn" data-id="${delay.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
    
    // szerkesztés és törlés gomb
    $(".edit-delay-btn").on("click", function() {
        editDelay($(this).data("id"), delays);
    });
    
    $(".delete-delay-btn").on("click", function() {
        deleteDelay($(this).data("id"));
    });
}

//késés szerkesztés
function editDelay(id, delays) {
    const delay = delays.find(d => d.id == id);
    
    if (delay) {
        // kitöltés
        $("#delay-id").val(delay.id);
        $("#route-name").val(delay.route_name);
        $("#delay-date").val(delay.datum);
        $("#delay-time").val(delay.ido);
        $("#delay-minutes").val(delay.keses_perc);
        
        // cím és megjelenítés
        $("#addDelayModalLabel").text("Késés szerkesztése");
        $("#addDelayModal").modal("show");
    }
}
//lésés törlése
function deleteDelay(id) {
    if (confirm("Biztosan törölni szeretné ezt a késést?")) {
        apiRequest(`${API_URL}?id=${id}`, "DELETE")
            .then(() => location.reload())
            .catch(error => showAlert("delays-alert", error.message, "danger"));
    }
}
//késés mentése 
function saveDelay() {
     
    const id = $("#delay-id").val();
    const routeName = $("#route-name").val();
    const date = $("#delay-date").val();
    const time = $("#delay-time").val();
    const minutes = $("#delay-minutes").val();
    
    // ellenőrzés
    if (!routeName || !date || !time || !minutes) {
        showAlert("modal-alert", "Minden mező kitöltése kötelező!");
        return;
    }
    
    // szerkezet
    const data = {
        route_name: routeName,
        datum: date,
        ido: time,
        keses_perc: parseInt(minutes)
    };
    
    // kérés
    const method = id ? "PUT" : "POST";
    const url = id ? `${API_URL}?id=${id}` : API_URL;
    
    apiRequest(url, method, data)
        .then(() => {
            $("#addDelayModal").modal("hide");
            location.reload();
        })
        .catch(error => showAlert("modal-alert", error.message));
}


 //hírek betöltése
function loadNews() {
    apiRequest(`${API_URL}?type=news`, "GET")
        .then(result => {
            news = result;
            populateNewsTable();
        })
        .catch(error => showAlert("news-alert", error.message, "danger"));
}

//hírek táblázat feltöltés
function populateNewsTable() {
    const tableBody = $("#news-table tbody");
    tableBody.empty();
    
    if (news.length === 0) {
        tableBody.append('<tr><td colspan="5" class="text-center">Nincs rögzített hír</td></tr>');
        return;
    }
    
    news.forEach(item => {
        tableBody.append(`
            <tr>
                <td>${item.id}</td>
                <td>${item.title}</td>
                <td>${formatDateFromISO(item.date)}</td>
                <td>${item.image_count || 0}</td>
                <td class="action-buttons">
                    <button class="btn btn-sm btn-info view-news-btn" data-id="${item.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary edit-news-btn" data-id="${item.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-news-btn" data-id="${item.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
    
    // Gomb kattintás 
    $(".view-news-btn").on("click", function() {
        viewNews($(this).data("id"));
    });
    
    $(".edit-news-btn").on("click", function() {
        editNews($(this).data("id"));
    });
    
    $(".delete-news-btn").on("click", function() {
        currentNewsId = $(this).data("id");
        $("#deleteNewsModal").modal("show");
    });
}
//hír megtekintés
function viewNews(id) {
    apiRequest(`${API_URL}?type=news&id=${id}`, "GET")
        .then(data => {
            // létrehozás megjelenítés
            createNewsViewModal(data);
        })
        .catch(error => showAlert("news-alert", error.message, "danger"));
}


 //hír megtekintő
function createNewsViewModal(data) {
    const formattedDate = formatDateFromISO(data.news.date);
    
    // modal HTML modal(párbeszédpanel)
    const viewModal = `
        <div class="modal fade" id="viewNewsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark-red text-white">
                        <h5 class="modal-title">Hír részletei</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h4>${data.news.title}</h4>
                        <p class="text-muted">${formattedDate}</p>
                        <div class="my-3">
                            ${data.news.details.replace(/\n/g, '<br>')}
                        </div>
                        ${data.images.length > 0 ? '<h5 class="mt-4">Képek:</h5>' : ''}
                        <div class="row mt-2">
                            ${data.images.map(img => `
                                <div class="col-md-4 mb-3">
                                    <img src="${img.image_url}" alt="Hír kép" class="img-thumbnail preview-img" 
                                        style="cursor: pointer; height: 150px; object-fit: cover;">
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Meglévő modal eltávolítása
    $("#viewNewsModal").remove();
    
    // hozzáadása és megjelenítése
    $("body").append(viewModal);
    $("#viewNewsModal").modal("show");
    
    // kép kattintás || előnézet 
    $(".preview-img").on("click", function() {
        $("#preview-image").attr("src", $(this).attr("src"));
        $("#imagePreviewModal").modal("show");
    });
}


//hír törlés
function deleteNews(id) {
    apiRequest(`${API_URL}?type=news&id=${id}`, "DELETE")
        .then(() => {
            $("#deleteNewsModal").modal("hide");
            loadNews();
            showAlert("news-alert", "A hír sikeresen törölve!", "success");
        })
        .catch(error => {
            $("#deleteNewsModal").modal("hide");
            showAlert("news-alert", error.message, "danger");
        });
}

//hír szerkesztés
function editNews(id) {
    apiRequest(`${API_URL}?type=news&id=${id}`, "GET")
        .then(data => {
            resetNewsForm();
            $("#news-id").val(data.news.id);
            $("#news-title").val(data.news.title);
            $("#news-date").val(data.news.date.split('T')[0]);
            $("#news-details").val(data.news.details);
            
            // képek hozzáadása
            $("#images-container").empty();
            if (data.images.length > 0) {
                data.images.forEach(image => addImageField(image.image_url));
            } else {
                addImageField();
            }
            
            // cím és megjelenítés
            $("#addNewsModalLabel").text("Hír szerkesztése");
            $("#addNewsModal").modal("show");
        })
        .catch(error => showAlert("news-alert", error.message, "danger"));
}


 //Hír űrlap alaphelyzet
function resetNewsForm() {
    $("#news-id").val("");
    $("#news-title").val("");
    $("#news-date").val(new Date().toISOString().split('T')[0]);
    $("#news-details").val("");
    $("#images-container").empty();
    addImageField();
}


 //új kép beviteli mező hozzáadása
function addImageField(imageUrl = '') {
    const imageField = `
        <div class="input-group mb-2 image-input-group">
            <input type="text" class="form-control image-url" placeholder="Kép URL" value="${imageUrl}">
            <button class="btn btn-outline-secondary preview-image-btn" type="button">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-outline-danger remove-image-btn" type="button">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    $("#images-container").append(imageField);
    
    // kép kezelő gombok eltávolít, előnézet 
    $(".remove-image-btn").off("click").on("click", function() {
        $(this).closest('.image-input-group').remove();
    });
    
    $(".preview-image-btn").off("click").on("click", function() {
        const imageUrl = $(this).siblings('.image-url').val();
        if (imageUrl) {
            $("#preview-image").attr("src", imageUrl);
            $("#imagePreviewModal").modal("show");
        } else {
            alert("Nincs megadva kép URL!");
        }
    });
}
//hír mentése
function saveNews() {
    // adatok lekérése
    const id = $("#news-id").val();
    const title = $("#news-title").val();
    const date = $("#news-date").val();
    const details = $("#news-details").val();
    
    // kép URL-ek összegyűjtése
    const images = [];
    $('.image-url').each(function() {
        const url = $(this).val().trim();
        if (url) images.push({ image_url: url });
    });
    
    // ellenőrzés
    if (!title || !date || !details) {
        showAlert("news-modal-alert", "A cím, dátum és részletek mezők kitöltése kötelező!");
        return;
    }
    
    // kérés
    const data = { title, date, details, images };
    const method = id ? "PUT" : "POST";
    const url = id ? `${API_URL}?type=news&id=${id}` : `${API_URL}?type=news`;
    
    apiRequest(url, method, data)
        .then(() => {
            $("#addNewsModal").modal("hide");
            loadNews();
            showAlert("news-alert", "A hír sikeresen mentve!", "success");
        })
        .catch(error => showAlert("news-modal-alert", error.message));
}

// API kérés kezelés
function apiRequest(url, method, data = null) {
    const options = {
        method: method,
        headers: {
            "Accept": "application/json"
        }
    };
    
    if (data) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
    }
    
    return fetch(url, options)
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || "Hiba történt a művelet során");
                });
            }
            return response.json();
        });
}


 // figyelmeztető üzenet
function showAlert(elementId, message, type = "danger") {
    const alertEl = $(`#${elementId}`);
    alertEl.text(message);
    alertEl.removeClass("d-none alert-success alert-danger alert-warning");
    alertEl.addClass(`alert-${type}`);
    
    // 3 mp után elrejtés
    setTimeout(() => alertEl.addClass("d-none"), 3000);
}

//dátum formázása
function formatDateFromISO(isoDateString) {
    const date = new Date(isoDateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}.${month}.${day}`;
}
        </script>
    </body>
</html>
<?php
}