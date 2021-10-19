<?php
session_start();
// Test de validation du formulaire
require_once('postValidation.php');
?>

<!doctype html>
<html lang="fr">
 <head>
   <meta charset="utf-8">
   <title>Espace connexion</title>
   <link rel="stylesheet" href="style.css">
 </head>
 <body>
  <?php if(isset($msg)): ?>
    <span class="msg"><?= (isset($msg))? $msg : ''; ?></span>
  <?php endif; ?>
	<form method="post" action="index.php">
  <fieldset>
    <legend>Se connecter</legend>
    <div>
      <input type="text" name="username" id="username" value="<?= (isset($_POST['username']))? $_POST['username']: ''; ?>" placeholder="Username..." />
      <label for="username">Username</label>
    </div>
    <?php if(isset($errors['username'])): ?>
      <span class="errors"><?= (isset($errors['username']))? $errors['username'] : ""; ?></span>
    <?php elseif(isset($success['username'])): ?>
      <span class="success"><?= (isset($success['username']))? $success['username'] : ""; ?></span>
    <?php endif; ?>
    <div>
      <input type="email" name="email" id="email" value="<?= (isset($_POST['email']))? $_POST['email']: ''; ?>" placeholder="Email..."/>
      <label for="email ">Email</label>
    </div>
    <?php if(isset($errors['email'])): ?>
      <span class="errors"><?= (isset($errors['email']))? $errors['email'] : ""; ?></span>
    <?php elseif(isset($success['email'])): ?>
      <span class="success"><?= (isset($success['email']))? $success['email'] : ""; ?></span>
    <?php endif; ?>
    <div>
      <input type="password" name="password" id="password" value="<?= (isset($_POST['password']))? $_POST['password']: ''; ?>" placeholder="Password.."/>
      <label for="password">Password</label>
    </div>
    <?php if(isset($errors['password'])): ?>
      <span class="errors"><?= (isset($errors['password']))? $errors['password'] : ""; ?></span>
    <?php elseif(isset($success['password'])): ?>
      <span class="success"><?= (isset($success['password']))? $success['password'] : ""; ?></span>
    <?php endif; ?>
    <div>
	<input type="submit" name="new_user"  value="Se connecter"/>
    </div>
  </fieldset>
</form>
 <body>
</html>


