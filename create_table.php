<?php
    /*This file should be ran once*/
    $link = mysqli_connect("localhost", "root", "", "zuri_crud");
    
    // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    // Attempt create table query execution
    $sql = "CREATE TABLE users(
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(30) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(70) NOT NULL UNIQUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    if(mysqli_query($link, $sql)){
        echo "User Table created successfully.";
    } else{
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }

    $sql = "CREATE TABLE courses(
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        lecturer VARCHAR(30) NOT NULL,
        user_id INT(10) NOT NULL UNIQUE, 
        course_title VARCHAR(50) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";    
    if(mysqli_query($link, $sql)){
        echo "Course Table created successfully.";
    } else{
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
    // Close connection
    mysqli_close($link);
?>