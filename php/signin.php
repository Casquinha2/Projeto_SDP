<!DOCTYPE html>
<html>
<head>
    <title>Criar conta</title>
</head>
<body>

    <h2>Registar</h2>
    <form method="post" action="register.php">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <input type="submit" value="Register">
    </form>
    <br><br>
    
    <?php echo '<button onclick="window.location.href=\'index.php\'">Login</button>'; ?>

</body>
</html>