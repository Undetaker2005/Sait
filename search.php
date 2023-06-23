<?php
include_once "../database/db.php";

function get_news($search_query = '') {
  global $conn;
  $sql = "SELECT * FROM news";

  if (!empty($search_query)) {
    $search_query = '%' . $search_query . '%';
    $sql .= " WHERE title LIKE ?";
  }

  $stmt = $conn->prepare($sql);

  if (!empty($search_query)) {
    $stmt->bind_param("s", $search_query);
  }

  $stmt->execute();
  $result = $stmt->get_result();
  $news = mysqli_fetch_all($result, MYSQLI_ASSOC);

  return $news;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $search_query = $_POST['search'];
  $news = get_news($search_query);

  if (!empty($news)) {
    // Перенаправлення на сторінку «shlyh» першого результату пошуку
    $firstResult = $news[0];
    $redirectUrl = "../" . $firstResult['shlyh'];
    header("Location: " . $redirectUrl);// Перенаправляє на сторінку знайденого аніме
    exit();
  } else {
    // Показує повідомлення про помилку та зберегає введене значення пошуку
    $errorMessage = "Нічого не знайдено. Введіть правильний пошуковий запит.";
    $searchValue = htmlentities($search_query);
    header("Location: ../admin.php"); // Перенаправлення на admin.php, якщо результатів не знайдено
    exit();
  }
} else {
  $news = get_news();
}

?>