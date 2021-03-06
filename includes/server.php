<?php
if (!isset($_SESSION)) {
  session_start();
}
// initializing variables

$firstname = "";
$lastname = "";
$email    = "";
$gender = "";
$errors = array();
$username = $firstname . ' ' . $lastname;
// connect to the database
$db = mysqli_connect('localhost', 'root', 'root', 'social_network');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $firstname = mysqli_real_escape_string($db, $_POST['Firstname']);
  $lastname = mysqli_real_escape_string($db, $_POST['Lastname']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $gender = mysqli_real_escape_string($db, $_POST['gender']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  $username = $firstname . ' ' . $lastname;
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($firstname)) {
    array_push($errors, "Firstname is required");
  }
  if (empty($lastname)) {
    array_push($errors, "Lastname is required");
  }
  if (empty($email)) {
    array_push($errors, "Email is required");
  }
  if (empty($password_1)) {
    array_push($errors, "Password is required");
  }
  if ($password_1 != $password_2) {
    array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR user_email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['user_email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
    $password = md5($password_1); //encrypt the password before saving in the database

    $query = "INSERT INTO users (username, user_email, user_pass , f_name, l_name,user_gender) 
  			  VALUES('$username', '$email', '$password' ,'$firstname',' $lastname','$gender')";
    $result = mysqli_query($db, $query);

    $_SESSION['result'] = $result;
    $_SESSION['user_email'] = $email;
    $_SESSION['username'] = $username;
    $_SESSION['success'] = "You are now logged in";
    header('location: ../includes/index.php');
  }
}
if (isset($_POST['login_user'])) {

  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);


  if (empty($email)) {
    array_push($errors, "Email is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $password = md5($password);
    $query = "SELECT * FROM users WHERE user_email='$email' AND user_pass='$password'";
    $sql = "SELECT 'f_name' FROM users WHERE user_email='$email' AND user_pass='$password'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($results);
    if (mysqli_num_rows($results) == 1) {
      $_SESSION['user_email'] = $email;
      $_SESSION['username'] = $row['f_name'] . ' ' . $row['l_name'];
      $_SESSION['success'] = "You are now logged in";
      header('location: ../includes/index.php');
    } else {
      array_push($errors, "Wrong email/password combination");
    }
  }
}
