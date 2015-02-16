window.onload = function()
{
    /*
    var video_inventory = {
	categories:getCategories(),
	videos:getVideos()
	};
    */
    //while (1) console.log(video_inventory.videos);
    getCategories();
    getVideos();
}

function getCategories(){
    sendRequest('getCategories=true',fillCategories);
}

function fillCategories(parsedResult){
    var genreFilter = document.getElementById('genreFilter');
    for (var i=0; i<parsedResult.length; i++)
	{
	    genreFilter.innerHTML += '<option>'+parsedResult[i]+'</option>';
	}
}

function filterVideos(){
    getVideos();
    
    //reloadPage();
    /*
    var filter = document.getElementById('genreFilter').value; // console.log(filter);
    if (filter != 'All Categories')
	{
	    var resultsBodyOld = document.getElementById('resultsBody');
	    var resultsBodyNew = resultsBodyOld.cloneNode(true);
	    
	    resultsBodyOld.innerHTML=''; // clear old results
	    for (var i=1; i < resultsBodyNew.childNodes.length; i++)
	    {
		// get category, the third <td> in the row
		var row = resultsBodyNew.childNodes[i];
		var category = row.childNodes[2]; //console.log(category);
		if (category.innerHTML === filter)
		    resultsBodyOld.appendChild(row);
	    }
	    console.log('done');
	}
	*/
}


function reloadPage(value){
    window.location.reload();
}

function addVideo(){
    var name = document.getElementById('name');
    var category = document.getElementById('category');
    var length = document.getElementById('length');
    
    console.log(name.value,category.value,length.value);

    if (name.value.length < 1 || name.value === '')
    {
	alert("Name field cannot be empty.");
    }
    else if (isNaN(length))
    {
	alert("Length must be a number.");
    }
    else 
    {
	sendRequest('addVideo=true&name='+name.value+'&category='+category.value+'&length='+length.value,getVideos);
		    //displayVideos);
    }
}


function toggleAvailability(idNum){
    sendRequest('toggleVideo='+idNum,reloadPage);
}

function deleteVideo(idNum){
    sendRequest('deleteVideo='+idNum,reloadPage);
}

function deleteAllVideos(){
    sendRequest('deleteAllVideos=true',reloadPage);
}


function getVideos(){
    sendRequest('getAllVideos=true',displayVideos);
    //displayVideos(parsedResult);
}

function sendRequest(requestText,callback){
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
		return callback(parsedResult);
		//displayVideos(parsedResult);
	    }
	    //else
	    //console.log("Error", request.statusText);
	}
		
    }
    request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //request.setRequestHeader("Content-length",requestText.length);
    //request.setRequestHeader("Connection","close");
    request.send(requestText);

}


function displayVideos(parsedResult){
    var thisString = '';
    var table = document.getElementById('resultsBody');
    var filter = document.getElementById('genreFilter').value; // console.log(filter);
    table.innerHTML = '';
    for (var i=0; i<parsedResult.length; i++)
	{
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

	    //table.innerHTML += ;	    
	    //if (filter != 'All Categories')
	    if (category === filter || filter === 'All Categories' || filter === 'Select Category')
	    {
	    table.innerHTML += '<tr><td>'+id+'</td>'+
		'<td>'+name+'</td>'+
		'<td>'+category+'</td>'+
		'<td>'+length+'</td>'+
		'<td>'+available+'</td>'+
		'<td>'+
		'<input type="submit" name='+id+' value="'+buttonText+
						   '" onClick="toggleAvailability('+id+')">'+
		'<input type="submit" name='+id+' value="Delete" onClick="deleteVideo('+id+')"></td></tr>';
	    }

	}
    //filterVideos();
    return parsedResult;
}
