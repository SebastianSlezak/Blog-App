<?php
require 'config/database.php';

if(isset($_POST['submit'])) {
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $avatar = $_FILES['avatar'];

    if(!$firstname) {
        $_SESSION['signup'] = "Please enter your first name.";
    } elseif(!$lastname) {
        $_SESSION['signup'] = "Please enter your last name.";
    } elseif(!$username) {
        $_SESSION['signup'] = "Please enter a username.";
    } elseif(!$email) {
        $_SESSION['signup'] = "Please enter a valid email address.";
    } elseif(strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['signup'] = "Password must be at least 8 characters long.";
    } elseif($createpassword !== $confirmpassword) {
        $_SESSION['signup'] = "Passwords do not match.";
    } elseif(!$avatar['name']) {
        $_SESSION['signup'] = "Please upload an avatar image.";
    } else {
        if ($createpassword !== $confirmpassword) {
            $_SESSION['signup'] = "Passwords do not match.";
        } else {
            $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

            $user_check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
            $user_check_result = mysqli_query($connection, $user_check_query);

            if (mysqli_num_rows($user_check_result) > 0) {
                $_SESSION['signup'] = "Username or email already exists.";
            } else {
                $time = time();
                $avatar_name = $time . $avatar['name'];
                $avatar_tmp_name = $avatar['tmp_name'];
                $avatar_destination_path = 'images/' . $avatar_name;

                $allowed_files = ['png', 'jpg', 'jpeg'];
                $extension = explode('.', $avatar_name);
                $extension = end($extension);

                if(in_array($extension, $allowed_files)) {
                    if($avatar['size'] < 1000000) {
                        move_uploaded_file($avatar_tmp_name, $avatar_destination_path);
                    } else {
                        $_SESSION['signup'] = "File size too large. Must be less than 1MB.";
                    }
                } else {
                    $_SESSION['signup'] = "File type not allowed. Please upload a PNG, JPG or JPEG image.";
                }
            }
        }
    }

    if($_SESSION['signup']) {
        $_SESSION['signup-data'] = $_POST;
        header('location: ' . ROOT_URL . 'signup.php');
        die();
    } else {
        $insert_user_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) VALUES ('$firstname', '$lastname', '$username', '$email', '$hashed_password', '$avatar_name', 0)";
        $insert_user_result = mysqli_query($connection, $insert_user_query);

        if(!mysqli_errno($connection)) {
            $_SESSION['signup-success'] = "Registration successful. You can now sign in.";
            header('location: ' . ROOT_URL . 'signin.php');
            die();
        } else {
            $_SESSION['signup'] = "Registration failed. Please try again.";
            header('location: ' . ROOT_URL . 'signup.php');
            die();
        }
    }

} else {
    header('location: ' . ROOT_URL . 'signup.php');
    die();
}