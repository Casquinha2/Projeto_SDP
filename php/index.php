<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
</head>
<body>
    <h1>User Management Interface</h1>

    <h2>Register User</h2>
    <form method="post" action="register.php">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="admin">Admin:</label><br>
        <input type="checkbox" id="admin" name="admin" value="1"><br><br>

        <input type="submit" value="Register">
    </form>

    <h2>Login</h2>
    <form method="post" action="login.php">
        <label for="login_name">Name:</label><br>
        <input type="text" id="login_name" name="name" required><br><br>

        <label for="login_password">Password:</label><br>
        <input type="password" id="login_password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>

    <h2>Create Event</h2>
    <form method="post" action="event.php">
        <label for="event">Event:</label><br>
        <input type="text" id="event" name="event" required><br><br>

        <label for="local">Local:</label><br>
        <input type="text" id="local" name="local" required><br><br>

        <label for="date">Date:</label><br>
        <input type="date" id="date" name="date" required><br><br>

        <label for="start_time">Start Time:</label><br>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <label for="end_time">End Time:</label><br>
        <input type="time" id="end_time" name="end_time" required><br><br>

        <label for="info">Information:</label><br>
        <textarea id="info" name="info"></textarea><br><br>

        <input type="submit" value="Create Event">
    </form>
</body>
</html>
