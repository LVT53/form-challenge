<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="views/stylesheets/style.css" type="text/css">
    <title>Form Challenge - IDS</title>
</head>
<body>
    <section class="header">
        <div class="title-wrapper">
            <div>
                <h1 style="margin-bottom: .5rem;">Learning PHP</h1>
                <h2>The form challenge</h2>
                <p>Now upgraded.</p>
            </div>
            <p>
                By completing the form, you agree to have fun. <br><br>
                Scroll down to see the results.
            </p>
        </div>
        <div class="form-wrapper">
            <form method="POST" enctype="multipart/form-data">
                <label for="title">Album title</label>
                <input type="text" id="title" name="title" placeholder="Meteora">
                <label for="author">Artist</label>
                <input type="text" id="author" name="author" placeholder="Linkin Park">
                <label for="date">Year of release</label>
                <input type="number" id="date" name="date" placeholder="2003">
                <div class="file-wrapper">
                    <p>Upload an album cover image</p>
                    <div>
                        <input type="file" id="cover" name="cover" accept="image/*">
                        <img src="views/stylesheets/images/file.svg" alt="file icon">
                    </div>
                </div>
                <div>
                    <p>Tick the appropriate genre boxes!</p>
                    <label for="pop-genre">Pop</label>
                    <input type="checkbox" id="pop-genre" name="pop-genre">
                    <label for="rock-genre">Rock</label>
                    <input type="checkbox" id="rock-genre" name="rock-genre">
                    <label for="indie-genre">Indie</label>
                    <input type="checkbox" id="indie-genre" name="indie-genre">
                    <label for="jazz-genre">Jazz</label>
                    <input type="checkbox" id="jazz-genre" name="jazz-genre">
                    <p class="silent-text">Who needs more genres anyway?</p>
                </div>
                <input type="submit" value="Submit">
            </form>
            <?php if($_POST): ?>
                <div class="success">
                    <p>Your form has been submitted!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <section id="results">
        <h2>Database entries</h2>
        <div class="list-items">
            <div class="info-item-wrapper">
                <div class="info-item">





                </div>
                <form class="refresh" method="GET" action="/">
                    <button type="submit" formaction="#results">
                        Refresh
                        <img src="views/stylesheets/images/refresh.svg" alt="refresh icon">
                    </button>
                </form>
            </div>
            <?php if (empty($entries)): ?>
                <p style="text-align: center;">No entries yet. Complete the form above and click refresh!</p>
            <?php else: ?>
                <?php foreach ($entries as $entry): ?>
                    <div class="item">
                        <div>
                            <p>Album title</p>
                            <p><?=$entry['title']?></p>
                        </div>
                        <div>
                            <p>Artist</p>
                            <p><?=$entry['author']?></p>
                        </div>
                        <div>
                            <p>Year of release</p>
                            <p><?=$entry['date']?></p>
                        </div>
                        <img src="<?=$entry['cover_path']?>" alt="<?=$entry['title']?> cover image">
                    </div>
                <?php endforeach ?>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>