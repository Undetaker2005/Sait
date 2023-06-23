<link rel="stylesheet" href="css/Edit.css">
<title>LunAnime</title>

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

<?php
// Підключення до бази даних
include_once "../../database/db.php";
session_start(); // Ініціалізація сесії
global $conn;

// Функція для додавання аніме
function addAnime($title, $image, $shlyh, $id = null) {
    global $conn;

    // Запит до бази даних
    $query = "INSERT INTO news (title, image, shlyh";
    
    // Додавання ID до запиту, якщо він вказаний
    if ($id !== null) {
        $query .= ", id";
    }
    
    $query .= ") VALUES (?, ?, ?";
    
    // Додавання місця для ID, якщо він вказаний
    if ($id !== null) {
        $query .= ", ?";
    }
    
    $query .= ")";

    // Підготовка запиту
    $statement = $conn->prepare($query);

    // Прив'язка значень до запиту
    if ($id !== null) {
        $statement->bind_param("sssi", $title, $image, $shlyh, $id);
    } else {
        $statement->bind_param("sss", $title, $image, $shlyh);
    }

    // Виконання запиту
    $statement->execute();

    // Перевірка успішності додавання аніме
    if ($statement->affected_rows > 0) {
        return true; // Додавання аніме успішне
    } else {
        return false; // Додавання аніме не вдалось
    }
}



// Функція для видалення аніме
function deleteAnime($id) {
    global $conn;

    // Запит до бази даних
    $query = "DELETE FROM news WHERE id = ?";

    // Підготовка запиту
    $statement = $conn->prepare($query);

    // Прив'язка значення ID до запиту
    $statement->bind_param("i", $id);

    // Виконання запиту
    $statement->execute();

    // Перевірка успішності видалення аніме
    if ($statement->affected_rows > 0) {
        return true; // Видалення аніме успішне
    } else {
        return false; // Видалення аніме не вдалось
    }
}

// Функція для редагування аніме
function editAnime($id, $title, $image, $shlyh) {
    global $conn;

    // Запит до бази даних
    $query = "UPDATE news SET title = ?, image = ?, shlyh = ? WHERE id = ?";

    // Підготовка запиту
    $statement = $conn->prepare($query);

    // Прив'язка значень до запиту
    $statement->bind_param("sssi", $title, $image, $shlyh, $id);

    // Виконання запиту
    $statement->execute();

    // Перевірка успішності редагування аніме
    if ($statement->affected_rows > 0) {
        return true; // Редагування аніме успішне
    } else {
        return false; // Редагування аніме не вдалось
    }
}
// Функція для відображення бази даних аніме
function displayAnimeDatabase($searchId = null, $searchTitle = null) {
    global $conn;
    $counter = 0;
    // Запит до бази даних з урахуванням пошуку
    $query = "SELECT id, title, image, shlyh FROM news";

    // Перевірка, чи вказано значення пошуку по ID або назві
    if ($searchId !== null || $searchTitle !== null) {
        $query .= " WHERE";
        $conditions = [];

        if ($searchId !== null) {
            $conditions[] = " id = " . $searchId;
        }

        if ($searchTitle !== null) {
            $conditions[] = " title LIKE '%" . $searchTitle . "%'";
        }

        $query .= implode(" AND", $conditions);
    }

    // Виконання запиту
    $result = $conn->query($query);

    // Перевірка наявності результатів
    if ($result->num_rows > 0) {
        // Виведення результатів з оформленням
        echo '<div class="anime-database">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="anime-entry">';
            echo '<span class="entry-label">ID:</span>' . $row['id'] . '<br>';
            echo '<span class="entry-label">Назва:</span>' . $row['title'] . '<br>';
            echo '<span class="entry-label">Зображення:</span>' . $row['image'] . '<br>';
            echo '<span class="entry-label">Шлях:</span>' . $row['shlyh'] . '<br>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo "База даних аніме порожня.";
    }
}

if (isset($_POST['addAnime'])) {
    $id = isset($_POST['animeId']) ? $_POST['animeId'] : null; // Отримання значення id з форми
    $title = $_POST['title'];
    $image = $_POST['image'];
    $shlyh = $_POST['shlyh'];

    // Виклик функції для додавання аніме
    if (addAnime($title, $image, $shlyh, $id)) { // Передача значення id до функції
        // Додавання аніме успішне
        echo "Аніме було додано.";
    } else {
        // Додавання аніме не вдалось
        echo "Не вдалося додати аніме.";
    }
    header("Location: Edit.php");
    exit();
}


