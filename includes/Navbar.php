
<!-- Head Bar -->
<?php 
require_once("config.php");  
require_once("header.php")
?>

<div id="pageContainer">
        <div id="headContainer">
            <button class="navShowHide">
                <img src="assets/images/icons/menu.png" alt="brand"/>
            </button>
            <a href="index.php" class="logo">
                <img src="assets/images/icons/VideoTubeLogo.png" alt="sitelogo">
            </a>
            <div class="searchBar">
                <form action="" method="get">
                    <input type="text" placeholder="search..." name="term" class="search" />
                    <button class="searchBtn">
                        <img src="assets/images/icons/search.png" />
                    </button>
                </form>
            </div>
            <div class="rightIcons">
                <a href="upload.php">
                <img class="upload" src="assets/images/icons/upload.png">
                </a>
                <a href="signUp.php">
                <img class="upload" src="assets/images/profilePictures/default.png">
                </a>
            </div>
        </div>
        <div id="sidenavContainer" style="display: none;">
        hello 2
        </div>
        <div id="mainSectionContainer">
            <div id="mainContentContainer">