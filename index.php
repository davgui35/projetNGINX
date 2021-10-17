<?php
$user = "newuser";
$pass = "password";
$errors = [];

<<<<<<< HEAD
try {
    $dbh = new PDO('mysql:host=localhost;dbname=newbase', $user, $pass);
	  // Test de validation du formulaire
    if(isset($_POST['new_user'])){
      print('form validate !');
      // Test présence username
      if(isset($_POST['username']) && empty($_POST['username'])) { 
        $errors['username'] = 'Username is empty!';
      }
       // Test présence email
       if(isset($_POST['email']) && empty($_POST['email'])) { 
        $errors['email'] = 'Email is empty!';
      }
      // Test présence password
      if(isset($_POST['password']) && empty($_POST['password'])) { 
        $errors['password'] = 'Password is empty!';
      }
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
}
=======
echo "Hello World";
>>>>>>> 8b33df35a950c6d238ad6ab4dc2bb4e40b3c57b8
?>

<!doctype html>
<html lang="fr">
 <head>
   <meta charset="utf-8">
   <title>Titre de la page</title>
   <link rel="stylesheet" href="style.css">
 </head>
 <body>
	<form method="post" action="index.php">
  <fieldset>
    <legend>Se connecter</legend>
    <div>
      <input type="text" name="username" id="username" value="" placeholder="Username..." />
      <label for="username">Username</label>
    </div>
    <?php if(isset($errors['username'])): ?>
      <span class="errors"><?= (isset($errors['username']))? $errors['username'] : ""; ?></span>
    <?php endif; ?>
    <div>
      <input type="email" name="email" id="email" value="" placeholder="Email..."/>
      <label for="email ">Email</label>
    </div>
    <?php if(isset($errors['email'])): ?>
      <span class="errors"><?= (isset($errors['email']))? $errors['email'] : ""; ?></span>
    <?php endif; ?>
    <div>
      <input type="password" name="password" id="password" value="" placeholder="Password.."/>
      <label for="password">Password</label>
    </div>
    <?php if(isset($errors['password'])): ?>
      <span class="errors"><?= (isset($errors['password']))? $errors['password'] : ""; ?></span>
    <?php endif; ?>
    <div>
	<input type="submit" name="new_user"  value="Se connecter"/>
    </div>
  </fieldset>
</form>
 <body>
</html>


