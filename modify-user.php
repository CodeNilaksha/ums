<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>
<?php 

    if(!isset($_SESSION['user_id'])){
        header('Location: index.php');
    }


    $errors = array();
    $user_id = '';
    $first_name = '';
    $last_name = '';
    $email = '';
    // $password = '';

    if(isset($_GET['user_id'])){
        //getting the user information
        $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);
        $query = "SELECT * FROM user WHERE id = {$user_id} LIMIT 1";

        $result_set = mysqli_query($connection, $query);

        if($result_set){
            if(mysqli_num_rows($result_set) == 1){
                //user found
                $result =  mysqli_fetch_assoc($result_set);
                $first_name = $result['first_name'];
                $last_name = $result['last_name'];
                $email = $result['email'];
            } else {
                //user not found
                header('Location: users.php?err=user_not_found');
            }
        }else{
            //query unsuccessfull
            header('Location: users.php?err=query_faild');
        }
    }


    if(isset($_POST['submit'])){
        $user_id = $_POST['user_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        // //checking required fields
        $req_fields = array('user_id', 'first_name', 'last_name', 'email');
        $errors = array_merge($errors, check_req_feilds($req_fields));

        //checking max length
        $max_len_fields = array('first_name' => 50, 'last_name' =>100, 'email' =>100);
        $errors = array_merge($errors, check_max_len($max_len_fields));

        //checking email address
        if(!is_email($_POST['email'])){
            $errors[] = 'Email address is invalid';
        }
        
        //checking if email is in the db
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $query = "SELECT * FROM user WHERE email = '{$email}' AND id != {$user_id} LIMIT 1";

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
      

       $query = "UPDATE user SET ";
       $query .= "first_name = '{$first_name}', ";
       $query .= "last_name = '{$last_name}', ";
       $query .= "email = '{$email}' ";
       $query .= "WHERE id = {$user_id} LIMIT 1";


        $result = mysqli_query($connection, $query);

        if($result){
            header('Location: users.php?user_modified=true');
        } else{
            $errors[]='Faild to add Update User Record';
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
    <title>View / Modify User</title>
    <link rel="stylesheet" href="./css/main.css">
</head>
<body>
    <header>
        <div class="appname">User Management System</div>
        <div class="loggedin">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Logout</a></div>
    </header>

    <main>
        <h1>Update User <span><a href="users.php"> <-Back To User</a></span> </h1>
        <?php 
            if(!empty($errors)){
            display_errors($errors);
            }
        ?>
        <form action="modify-user.php" class="userform" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
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
                <span>******</span> | <a href="change-password.php?user_id=<?php echo $user_id; ?>">Change Password</a>
            </p>
            <p>
                <label for="">&nbsp;</label>
                <button type="submit" name="submit">Update</button>
            </p>
        </form>
       
    </main>
</body>
</html>