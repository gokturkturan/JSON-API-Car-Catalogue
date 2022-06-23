<?php 
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
        } else {
            $code = 400;
            $json["Error"] = TRUE;
            $json["Message"] = "This car is already added to the table.";
        }

    } else {
        $json["Error"] = TRUE;
        $json["Message"] = "Request does not found.";
    }

    SetHeader($code);
    $json[$code] = HttpStatus($code);
    echo json_encode($json);

?>