<?php
session_start();

$event_id = $_SESSION['event_id'];
$user_id = $_SESSION['user_id'];

if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 2){
    header("Location: index.php");
    exit();
}
$event = '';
$local = '';
$date = '';
$start_time = '';
$end_time = '';
$info = '';
$price = 0.0;
$ticket_available = 0;

function getEventsByIds($event_id) {
    global $event, $local, $date, $start_time, $end_time, $info, $price, $ticket_available;

    $url = "http://management_service:5000/management/" . $event_id;

    $response = file_get_contents($url);
    if ($response === FALSE) {
        return [];
    }

    $event_data = json_decode($response, true);

    // Check if the event data is not empty and is an array
    if (!empty($event_data) && is_array($event_data)) {
        $event = $event_data['event'];
        $local = $event_data['local'];
        $date = $event_data['date'];
        $start_time = $event_data['start_time'];
        $end_time = $event_data['end_time'];
        $info = $event_data['info'];
        $price = $event_data['ticket_price'];
        $ticket_available = $event_data['ticket_available'];
    }
    return $event_data;
}

// Fetch event data
getEventsByIds($event_id);

function addTicket() {
    global $event_id, $user_id;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = ['event_id' => $event_id, 'user_id' => $user_id];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://ticket_service:3000/ticket");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status_code === 201) {
            $response_data = json_decode($response, true);

            if (!empty($response_data['id'])) {
                $_SESSION['ticket_id'] = $response_data['id'];
                header('Location: payment.php');
                exit();
            } else {
                echo 'Erro: Ticket ID não foi criado corretamente.';
            }
        } else {
            $response_message = 'Erro ao criar bilhete: ' . $response;
            echo $response_message;
        }
    }
}


// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    addTicket();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comprar Bilhete</title>
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

        .container {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            color: #ff6f61;
            text-align: center;
            margin-bottom: 20px;
        }

        .payment-methods {
            text-align: center;
            margin-bottom: 20px;
        }

        .payment-methods img {
            width: 50px;
            height: auto;
            margin: 0 10px;
        }

        .buy-button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            text-align: center;
            font-size: 18px;
        }

        .buy-button:hover {
            background: #0056b3;
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
    <h2>Comprar Bilhete</h2>

    <div class="event-info">
        <p><strong>Evento:</strong> <?= htmlspecialchars($event) ?></p>
        <p><strong>Local:</strong> <?= htmlspecialchars($local) ?></p>
        <p><strong>Data:</strong> <?= htmlspecialchars($date) ?></p>
        <p><strong>Hora de começo:</strong> <?= htmlspecialchars($start_time) ?></p>
        <p><strong>Hora de término:</strong> <?= htmlspecialchars($end_time) ?></p>
        <p><strong>Informações adicionais:</strong> <?= htmlspecialchars($info) ?></p>
    </div>

    <div class="price">Preço: €<?= number_format($price, 2, ',', '.') ?></div>

    <div class="payment-methods">
        <p><strong>Métodos de Pagamento:</strong></p>
        <img src="../images/visa.png" alt="Visa">
        <img src="../images/mastercard.png" alt="MasterCard">
        <img src="../images/paypal.png" alt="PayPal">
    </div>

    <form method="post" action="">
        <button class="buy-button" type="submit">Comprar Bilhete</button>
    </form>
    <button class="back-button" onclick="window.location.href='main.php'">Voltar</button>
</div>

</body>
</html>
