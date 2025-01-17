<?php
session_start();

$response_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $data = ['name' => $name, 'password' => $password, 'email' => $email];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://10.101.168.192/users");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status_code === 201) {
        $response_message = 'Conta criada com sucesso!';
    } else {
        $response_message = 'Erro ao criar conta: ' . $response;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Criar conta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 400px;
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
            margin-top: 10px;
            display: block;
            font-weight: bold;
            color: #333;
        }

        input {
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

        .message {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Registar</h2>

        <?php if (!empty($response_message)): ?>
            <p class="message"><?php echo $response_message; ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required>

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <input type="submit" value="Registar">
        </form>

        <br>
        <button onclick="window.location.href='index.php'">Login</button>
    </div>

</body>
</html>