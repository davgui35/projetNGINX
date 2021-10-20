<?php
$test_email = false;
$test_password = false;
$test_username = false;

// Connection
require_once('Connect.php');
$connexion = new Connect('newbase');
$users = $connexion->find("SELECT * FROM users");
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
          $success['username'] = 'Username conform!';
          unset($errors['username']);
        foreach ($users as $key => $user) {
            if($user['username'] === $username) {
              $test_username = true;
          }
      }
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
          $success['email'] = 'Email conforme!';
          unset($errors['email']);
          foreach ($users as $key => $user) {
              if($user['email'] === $email) {
                $test_email = true;
            }
        }
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
      }
      else{
          unset($errors['password']);
          $success['password'] = 'Password conform!';
        foreach ($users as $key => $user) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            if(password_verify($_POST['password'], $user['password'])
                && $email = $user['email']
                && $username = $user['username']
            
            ) {
                $test_password = true;
            }
        }
      }
    }
    
    
    if($test_email && $test_password && $test_username && empty($errors)) {
        $_SESSION['username'] = ucfirst($username);
        header('location: home.php');
    }else{
        $msg = "Tous les champs ne sont pas renseignés correctement!";
    }
    
    if(count($success) === 3 && !empty($errors)){
        header('location: error.php');
    }
      
}
  
  