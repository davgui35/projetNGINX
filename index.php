<?php
session_start();
$test_email = false;
$test_password = false;
$test_username = false;
// Connection
require_once('Connect.php');
$connexion = new Connect('newbase');
$users = $connexion->find("SELECT * FROM users");

// Test de validation du formulaire
if(isset($_POST['new_user'])){

  //print('form validate !');
  // Test présence username Test => (' ', '')
  if(isset($_POST['username']) && empty($_POST['username'])) { 
    $errors['username'] = 'Username is empty!';
  }
  else{
    $username = trim($_POST['username']);
    $username = strtolower(strip_tags($username));
    // Test longueur et pas de caractères spéciaux Test => (a, @ , AD, @o , lea , @la)
    if(strlen($username) <= 2 || preg_match('/[^A-Za-z\-]/', $username)) {
      $errors['username'] = 'Username is invalid';
    }else{
      unset($errors['username']);
      $test_username = true;
      $success['username'] = 'Yes!';
    }

  }
    // Test présence email
    if(isset($_POST['email']) && empty($_POST['email'])) { 
    $errors['email'] = 'Email is empty!';
  }
  else{
    // Test pour valider l'email => renvoie true ou false
    $email = trim($_POST['email']);
    $email = strip_tags($email);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'Email invalide';
    }
    else{
      unset($errors['email']);
      $test_email = true;
      $success['email'] = 'Yes!';
    }

  }
  // Test présence password
  if(isset($_POST['password']) && empty($_POST['password'])) { 
    $errors['password'] = 'Password is empty!';
  }
  else{
    $password = trim($_POST['password']);
    $password = strip_tags($password);

    // Test longueur et minimum une lettre majuscule, une lettre minuscule,un chiffre, et un caractère spéciaux
    // Test => ('abcDeF1#','aazazaz', 'a1a@', 'ASED23@#')
    if(!preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $password)) {
      $errors['password'] = 'password is invalid';
    }else{
      unset($errors['password']);
      $test_password = true;
      $success['password'] = 'Yes!';
    }

  }

  if($test_email && $test_password && $test_username && empty($errors)) {
    foreach ($users as $key => $user) {
      if(
        $user['email'] === $email 
        && $user['password'] === $password 
        && $user['username'] === $username
      ) {
        $_SESSION['username'] = ucfirst($username);
        header('location: home.php');
      }
      else{
        $msg = "Vous n'êtes pas chez nous!!";
      }
    }
    
  }

}
?>

<!doctype html>
<html lang="fr">
 <head>
   <meta charset="utf-8">
   <title>Titre de la page</title>
   <link rel="stylesheet" href="style.css">
 </head>
 <body>
   <?= (isset($msg))? $msg : ''; ?>
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


