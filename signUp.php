<?php   
include_once("includes/header.php"); 
include_once("includes/config.php");
include_once("includes/classes/FormSanitizer.php");
include_once("includes/classes/Account.php");
include_once("includes/classes/Constants.php");

$account =new Account($con);

if(isset($_POST['submitButton'])){
    // form sanitizze
    $fn = FormSanitizer::sanitizeString($_POST['firstName']);
    $ln = FormSanitizer::sanitizeString($_POST['lastName']);
    $un = FormSanitizer::sanitizeUsername($_POST['username']);
    $em1 = FormSanitizer::sanitizeEmail($_POST['email']);
    $em2 = FormSanitizer::sanitizeEmail($_POST['email2']);
    $pass1 = FormSanitizer::sanitizePassword($_POST['password']);
    $pass2 = FormSanitizer::sanitizePassword($_POST['password2']);

    $wasSuccesssful = $account->register($fn, $ln, $un, $em1, $em2, $pass1, $pass2);
    // echo $wasSuccesss;
    if($wasSuccesssful){
        echo `<script>alert('Registation Success')<script>`;
        $_SESSION["userLoggedIn"] = $un;
        header("Location: signIn.php");
    }
    
}
// $fn = FormSanitizer::sanitizeString("nAh id is@gmail.com");


?>
    <div class="signInContainer">

        <div class="form-column">

            <div class="header">
                <img src="assets/images/icons/VideoTubeLogo.png" title="logo" alt="Site logo">
                <h3>Sign Up</h3>
                <span>to continue to VideoTube</span>
            </div>

            <div class="loginForm">

                <form action="signUp.php" method="POST">
                <?php    //echo $fn . $un . $pass2; ?>
                <?php  echo $account->getError(Constants::$firstNameCharacters); ?>  
                <input type="text" name="firstName" placeholder="First name" value="<?php $account->getInputValue('firstName'); ?>" autocomplete="off" required>

                <?php  echo $account->getError(Constants::$lastNameCharacters); ?>  
                <input type="text" name="lastName" placeholder="Last name" autocomplete="off" value="<?php $account->getInputValue('lastName'); ?>" required>

                <?php  echo $account->getError(Constants::$usernameCharacters); ?>
                <?php  echo $account->getError(Constants::$usernameTaken); ?>  
                <input type="text" name="username" placeholder="Username" autocomplete="off" value="<?php $account->getInputValue('username'); ?>" required>

                <?php  echo $account->getError(Constants::$emailInvalid); ?>  
                <?php  echo $account->getError(Constants::$emailsDoNotMatch); ?> 
                <?php  echo $account->getError(Constants::$emailTaken); ?> 

                <input type="email" name="email" placeholder="Email" autocomplete="off" value="<?php $account->getInputValue('email'); ?>" required>

                <input type="email" name="email2" placeholder="Confirm email" autocomplete="off" value="<?php $account->getInputValue('email2'); ?>" required>
                
                <?php  echo $account->getError(Constants::$passwordLength); ?> 
                <?php  echo $account->getError(Constants::$passwordNotAlphanumeric); ?>   
                <?php  echo $account->getError(Constants::$passwordsDoNotMatch); ?>  
                <input type="password" name="password" placeholder="Password" autocomplete="off" required>

                <input type="password" name="password2" placeholder="Confirm password" autocomplete="off" required>

                <input type="submit" name="submitButton" value="SUBMIT">

                </form>

            </div>

            <a class="signInMessage" href="signIn.php">Already have an account? Sign in here!</a>
        
        </div>
    
    </div>
   

<?php require_once("includes/footer.php"); ?>