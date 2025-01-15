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

        .payment-methods img {
            width: 50px;
            height: auto;
            margin: 0 10px;
            cursor: pointer;
            transition: transform 0.3s ease;
            opacity: 0.6;
        }

        .payment-methods img:hover, .payment-methods img.selected {
            transform: scale(1.1);
            opacity: 1;
        }

        .payment-form {
            display: none;
        }

        .payment-form.active {
            display: block;
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
    <script>
        function showPaymentForm(method) {
            var forms = document.getElementsByClassName('payment-form');
            for (var i = 0; i < forms.length; i++) {
                forms[i].classList.remove('active');
            }
            document.getElementById(method).classList.add('active');

            var imgs = document.getElementsByClassName('pay-img');
            for (var j = 0; j < imgs.length; j++) {
                imgs[j].classList.remove('selected');
            }
            document.getElementById(method + '-img').classList.add('selected');
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Informações de Pagamento</h2>

    <div class="payment-methods">
        <img src="../images/visa.png" alt="Visa" id="visa-img" class="pay-img" onclick="showPaymentForm('visa')">
        <img src="../images/mastercard.png" alt="MasterCard" id="mastercard-img" class="pay-img" onclick="showPaymentForm('mastercard')">
        <img src="../images/paypal.png" alt="PayPal" id="paypal-img" class="pay-img" onclick="showPaymentForm('paypal')">
    </div>
    
    <!-- Visa payment form -->
    <div id="visa" class="payment-form">
        <form method="post" action="confirm.php">
            <label for="visa-name">Nome Completo:</label>
            <input type="text" id="visa-name" name="name" required>

            <label for="visa-card-number">Número do Cartão:</label>
            <input type="number" id="visa-card-number" name="card_number" required>

            <label for="visa-exp-date">Data de Validade:</label>
            <input type="text" id="visa-exp-date" name="exp_date" placeholder="MM/YY" required>

            <label for="visa-cvv">CVV:</label>
            <input type="number" id="visa-cvv" name="cvv" required>

            <button class="buy-button" type="submit">Confirmar Compra</button>
        </form>
    </div>

    <!-- MasterCard payment form -->
    <div id="mastercard" class="payment-form">
        <form method="post" action="confirm.php">
            <label for="mastercard-name">Nome Completo:</label>
            <input type="text" id="mastercard-name" name="name" required>

            <label for="mastercard-card-number">Número do Cartão:</label>
            <input type="number" id="mastercard-card-number" name="card_number" required>

            <label for="mastercard-exp-date">Data de Validade:</label>
            <input type="text" id="mastercard-exp-date" name="exp_date" placeholder="MM/YY" required>

            <label for="mastercard-cvv">CVV:</label>
            <input type="number" id="mastercard-cvv" name="cvv" required>

            <button class="buy-button" type="submit">Confirmar Compra</button>
        </form>
    </div>

    <!-- PayPal payment form -->
    <div id="paypal" class="payment-form">
        <form method="post" action="confirm.php">
            <label for="paypal-name">Nome Completo:</label>
            <input type="text" id="paypal-name" name="name" required>

            <label for="paypal-account">Conta PayPal:</label>
            <input type="text" id="paypal-account" name="paypal_account" required>

            <button class="buy-button" type="submit">Confirmar Compra</button>
        </form>
    </div>
</div>

</body>
</html>
