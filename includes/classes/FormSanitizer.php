<?php

class FormSanitizer{
    public static function sanitizeString($text){
        $text = strip_tags($text);
        $text = str_replace(" ","",$text);
        $text = strtolower($text);
        $text = ucfirst($text);
        return $text;
    }
    public static function sanitizeUsername($text){
        $text = strip_tags($text);
        $text = str_replace(" ","",$text);
        return $text;
    }
    public static function sanitizeEmail($text){
        $text = strip_tags($text);
        $text = str_replace(" ","",$text);
        return $text;
    }
    public static function sanitizePassword($text){
        $text = strip_tags($text);
        return $text;
    }
    
}

?>