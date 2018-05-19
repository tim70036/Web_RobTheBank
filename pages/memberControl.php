<?php
use AWSCognitoApp\AWSCognitoWrapper;
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ProcessType']))
{
    require_once('credentials.php');
    require_once('../vendor/autoload.php');
    require_once('AWSCognitoWrapper.php');
    
    $wrapper = new AWSCognitoWrapper();
    $wrapper->initialize();

    try
    {
        if($_POST['ProcessType'] === 'register')
        {
            $account = filter_input(INPUT_POST, 'account');
            $password = filter_input(INPUT_POST, 'password');
            $password2 = filter_input(INPUT_POST, 'password2');
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            /* Checking */
            if(!$account) throw new Exception("Invalid Account!");
            if(!$password) throw new Exception("Invalid Password!");
            if(!$email) throw new Exception("Invalid Email!");
            if(strcmp($password, $password2) != 0) throw new Exception("Please Confirm Password!");

            /* Register through AWS */
            $error = $wrapper->signup($account, $email, $password);
            if(empty($error)) 
            {
                echo "succeed"; // Let front end check whether it is success
                exit;
            }
            else 
            {
                throw new Exception($error);
            }
        }

        else if($_POST['ProcessType'] === 'confirm')
        {
            $account = filter_input(INPUT_POST, 'account');
            $confirmation = filter_input(INPUT_POST, 'confirmation');
            
            /* Checking */
            if(!$account) throw new Exception("Invalid Account!");
            if(!$confirmation) throw new Exception("Invalid Confirmation Code!");

            /* Register through AWS */
            $error = $wrapper->confirmSignup($account, $confirmation);
            if(empty($error)) 
            {
                echo 'succeed'; // Let front end check whether it is success
                exit;
            }
            else 
            {
                throw new Exception($error);
            }
        }

        else if($_POST['ProcessType'] === 'login')
        {
            $account = filter_input(INPUT_POST, 'account');
            $password = filter_input(INPUT_POST, 'password');

            /* Checking */
            if(!$account) throw new Exception("Invalid Account!");
            if(!$password) throw new Exception("Invalid Password!");

            /* Login through AWS */
            $error = $wrapper->authenticate($account, $password);
            if(empty($error)) 
            {
                echo "succeed";
                exit;
            }
            else 
            {
                throw new Exception($error);
            }
        }
    }
    catch(Exception $e)
    {
        echo $e->getMessage();
        exit;
    }
}
?>
