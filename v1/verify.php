<?php
include 'dbcon.php';

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    // Verify data
    $email = mysqli_real_escape_string($conn, $_GET['email']); // Set email variable
    $hash = mysqli_real_escape_string($conn, $_GET['hash']); // Set hash variable
                 
    $search = mysqli_query($conn, "SELECT email, hash, verify FROM user WHERE email='$email' AND hash='$hash' AND verify='0'");
    $match  = mysqli_num_rows($search);
                 
    if($match > 0){
        // We have a match, activate the account
        mysqli_query($conn, "UPDATE user SET verify='1' WHERE email='$email' AND hash='$hash' AND verify='0'");
        echo '<div class="statusmsg">Your account has been activated, you can now <a href="login.php">login</a></div>';
    }else{
        // No match -> invalid url or account has already been activated.
        echo '<div class="statusmsg">The url is either invalid or you already have activated your account.</div>';
    }
                 
}else{
    // Invalid approach
    echo '<div class="statusmsg">Invalid approach, please use the link that has been send to your email.</div>';
}

?>