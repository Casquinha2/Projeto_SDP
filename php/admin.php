<?php
session_start();

if($_SESSION['user_id'] != 2){
    header("Location: index.php");
    exit();
}

$response_message = '';

function getEvents() {
    $url = "http://management_service:5000/management";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Erro ao conectar com o servidor Management: ' . curl_error($ch);
        return [];
    }

    curl_close($ch);
    return json_decode($response, true);
}

function createEvent($data){
    global $response_message;

    $url = "http://management_service:5000/management";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $response_data = json_decode($response, true);

    if ($status_code == 201) {
        $response_message = "Evento criado com sucesso!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $response_message = "Erro ao criar evento: " . ($response_data['error'] ?? 'Erro desconhecido.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_event'])) {
        $event_data = [
            'event' => $_POST['event'],
            'local' => $_POST['local'],
            'data' => $_POST['date'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'ticket_total' => $_POST['total_tickets'],
            'ticket_available' => $_POST['total_tickets'],
            'ticket_price' => $_POST['price'],
            'info' => $_POST['info']
        ];

        createEvent($event_data);
    }
}
$events = getEvents();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin - Eventos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .content {
            display: flex;
            width: 90%;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .column {
            flex: 1;
            padding: 20px;
        }
        h2 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }
        input[type="submit"], button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        input[type="submit"]:hover, button:hover {
            background: #0056b3;
        }
        .event-list {
            max-height: 500px;
            overflow-y: auto;
        }
        .event-item {
            background: #f0f4ff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
        }
        .event-item p {
            margin: 0;
        }
        .event-item button {
            background: #f44336;
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
        }
        .event-item button:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
<div class="content">
    <div class="column">
        <h2>Eventos</h2>
        <div class="event-list">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-item">
                        <div>
                            <p><strong>Evento: <?= htmlspecialchars($event['event'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                            <p>Data: <?= htmlspecialchars($event['data'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <form method="post" action="delete_event.php">
                            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum evento encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="column">
        <h2>Criar evento</h2>

        <?php if (!empty($response_message)): ?>
            <p class="message"><?= htmlspecialchars($response_message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="create_event" value="1">
            
            <label for="event">Evento:</label>
            <input type="text" id="event" name="event" required>

            <label for="local">Local:</label>
            <input type="text" id="local" name="local" required>
            
            <label for="date">Data:</label>
            <input type="date" id="date" name="date" required>
            
            <label for="start_time">Hora de começo:</label>
            <input type="time" id="start_time" name="start_time" required>
            
            <label for="end_time">Hora de término:</label>
            <input type="time" id="end_time" name="end_time" required>
            
            <label for="total_tickets">Bilhetes totais:</label>
            <input type="text" id="total_tickets" name="total_tickets" required>
            
            <label for="price">Preço por bilhete:</label>
            <input type="text" id="price" name="price" required>

            <label for="info">Informações adicionais:</label>
            <textarea id="info" name="info"></textarea>

            <input type="submit" value="Criar Evento">
        </form>
    </div>
</body>
</html>