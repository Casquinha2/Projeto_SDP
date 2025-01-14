<?php
function getEventsFromFlask() {
    $url = "http://localhost:5000/management";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute request
    $response = curl_exec($ch);

    // Handle errors
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
        return [];
    }

    curl_close($ch);
    return json_decode($response, true);
}

$events = getEventsFromFlask();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Events</title>
</head>
<body>

<!-- Button to trigger event fetching -->
<form method="post">
    <button type="submit" name="fetch_events">Verificar Eventos</button>
</form>

<!-- Display the events if they are fetched -->
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch_events'])): ?>
    <div id="events">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div>
                    <p>Evento: <?= htmlspecialchars($event['event'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Local: <?= htmlspecialchars($event['local'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Data: <?= htmlspecialchars($event['data'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Começo: <?= htmlspecialchars($event['start_time'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Termino: <?= htmlspecialchars($event['end_time'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Informações: <?= htmlspecialchars($event['info'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>
