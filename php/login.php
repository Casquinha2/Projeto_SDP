<?php
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


    session_start();
    
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $response_data = json_decode($response, true);
    
    if ($status_code == 200) {
        if ($response_data['admin'] == 1) {
            header('Location: admin.php');
        } else {
            header('Location: main.php');
        }
        exit();
    } else {
        $_SESSION['error_message'] = "Utilizador não encontrado: Por favor, verifique se o nome e a password estão corretos.";
        header('Location: index.php'); 
        exit();
    }

    
}
?>