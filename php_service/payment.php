<?php
session_start();

$event_id = $_SESSION['event_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$ticket_id = $_SESSION['ticket_id'] ?? null;

if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 2){
    header("Location: index.php");
    exit();
}

if (!isset($ticket_id)) {
    error_log('Erro: Ticket ID não encontrado na sessão.');
    exit("Erro: Ticket ID não encontrado na sessão.");
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
        error_log('Erro ao buscar evento.');
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

function updateEvent($event_id, $ticket_available) {
    if ($ticket_available > 0) {
        $ticket_available -= 1;
    }

    $data = ['event_id' => $event_id, 'ticket_available' => $ticket_available];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://management_service:5000/update");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status_code !== 200) {
        error_log("Error updating event: " . $response);
    }
}

function storePayment() {
    global $event_id, $user_id, $ticket_id, $ticket_available;

    $payment_method = $_POST['payment_method'];
    $name = $_POST['name'];
    $card_number = $_POST['card_number'];
    $validation_date = $_POST['exp_date'];
    $cvv = $_POST['cvv'];
    $paypal_email = $_POST['paypal_account'];

    $data = [
        'ticket_id' => $ticket_id,
        'user_id' => $user_id,
        'payment_method' => $payment_method,
        'name' => $name,
        'card_number' => $card_number,
        'validation_date' => $validation_date,
        'cvv' => $cvv,
        'paypal_email' => $paypal_email
    ];


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://payment_service:4000/payment");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status_code === 201) {
        updateEvent($event_id, $ticket_available);
        header("Location: main.php");
        exit();
    } else {
        $response_message = 'Erro ao realizar compra: ' . $response;
        echo $response_message;
    }
}

// Check for final form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    storePayment();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informações de Pagamento</title>
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

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }

        .payment-methods {
            text-align: center;
            margin-bottom: 20px;
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
            margin-top: 20px;
        }

        .buy-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Informações de Pagamento</h2>

    <div class="payment-methods">
        <form method="post" action="">
            <label for="payment_method">Método de Pagamento:</label>
            <select name="payment_method" id="payment_method" onchange="this.form.submit()">
                <option value="">Selecione...</option>
                <option value="visa">Visa</option>
                <option value="mastercard">MasterCard</option>
                <option value="paypal">PayPal</option>
            </select>
        </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])): ?>
        <form method="post" action="">
            <input type="hidden" name="payment_method" value="<?= htmlspecialchars($_POST['payment_method']) ?>">
            <input type="hidden" name="confirm_payment" value="1">
            
            <?php if ($_POST['payment_method'] === 'visa' || $_POST['payment_method'] === 'mastercard'): ?>
                <label for="name">Nome Completo:</label>
                <input type="text" id="name" name="name" required>

                <label for="card_number">Número do Cartão:</label>
                <input type="number" id="card_number" name="card_number" required>

                <label for="exp_date">Data de Validade:</label>
                <input type="text" id="exp_date" name="exp_date" placeholder="MM/YY" required>

                <label for="cvv">CVV:</label>
                <input type="number" id="cvv" name="cvv" required>

            <?php elseif ($_POST['payment_method'] === 'paypal'): ?>
                <label for="name">Nome Completo:</label>
                <input type="text" id="name" name="name" required>

                <label for="paypal_account">Conta PayPal:</label>
                <input type="text" id="paypal_account" name="paypal_account" required>
            <?php endif; ?>

            <button class="buy-button" type="submit">Confirmar Compra</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
