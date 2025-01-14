<?php 
session_start();
?>
<!DOCTYPE html>
<html>
<body>

    <h2>Login</h2>

    <?php 
    if (isset($_SESSION['error_message'])) { 
        echo "<div class='error-popup'>" . $_SESSION['error_message'] . "</div>"; 
        unset($_SESSION['error_message']); 
    }
    ?>

    <form method="post" action="login.php">
        <label for="login_name">Name:</label><br>
        <input type="text" id="login_name" name="name" required><br><br>

        <label for="login_password">Password:</label><br>
        <input type="password" id="login_password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
    <br><br>
    <?php
        echo '<button onclick="window.location.href=\'signin.php\'">Criar conta</button>';
    ?>
</body>
</html>

