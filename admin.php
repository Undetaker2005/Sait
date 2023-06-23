<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LunAnime</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/switcher.css">
  <link rel="stylesheet" href="css/poshyk.css">
</head>

<!-- icon.Сайта -->
<body>
    <link rel="apple-touch-icon" sizes="57x57" href="icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="icon/favicon-16x16.png">
    <link rel="manifest" href="icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</body>
<!--кінець icon  -->

<body>
  
  <?php
  // Підключення до бази даних
  include_once "database/db.php";
  session_start(); // Ініціалізація сесії
  global $conn;

  function get_news() {
    global $conn;
    $sql = "SELECT * FROM news";
    $result = mysqli_query($conn, $sql);
    $news = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $news;
  }

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
          $login_link = '<a href="Login/Kabinet/Kabinet.php" class="profile-btn">' . $username . '</a>';
      } else {
          echo "Помилка отримання даних користувача";
      }
  } else {
      // Користувач не увійшов в систему, кнопка залишається незмінною
      $login_link = '<a href="Login/login.php" class="login-btn">Логін</a>';
  }
  ?>

  <header>
    <nav class="navigation">
      <div class="nav-container">
        <a href="#">Головна</a>
        <a href="#">Топ_2023</a>
        <a href="Navidation/Genres.php">Жанри</a>
        <a href="#">Онгоїнги</a>
        <div class="theme-switcher"></div>
        <?php echo $login_link; ?>
      </div>
    </nav>
  </header>

  <main>
    <div class="block fon-left block-left">
<div class="search">
  <form method="POST" action="dodatok/search.php">
    <div class="input-group">
      <input type="text" name="search" id="search-input" class="search-input" placeholder="Введіть назву аніме" value="<?php echo isset($searchValue) ? $searchValue : ''; ?>" pattern="[a-zA-Zа-яА-ЯіІїЇєЄ0-9\s]+" autocomplete="off">
      <button type="submit" id="search-button" class="search-button">&#128269;</button>
    </div>
    <?php if (!empty($errorMessage)): ?>
      <p class="error-message"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <?php if (!empty($autocompleteHints)): ?>
      <ul class="autocomplete-hints">
        <?php foreach ($autocompleteHints as $hint): ?>
          <li><?php echo $hint; ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </form>
</div>
</div>

</div>
    </div>

    <div class="block block-center">
      <?php
      $news = get_news();
      foreach ($news as $new):
      ?>
        <div class="block-img">
          <a href="<?= $new['shlyh']?>"><img src="<?= $new['image']; ?>" class="image-center" alt="<?= $new['title']?>"></a>
          <div class="block-title">
            <h5 class="caption"><?= $new['title']; ?></h5>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="block fon-right block-right">
      <p>Right block</p>
    </div>
  </main>
  <script src="js/theme-switcher.js"></script>
  <script src="js/cookie.js"></script>
</body>
</html>
