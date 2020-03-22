//var stats;

// Drawing stuff
var camera, scene, renderer, pointLight;
var mesh;
var controlls;

// Ajax stuff
var graphicsAjax = new ajax(ajaxHandleGraphicsRefresh);
var controlsAjax = new ajax(function(){});

var planes = new Array();
var playerId = 1;
var cameraDistance = 100;
var popupContainer
var refreshTimer;

// For clicks
var raycaster = new THREE.Raycaster();
var mouse = new THREE.Vector2();

// Call functions
init();
jsonUpdate();
animate();

// Initiates everything
function init()
{
	// FPS Counter
	//stats = new Stats();
	//stats.domElement.style.position = 'absolute';
	//stats.domElement.style.top = '0px';
	//document.body.appendChild( stats.domElement );

	// Renderer
	renderer = new THREE.WebGLRenderer({antialias: true, alpha: true});
	renderer.setSize( window.innerWidth, window.innerHeight );
	renderer.setClearColor( 0xffffff, 0);
	renderer.sortObject = true;
	document.body.appendChild( renderer.domElement );

	// Camera
	camera = new THREE.PerspectiveCamera( 80, window.innerWidth / window.innerHeight, 1, 100000 );
	camera.position.x = 0;
	camera.position.y = 0;
	camera.position.z = 200;

	// Scene
	scene = new THREE.Scene();
	scene.add(camera);

	// Light
	var light = new THREE.AmbientLight( 0xffffff );
	scene.add(light);
	
	// Events
	window.addEventListener( 'resize', onWindowResize, false );
	document.addEventListener("wheel", onScroll, false)
	document.body.addEventListener('keydown', keydown);
	document.body.addEventListener('keyup', keyup);
	document.body.addEventListener('mousedown', onMouseDown);
	
	// Starts the refreshing
	refreshTimer = setInterval(jsonUpdate, 100);
}

// Reacts to size changes in the window
function onWindowResize()
{
	// Adjusts the aspectratio
	camera.aspect = window.innerWidth / window.innerHeight;
	camera.updateProjectionMatrix();
	
	// Adjusts the renderer
	renderer.setSize( window.innerWidth, window.innerHeight );
}

// Gets called to render a new frame
function animate()
{
	renderer.render( scene, camera );
	//stats.update();
}

// Reacts to scroll events
function onScroll(e)
{
	// Checks if there even is an event
	if(e!=null)
	{
		cameraDistance = cameraDistance * (1 + (e.deltaY * 0.005));	// Updates the camera distance (we use logarithmic scrolling)
		// Makes shure we newer get closer than 10
		if(cameraDistance < 10)
		{
			cameraDistance = 10;
		}
		// Makes shure we newer get farther away than 10000
		else if(cameraDistance > 10000)
		{
			cameraDistance = 10000;
		}
		updateCamera(); // Actually updates the camera
		animate();
	}
}

// Adds or updates the given item
function addOrUpdateItem(id, x, y, z, width, height, color)
{
	var plane = null;
	var geometry = new THREE.PlaneGeometry( width, height ); // Creates a geometry with the new values
	var material = new THREE.MeshBasicMaterial( {color: color, side: THREE.DoubleSide} ); // Creates a material with the current values
	// Checks if the item allready exists
	if(planes[id] == null)
	{
		console.log("New object: id=" + id); 
		var plane = new THREE.Mesh( geometry, material ); // Create a new item
		plane.callback = function() { sendSwitchPlayerRequest(id);}; // Adds the callback for the middleclick
		scene.add( plane );
		planes[id] = new item(plane, width, height);
		addNumber(planes[id], id); // Adds the number
	}
	else
	{
		// Just updates the existing one
		plane = planes[id].plane;
		plane.geometry.dispose();
		plane.material.dispose();
		plane.geometry = geometry;
		plane.material = material;
	}
	moveItem(planes[id], x, y, z); // Updates the position
	
	// If the player moved move the camera
	if(id == playerId)
	{
		updateCamera();
	}
}

