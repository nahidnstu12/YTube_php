<?php

class VideoProcessor{
    private $con;
    private $videoLimit = 50000000; //49mb
    private $allwoedTypes = ["mp4","flv","webm","mov","mkv","vob","ogv","ogg","mpeg","avi","wmp","mpg"];
    private $ffmpegPath = "ffmpeg/windows/ffmpeg.exe"; //  *** WINDOWS ***
    public function __construct($con)
    {
        $this->con = $con;
        $this->ffmpegPath = realpath("ffmpeg/windows/ffmpeg.exe");
    }
    public function upload($videoUploadData){
        $tagetdir = "uploads/videos/";
        $videoFile = $videoUploadData->videoDataArray;
        $tempFilePath = $tagetdir . uniqid() . basename($videoFile['name']); 

        $tempFilePath = str_replace(" ","_",$tempFilePath);
        // videos/upload/5f795a6892e4fCF_prob_find.mp4

        $isValidData = $this->processData($videoFile,$tempFilePath);

        if(!$isValidData){
            return false;
        }
        if(move_uploaded_file($videoFile["tmp_name"],$tempFilePath)){
            $finalPath = $tagetdir . uniqid().".mp4";
            if(!$this->insertVideoData($videoUploadData,$finalPath)){
                echo "Insert query failed\n";
                return false;
            }
            if(!$this->convertVideoToMp4($tempFilePath,$finalPath)){
                echo "upload failed\n";
                return false;
            }
            if(!$this->deleteFile($tempFilePath)){
                echo "upload failed\n";
                return false;
            }
        }

        
    }
    private function processData($videoData,$filePath){
        $videoType = pathinfo($filePath,PATHINFO_EXTENSION);
        if(!$this->isValidSize($videoData)){
            echo "File too large. Can't be more than ". $this->videoLimit." bytes";
            return false;
        }
        elseif(!$this->isValidType($videoType)){
            echo "Invalid video type";
            return false;
        }
        elseif($this->hasError($videoData)){
            echo "Error Occured ".$videoData['error'];
            return false;
        }
        return true;
    }
    private function isValidSize($data){
        return $data['size'] <= $this->videoLimit;
    }
    private function isValidType($data){
        $lowercased = strtolower($data);
        return in_array($lowercased,$this->allwoedTypes);
    }
    private function hasError($data){
        return $data['error'] != 0;
    }
    private function insertVideoData($data,$filePath){
        $query = $this->con->prepare("insert into videos (title,uploadedBy,description,privacy,category,filePath) values (:title,:uploadedBy,:description,:privacy,:category,:filePath)");

        $query->bindParam(":title",$data->title);
        $query->bindParam(":uploadedBy",$data->uploadedBy);
        $query->bindParam(":description",$data->description);
        $query->bindParam(":privacy",$data->privacy);
        $query->bindParam(":category",$data->category);
        $query->bindParam(":filePath",$filePath);
        return $query->execute();
    }
    public function convertVideoToMp4($tempFilePath, $finalFilePath) {
        $cmd = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1";

        $outputLog = array();
        exec($cmd, $outputLog, $returnCode);
        
        if($returnCode != 0) {
            //Command failed
            foreach($outputLog as $line) {
                echo $line . "<br>";
            }
            return false;
        }

        return true;
    }
    private function deleteFile($filePath){
        if(!unlink($filePath)){
            echo "Couldn't delete file";
            return false;
        }
        return true;
    }
}

?>