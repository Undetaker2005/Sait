<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>LunAnime</title>
  <link rel="stylesheet" href="Genres.css">
</head>
<!-- icon.Сайта -->
<body>
    <link rel="apple-touch-icon" sizes="57x57" href="../icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="../icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../icon/favicon-16x16.png">
    <link rel="manifest" href="../icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</body>
<!--кінець icon  -->

<?php
  // Підключення до бази даних
  include_once "../database/db.php";
  session_start(); // Ініціалізація сесії
  global $conn;

  // Перевірка, чи користувач увійшов в систему
  if (isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];

      // Отримання даних користувача з бази даних
      $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 1) {
          $row = $result->fetch_assoc();
          $username = $row['login'];

          // Зміна кнопки "Логін" на ім'я користувача
          $login_link = '<a href="../Login/Kabinet/Kabinet.php" class="profile-btn">' . $username . '</a>';
      } else {
          echo "Помилка отримання даних користувача";
      }
  } else {
      // Користувач не увійшов в систему, кнопка залишається незмінною
      $login_link = '<a href="../Login/login.php" class="login-btn">Логін</a>';
  }
?>
<body>
<header>
    <nav class="navigation">
      <div class="nav-container">
        <a href="../admin.php">Головна</a>
        <a href="#">Топ_2023</a>
        <a href="#">Жанри</a>
        <a href="#">Онгоїнги</a>
        <?php echo $login_link; ?>
      </div>
    </nav>
  </header>
  <div class="container">
    <div class="left-container">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
        <table>
          <?php
          $genreMappings = [
            "Кодомо",
            "Сьодзьо",
            "Сьонен",
            "Сейнен",
            "Дзьосен",
            "Юрі",
            "Яой",
            "Гарем",
            "Лолі",
            "Меха",
            "Спорт",
            "Наукова-Фантастика",
            "Апокаліпсис",
            "Постапокаліпсис",
            "Фентазі",
            "Жахи",
            "Готика",
            "Пригоди",
            "Школа",
            "Бойовик",
            "Бойові-мистецтва",
            "Детектив",
            "Кіберпанк",
            "Казка",
            "Драма",
            "Комедія",
            "Романтика",
            "Еротика",
            "Космос",
            "Повнометражка",
            "Короткометражка",
            "Екшен"
          ];

          $checkboxCount = count($genreMappings);
          $checkboxesPerRow = 2;
          $rows = ceil($checkboxCount / $checkboxesPerRow);
          $checkboxIndex = 0;

          for ($i = 0; $i < $rows; $i++) {
            echo '<tr>';
            for ($j = 0; $j < $checkboxesPerRow; $j++) {
              if ($checkboxIndex < $checkboxCount) {
                $genreNameUkrainian = $genreMappings[$checkboxIndex];

                // Перевірка, чи жанр вибраний
                $checked = (isset($_GET['genres']) && in_array($genreNameUkrainian, $_GET['genres'])) ? 'checked' : '';

                echo '<td><label class="checkbox-button"><input type="checkbox" name="genres[]" value="' . $genreNameUkrainian . '" ' . $checked . '> ' . $genreNameUkrainian . '</label></td>';
                $checkboxIndex++;
              } else {
                echo '<td></td>';
              }
            }
            echo '</tr>';
          }
          ?>
          <tr>
            <td colspan="<?php echo $checkboxesPerRow; ?>">
              <button style="width:100%; height:40px " type="submit" name="submit">Пошук</button>
            </td>
          </tr>
        </table>
      </form>
    </div>
    <div class="right-container">
      <?php
$selectedGenres = isset($_GET['genres']) ? $_GET['genres'] : [];

if (!empty($selectedGenres)) {
  $genreConditions = [];
  $paramTypes = '';
  $params = [];

  foreach ($selectedGenres as $genre) {
    $genreConditions[] = 'genre_id = (SELECT id FROM Genres WHERE genre = ?)';
    $paramTypes .= 's';
    $params[] = $genre;
  }

  $placeholders = implode(' OR ', $genreConditions);

  $stmt = $conn->prepare("SELECT title, shlyh, image FROM News WHERE id IN (
    SELECT news_id FROM NewsGenres WHERE $placeholders
    GROUP BY news_id HAVING COUNT(DISTINCT genre_id) = ?
  )");

  $paramTypes .= 'i';
  $params[] = count($selectedGenres);
  
  $stmt->bind_param($paramTypes, ...$params);
  $stmt->execute();

  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    if (isset($row['shlyh']) && isset($row['image'])) {
      echo '<div class="block-img">';
      echo '<a href="../'.$row['shlyh'].'"><img src="../'. $row['image'] .'" class="card-img-top" alt="'. $row['title'].'"></a>';
      echo '<div class="card-body">';
      echo '<h5 class="caption">' . $row['title'] . '</h5>';
      echo '</div>';
      echo '</div>';
    }
  }
} else {
  echo 'Будь ласка, виберіть хоча б один жанр.';
}
?>

    </div>
  </div>
</body>
</html>
