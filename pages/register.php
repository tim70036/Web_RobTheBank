<?php
use AWSCognitoApp\AWSCognitoWrapper;
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    require_once('credentials.php');
    require_once('../vendor/autoload.php');
    require_once('AWSCognitoWrapper.php');
    
    $wrapper = new AWSCognitoWrapper();
    $wrapper->initialize();

    try
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
    catch(Exception $e)
    {
        echo $e->getMessage();
        exit;
    }
}

?>


<!-- Register content for popup.html -->
<div class="card-header" style="text-align: center; font-size: 1.4em;">註冊</div>
<div class="card-body">
    <form method="post"  id="register-form" action="register.php">
        <div class="form-group">
            <label for="InputEmail1">電子信箱</label>
            <input class="form-control" name='email' id="InputEmail1" type="email" aria-describedby="emailHelp" placeholder="請輸入電子信箱">
        </div>
        <div class="form-group">
            <label for="InputAccount1">帳號</label>
            <input class="form-control" name='account' id="InputAccount1" type="text" aria-describedby="accountHelp" placeholder="請輸入帳號">
        </div>
        <div class="form-group">
            <label for="InputPassword1">密碼</label>
            <input class="form-control" name='password' id="InputPassword1" type="password" placeholder="請輸入密碼">
        </div>
        <div class="form-group">
            <label for="InputPassword2">確認密碼</label>
            <input class="form-control" name='password2' id="InputPassword2" type="password" placeholder="請再輸入相同密碼">
        </div>

        <!-- Show Alert message if any error happended in AJAX -->
        <div class="alert alert-danger" id="alert-message" style="display: none;">
            <strong>Error: </strong> <span id="response"> Indicates a dangerous or potentially negative action. </span>
        </div>

        <button type="submit" id="submit-btn" class="btn btn-info btn-block" style="margin-top: 25px;">註冊</button>
    </form>

    <!-- Change to another content -->
    <div class="text-center" style="margin-top: 10px;">
        <a class="d-block small mt-3" onclick="LoadLogin()" style="margin: 0px 10px;">登入已有帳號</a>
    </div>
</div>
<!-- /.card-body -->

<!-- No redirect, so send form by using AJAX -->
<script type="text/javascript">
    
    // Invoke function when submit
    $("#register-form").submit(function(e) {

        // Make sure that btn can't be click again and alert is hided
        $("#submit-btn").addClass("disabled")
        $("#alert-message").fadeOut(200);

        // Record the account for confirm
        confirmAccount = $("#InputAccount1").val(); 

        var url = "register.php"; // the script where you handle the form input.
        
        $.ajax({
               type: "POST",
               url: url,
               data: $("#register-form").serialize(), // serializes the form's elements.
               success: function(data)
               {
                    // If register succeed, load confirm content to popup (popup.html)
                    if(data == "succeed")
                    {
                        LoadConfirm();
                    }
                    // Else show alert
                    else
                    {
                        $("#response").html(data); // show response from the php script. used for error
                        $("#alert-message").fadeIn(550);
                    }   
               }
             });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });
</script>