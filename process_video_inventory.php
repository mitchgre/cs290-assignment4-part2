<?php

//echo json_encode( $_POST);

if(isset($_POST['getAllVideos']))
    //echo ( json_encode(getAllVideos()));
    echo getAllVideos();
    //echo 'nonempty';
else
    echo 'empty';

function getAllVideos()
{
    $ids = [];
    $names = [];
    $categories = [];
    $lengths = [];
    $renteds = [];
    
    $entries = [];

    $mysqli = connectToDB();

    $query = "select id,name,category,length,rented from video_inventory";
    if ($sql=$mysqli->prepare($query))
        {
            
            $sql->execute();
            
            // bind results
            $sql->bind_result($id,$name,$category,$length,$rented);
            // check if category is in filter
            
            while($sql->fetch())
                {
                    $entry = [];
                    array_push($entry,$id); //echo $id;
                    array_push($entry,$name); //echo $name;
                    array_push($entry,$category);
                    array_push($entry,$length);
                    array_push($entry,$rented);
                    //array_push($entries, json_encode($entry));
                    array_push($entries, $entry);
                }
        }
    //echo $entry;
    /*
    $entry = [
        json_encode($ids),
        json_encode($names),
        json_encode($categories),
        json_encode($lengths),
        json_encode($renteds)];
    */
    return json_encode($entries);
}


function connectToDB(){
    include './credentials.php';  // username, hostname,password
    $mysqli = new mysqli($hostname, $username, $password, "290_assignment4_video_tracker");
    if ($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . 
                $mysqli->connect_errno. ") " . 
                $mysqli->connect_error;
        }
    return $mysqli;
}

function resetIDs() {
        // reset counter on primary key
    $mysqli = connectToDB();
    $query = "alter table video_inventory drop id";
    if (preparedStatement($query)){
        $query = "alter table video_inventory auto_increment = 1";
        if (preparedStatement($query)){
            $query = "alter table video_inventory add id".
                " int unsigned not null auto_increment primary key first";
            if (preparedStatement($query)){
                return true;
            }
        }
    }
}

?>