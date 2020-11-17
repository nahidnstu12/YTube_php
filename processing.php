<?php 
require_once ("includes/Navbar.php"); 
require_once ("includes/classes/VideoUploadData.php");
require_once ("includes/classes/VideoProcessor.php");
?>  
        
<?php 
    if(!isset($_POST['uploadBtn'])){
        echo "No file sent to page";
        exit();
    }

    // 1)Create user data
    $videoUploadData = new VideoUploadData($_FILES['fileInput'],$_POST['title'],$_POST['description'],$_POST['privacyInput'],$_POST['categoryInput'],"replace-username");
    // print_r( $videoUploadData);

    // 2)process video data
    $videoProcessor = new VideoProcessor($con);
    $wasSuccessful = $videoProcessor->upload($videoUploadData);

    // 3) check it if successful
    if($wasSuccessful){
        echo "Upload successful";
    }

?>

<?php require_once("includes/footer.php") ?>
           
