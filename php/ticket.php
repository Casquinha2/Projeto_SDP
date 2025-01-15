<?php
session_start();

if($_SESSION['user_id'] == 2){
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['event'])) {
    $_SESSION['event'] = [
        'id' => 1,
        'event' => 'Concerto de Verão',
        'local' => 'Anfiteatro Central',
        'data' => '15/01/2025',
        'start_time' => '18:00',
        'end_time' => '21:00',
        'info' => 'Traga seu próprio lanche',
        'price' => 20.00
    ];
}
$event = $_SESSION['event'];
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
    </style>
</head>
<body>

<div class="container">
    <h2>Comprar Bilhete</h2>

    <div class="event-info">
        <p><strong>Evento:</strong> <?= htmlspecialchars($event['event'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Local:</strong> <?= htmlspecialchars($event['local'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Data:</strong> <?= htmlspecialchars($event['data'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Hora de começo:</strong> <?= htmlspecialchars($event['start_time'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Hora de término:</strong> <?= htmlspecialchars($event['end_time'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Informações adicionais:</strong> <?= htmlspecialchars($event['info'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="price">Preço: €<?= number_format($event['price'], 2, ',', '.') ?></div>

    <div class="payment-methods">
        <p><strong>Métodos de Pagamento:</strong></p>
        <img src="../visa.png" alt="Visa">
        <img src="../mastercard.png" alt="MasterCard">
        <img src="../paypal.png" alt="PayPal">
    </div>

    <?php echo '<button class= buy-button onclick="window.location.href=\'payment.php\'">Login</button>'; ?>
</div>

</body>
</html>