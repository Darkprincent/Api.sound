<?php

function getTracks($pdo) {
    $stmt = $pdo->prepare("
        SELECT t.id, t.title, a.name AS author_name
        FROM tracks t
        JOIN authors a ON t.author_id = a.id
        ORDER BY t.id DESC
    ");
    $stmt->execute();
    $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tracks);
}

function getTrack($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT t.id, t.title, t.author_id, a.name AS author_name
        FROM tracks t
        JOIN authors a ON t.author_id = a.id
        WHERE t.id = :id
    ");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount() === 1) {
        $track = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($track);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Track not found']);
    }
}

function addTrack($pdo) {
    $data = [
        ':title' => $_POST['title'],
        ':author_id' => $_POST['author_id']
    ];

    // Добавлены пустые значения для lyric и link, чтобы удовлетворить структуру БД
    $stmt = $pdo->prepare("INSERT INTO tracks (title, author_id, lyric, link) VALUES (:title, :author_id, '', '')");
    $stmt->execute($data);
    $id = $pdo->lastInsertId();

    if (isset($id) && $id > 0) {
        echo json_encode([
            'status' => true,
            'message' => 'Created!',
            'track_id' => $id,
            'new_track' => $data
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Error! Bad request']);
    }
}

function deleteTrack($pdo, $id) {
    if (!$id) {
        echo json_encode([
            'status' => false,
            'message' => 'ID required']);
        return;
    }
    $stmt = $pdo->prepare("DELETE FROM tracks WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    echo json_encode(['status' => true, 'message' => 'Deleted!']);
}

function updateTrack($pdo, $data, $id) {
    $stmt = $pdo->prepare("UPDATE tracks SET title = :title, author_id = :author_id WHERE id = :id");
    $stmt->execute(['title' => $data['title'], 'author_id' => $data['author_id'], 'id' => $id]);
    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'Modified!']);
}