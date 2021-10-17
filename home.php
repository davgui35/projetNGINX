<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
</head>
<body>
    <a href="/">Retour</a>
    <?php if(isset($_SESSION['username'])) : ?>
        <h1><?=  'Hello ' . $_SESSION['username']; ?></h1> 
    <?php endif; ?>
</body>
</html>