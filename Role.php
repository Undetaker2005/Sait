<?php
session_start();

// Підключення до бази даних
$host = "localhost";
$user = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Перевірка, чи вибрана роль зберігається у сесії
if (!isset($_SESSION['role'])) {
    // Задайте роль за замовчуванням
    $_SESSION['role'] = 'Салага';
}

// Перевірка, чи користувач увійшов в систему
if (isset($_SESSION['user_id'])) {
    // Отримання ID користувача з сесії
    $userId = $_SESSION['user_id'];

    // Отримання даних про користувача з бази даних
    $user = getUserById($userId);

    // Перевірка, чи користувач знайдений
    if ($user) {
        // Обробка запиту на зміну ролі
        if (isset($_POST['role'])) {
            // Отримання вибраної ролі з форми
            $selectedRole = $_POST['role'];

            // Перевірка, чи вибрана роль є допустимою
            if (in_array($selectedRole, getAvailableRoles($user))) {
                // Збереження вибраної ролі в базі даних
                saveUserRole($userId, $selectedRole);

                // Оновлення вибраної ролі у сесії
                $_SESSION['role'] = $selectedRole;

                // Повідомлення про успішне вибрання ролі
                $message = 'Роль успішно змінена.';
            }
        }
    }
}

// Функція для отримання даних про користувача з бази даних за його ID
function getUserById($userId) {
    global $conn;

    // Запит до бази даних
    $query = "SELECT * FROM users WHERE id = ?";

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
        return $row;
    } else {
        return null;
    }
}

// Функція для збереження ролі користувача в базі даних
function saveUserRole($userId, $role) {
    global $conn;
    // Запит до бази даних
    $query = "UPDATE users SET role = ? WHERE id = ?";

    // Підготовка запиту
    $statement = $conn->prepare($query);

    // Прив'язка значень ролі та ID до запиту
    $statement->bind_param("si", $role, $userId);

    // Виконання запиту
    $statement->execute();
}

// Функція для отримання списку доступних ролей для користувача
function getAvailableRoles($user) {
    $roles = ['Салага'];

    // Додавання ролі 'Стажер' тільки якщо пройшло більше 6 місяців з дати реєстрації користувача
    $registrationDate = strtotime($user['registration_time']);
    $currentDate = time();
    $sixMonthsAgo = strtotime('-6 months', $currentDate);

    if ($registrationDate <= $sixMonthsAgo) {
        $roles[] = 'Стажер';
    }

    // Додавання ролі 'Староста' тільки якщо пройшло більше 18 місяців з дати реєстрації користувача
    $eighteenMonthsAgo = strtotime('-18 months', $currentDate);

    if ($registrationDate <= $eighteenMonthsAgo) {
        $roles[] = 'Староста';
    }

    return $roles;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>LunAnime</title>
    <style>
        .available-role {
            color: green;
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
    <?php if ($user): ?>
        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <h2>Доступні Ролі:</h2>
            <?php $allowedRoles = getAvailableRoles($user); ?>
            <?php foreach ($allowedRoles as $role): ?>
                <button type="submit" name="role" value="<?php echo $role; ?>" class="available-role"><?php echo $role; ?></button>
            <?php endforeach; ?>
        </form>
        <form action="Kabinet.php" method="post">
            <button type="submit" name="GoToKabinet">Кабінет</button>
        </form>
    <?php else: ?>
        <p>Ви не увійшли в систему.</p>
    <?php endif; ?>
</body>
</html>
