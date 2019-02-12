<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>
<?php 

    if(!isset($_SESSION['user_id'])){
        header('Location: index.php');
    }


    $errors = array();
    $first_name = '';
    $last_name = '';
    $email = '';
    $password = '';


    if(isset($_POST['submit'])){

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // //checking required fields
        $req_fields = array('first_name', 'last_name', 'email', 'password');
        $errors = array_merge($errors, check_req_feilds($req_fields));

        //checking max length
        $max_len_fields = array('first_name' => 50, 'last_name' =>100, 'email' =>100, 'password'=>40);
        $errors = array_merge($errors, check_max_len($max_len_fields));

        //checking email address
        if(!is_email($_POST['email'])){
            $errors[] = 'Email address is invalid';
        }
        
        //checking if email is in the db
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $query = "SELECT * FROM user WHERE email = '{$email}' LIMIT 1";

        $result_set = mysqli_query($connection, $query);

        if($result_set){
            if(mysqli_num_rows($result_set) ==1){
                $errors[] =  'Email address already exists';
            }
        }

        if(empty($errors)){
            //then no errors
        $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        // email address is already sanitized
        $hashed_password = sha1($password);

        $query = "INSERT INTO user (";
        $query .= "first_name, last_name, email, password, is_deleted";
        $query .= ") VALUES (";
        $query .= "'{$first_name}', '{$last_name}', '{$email}', '{$hashed_password}', 0";
        $query .= ")";

        $result = mysqli_query($connection, $query);

        if($result){
            header('Location: users.php?user_added=true');
        } else{
            $errors[]='Faild to add new Record';
        }


        }
       
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add New User</title>
    <link rel="stylesheet" href="./css/main.css">
</head>
<body>
    <header>
        <div class="appname">User Management System</div>
        <div class="loggedin">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Logout</a></div>
    </header>

    <main>
        <h1>Add New User <span><a href="users.php"> <-Back To User</a></span> </h1>
        <?php 
            if(!empty($errors)){
            display_errors($errors);
            }
        ?>
        <form action="add-user.php" class="userform" method="POST">
            <p>
                <label for="">First Name:</label>
                <input type="text" name="first_name" id="" <?php echo 'value="' . $first_name . '"'; ?> >
            </p>
            <p>
                <label for="">Last Name:</label>
                <input type="text" name="last_name" id="" <?php echo 'value="' . $last_name . '"'; ?> >
            </p>
            <p>
                <label for="">Email Address:</label>
                <input type="text" name="email" id="" <?php echo 'value="' . $email . '"'; ?> >
            </p>
            <p>
                <label for="">Password:</label>
                <input type="password" name="password" id="">
            </p>
            <p>
                <label for="">&nbsp;</label>
                <button type="submit" name="submit">Save</button>
            </p>
        </form>
       
    </main>
</body>
</html>