// Requests to switch the avatar to the given id
function sendSwitchPlayerRequest(id)
{
	// There is no use in switching to itself
	if(playerId != id)
	{
		console.log("Requesting to switch player to " + id);
		controlsAjax.send("/cd/game?a=setavatar&id=" + id);
	}
}

// Switches the player to the given id
function switchPlayer(id)
{
	// There is no use in switching to itself again
	if(playerId != id)
	{
		console.log("Player switched to: " + id);
		playerId = id;
		updateCamera();
		animate();
	}
}

// Updates the position of the item
function moveItem(item, x, y, z)
{
	item.plane.position.x = x;
	item.plane.position.y = y;
	item.plane.position.z = z;
	setNumberplanePosition(item.numberplane, item.plane);
}

// Updates the position of the camera
function updateCamera()
{
	// If the current avatar does not exist do nothing
	if(planes[playerId] != null)
	{
		camera.position.x = planes[playerId].plane.position.x;
		camera.position.y = planes[playerId].plane.position.y;
		camera.position.z = planes[playerId].plane.position.z + cameraDistance;
	}
}

// Adds the number to the given item
function addNumber(item, id)
{
	// Draws the number onto a new canvas
	var canvas = document.createElement('canvas');
	canvas.width = 256;
	canvas.height = 256;
	var context = canvas.getContext('2d');
	context.font = "Bold 200px Helvetica";
	context.fillStyle = "rgba(128,0.5,0.5,0.5)";
	context.textAlign = "center";
	context.textBaseline="middle";
	context.fillText(id, 128, 132);
	// Makes a texture out of the canvas
	var texture = new THREE.Texture(canvas)
	texture.needsUpdate = true;
	var geometry = new THREE.PlaneGeometry( 5, 5 );
	var material = new THREE.MeshBasicMaterial( {map: texture, transparent: true, side: THREE.DoubleSide} );
	// Makes a new plane out of that stuff
	var numberplane = new THREE.Mesh( geometry, material );
	numberplane.callback = item.plane.callback;
	setNumberplanePosition(numberplane, item.plane);
	scene.add(numberplane);
	item.numberplane = numberplane;
}

// Sets the position of the numberplane
function setNumberplanePosition(numberplane, parent)
{
	numberplane.position.x = parent.position.x;
	numberplane.position.y = parent.position.y;
	numberplane.position.z = parent.position.z + 0.001;
}

// Containerclass for items 
function item(plane, width, height)
{
	this.plane = plane;
	this.width = width;
	this.height = height;
	this.numberplane;
}

// Sends keydowns to the server
function keydown(e)
{
	controlsAjax.send("/cd/game?a=keypress&key=" + e.keyCode);
	if (!e.ctrlKey)
	{
		e.preventDefault();
	}
	jsonUpdate()
}

// Maybe later
function keyup(e)
{

}

// Reacts to refreshes form the server
function ajaxHandleGraphicsRefresh(r)
{
	jsonDecode(r);
	r = undefined;
	//jsonUpdate();
}

// Asks for an update
function jsonUpdate()
{
	graphicsAjax.send("/cd/game?a=refresh");
}

// Decodes the data from the server
function jsonDecode(input)
{
	try
	{
		var decode = JSON.parse(input);
		var items = decode["objects"];
		var activeIDs = new Array(); // Array containing all existing ids
		// Decodes each item
		for(i in items)
		{
			var id = items[i]["id"];
			if(id != null)
			{
				activeIDs.push(id);
				var x = items[i]["x"];
				var y = items[i]["y"];
				var z = items[i]["z"];
				var width = items[i]["width"];
				var height = items[i]["length"];
				var depth = items[i]["depth"];
				var color = items[i]["color"];
				if(x != null && y != null && z != null && width != null && height != null && depth != null && color != null)
				{
					addOrUpdateItem(id, x, y, z + depth, width, height, color);
				}
				else
				{
					console.error("wrong content!");
				}
			}
			else
			{
				console.error("no id!");
			}
		}
		// Looks for dead objects and puts them out of their misery
		for(i in planes)
		{
			if(!findInArray(i, activeIDs))
			{
				console.log("you dead man: " + i);
				var victim = planes[i];
				scene.remove(victim.plane);
				scene.remove(victim.numberplane);
				planes.splice(i);
			}
		}
		// Checks if the player has to be switched
		if(decode["avatarid"] != playerId)
		{
			switchPlayer(decode["avatarid"]);
		}
		// Updates the popup
		updatePopup(decode["popup"])
		animate();
		decode = undefined;
	}
	catch(err)
	{
		console.error(err);
		console.error("Received JSON: "+input);
	}
}

