<?php
session_start();

if($_SESSION['user_id'] != 2){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

function getEventsFromManagement() {
    $url = "http://management_service:5000/management"; 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Error: ' . curl_error($ch));
        return [];
    }

    curl_close($ch);

    $events = json_decode($response, true);

    $events = array_filter($events, function($event) {
        return $event['ticket_available'] > 0;
    });
    
    return $events;
}

function getPurchasedTickets($user_id) {
    $url = "http://ticket_service:3000/ticket";

    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n" .
                        "Accept: application/json\r\n",
            'method' => 'GET',
        ],
    ];
    $context = stream_context_create($options);

    $response = @file_get_contents($url, false, $context);
    if ($response === FALSE) {
        error_log('Error: não foi possível conectar ao servidor de tickets.');
        return [];
    }
    return json_decode($response, true);
}


$events = getEventsFromManagement();
$purchasedTickets = getPurchasedTickets($user_id);

function getEventsByIds($event_ids) {
    if (empty($event_ids)) {
        return [];
    }

    $url = "http://management_service:5000/management";
    $ids_query = implode(',', $event_ids);
    $url .= "?event_ids=" . urlencode($ids_query);

    $response = file_get_contents($url);
    if ($response === FALSE) {
        return [];
    }
    return json_decode($response, true);
}

function buyTicket(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['event_id'] = $_POST['event_id'];
    
        header("Location: ticket.php");
    }
}

buyTicket();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Eventos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #d8eaff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            margin-top: 20px;
        }

        .section {
            width: 45%;
        }

        h2 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 20px;
        }

        button {
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

        button:hover {
            background: #0056b3;
        }

        .event {
            background: #f0f4ff;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .event p {
            margin: 5px 0;
        }

        .no-events {
            text-align: center;
            color: #ff6f61;
            margin-top: 20px;
        }
        .back-button {
            background: #6c757d;
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            color: white;
        }
        .back-button:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="section">
        <h2>Eventos</h2>
        <div id="events">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event">
                        <p><strong>Evento:</strong> <?= htmlspecialchars($event['event'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Local:</strong> <?= htmlspecialchars($event['local'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Data:</strong> <?= htmlspecialchars($event['data'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Hora de começo:</strong> <?= htmlspecialchars($event['start_time'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Hora de término:</strong> <?= htmlspecialchars($event['end_time'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Informações adicionais:</strong> <?= htmlspecialchars($event['info'], ENT_QUOTES, 'UTF-8') ?></p>
                        
                        <form method="post" action="">
                            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit">Selecionar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-events">Não existem eventos.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="section">
        <h2>Bilhetes Adquiridos</h2>
        <div id="tickets">
            <?php if (!empty($purchasedTickets)): ?>
                <?php foreach ($purchasedTickets as $ticket): ?>
                    <div class="event">
                        <p><strong>Evento:</strong> <?= htmlspecialchars($ticket['event'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Local:</strong> <?= htmlspecialchars($ticket['local'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Data:</strong> <?= htmlspecialchars($ticket['data'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Hora de começo:</strong> <?= htmlspecialchars($ticket['start_time'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Hora de término:</strong> <?= htmlspecialchars($ticket['end_time'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Informações adicionais:</strong> <?= htmlspecialchars($ticket['info'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Preço:</strong> €<?= htmlspecialchars($ticket['price'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-events">O utilizador ainda não comprou bilhetes.</p>
            <?php endif; ?>
        </div>
        <button class="back-button" onclick="window.location.href='index.php'">Logout</button>
    </div>
</div>
</body>
</html>