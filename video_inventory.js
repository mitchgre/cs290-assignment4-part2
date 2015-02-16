window.onload = function()
{
    // connect to php
    getVideos();
}


function getVideos(){
    var parsedResult; // container
    var request = new XMLHttpRequest();
    request.open('POST','process_video_inventory.php',true);
    request.onreadystatechange=function(e)
    {
	if (request.readyState === 4)
	{
	    if (request.status === 200)
	    {
		parsedResult = JSON.parse(request.responseText);
		//document.body.innerHTML += parsedResult;
		displayVideos(parsedResult);
	    }
	    else
		console.log("Error", request.statusText);
	}
		
    }
    request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    request.send('getAllVideos=true');
    
}


function displayVideos(parsedResult){
    var thisString = '';
    var table = document.getElementById('resultsBody');
    for (var i=0; i<parsedResult.length; i++)
	{
	    //table.innerHTML += '<tr>'+parsedResult[i]+'</tr>';
	    table.innerHTML += '<tr>';	    
	    //thisString +='<tr>';
	    var toParse = parsedResult[i];
	    
	    var id = parsedResult[i][0];
	    var name = parsedResult[i][1];
	    var category = parsedResult[i][2];
	    var length = parsedResult[i][3];
	    var rented = parsedResult[i][4];
	    var available,buttonText;

	    if (rented === 0) 
		{
		    available = 'available';
		    buttonText = 'check out';
		}
	    else 
		{
		    available = 'checked out';
		    buttonText = 'check in ';
		}

	    table.innerHTML += '<td>'+id+'</td>'+
		'<td>'+name+'</td>'+
		'<td>'+category+'</td>'+
		'<td>'+length+'</td>'+
		'<td>'+available+'</td>'+
		'<td>'+
		'<input type="submit" name='+id+' value="'+buttonText+
						   '" onClick="toggleAvailability('+id+')">'+
		'<input type="submit" name='+id+' value="Delete" onClick="deleteVideo('+id+')"></td>'+
		'</tr>';

	}

}
