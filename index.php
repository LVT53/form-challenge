<?php
require ('Database.php');

$db = new Database();
$entries = $db->query('SELECT * FROM entries')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = __DIR__ . '/uploads';

    $coverPath = null;

    $basename = isset($_FILES['cover']['name']) ? $_FILES['cover']['name'] : '';
    $destAbs = $uploadDir . '/' . $basename;
    $destRel = 'uploads/' . $basename;
    $coverPath = $basename !== '' ? $destRel : null;

    if (
        isset($_FILES['cover']) &&
        is_array($_FILES['cover']) &&
        isset($_FILES['cover']['error'], $_FILES['cover']['tmp_name']) &&
        $_FILES['cover']['error'] === UPLOAD_ERR_OK
    ) {
        $tmp = $_FILES['cover']['tmp_name'];
        $origName = $_FILES['cover']['name'];

        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($origName));
        if ($safeName !== '') {
            $destAbs = $uploadDir . '/' . $safeName;

            if (file_exists($destAbs)) {
                $pi = pathinfo($safeName);
                $ext = isset($pi['extension']) && $pi['extension'] !== '' ? '.' . $pi['extension'] : '';
                $safeName = $pi['filename'] . '_' . uniqid() . $ext;
                $destAbs = $uploadDir . '/' . $safeName;
            }

            if (is_uploaded_file($tmp) && move_uploaded_file($tmp, $destAbs)) {
                $coverPath = 'uploads/' . $safeName;
            }
        }
    }


    $db->query('INSERT INTO entries(title, author, date, cover_path) VALUES(:title, :author, :date, :cover_path)',[
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'date' => $_POST['date'],
        'cover_path' => $coverPath
    ]);
}

require 'views/index.view.php';