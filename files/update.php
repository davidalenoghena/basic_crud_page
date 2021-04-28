<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login_page.php");
    exit;
}

// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$lecturer = $user_id = $course_title = "";
$lecturer_err = $user_id_err = $course_title_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_lecturer = trim($_POST["lecturer"]);
    if(empty($input_lecturer)){
        $lecturer_err = "Please enter lecturer's name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $lecturer_err = "Please enter a valid name.";
    } else{
        $lecturer = $input_lecturer;
    }
    
    // Validate course title
    $input_course_title = trim($_POST["course_title"]);
    if(empty($input_course_title)){
        $course_title_err = "Please enter the course's title.";     
    } else{
        $course_title = $input_course_title;
    }
    
    // Validate user_id
    $input_user_id = trim($_SESSION["id"]);
    if(empty($input_user_id)){
        $user_id_err = "Not logged in";     
    } else{
        $user_id = $input_user_id;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($course_title_err) && empty($user_id_err)){
        // Prepare an update statement
        $sql = "UPDATE courses SET name=?, course_title=?, user_id=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sis", $param_name, $param_user_id, $param_course_title);
            
            // Set parameters
            $param_lecturer = $lecturer;
            $param_user_id = $user_id;
            $param_course_title = $course_title;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: welcome_page.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM courses WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $lecturer = $row["lecturer"];
                    $course_title = $row["course_title"];
                    $created_at = $row["created_at"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: 404.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: 404.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Course</h2>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Lecturer</label>
                            <input type="text" name="lecturer" class="form-control <?php echo (!empty($lecturer_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lecturer; ?>">
                            <span class="invalid-feedback"><?php echo $lecturer_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <textarea name="address" class="form-control <?php echo (!empty($course_title_err)) ? 'is-invalid' : ''; ?>"><?php echo $course_title; ?></textarea>
                            <span class="invalid-feedback"><?php echo $address_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Date Created</label>
                            <input type="text" name="created_at" class="form-control <?php echo (!empty($created_at_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $created_at; ?>">
                            <span class="invalid-feedback"><?php echo $created_at_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="welcome_page.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
