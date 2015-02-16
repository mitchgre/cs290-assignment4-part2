<?php

//echo json_encode( $_POST);

if(isset($_POST['getAllVideos']))
    echo getAllVideos();
else if (isset($_POST['getCategories']))
    echo getCategories();
else if (isset($_POST['deleteVideo']))
    echo deleteVideo($_POST['deleteVideo']);
else if (isset($_POST['deleteAllVideos']))
    echo deleteAllVideos();
else if (isset($_POST['toggleVideo']))
    echo toggleVideo($_POST['toggleVideo']);
else if (isset($_POST['addVideo']))
    echo addVideo($_POST['name'],$_POST['category'],$_POST['length']);


/*
  search database for categories.
  Return an array of categories that can be used to display to user 
  what categories are in the database. 
 */
function getCategories(){
    $mysqli = connectToDB();
    $categories = []; // empty container array
    $query = "select distinct category from video_inventory where category!=''";
    
    if ($sql=$mysqli->prepare($query))
        {
            $sql->execute();
            // bind results
            $sql->bind_result($category);
            while($sql->fetch()) // loop over results and push into array
                array_push($categories,$category);
            // print_r($categories);
            return json_encode($categories);
        }
}


function addVideo($name,$category,$length){
    $mysqli = connectToDB();
    $query = "insert into video_inventory ".
        "(name,category,length) ".
        "values (\"$name\",\"$category\",\"$length\");";
    // echo "Trying to add $sql";
    //$result = $mysqli->query($sql);
    if (preparedStatement($query)) 
        {
            return json_encode("result processed");
        }
}

function toggleVideo($id){
    $mysqli = connectToDB();

    $query = "update video_inventory ".
        "set rented = !rented where id = ".$id;

    if ($sql=$mysqli->prepare($query))
        {
            
            $sql->execute();
            mysqli_close($mysqli);
            return json_encode('toggled '+$id);
        }
}


function deleteAllVideos(){
    $mysqli = connectToDB();
   // stage1: prepare
    $query = "Truncate video_inventory ";
    
    // stage2: execute (no need to bind)
    if( $sql = $mysqli->prepare($query)){
        $sql->execute();
        mysqli_close($mysqli);
        return json_encode('Deleted All Videos');
    }
}

function deleteVideo($id){
    $mysqli = connectToDB();

    $query = "delete from video_inventory ".
        "where id = ".$id;

    if ($sql=$mysqli->prepare($query))
        {
            
            $sql->execute();
            mysqli_close($mysqli);
            return 'deleted '+$id;
        }
}

function getAllVideos()
{

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
    mysqli_close($mysqli);
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

function preparedStatement($query){
    $mysqli = connectToDB();
    if( $sql = $mysqli->prepare($query)){
        $sql->execute();
        mysqli_close($mysqli);
        return true;
    }
    mysqli_close($mysqli);
    return false;
    
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