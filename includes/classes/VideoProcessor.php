<?php

class VideoProcessor{
    private $con;
    private $videoLimit = 50000000; //49mb
    private $allwoedTypes = ["mp4","flv","webm","mov","mkv","vob","ogv","ogg","mpeg","avi","wmp","mpg"];
    // private $ffmpegPath = "ffmpeg/windows/ffmpeg.exe";
    // private $ffprobe = "ffmpeg/windows/ffprobe.exe"; 

    public function __construct($con)
    {
        $this->con = $con;
        $this->ffmpegPath = realpath("ffmpeg/windows/ffmpeg.exe");
        $this->ffprobe = realpath("ffmpeg/windows/ffprobe.exe");
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
            if(!$this->convertVideoToMp4($tempFilePath,$finalPath)){
                echo "upload failed to convert\n";
                return false;
            }
            if(!$this->insertVideoData($videoUploadData,$finalPath)){
                echo "Insert query failed\n";
                return false;
            }
            
            if(!$this->deleteFile($tempFilePath)){
                echo "upload failed\n";
                return false;
            }
            if(!$this->generateThumbnails($finalPath)) {
                echo "Upload failed - could not generate thumbnails\n";
                return false;
            }
            return true;
        }

        
    }
    private function processData($videoData,$filePath){
        $videoType = pathinfo($filePath,PATHINFO_EXTENSION);
        if(!$this->isValidSize($videoData)){
            echo "File too large. Can't be more than ". $this->videoLimit." bytes\n";
            return false;
        }
        elseif(!$this->isValidType($videoType)){
            echo "Invalid video type\n";
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
    private function convertVideoToMp4($tempFilePath, $finalFilePath) {
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
    private function generateThumbnails($filePath){
        $thumbnailSize = "210x118";
        $numThumbnails = 3;
        $thumbnailsPath = "uploads/videos/thumbnails";

        $duration = $this->getVideoDuration($filePath);
        // echo $duration;
        $videoId = $this->con->lastInsertId();
        $this->updateDBDuration($duration,$videoId);
        // processing each image
        for($num=1; $num <= $numThumbnails; $num++){
            $imgName = uniqid() . ".jpg";
            $interval = ($duration * .8)/$numThumbnails * $num;
            $path = "$thumbnailsPath/$videoId-$imgName";

            // thumbnails images
            $cmd = "$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $path 2>&1";

            $outputLog = array();
            exec($cmd, $outputLog, $returnCode);
            
            if($returnCode != 0) {
                //Command failed
                foreach($outputLog as $line) {
                    echo $line . "<br>";
                }
                return false;
            }
            $selected = $num == 1 ? 1 : 0;
            $query = $this->con->prepare("insert into thumbnails (videoId, filePath, selected) values (:videoId, :filePath, :selected)");
            $query->bindParam("videoId",$videoId);
            $query->bindParam("filePath",$path);
            $query->bindParam("selected",$selected);
            
            $success = $query->execute();
            if(!$success){
                echo "Error inserting thumbail\n";
                return false;
            }

        }
        return true;
    }
    private function getVideoDuration($filePath){
        return (int)shell_exec("$this->ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
    }
    private function updateDBDuration($duration,$videoId){
        $hr = floor($duration/3600);
        $min = floor(($duration - ($hr*3600))/60);
        $sec = floor($duration % 60);

        $hr = $hr < 1 ? "" : $hr.":";
        $min = $min < 10 ? "0$min:" : "$min:";
        $sec = $sec < 10 ? "0$sec" : $sec;

        $duration = $hr.$min.$sec;
        // echo $duration;
        $query = $this->con->prepare("update videos set duration=:duration where id=:videoId");
        $query->bindParam("duration",$duration);
        $query->bindParam("videoId",$videoId);
        $query->execute();
    }
}

?>