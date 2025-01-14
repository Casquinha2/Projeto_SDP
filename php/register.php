<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = 2;
    $name = $_POST['name'];
    $password = $_POST['password'];
    $email = $_POST['email'];


    $data = ['name' => $name, 'password' => $password, 'email' => $email, 'admin' => $admin];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://user_service:6000/users");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "$name 1 ";
    echo "$password 2 ";
    echo "$email 3 ";
    echo "$admin 4 ";
    echo "Status Code: $status_code<br>";
    echo "Response: $response<br>";
}
?>

