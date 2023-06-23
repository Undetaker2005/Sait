<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./style/style_form2.css"/>
    <!-- icon.Сайта -->
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
<!--кінець icon  -->
    <title>LunAnime</title>
</head>
<?php
include_once "../database/db.php";
global $conn;

if(isset($_POST['login']) && isset($_POST['password']) && isset($_POST['password2']) && isset($_POST['e'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $email = $_POST['e'];

    if($password !== $password2) {
        $error = "Паролі не одінакові";
    } elseif(!preg_match('/^[A-Za-z0-9._%+-]+@(gmail\.com|mail\.ru)$/', $email)) {
        $error = "Неправильний формат електронної пошти";
    } else {
        // Check if email already exists in the database
        $stmt = $conn->prepare("SELECT mail FROM users WHERE mail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $error = "Користувач з такою поштою вже існує";
        } else {
            $stmt = $conn->prepare("SELECT login FROM users WHERE login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows === 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO users (login, password, mail, role) VALUES (?, ?, ?, ?)");
                $role = "Салага"; // Set the default role to 'Салага'
                $stmt->bind_param("ssss", $login, $hashed_password, $email, $role);

                if($stmt->execute()) {
                    header("Location: login.php");
                    exit();
                }
            } else {
                $error = "Користувач з таким логіном вже існує";
            }
        }
    }
}
?>
<body>
    <form  method="post">
        <h1 title="Форма реєстрація на сайті">Реєстрація</h1>
        <div class="group">
            <LAbel for="signupname">Ім'я користувача:</LAbel>
            <input id="signupname" name="login" type="text" data-reg="^[а-яА-ЯёЁa-zA-Z0-9]+$" >
        </div>
        <div class="group">
            <LAbel for="signuppassword">Пароль:</LAbel>
            <input id="signuppassword" name="password" type="password" data-reg="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$">
        </div>
        <div class="group">
            <label for="signuppassword2">Повторний пароль:</label>
            <input id="signuppassword2" name="password2" type="password" data-reg="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$">
        </div>
        <div class="group">
            <LAbel for="signupemail">Електроний Адрес</LAbel>
            <input id="signupemail" name="e" type="email" data-reg="^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$">
        </div>
        <div class="group">
            <center><button type="submit">Реєстрація</button></center>
        </div>
            <?php if(isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
            <?php } ?>
    </form>
</body>
</html>