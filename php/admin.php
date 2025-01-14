<DOCTYPE html>
<html>
    <body>
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

        <label for="total_tickets">Bilhetes totais:</label><br>
        <input type="text" id="total_tickets" name="total_tickets" required><br><br>

        <label for="price">Preço por bilhete:</label><br>
        <input type="text" id="price" name="price" required><br><br>

        <label for="info">Information:</label><br>
        <textarea id="info" name="info"></textarea><br><br>

        <input type="submit" value="Create Event">
    </form>
    </body>
</html>