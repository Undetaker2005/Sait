<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./style/style_form2.css"/>
    <title>LunAnime</title>
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
<body>
    <form method="POST" action="">
        <h1 title="Введіть дані акаунта">Login</h1>
        <div class="group">
            <label for="signupname">name or email:</label>
            <input id="signupname" type="text" name="signupname" data-reg="^[а-яА-ЯёЁa-zA-Z0-9]+$" >
        </div>
        <div class="group">
            <label for="signuppassword">password:</label>
            <input id="signuppassword" type="password" name="signuppassword" data-reg="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$">
        </div>
        <div class="group">
            <center><button type="submit">Ввойти</button></center>
        </div>
        <div class="text-center">
            <a href="registration.php" class="style">Sign Up</a>
        </div>
    </form>
</body>

<?php
include_once "../database/db.php";
session_start();
global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['signupname']) && isset($_POST['signuppassword'])) {
        $username = $_POST['signupname'];
        $password = $_POST['signuppassword'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE (login = ? OR mail = ?)");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            if (password_verify($password, $hashed_password)) {
                // Successful login
                $_SESSION['user_id'] = $row['id']; // Save user ID in the session

                // Redirect to another page with success message
                header("Location: Kabinet/Kabinet.php");
                exit();
            } else {
                // Incorrect password
                $error = "Неправильний логін або пароль";
            }
        } else {
            // Incorrect username or email
            $error = "Неправильний логін або пароль";
        }
    }
}
?>

</html>