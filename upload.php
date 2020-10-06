<?php 
require_once("includes/header.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
?>
<div class="column">
<?php
    $formProvider = new VideoForm($con);
    echo $formProvider->createUploadForm();
    // $query = $con->prepare("select * from categories");
    //     $query->execute();
    //     while($row = $query->fetch()){
    //         echo $row["name"];
    //     }
?>
</div>

<?php require_once("includes/footer.php") ?>