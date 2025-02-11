<?php
require_once 'core/init.php';
$error = null;
if (isset($_SESSION['user'])) {
    header('Location:index.php');
}
if (isset($_POST['submit'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    if (!empty(trim($user)) && !empty(trim($pass))) {
        if (cek_nama($user) !== 0) {
            if (cek_data($user, $pass)) {
                $_SESSION['user'] = $user;
                header('Location:index.php');
            } else $error = "Username atau password salah!";
        } else $error = "Username tidak ada!";
    } else $error = "Tidak boleh kosong!";

}
require_once 'view/header.php';
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'].'<br>';
    unset($_SESSION['message']);
}
?>
    <br>
    <main>
        <form action="login.php" method="post">
            <label for="username">Username</label>
            <input type="text" name="username" id="username">
            <br><br>
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
            <br><br>
            <input type="submit" name="submit" value="Login">
            <br><br>
            <?php if ($error !== null) { ?>
                <div id="error">
                    <?php
                    echo $error;
                    ?>
                </div>
            <?php } ?>
        </form>
    </main>
<?php
require_once 'view/footer.php';
?>