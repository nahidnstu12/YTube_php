<?php
class VideoForm{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }
    public function createUploadForm(){
        $fileInput = $this->createFileInput();
        $titleInput = $this->createTitleInput();
        $descriptInput = $this->createDescriptInput();
        $typeInput = $this->createTypeInput();
        $categoryInput = $this->createCategoryInput();
        $uploadBtn = $this->uploadBtn();
        return "<form action='processing.php' method='POST' enctype='multipart/form-data' style='width:80%' >
            $fileInput
            $titleInput
            $descriptInput
            $typeInput
            $categoryInput
            $uploadBtn
        </form>";
    }
    private function createFileInput(){
        return "<div class='form-group'>
        <label for='exampleFormControlFile1'>Your Video file</label>
        <input type='file' class='form-control-file' id='exampleFormControlFile1' name='fileInput' required>
        </div>";
    }
    private function createTitleInput(){
        return "<div class='form-group'>
        <input type='text' class='form-control' id='exampleFormControlName' name='title' placeholder='Title' required>
        </div>";
    }
    private function createDescriptInput(){
        return "<div class='form-group'>
        <textarea class='form-control' name='description' placeholder='Description' rows='3' required></textarea>
        </div>";
    }
    private function createTypeInput(){
        return "<div class='form-group'>
        <select class='form-control' name='privacyInput'>
            <option value='0'>Private</option>
            <option value='1'>Public</option>
        </select>
    </div>";
    }
    private function createCategoryInput(){
        $query = $this->con->prepare("select * from categories");
        $query->execute();
        $html = "<div class='form-group'>
        <select class='form-control' name='categoryInput'>";
        while($row = $query->fetch()){
            $id = $row["id"];
            $name = $row["name"];
            $html .= "<option value='$id'>$name</option>";
        }

        $html .="</select></div>";
        return $html;
    }
    private function uploadBtn(){
        return "<button type='submit' class='btn btn-primary' name='uploadBtn'>Upload</button>";
    }
}


?>