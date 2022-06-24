<?php 
    //http://localhost/JSON-API-Car-Catalogue/index.php?id=2
    include "functions.php";

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "api";
    $connect = mysqli_connect($host,$user,$pass);
    
    $json = array();
    $json["Error"] = FALSE;
    $code = 200;

    if($connect) {
        mysqli_select_db($connect, $db);
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            if(!isset($_POST["make"]) || !isset($_POST["model"]) || !isset($_POST["trims"]) || !isset($_POST["year"])) {
                $code = 400;
                $json["Error"] = TRUE;
                $json["Message"] = "Please POST every data.";
            } else {
                $make = addslashes($_POST["make"]);
                $model = addslashes($_POST["model"]);
                $trims = addslashes($_POST["trims"]);
                $year = addslashes($_POST["year"]);
                $agency = addslashes($_POST["agency"]);
                $rating = addslashes($_POST["rating"]);

                if(empty($make) || empty($model) || empty($trims) || empty($year)) {
                    $code = 400;
                    $json["Error"] = TRUE;
                    $json["Message"] = "Please do not leave a blank space.";
                } else {
                    $getTable = mysqli_query($connect,"SELECT * FROM cars WHERE make='$make' AND model='$model' AND trims='$trims' AND year='$year'");
    
                    if(mysqli_num_rows($getTable) == 0) {
                        $addTable1 = mysqli_query($connect,"INSERT INTO cars (make,model,trims,year) VALUES('$make','$model','$trims','$year')");
                        $getTable = mysqli_query($connect,"SELECT * FROM cars WHERE make='$make' AND model='$model' AND trims='$trims' AND year='$year'");
                        if(mysqli_num_rows($getTable) == 1) {
                            while($row = mysqli_fetch_assoc($getTable)) {
                                $id = $row['id'];
                                $addTable2 = mysqli_query($connect,"INSERT INTO rating (car_id,agency,rating) VALUES('$id','$agency','$rating')");
                                $json["Message"] = "Your inputs added to the cars and rating table.";
                            }
                        }
                    } else if (mysqli_num_rows($getTable) == 1) {
                        $getTable = mysqli_query($connect,"SELECT * FROM cars WHERE make='$make' AND model='$model' AND trims='$trims' AND year='$year'");
                        if(mysqli_num_rows($getTable) == 1) {
                            while($row = mysqli_fetch_assoc($getTable)) {
                                $id = $row['id'];
                                $getTable = mysqli_query($connect,"SELECT * FROM rating WHERE car_id='$id'");
                                if(mysqli_num_rows($getTable) >= 1) {
                                    $getTable = mysqli_query($connect,"SELECT * FROM rating WHERE agency = '$agency' AND car_id = '$id'");
                                    if(mysqli_num_rows($getTable) == 0) {
                                        $addTable2 = mysqli_query($connect,"INSERT INTO rating (car_id,agency,rating) VALUES ('$id','$agency','$rating')");
                                        $json["Message"] = "New rating is added to the table.";
                                    } else {
                                        $code = 400;
                                        $json["Error"] = TRUE;
                                        $json["Message"] = "This evaluation is already added.";
                                    }
                                    break;
                                }
                            }
                        } 
                    } else {
                        $code = 400;
                        $json["Error"] = TRUE;
                        $json["Message"] = "This car is already added to the table.";
                    }
                }
            }
        } else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
            if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
                $CarTableID = intval($_GET["id"]);
                
                $deleteCar = mysqli_query($connect,"DELETE FROM cars WHERE id='$CarTableID'");
                $deleteRating = mysqli_query($connect,"DELETE FROM rating WHERE car_id='$CarTableID'");
    
                if($deleteCar && $deleteRating) {
                    $code = 200;
                    $json["Message"] = "Car and its rating were deleted.";
                } else {
                    $code = 400;
                    $json["Error"] = TRUE;
                    $json["Message"] = "Car and its rating are not deleted.";
                }
                
                
            } else {
                $code = 400;
                $json["Error"] = TRUE;
                $json["Message"] = "Please send datas";
            }
        } else if ($_SERVER['REQUEST_METHOD'] == "GET") {
            if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
                $id = intval($_GET["id"]);
                $getTable = mysqli_query($connect,"SELECT * FROM cars WHERE id='$id'");
                if(mysqli_num_rows($getTable) == 1) {
                    $carData = mysqli_query($connect,"SELECT * FROM cars WHERE id='$id'");
                    $carRatingData = mysqli_query($connect,"SELECT * FROM rating WHERE car_id='$id'");
                    if($row = mysqli_fetch_assoc($carData)) {
                        $json["carData"] = $row;
                    }
                    if($row = mysqli_fetch_assoc($carRatingData)) {
                        $json["carRatingData"] = $row;
                    }
                } 
            } else {
                $code = 400;
                $json["Error"] = TRUE;
                $json["Message"] = "Please send id";
            }
        } else {
            $json["Error"] = TRUE;
            $json["Message"] = "Request does not found.";
        }
    } else {
        $code = 500;
        $json["Error"] = TRUE;
        $json["Message"] = "Database connection failed";
    }
    

    SetHeader($code);
    $json[$code] = HttpStatus($code);
    echo json_encode($json);

?>