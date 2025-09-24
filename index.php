<?php
require ('Database.php');

$db = new Database();
$entries = $db->query('SELECT * FROM entries')->fetchAll(PDO::FETCH_ASSOC);

// Count the number of entries
$total_entries = $db->query('SELECT COUNT(*) as count FROM entries')->fetchAll(PDO::FETCH_ASSOC);
$total_entries = $total_entries[0]['count'];

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
        'indie_genre' => $_POST['indie-genre']
    ]);

    // Refresh the page to load the new entry in the list
    header('Location: /');
    exit;
}
// Search form fulfillment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['_form']) && $_GET['_form'] === 'search')) {
    $whereClauses = [];
    $params = [];
    $q = trim($_GET['search'] ?? '');

    if ($q !== '') {
        $whereClauses[] = '(title LIKE :q OR author LIKE :q OR date LIKE :q)';
        $params[':q'] = "%{$q}%";
    }

    $inputName = [
        'pop_genre' => 'search-pop-genre',
        'rock_genre' => 'search-rock-genre',
        'jazz_genre' => 'search-jazz-genre',
        'indie_genre' => 'search-indie-genre'
    ];

    $genreClauses = [];

    foreach ($inputName as $columnName => $inputColumnName) {
        if(isset($_GET[$inputColumnName]) && $_GET[$inputColumnName] === 'on') {
            $genreClauses[] = $columnName . '=1';
        }
    }

    if(!empty($genreClauses)) {
        $whereClauses[] = "(" . implode(" OR ", $genreClauses) . ")";
    }

    $searchSql = "SELECT * FROM entries";


    if(!empty($whereClauses)) {
        $searchSql = $searchSql . " WHERE " . implode(" AND ", $whereClauses);
        $entries = $db->query($searchSql,$params)->fetchAll(PDO::FETCH_ASSOC);
    } else{($entries = $db->query($searchSql)->fetchAll(PDO::FETCH_ASSOC));}
}

require 'views/index.view.php';