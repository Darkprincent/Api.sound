<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-Type: application/json');
require 'connectDB.php';
require 'functions.php';

$method = $_SERVER['REQUEST_METHOD'];

$params = explode('/', $_GET['q']);
$type = $params[0];
if (isset($params[1])) {
    $id = $params[1];
}

switch ($method) {
    case 'GET':
        if (isset($id)) {
            getTrack($pdo, $id);
        } else {
            getTracks($pdo);
        }
        break;
    case 'POST':
        addTrack($pdo);
        break;
    case 'DELETE':
        deleteTrack($pdo, $id);
        break;
    case 'PATCH':
        if ($type === 'tracks' && isset($id)) {
            $data = json_decode(file_get_contents('php://input'), true);
            updateTrack($pdo, $data, $id);
        }
        break;
}