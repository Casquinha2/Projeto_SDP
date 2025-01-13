<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = $_POST['event'];
    $local = $_POST['local'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    if ($_POST['info'] != ''){
        $info = 'None';
    }else{
        $info = $_POST['info'];
    }
        

    $data = ['event' => $event, 'local' => $local, 'data' => $date, 'start_time' => $start_time, 'end_time' => $end_time, 'info' => $info];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://management_service:5000/management");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Status Code: $status_code<br>";
    echo "Response: $response<br>";
}
?>
