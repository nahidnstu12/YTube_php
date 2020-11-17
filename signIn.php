<?php   
include_once("includes/header.php"); 
include_once("includes/config.php");
include_once("includes/classes/Account.php");
include_once("includes/classes/Constants.php");

    $account = new Account($con);
    if(isset($_POST['submitButton'])){
        $un = $_POST['username'];
        $pass = $_POST['password'];

        $wasSuccessful = $account->login($un,$pass);
        if($wasSuccessful){
            $_SESSION["userLoggedIn"] = $un;
            header("Location: index.php");
        }
    }
?>

    <div class="signInContainer">

        <div class="form-column">

            <div class="header">
                <img src="assets/images/icons/VideoTubeLogo.png" title="logo" alt="Site logo">
                <h3>Sign In</h3>
                <span>to continue to VideoTube</span>
            </div>

            <div class="loginForm">

                <form action="signIn.php" method="POST">
                    <?php  echo $account->getError(Constants::$loginFailed); ?>  
                    <input type="text" name="username" placeholder="Username" value="<?php $account->getInputValue('username') ?>" 
                    required autocomplete="off">
                    <input type="password" name="password" placeholder="Password" required autocomplete="off">
                    <input type="submit" name="submitButton" value="SUBMIT">            
                </form>

            </div>

            <a class="signInMessage" href="signUp.php">Need an account? Sign up here!</a>
        
        </div>
    
    </div>

<?php require_once("includes/footer.php"); ?>