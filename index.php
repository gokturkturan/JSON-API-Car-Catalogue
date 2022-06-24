<?php 
    //http://localhost/JSON-API-Car-Catalogue/index.php?id=2
    include "db.php";
    include "functions.php";
    $request = isset($_GET["request"]) ? $_GET["request"] : null;
    $json = array();
    $json["Error"] = FALSE;
    $code = 200;

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $make = $_POST["make"];
        $model = $_POST["model"];
        $trims = $_POST["trims"];
        $year = $_POST["year"];
        $agency = $_POST["agency"];
        $rating = $_POST["rating"];
        
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
    } else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
        if(isset($_GET["id"])) {
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
        if(isset($_GET["id"])) {
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

    SetHeader($code);
    $json[$code] = HttpStatus($code);
    echo json_encode($json);

?>