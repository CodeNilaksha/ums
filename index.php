<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>
<?php 

    //check fro form submition
    if(isset($_POST['submit'])){

        $errors = array();
        
        //check if the username and password entered
        if(!isset($_POST['email']) || strlen(trim($_POST['email'])) < 1 ){
            $errors[] = 'Username is Missing/Invalid';
        }
        if(!isset($_POST['password']) || strlen(trim($_POST['password'])) < 1 ){
            $errors[] = 'Password is Missing/Invalid';
        }

        //check if there are any errors in the form
        if(empty($errors)){
        
            //save username and password into variables
            $email    = mysqli_real_escape_string($connection, $_POST['email']);
            $password = mysqli_real_escape_string($connection, $_POST['password']);
            $hashed_password = sha1($password);

            //prepare database query
            $query = "SELECT * FROM user
                      WHERE email = '{$email}'
                      AND password = '{$hashed_password}'
                      LIMIT 1";

            $result_set = mysqli_query($connection, $query);

            verify_query($result_set);
            //query successfull
            if(mysqli_num_rows($result_set) == 1 ){
                //valid user found
                $user = mysqli_fetch_assoc($result_set);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];

                //updating last login
                $query = "UPDATE user SET last_login = NOW() ";
                $query .= "WHERE id = {$_SESSION['user_id']} LIMIT 1";

                $result_set = mysqli_query($connection, $query);
                verify_query($result_set);/*{
                    die('Database Query Fail');
                } */

                //redirect to users.php
                header('Location: users.php');
            }else{
                //username and password invalid
                $errors[] = 'Invalid Username/Password';
            }
        // }else{
        //     $errors[] = 'Database query faild'; 
        // }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - User Management System</title>
    <link rel="stylesheet" href="./css/main.css">
</head>
<body> 
    <!-- login_start -->
    <div class="login">
        <!-- form_start -->
        <form action="index.php" method="POST">
            <fieldset>
                <legend><h1>Login</h1></legend>
                    <!--errors message-->
                    <?php 
                        if(isset($errors) && !empty($errors)){
                            echo '<p class="error">Invalid Username or Password</p>';    
                        }
                    ?>
                    <?php
                        if(isset($_GET['logout'])){
                            echo '<p class="info">You Successfully Logout</p>';    
                        }
                    ?>
                    <p>
                        <label for="">Username:</label>
                        <input type="text" name="email" id="" placeholder="Email Address">
                    </p>
                    <p>
                        <label for="">Password:</label>
                        <input type="password" name="password" id="" placeholder="Enter Password">
                    </p>
                    <p>
                        <button type="submit" name="submit">Login</button>
                    </p>
            </fieldset>
        </form>
        <!-- from_end -->
    </div>
    <!-- login_end -->
</body>
</html>


<?php mysqli_close($connection); ?>