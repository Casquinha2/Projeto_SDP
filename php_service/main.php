<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 2){
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

    if (!is_array($events)) {
        error_log('Invalid response format from management service');
        return [];
    }

    $event1s = array_filter($events, function($event) {
        return isset($event['ticket_available']) && $event['ticket_available'] > 0;
    });
    
    return $event1s;
}

function getEventIdsFromPurchasedTickets($user_id) {
    $url = "http://ticket_service:3000/ticket"; 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Error: ' . curl_error($ch));
        return [];
    }

    curl_close($ch);

    $tickets = json_decode($response, true);

    if (!is_array($tickets)) {
        error_log('Invalid response format from ticket service');
        return [];
    }

    // Log the tickets for debugging
    error_log('Tickets: ' . print_r($tickets, true));

    // Filter tickets by user_id
    $user_tickets = array_filter($tickets, function($ticket) use ($user_id) {
        return (int)$ticket['user_id'] === (int)$user_id;
    });

    // Log the user tickets for debugging
    error_log('User Tickets: ' . print_r($user_tickets, true));

    // Extract event_id from the filtered tickets
    $events_user = array_map(function($ticket) {
        return $ticket['event_id'];
    }, $user_tickets);

    return $events_user;
}

function getEventsByIds($event_ids) {
    if (empty($event_ids)) {
        error_log('No event IDs to fetch.');
        return [];
    }

    $unique_event_ids = array_unique($event_ids);
    $url = "http://management_service:5000/management";
    $ids_query = implode(',', $unique_event_ids);
    $url .= "?event_ids=" . urlencode($ids_query);

    $response = file_get_contents($url);
    if ($response === FALSE) {
        error_log('Error fetching events by IDs');
        return [];
    }
    $events = json_decode($response, true);

    if (!is_array($events)) {
        error_log('Invalid response format for events by IDs');
        return [];
    }

    // Log the events for debugging
    error_log('Events by IDs: ' . print_r($events, true));

    // Map events to their IDs
    $events_by_id = [];
    foreach ($events as $event) {
        $events_by_id[$event['id']] = $event;
    }

    // Duplicate events based on the count of event IDs
    $result = [];
    foreach ($event_ids as $event_id) {
        if (isset($events_by_id[$event_id])) {
            $result[] = $events_by_id[$event_id];
        }
    }

    return $result;
}

$events = getEventsFromManagement();
$purchasedTickets = getEventIdsFromPurchasedTickets($user_id);
$events_user = getEventsByIds($purchasedTickets);

function buyTicket(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['event_id'] = $_POST['event_id'];
    
        header("Location: ticket.php");
        exit();
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
            min-height: 100vh; /* Use min-height instead of height */
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

        .scroll-container {
            max-height: 500px; /* Adjust the height as needed */
            overflow-y: auto;
            padding-right: 10px; /* Add some padding to the right to prevent content from being cut off by the scrollbar */
        }

        .scroll-container::-webkit-scrollbar {
            width: 10px;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background: #007BFF;
            border-radius: 5px;
        }

        .scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="section">
        <h2>Eventos</h2>
        <div class="scroll-container">
            <div id="events">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="event">
                            <p><strong>Evento:</strong> <?= htmlspecialchars($event['event'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong>Local:</strong> <?= htmlspecialchars($event['local'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong>Data:</strong> <?= htmlspecialchars($event['date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong>Hora de começo:</strong> <?= htmlspecialchars($event['start_time'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong>Hora de término:</strong> <?= htmlspecialchars($event['end_time'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong>Informações adicionais:</strong> <?= htmlspecialchars($event['info'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                            
                            <form method="post" action="">
                                <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit">Selecionar</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-events">Não existem eventos.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Bilhetes Adquiridos</h2>
        <div class="scroll-container">
        <div id="tickets">
            <?php if (!empty($events_user)): ?>
                <?php foreach ($events_user as $ticket): ?>
                    <div class="event">
                        <p><strong>Evento:</strong> <?= htmlspecialchars($ticket['event'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Local:</strong> <?= htmlspecialchars($ticket['local'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Data:</strong> <?= htmlspecialchars($ticket['date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Hora de começo:</strong> <?= htmlspecialchars($ticket['start_time'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Hora de término:</strong> <?= htmlspecialchars($ticket['end_time'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Informações adicionais:</strong> <?= htmlspecialchars($ticket['info'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Preço:</strong> €<?= htmlspecialchars($ticket['ticket_price'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-events">O utilizador ainda não comprou bilhetes.</p>
            <?php endif; ?>
        </div>
        <button class="back-button" onclick="window.location.href='index.php'">Logout</button>
        </div>
    </div>
</div>
</body>
</html>