// Перевірка, чи користувач натиснув кнопку "Видалити аніме"
if (isset($_POST['deleteAnime'])) {
    $id = isset($_POST['animeId']) ? $_POST['animeId'] : null;


    // Виклик функції для видалення аніме
    if (deleteAnime($id)) {
        // Видалення аніме успішне
        echo "Аніме було видалено.";
    } else {
        // Видалення аніме не вдалось
        echo "Не вдалося видалити аніме.";
    }
    header("Location: Edit.php");
    exit();
}

// Перевірка, чи користувач натиснув кнопку "Редагувати аніме"
if (isset($_POST['editAnime'])) {
    $id = isset($_POST['animeId']) ? $_POST['animeId'] : null;;
    $title = $_POST['title'];
    $image = $_POST['image'];
    $shlyh = $_POST['shlyh'];

    // Виклик функції для редагування аніме
    if (editAnime($id, $title, $image, $shlyh)) {
        // Редагування аніме успішне
        echo "Аніме було відредаговано.";
    } else {
        // Редагування аніме не вдалось
        echo "Не вдалося відредагувати аніме.";
    }
    header("Location: Edit.php");
    exit();
}
?>

<form action="Kabinet.php" method="post">
    <button type="submit" name="GoToKabinet">Кабінет</button>
</form>
<form action="Genre.php" method="post">
    <button type="submit" name="GoToGenres">Жанри</button>
</form>
<!-- Форма для додавання аніме -->
<form action="Edit.php" method="post">
    <h3>Додати аніме</h3>
    <label for="animeId">ID:</label>
    <input type="text" name="animeId">
    <label for="title">Назва:</label>
    <input type="text" name="title" required>
    <label for="image">Зображення:</label>
    <input type="text" name="image" required>
    <label for="shlyh">Шлях:</label>
    <input type="text" name="shlyh" required>
    <button type="submit" name="addAnime">Додати</button>
</form>



<!-- Форма для видалення аніме -->
<form action="" method="post">
    <h3>Видалити аніме</h3>
    <label for="animeId">ID аніме:</label>
    <input type="text" name="animeId" required>
    <button type="submit" name="deleteAnime">Видалити</button>
</form>

<!-- Форма для редагування аніме -->
<form action="" method="post">
    <h3>Редагувати аніме</h3>
    <label for="animeId">ID аніме:</label>
    <input type="text" name="animeId" required>
    <label for="title">Нова назва:</label>
    <input type="text" name="title" required>
    <label for="image">Нове зображення:</label>
    <input type="text" name="image" required>
    <label for="shlyh">Новий шлях:</label>
    <input type="text" name="shlyh" required>
    <button type="submit" name="editAnime">Редагувати</button>
</form>
<form action="" method="get" class="search-form">
    <h3>Пошук аніме</h3>
    <label for="searchId">ID:</label>
    <input type="text" name="searchId">
    <label for="searchTitle">Назва:</label>
    <input type="text" name="searchTitle">
    <button type="submit" name="searchAnime">Пошук</button>
    <button type="submit" name="showAll">Всі записи</button>
</form>
<div style="text-align: center;">
  <h3>База даних аніме</h3>
</div>
<div class = "container">
<?php
// Перевірка, чи користувач натиснув кнопку "Пошук аніме" або "Всі записи"
if (isset($_GET['searchAnime']) || isset($_GET['showAll'])) {
    $searchId = $_GET['searchId'];
    $searchTitle = $_GET['searchTitle'];

    // Перевірка, чи введені значення пошуку
    if (!empty($searchId) || !empty($searchTitle)) {
        // Виклик функції з передачею значень пошуку
        displayAnimeDatabase($searchId, $searchTitle);
    } elseif (isset($_GET['showAll'])) {
        // Виклик функції без значень пошуку (виведення всіх записів)
        displayAnimeDatabase();
    } else {
        // Виведення повідомлення для користувача
        echo "Введіть ID або назву для пошуку.";
    }
} 
?>
</div>