// Updates the popup container with the given test
function updatePopup(message)
{
	// Creates the container if it does not exist
	if(popupContainer == null)
	{
		createPopupContainer();
	}
	// If the message is empty hide the popup
	if(message == 0 || message == "")
	{
		popupContainer.style.visibility = "hidden";
	}
	else
	{
		// if the content has changed update and show the popup
		if(popupContainer.children["popupTextContainer"].innerHTML != message)
		{
			popupContainer.children["popupTextContainer"].innerHTML = message;
			document.body.appendChild(popupContainer);
			popupContainer.style.visibility = "";
		}
	}
}

// Creates the container for the popup
function createPopupContainer()
{
	popupContainer = document.createElement("div");
	popupContainer.style.background = "blue";
	popupContainer.style.opacity = "0.5";
	popupContainer.style.color = "white";
	popupContainer.style.position = "absolute";
	popupContainer.style.padding = "25px";
	popupContainer.style.top = "50%";
	popupContainer.style.left = "50%";
	popupContainer.style.transform = "translate(-50%, -50%)";
	popupContainer.style.visibility = "hidden";

	var closeButton = document.createElement("div");
	closeButton.style.position = "absolute";
	closeButton.style.top = "2px";
	closeButton.style.right = "2px";
	closeButton.innerHTML = "X";
	closeButton.style.background = "red";
	closeButton.style.paddingLeft = "3px";
	closeButton.style.paddingRight = "3px";
	closeButton.style.paddingTop = "1px";
	closeButton.style.paddingBottom = "1px";
	closeButton.opacity = "0.5";
	closeButton.onclick = function(){popupContainer.style.visibility = "hidden";};
	popupContainer.appendChild(closeButton);

	var textContainer = document.createElement("pre");
	textContainer.id = "popupTextContainer";
	textContainer.style.fontFamily = "helvetica";
	textContainer.style.height = "100%";
	textContainer.style.width = "100%";
	popupContainer.appendChild(textContainer);
}

// -- mini librarys

// Checks if an item is in an array
function findInArray(item, array)
{
	return array.some(x => x == item);
}

// Handles the middleclick
function onMouseDown(event)
{
	if(event.which != 2)
	{
		return;
	}
	event.preventDefault();

	mouse.x = ( event.clientX / renderer.domElement.clientWidth ) * 2 - 1;
	mouse.y = - ( event.clientY / renderer.domElement.clientHeight ) * 2 + 1;

	raycaster.setFromCamera( mouse, camera );

	var intersects = raycaster.intersectObjects(scene.children);

	if(intersects.length > 0)
	{
		intersects[0].object.callback();
	}
}

// Ajax class 
function ajax(f) {
    this.send = function(file, getorpost) {
        var xmlhttp;
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                f(xmlhttp.responseText);
            }
        }
        var ispost = typeof getorpost === 'string' && getorpost.toUpperCase() === "POST";
        if (ispost) {
            var index = file.indexOf("?");
            if (index > -1) {
                // has params
                var arr = file.split("?");
                xmlhttp.open("POST", arr[0], true);
                xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(arr[1]);
            } else {
                ispost = false;
            }
        }
        if (!ispost) {
            xmlhttp.open("GET", file, true);
            xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xmlhttp.send();
        }
    }
}
