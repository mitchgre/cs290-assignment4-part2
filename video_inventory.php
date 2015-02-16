<html>
  <head>
    <title>video inventory</title>
  </head>
  <body>



<?php
session_start();

addVideoForm();
checkRequests();
//drawVideos('All Categories');

//$mysqli = connectToDB();
//drawVideos($mysqli);


function preparedStatement($query){
    $mysqli = connectToDB();
    if( $sql = $mysqli->prepare($query)){
        $sql->execute();
        return true;
    }
    return false;
}

// toggle check-in/check-out buttons
function checkAvailabilityToggle(){
  $mysqli = connectToDB();
  if ($id = array_search('check-out',$_POST)){
    // echo $id."<br>";
    $query = "update video_inventory set rented = !rented where id=".$id.";";
    if (preparedStatement($query))
        {
            echo "<script>location.reload();</script>";
            return true;
        }
  } // end check-out search
  else if ($id = array_search('check-in',$_POST)){  // gotta be a cleaner way to do this
    // echo $id."<br>";
    $query = "update video_inventory set rented = !rented where id=".$id.";";
    if ( preparedStatement($query))
        {
            echo "<script>location.reload();</script>";
            return true;
        }
  } // end check-out search
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



// delete a row from the table with the given id
// the alternate approach is to use hidden inputs, which might be more robust
function checkDeletions(){
    $mysqli = connectToDB();

    if ( isset($_POST["DeleteAll"]) && $_POST["DeleteAll"] != null){
        // stage1: prepare
        $query = "Truncate video_inventory ";
        
        // stage2: execute (no need to bind)
        if( $sql = $mysqli->prepare($query)){
            $sql->execute();
            echo "<script>location.reload();</script>";
            return true;
        }
    }

    $id = array_search('Delete',$_POST); // search post array for the id

    $query = "delete from video_inventory ".
        "where id = ".$id;

    if (preparedStatement($query))    
        resetIDs($mysqli);

    return false;
}


// check if parameters to insert a movie are valid
function validateInsertion($name,$category,$length){
    if ( empty($name) && !(empty($category) || empty($length))) // if category or length are set but name is not, that's not okay
        {
            echo "'Name' is a required field.";
        }
    else if ( !empty($name) )
        {
            $mysqli = connectToDB();
            $query = "insert into video_inventory ".
                "(name,category,length) ".
                "values (\"$name\",\"$category\",\"$length\");";
            // echo "Trying to add $sql";
            //$result = $mysqli->query($sql);
            if (preparedStatement($query)) 
                {
                    // close $mysqli connection
                    mysqli_close($mysqli);
                    //resetIDs();
                    return true;
                }

        }
    
    return false;
}

function validateGenres($genreFilter){
    //if (!empty($genreFilter))
};

// http://community.developer.authorize.net/t5/The-Authorize-Net-Developer-Blog/Handling-Online-Payments-Part-2-Reading-In-And-Sanitizing/ba-p/9446
function sanitize($value)
{
    return trim(strip_tags($value));
}

// handle post requests via mysql
function checkRequests(){
  if ($_SERVER['REQUEST_METHOD'] === 'POST') 
  {
      //print_r($_POST);
      // sanitize values from forms
      if (!empty($_POST['name']))
          $name = sanitize($_POST['name']);
      else
          $name = '';
      if (!empty($_POST['category']))
          $category = sanitize($_POST['category']);
      else
          $category = '';
      if (!empty($_POST['length']))
          $length = sanitize($_POST['length']);
      else
          $length = '';
      if (!empty($_POST['genreFilter']))
          $genreFilter = sanitize($_POST['genreFilter']);
      else
          $genreFilter = 'All Categories';
      /*
      if (!empty($_POST['DeleteAll']))
          $deleteAll = sanitize($_POST['DeleteAll']);
      if (!empty($_POST['Delete']))
          $Delete = sanitize($_POST['Delete']);
      */
      
      // validate data
      
      if (!empty($genreFilter))
          drawVideos($genreFilter);// validateGenres($genreFilter);
      else
          drawVideos('All Categories');// validateGenres($genreFilter);
      
      if (!empty($name)) 
          validateInsertion($name,$category,$length);
      checkDeletions();
      checkAvailabilityToggle();
      // header("Location: video_inventory_unsanitary.php&restart=true",true);
      //echo "<script>location.reload();</script>";
      //if (empty
      //validateDeletions($deleteAll,$Delete);
      //validCheckInCheckOut();
      
      

      /*
        if (! empty($_POST))
        {
        print_r($_POST);
        checkGenreFilter();
        checkAvailabilityToggle();
        if(!checkDeletions()) // if we're not deleting
        if(!checkInsertions())
                ;//checkAvailabilityToggle();
                } //
      */
  } // 
  else
      drawVideos('All Categories');
} // end 



// output a form to add videos
function addVideoForm() {
    echo <<<_END
<form method ='post' action='video_inventory.php'>
<table name='addBar'>
 <tr><td>name:</td> <td> <input type='text' name='name'></td>
     <td>category:</td> <td> <input type='text' name='category'></td>
     <td>length:</td> <td> <input type='text' name='length'></td>
     <td colspan='2' align='center'> <input type='submit' value='Add Video'></td></tr>
</table></form>
_END;
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


/*
  search database for categories.
  Return an array of categories that can be used to display to user 
  what categories are in the database. 
 */
function getCategories($mysqli){
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
            return $categories;
        }
}


function drawVideo($id,$name,$category,$length,$rented){
    echo "<tr><td> " . $id. "</td>".
        "<td>" . $name. "</td>".
        "<td>". $category. "</td>".
        "<td> " . $length. "</td>";
    // "<td>" . $row["rented"]. "</tr>";
    if ($rented == 0)
        {
            echo "<td>available</td>";
            echo "<td>";
            echo '<input type="submit" name="'.$id.'" value="check-out">';
            echo '<input type="submit" name="'.$id.'" value="Delete">';
            echo "</td></tr>";
        }
    else 
        {
            echo "<td>checked out</td>";
            echo "<td>";
            echo '<input type="submit" name="'.$id.'" value="check-in">';
            echo '<input type="submit" name="'.$id.'" value="Delete">';
            echo "</td></tr>";
        }
}

function drawVideos($genreFilter){
    $mysqli = connectToDB();
    $categories = getCategories($mysqli);
    echo "<form action='' method='post'>";
    echo "<table border=1><tr>".
        "<th>id<th>name".
        "<th>".
        // this dropdown will POST value back to this page.   Need to get the POST value of genreFilter ($_POST['genreFilter').
        "<table><tr><td>".
        "<select name='genreFilter' onchange='this.form.submit()'>".
         "<option>Select Category</option>".
	 "<option>All Categories</option>";
    // fill dropdown html select options with categories from database
	for ($i=0; $i<count($categories); $i++)
        {
            echo "<option>$categories[$i]</option>";
        }
    echo "</select>";
    if (!empty($genreFilter))
        {
            echo "<tr><td><b>" . $genreFilter . "</b></td></tr>" ;
        }
    echo "</table>";

    echo "<th>".
        "length<th>rented";
    echo '<th><input type="submit" name="DeleteAll" value="Delete All Videos"></tr>';
    
// prepare statement
    $query = "select id,name,category,length,rented from video_inventory";
    if ($sql=$mysqli->prepare($query)){
        
        $sql->execute();
    
// bind results
        $sql->bind_result($id,$name,$category,$length,$rented);
        // check if category is in filter
        
        while($sql->fetch())
            {
                // filter results from $_POST['genreFilter'] dropdown
                if (!empty($genreFilter))
                    {
                        if (strcmp($genreFilter,'All Categories') == 0 ||
                        strcmp($genreFilter,'Select Category') == 0 )
                            {
                                drawVideo($id,$name,$category,$length,$rented);
                            }
                        else if (strcmp($genreFilter,$category) == 0)
                            {
                                drawVideo($id,$name,$category,$length,$rented);
                            }
                    }
                else
                    drawVideo($id,$name,$category,$length,$rented);
                        
            }
    }
    echo "</table></form>";
}


?>

</body>
</html>
