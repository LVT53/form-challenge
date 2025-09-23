<?php
require ('Database.php');

$db = new Database();
$entries = $db->query('SELECT * FROM entries')->fetchAll(PDO::FETCH_ASSOC);

// Entry form fulfillment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process and place cover image
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

    // Checkboxes boolean conversion and sanitization
    // Checkboxes names - pop-genre, rock-genre, jazz-genre, indie-genre
    $_POST['pop-genre'] = isset($_POST['pop-genre']) ? 1 : 0;
    $_POST['rock-genre'] = isset($_POST['rock-genre']) ? 1 : 0;
    $_POST['jazz-genre'] = isset($_POST['jazz-genre']) ? 1 : 0;
    $_POST['indie-genre'] = isset($_POST['indie-genre']) ? 1 : 0;

    // Insert into database
    $db->query('INSERT INTO entries(title, author, date, cover_path, pop_genre, rock_genre, jazz_genre, indie_genre) VALUES(:title, :author, :date, :cover_path, :pop_genre, :rock_genre, :jazz_genre, :indie_genre)',[
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'date' => $_POST['date'],
        'cover_path' => $coverPath,
        'pop_genre' => $_POST['pop-genre'],
        'rock_genre' => $_POST['rock-genre'],
        'jazz_genre' => $_POST['jazz-genre'],
        'indie_genre' => $_POST['indie-genre'],
    ]);
}
// Search form fulfillment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['_form']) && $_GET['_form'] === 'search')) {









//    $q = trim($_GET['search'] ?? '');
//    if (str_contains($q, 'search-indie-genre=on') === true or str_contains($q, 'search-rock-genre=on') === true or str_contains($q, 'search-jazz-genre=on') === true or str_contains($q, 'search-pop-genre=on') === true) {
//        $stmt = $db->query('SELECT * FROM entries WHERE title LIKE :q OR author LIKE :q OR date LIKE :q AND pop_genre = 1 :q AND jazz_genre = 1 :q AND rock_genre = 1 :q AND indie_genre = 1 :q', ['q' => "%{$q}%"]);
//        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
//    } else {
//        $stmt = $db->query('SELECT * FROM entries WHERE title LIKE :q OR author LIKE :q OR date LIKE :q', ['q' => "%{$q}%"]);
//        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
//    }
}


require 'views/index.view.php';