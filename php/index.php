<?php
session_start();

$response_message = '';

function verifyUser(){
    global $response_message;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $password = $_POST['password'];
    
        $data = ['name' => $name, 'password' => $password];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://user_service:6000/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $response_data = json_decode($response, true);
    
        if ($status_code == 200) {
            $_SESSION['user_id'] = $response_data['user_id'];
            if ($response_data['admin'] == 1) {
                header('Location: admin.php');
            } else {
                header('Location: main.php');
            }
            exit();
        } else {
            $response_message = "Erro ao fazer login: " . ($response_data['error'] ?? 'Por favor, verifique se o nome e a senha estão corretos.');
        }
    }
}

// Call the function to handle form submission
verifyUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd; /* Fundo azul claro */
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
            background: #ffffff; /* Fundo do container branco */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #1976d2; /* Título em azul */
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
            color: #333; /* Labels em preto */
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #0d47a1; /* Borda dos inputs em azul escuro */
            border-radius: 10px;
            box-sizing: border-box;
            color: #333; /* Texto dos inputs em preto */
        }

        input[type="submit"], button {
            background: #1976d2; /* Botões em azul */
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
            background: #1565c0; /* Botões em azul mais escuro ao passar o mouse */
        }

        .error-message {
            color: #f44336; /* Mensagem de erro em vermelho */
            padding: 10px;
            text-align: center;
        }

        .message {
            color: #007BFF;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Login</h2>

        <?php if (!empty($response_message)): ?>
            <p class="message"><?= htmlspecialchars($response_message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="post" action="#">
            <label for="login_name">Nome:</label>
            <input type="text" id="login_name" name="name" required>

            <label for="login_password">Senha:</label>
            <input type="password" id="login_password" name="password" required>

            <input type="submit" value="Login">
        </form>
        <br>
        
        <button onclick="window.location.href='signin.php'">Criar conta</button>
    </div>
</body>
</html>
