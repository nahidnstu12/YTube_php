<?php

class Account{

    private $con;
    private $errorArray = array();

    public function __construct($con){
        $this->con = $con;
    }

    public function register($fn,$ln,$un,$em1,$em2,$pass1,$pass2){
        $this->validateFName($fn);
        $this->validateLName($ln);
        $this->validateUserName($un);
        $this->validateEmail($em1,$em2);
        $this->validatePassword($pass1,$pass2);

        if(empty($this->errorArray)){
            $this->insertUserData($fn,$ln,$un,$em1,$pass1);
            return true;
        }else{
            return false;
        }
    }
    private function insertUserData($fn,$ln,$un,$em,$pass){
        $pass = hash('sha512',$pass);
        $profilePic = "assets/images/profilePictures/default.png";

        $query = $this->con->prepare("insert into users (firstName,lastName,username,email,password,profilepic) values(:fn,:ln,:un,:em,:pass,:dp)");
        $query->bindParam(":fn",$fn);
        $query->bindParam(":ln",$ln);
        $query->bindParam(":un",$un);
        $query->bindParam(":em",$em);
        $query->bindParam(":pass",$pass);
        $query->bindParam(":dp",$profilePic);

        return $query->execute();
    }
    public function login($un,$pass){
        $pass = hash('sha512',$pass);
        $query = $this->con->prepare("select * from users where username = :un and password = :pass");
       
        $query->bindParam(":un",$un);
        $query->bindParam(":pass",$pass);
        $query->execute();
        //echo "select * from users where username = $un and password = $pass";
        if($query->rowCount() == 1){
            return true;
        }else{
            array_push($this->errorArray,Constants::$loginFailed);
            return false;
        }
    }

    public function validateFName($name){
        if(strlen($name) < 2 || strlen($name) > 25){
            array_push($this->errorArray, Constants::$firstNameCharacters);
        }
    }
    private function validateLName($name){
        if(strlen($name) < 2 || strlen($name) > 25){
            array_push($this->errorArray, Constants::$lastNameCharacters);
        }
    }
    private function validateUserName($name){
        if(strlen($name) < 5 || strlen($name) > 25){
            array_push($this->errorArray, Constants::$usernameCharacters);
        }
        // usernameTaken or not
        $q = $this->con->prepare("select email from users where username = :un");
        $q->bindParam(":un",$name);
        $q->execute();

        if($q->rowcount() !=0){
            array_push($this->errorArray,Constants::$usernameTaken); 
        }

    }
    private function validateEmail($em1,$em2){
        if($em1 != $em2){
            array_push($this->errorArray,Constants::$emailsDoNotMatch); 
        }
        if(!filter_var($em1,FILTER_VALIDATE_EMAIL)){
            array_push($this->errorArray,Constants::$emailInvalid); 
        }
        // emailTaken or not
        $q = $this->con->prepare("select email from users where email = :email");
        $q->bindParam(":email",$em1);
        $q->execute();

        if($q->rowcount() !=0){
            array_push($this->errorArray,Constants::$emailTaken); 
        }

    }
    private function validatePassword($p1,$p2){
        if($p1 != $p2){
            array_push($this->errorArray,Constants::$passwordsDoNotMatch); 
        }
        if(preg_match("/[^A-Za-z0-9]/",$p1)){
            array_push($this->errorArray,Constants::$passwordNotAlphanumeric); 
            return;
        }
        if(strlen($p1) > 15 || strlen($p1) < 5){
            array_push($this->errorArray,Constants::$passwordLength); 
        }
    }

    public function getError($err){
        if(in_array($err,$this->errorArray)){
            return "<span class='errorMessage'>$err</span>";
        }
    }
    public function getInputValue($name){
        if(isset($_POST[$name])){
            echo $_POST[$name];
        }
    }
}

?>