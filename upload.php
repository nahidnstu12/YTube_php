<?php 
require_once("includes/header.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
// require_once ("processing.php");
?>
<div class="column">
<?php
    $formProvider = new VideoForm($con);
    echo $formProvider->createUploadForm();
    function reload(){
        header("location:index.php");
    }
?>
</div>

<script>
$("form").submit(function() {
    $("#loadingModal").modal("show");
});
</script>

<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="<?php //reload()  ?>">
          <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body" style="display: flex;flex-direction:column">
        <img src="assets/images/icons/loader.gif" width="50%" height="50%" style="display: flex;margin:auto"/>
        <br>
        <span class="text-center"> wait. This might take a while.</span>
    </div>
    </div>
  </div>
</div>

<?php require_once("includes/footer.php") ?>