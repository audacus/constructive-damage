// Seting up the editor
var editor = ace.edit("editor");
editor.setTheme("ace/theme/solarized_dark");
editor.session.setMode("ace/mode/lua");
editor.$blockScrolling = Infinity;

// var getAllScriptsAjax = new ajax(ajaxHandleAllScripts);
// var saveAjax = new ajax(ajaxHandleSave);

var objectlist; // List of luascrypts with metadata
var currentItem = -1; // Currently selected item

// Started when the page has finished loading
function onLoad()
{
	window.addEventListener( 'resize', function(){ editor.resize();}, false );
	// Requests data from the server
	requestUpdate();
}

// Updates the list in the left menu
function updateList()
{
	try
	{
		var oldContainer = document.getElementById("contentContainer"); // Takes the old container
		var container = document.createElement("div"); // Makes a new container
		container.setAttribute('id', 'contentContainer'); 
		// Adds the items
		for(var i in objectlist)
		{
			var content = "" + Math.random();
			addListItem(container, i, objectlist[i].name, objectlist[i].description, objectlist[i].date, objectlist[i].author);
		}
		document.getElementById('leftMenu').replaceChild(container, oldContainer); // Replaces the old one with the new one
	}
	catch(ex)
	{
		console.error(ex);
	}
}

// Creates a new item for the list
function addListItem(container, id, name, description, date, author)
{
	// Containers
	var newdiv = document.createElement('div');
	var tooltip = document.createElement('div');
	var infoTable = document.createElement('table');
	
	// Description
	var trDesc = document.createElement('tr');
	var tdDesc = document.createElement('td');
	var thDesc = document.createElement('th');
	infoTable.appendChild(trDesc);
	trDesc.appendChild(thDesc);
	trDesc.appendChild(tdDesc);
	thDesc.textContent = "Description:"
	tdDesc.textContent = description;

	// Creation
	var trDate = document.createElement('tr');
	var tdDate = document.createElement('td');
	var thDate = document.createElement('th');
	infoTable.appendChild(trDate);
	trDate.appendChild(thDate);
	trDate.appendChild(tdDate);
	thDate.textContent = "Creation:"
	tdDate.textContent = date;

	// Author
	var trAuthor = document.createElement('tr');
	var tdAuthor = document.createElement('td');
	var thAuthor = document.createElement('th');
	infoTable.appendChild(trAuthor);
	trAuthor.appendChild(thAuthor);
	trAuthor.appendChild(tdAuthor);
	thAuthor.textContent = "Author:"
	tdAuthor.textContent = author;

	// Assembels the whole stuff
	tooltip.className = "tooltip";
	tooltip.appendChild(infoTable);
	newdiv.textContent = name;
	newdiv.className = "objectElement";
	newdiv.id = id;
	newdiv.onclick = function(){openCode(this.id);};
	newdiv.appendChild(tooltip);
	container.appendChild(newdiv);
}

// Adds a new item to the list
function addNewObject()
{
	var name = prompt("name:", "");
	if(name && name != "" && !objectlist.some(x => x.name == name))
	{
		// Chooses a random ID that will get replaced with an actual what when saved
		var id = Math.random(); 
		while(objectlist[id] != null)
		{
			id = Math.random();
		}
		objectlist[id] = new Object();
		objectlist[id].name = name;
		objectlist[id].description = "";
		objectlist[id].source = "";
		updateList();
	}
	else
	{
		if (name == "") 
		{
			return;
		} 
		else 
		{
			alert("Name allready exists");
		}
	}
}

// Requests an update from the server
function requestUpdate()
{
	console.log("requesing update");
	// getAllScriptsAjax.send("http://constructivedamage.xyz/cd/luascripts");
	new Ajax.request({
		url: 'luascripts?editor',
		success: ajaxHandleAllScripts
	}).send();
}

function validateScript()
{
	// TODO
}

// Sends the script to the server
function saveScript()
{
	if(currentItem<0)
	{
		return;
	}
	var item = objectlist[currentItem];
	item.source = window.btoa(editor.getValue());
	item.name = document.getElementById("nameProperty").value;
	item.description = document.getElementById("decriptionProperty").value;
	// saveAjax.send("http://constructivedamage.xyz/cd/luascripts/?"+JSON.stringify(item));
	new Ajax.request({
		url: 'luascripts',
		method: 'post',
		data: item,
		success: ajaxHandleSave
	}).send();
}

// Hides the properties
function hideProperty()
{
	document.getElementById("properties").style.top = "-1000%";
}

// Sets the property to the current value
function resetProperty()
{
	document.getElementById("nameProperty").value = objectlist[currentItem].name;
	document.getElementById("decriptionProperty").value = objectlist[currentItem].description;
}

// Shows the property window
function showProperty()
{
	if(currentItem<0)
	{
		return;
	}
	document.getElementById("properties").style.top = "40%";
}

// Opens the code with the given id
function openCode(id)
{
	if(currentItem == id)
	{
		return;
	}
	hideProperty();
	updateErrorBox("");
	console.log("Opening code: " + id)
	editor.setValue(window.atob(objectlist[id].source), -1);
	document.getElementById("nameProperty").value = objectlist[id].name;
	document.getElementById("decriptionProperty").value = objectlist[id].description;
	currentItem = id;
	updateSelection();
	editor.focus();
}

// Highlights the current item
function updateSelection()
{
	if(objectlist[currentItem] == null)
	{
		currentItem = -1;
	}
	[].slice.call(document.getElementById("contentContainer").children).forEach(function(x){if(x.id == currentItem){ x.className = "selectedObjectElement"; } else { x.className = "objectElement"; }});
}

// Decodes the data from the backend
function ajaxHandleAllScripts(response)
{
	try
	{
		var r = response.responseText;
		objectlist = [];
		var decode = JSON.parse(r);
		for(var i in decode)
		{
			objectlist[decode[i].id] = decode[i];
		}
		updateList();
		updateSelection();
		console.log("Received update from Server");
	}
	catch(ex)
	{
		console.error("Error loading scripts from server : " + ex + " ------------------ got JSON: " + r);
	}
}

// Gets the return value of the evaluate
function ajaxHandleSave(response)
{
	var e = response.responseText;
	console.log("Save reply: " + e);
	document.getElementById("outputPre").innerHTML = e;
}

// Updates the output box
function updateErrorBox(text)
{
	try
	{
		document.getElementById("outputPre").innerHTML = text;
	}
	catch(ex)
	{
		console.error("Error updating ErrorBox : " + ex);
	}
}

// -- mini librarys

// Handels Ajax stuff
function ajax(f)
{
	this.send = function(file, getorpost)
	{
		var xmlhttp;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.onreadystatechange = function()
		{
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
			{
				f(xmlhttp.responseText);
			}
		}
		var ispost = typeof getorpost === 'string' && getorpost.toUpperCase() === "POST";
		if (ispost)
		{
			var index = file.indexOf("?");
			if (index > -1) {
				// has params
				var arr = file.split("?");
				xmlhttp.open("POST", arr[0], true);
				xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(arr[1]);
			}
			else
			{
				ispost = false;
			}
		}
		if (!ispost)
		{
			xmlhttp.open("GET", file, true);
			xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xmlhttp.send();
		}
	}
}
