
<?php
// Підключення до бази даних
include_once "../../database/db.php";
session_start(); // Ініціалізація сесії
global $conn;

// Функція для отримання ролі користувача з бази даних
function getUserRole($userId) {
    global $conn;

    // Запит до бази даних
    $query = "SELECT role FROM users WHERE id = ?";

    // Підготовка запиту
    $statement = $conn->prepare($query);

    // Прив'язка значення ID до запиту
    $statement->bind_param("i", $userId);

    // Виконання запиту
    $statement->execute();

    // Отримання результатів запиту
    $result = $statement->get_result();

    // Перевірка, чи є результати
    if ($result->num_rows > 0) {
        // Отримання рядка з даними користувача
        $row = $result->fetch_assoc();
        return $row['role'];
    } else {
        return null;
    }
}

// Отримання даних користувача
$user_id = $_SESSION['user_id']; // ID користувача з сесії
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$username = "";
$last_online = "";
$is_online = false;

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $username = $row['login'];
    $last_online = $row['last_online'];

    // Перевірка часу останнього входу користувача
    $current_time = time();
    $last_online_timestamp = strtotime($last_online);
    $time_diff = $current_time - $last_online_timestamp;
    $is_online = ($time_diff < 300); // Часовий проміжок 300 секунд (5 хвилин)

    // Оновлення часу останнього входу користувача
    $stmt = $conn->prepare("UPDATE users SET last_online = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Отримання ролі користувача
    $user_role = getUserRole($user_id);
}

// Виведення даних користувача на сторінку
?>

<!DOCTYPE html>
<link rel="stylesheet" href="css/kabinet.css">
<html>
<head>
    <title>LunAnime</title>
    <style>
        .online-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: <?php echo ($is_online) ? 'green' : 'gray'; ?>;
            display: inline-block;
            margin-right: 5px;
        }
    </style>
</head>
   <!-- icon.Сайта -->
<body>
    <link rel="apple-touch-icon" sizes="57x57" href="../../icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../../icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../../icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../../icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../../icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../../icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../../icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../../icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="../../icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../../icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../icon/favicon-16x16.png">
    <link rel="manifest" href="../../icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</body>
<!--кінець icon  -->
<body>
    <div class="container">
        <div class="main-info">
            <div class="avatar-wrapper">
                <?php if (isset($user_avatar)): ?>
                    <img src="<?php echo $user_avatar; ?>" alt="Аватар">
                <?php else: ?>
                    <img src="img/Zaglushca.jpg" alt="Заглушка аватарки" onclick="document.querySelector('input[type=file]').click()">
                <?php endif; ?>
            </div>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="avatar" style="display:none;">
            </form>
            <div>
                <h1><?php echo $username; ?></h1>
                <p>Роль: <?php echo $user_role; ?></p>
                <p>
                    <span class="online-status"></span>
                    <?php echo ($is_online) ? 'В мережі' : 'Був в мережі'; ?>
                    <?php echo ($is_online) ? '' : ' о ' . $last_online; ?>
                </p>
                <p>Переглянуто аніме: 50</p>
            </div>
        </div>
    <div class="settings">
        <form action="" method="post">
            <button type="submit" name="GoToHome" class="button">Головна сторінка</button>
        </form>
            <button class="button">Налаштування</button>
        <form action="Friends.php" method="post">
            <button type="submit" name="GoToFriends" class="button">Друзі</button>
        </form>
        <form action="Role.php" method="post">
            <button type="submit" name="GoToRole" class="button">Роль</button>
        </form>
        <form action="" method="post">
            <button type="submit" name="logout" class="button">Вийти з профілю</button>
        </form>
        
        <?php if ($user_role == 'Модератор' || $user_role == 'Адмін'): ?>
            <form action="Edit.php" method="post">
                <button type="submit" name="Edit" class="button">Додавання аніме</button>
            </form>
        <?php endif; ?>
    </div>
    </div>
</body>
<?php
// Перевірка, чи користувач натиснув кнопку "Вийти з профілю"
if (isset($_POST['logout'])) {
    // Виконання дій для виходу з профілю, наприклад, знищення сесії
    // або будь-які інші необхідні дії
    session_destroy();
    // Перенаправлення на головну сторінку
    header("Location: ../login.php");
    exit();
}

// Перевірка, чи користувач натиснув кнопку "Головна сторінка"
if (isset($_POST['GoToHome'])) {
    // Перенаправлення на головну сторінку
    header("Location: ../../admin.php");
    exit();
}
?>
<script src="js/Reload.js"></script>
</html>
