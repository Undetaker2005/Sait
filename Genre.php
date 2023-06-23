<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>LunAnime</title>
  <link rel="stylesheet" href="css/genre.css">
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

<?php
// Підключення до бази даних
include_once "../../database/db.php";
session_start(); // Ініціалізація сесії
global $conn;

// Запит для вибірки даних з таблиці "news"
$sql_news = "SELECT id, title FROM news";
$result_news = $conn->query($sql_news);

// Запит для вибірки даних з таблиці "genres"
$sql_genres = "SELECT * FROM genres";
$result_genres = $conn->query($sql_genres);

// Запит для вибірки даних з таблиці "newsgenres" з'єднанням "news" і "genres"
$sql_newsgenres = "SELECT news.id, news.title, genres.genre 
                  FROM news 
                  INNER JOIN newsgenres ON news.id = newsgenres.news_id 
                  INNER JOIN genres ON newsgenres.genre_id = genres.id";
$result_newsgenres = $conn->query($sql_newsgenres);

// Додавання нового зв'язку
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['news_id'])) {
    $news_id = $_POST['news_id'];
  }
  if (isset($_POST['genre_id1'])) {
    $genre_id1 = $_POST['genre_id1'];
  } else {
    $genre_id1 = null; // Assign a default value if the input is not provided
  }
  if (isset($_POST['genre_id2'])) {
    $genre_id2 = $_POST['genre_id2'];
  } else {
    $genre_id2 = null;
  }
  if (isset($_POST['genre_id3'])) {
    $genre_id3 = $_POST['genre_id3'];
  } else {
    $genre_id3 = null;
  }
  if (isset($_POST['genre_id4'])) {
    $genre_id4 = $_POST['genre_id4'];
  } else {
    $genre_id4 = null;
  }
  if (isset($_POST['genre_id5'])) {
    $genre_id5 = $_POST['genre_id5'];
  } else {
    $genre_id5 = null;
  }
  // Підготовка SQL-запиту для перевірки наявності дублікатів
  $check_duplicate = $conn->prepare("SELECT COUNT(*) as count FROM newsgenres WHERE news_id = ? AND genre_id = ?");
  $check_duplicate->bind_param("ii", $news_id, $genre_id);

  // Підготовка SQL-запиту для вставки даних в таблицю "newsgenres"
  $insert_newsgenres = $conn->prepare("INSERT INTO newsgenres (news_id, genre_id) VALUES (?, ?)");
  $insert_newsgenres->bind_param("ii", $news_id, $genre_id);

  // Виконання вставки для кожного ID жанру
  $genre_ids = [$genre_id1, $genre_id2, $genre_id3, $genre_id4, $genre_id5];
  foreach ($genre_ids as $genre_id) {
    if (!empty($genre_id)) {
      // Перевірка наявності дублікатів перед вставкою
      $check_duplicate->execute();
      $result = $check_duplicate->get_result();
      $row = $result->fetch_assoc();
      if ($row["count"] == 0) {
        $insert_newsgenres->execute();
      }
    }
  }
}

// Закриття з'єднання з базою даних
$conn->close();
?>

<main>
  <div class="container">
    <div class="left-column">
      <?php
      // Виведення даних з таблиці "news"
      echo "<h2>Таблиця 'news'</h2>";
      if ($result_news->num_rows > 0) {
        echo "<table>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                </tr>";
        while ($row = $result_news->fetch_assoc()) {
          echo "<tr>
                  <td>".$row["id"]."</td>
                  <td>".$row["title"]."</td>
                </tr>";
        }
        echo "</table>";
      } else {
        echo "Немає даних у таблиці 'news'";
      }
      ?>
    </div>

    <div class="center-column">
      <?php
      // Виведення даних з таблиці "genres"
      echo "<h2>Таблиця 'genres'</h2>";
      if ($result_genres->num_rows > 0) {
        echo "<table>
                <tr>
                  <th>ID</th>
                  <th>Genre</th>
                </tr>";
        while ($row = $result_genres->fetch_assoc()) {
          echo "<tr>
                  <td>".$row["id"]."</td>
                  <td>".$row["genre"]."</td>
                </tr>";
        }
        echo "</table>";
      } else {
        echo "Немає даних у таблиці 'genres'";
      }
      ?>
    </div>

    <div class="right-column">
      <?php
      // Виведення даних з таблиці "newsgenres"
      echo "<h2>Таблиця 'newsgenres'</h2>";
      if ($result_newsgenres->num_rows > 0) {
        echo "<table>
                <tr>
                  <th>News ID</th>
                  <th>News Title</th>
                  <th>Genre</th>
                </tr>";
        while ($row = $result_newsgenres->fetch_assoc()) {
          echo "<tr>
                  <td>".$row["id"]."</td>
                  <td>".$row["title"]."</td>
                  <td>".$row["genre"]."</td>
                </tr>";
        }
        echo "</table>";
      } else {
        echo "Немає даних у таблиці 'newsgenres'";
      }
      ?>
    </div>
  </div>

  <div class="form-container">
    <h2>Додати новий зв'язок</h2>
    <form method="POST" action="">
      <label for="news_id">ID Аніме:</label>
      <input type="text" name="news_id" id="news_id" required>

      <label for="genre_id1">ID жанру 1:</label>
      <input type="text" name="genre_id1" id="genre_id1" required>

      <label for="genre_id2">ID жанру 2:</label>
      <input type="text" name="genre_id2" id="genre_id2">

      <label for="genre_id3">ID жанру 3:</label>
      <input type="text" name="genre_id3" id="genre_id3">

      <label for="genre_id4">ID жанру 4:</label>
      <input type="text" name="genre_id4" id="genre_id4">

      <label for="genre_id5">ID жанру 5:</label>
      <input type="text" name="genre_id5" id="genre_id5">

      <input type="submit" value="Додати">
    </form>
    <form action="Edit.php" method="post">
        <button type="submit" name="GoToEdit">Назад</button>
    </form>
    <?// Перевірка, чи користувач натиснув кнопку "Головна сторінка"
if (isset($_POST['GoToEdit'])) {
    // Перенаправлення на головну сторінку
    header("Location: Edit.php");
    exit();
}?>
  </div>
</main>
</html>
