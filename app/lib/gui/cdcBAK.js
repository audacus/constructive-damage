var stats;

var camera, scene, renderer, pointLight;
var mesh;
var controlls;

var graphicsAjax = new ajax(ajaxHandleGraphicsRefresh);
var controlsAjax = new ajax(ajaxHandleGraphicsRefresh);

var planes = new Array();
var player;
var refreshTimer;

init();
jsonUpdate();
animate();

function init() 
{	
	// FPS Counter
	stats = new Stats();
	stats.domElement.style.position = 'absolute';
	stats.domElement.style.top = '0px';
	document.body.appendChild( stats.domElement );
	
	// Renderer
	renderer = new THREE.WebGLRenderer({antialias: true, alpha: true});
	renderer.setSize( window.innerWidth, window.innerHeight );
	renderer.setClearColor( 0xffffff, 0);
	document.body.appendChild( renderer.domElement );

	// Camera
	camera = new THREE.PerspectiveCamera( 80, window.innerWidth / window.innerHeight, 1, 100000 );	
	camera.position.x = 0;
	camera.position.y = 0;
	camera.position.z = 200;

	// Szene
	scene = new THREE.Scene();		
	scene.add(camera);
	
	// Light
	var light = new THREE.AmbientLight( 0xffffff );
	scene.add(light);
	
	// Player	
	var geometry = new THREE.PlaneGeometry( 10, 10 );
	var material = new THREE.MeshBasicMaterial( {color: 0xFF0000, side: THREE.DoubleSide} );
	var plane = new THREE.Mesh( geometry, material );
	scene.add( plane );
	player = new item(plane, 10, 10);
	
	window.addEventListener( 'resize', onWindowResize, false );
	document.addEventListener("wheel", onScroll, false)
	document.body.addEventListener('keydown', keydown);
	document.body.addEventListener('keyup', keyup);
}

function onWindowResize() 
{
	camera.aspect = window.innerWidth / window.innerHeight;
	camera.updateProjectionMatrix();

	renderer.setSize( window.innerWidth, window.innerHeight );
}

function animate() 
{
	renderer.render( scene, camera );
	stats.update();
}

function onScroll(e)
{	
	if(e!=null)
	{
		var zDistance = camera.position.z - player.plane.position.z;
		zDistance = zDistance * (1 + (e.deltaY * 0.05));
		if(zDistance < 10)
		{
			zDistance = 10;
		}
		else if(zDistance > 10000)
		{
			zDistance = 10000;
		}
		camera.position.z = (camera.position.z - (camera.position.z - player.plane.position.z)) + zDistance;
		animate();
	}
}

function addOrUpdateItem(id, x, y, z, width, height, color)
{
	var plane = null;
	var geometry = new THREE.PlaneGeometry( width, height );
	var material = new THREE.MeshBasicMaterial( {color: color, side: THREE.DoubleSide} );
	if(planes[id] == null)
	{		
		var plane = new THREE.Mesh( geometry, material );
		scene.add( plane );
		planes[id] = new item(plane, width, height);
	}
	else
	{
		plane = planes[id].plane;
		plane.geometry = geometry;
		plane.material = material;
	}
	plane.position.x = x;
	plane.position.y = y;
	plane.position.z = z;			
	animate();
}

function moveItem(id, x, y, z)
{
	if(planes[id] != null)
	{		
		var item = planes[id];
		
		item.plane.position.x = x;
		item.plane.position.y = y;
		item.plane.position.z = z;		
		animate();
		return true;
	}
	return false;
}

function movePlayer(x, y, z)
{
	var zOffset = z - player.plane.position.z
	camera.position.x = x;
	camera.position.y = y;
	camera.position.z += zOffset;
	player.plane.position.x = x;
	player.plane.position.y = y;
	player.plane.position.z = z;
	animate();
}

function item(plane, width, height)
{
	this.plane = plane;
	this.width = width;
	this.height = height;
}

function keydown(e)
{
	controlsAjax.send("http://constructivedamage.xyz/lua/controller.php?a=keypress&key=" + e.keyCode);
	e.preventDefault();
}

function keyup(e)
{
	
}

function ajaxHandleGraphicsRefresh(r)
{
	jsonDecode(r);
}

function jsonUpdate()
{
	graphicsAjax.send("http://constructivedamage.xyz/lua/controller.php?a=refreshi");
}

function jsonDecode(input)
{
	var decode = JSON.parse(input);
	for(i in decode)
	{
		var id = decode[i]["id"];
		if(id != null)
		{
			var x = decode[i]["x"];
			var y = decode[i]["y"];
			var z = decode[i]["z"];
			var width = decode[i]["width"];
			var height = decode[i]["length"];
			var depth = decode[i]["depth"];
			var color = decode[i]["color"];
			if(x != null && y != null && z != null && width != null && height != null && depth != null && color != null)
			{
				addOrUpdateItem(id, x, y, z + depth, width, height, color);
			}
			else
			{
				console.log("gäbu ur data sucks");
			}
		}		
		else
		{
			console.log("gäbu ur data sucks hard");
		}
	}
}

function startRefreshTimer()
{
	refreshTimer = setInterval(jsonUpdate, 1000);
}

function stopRefreshTimer()
{
	clearInterval(refreshTimer);
}

// -- mini librarys

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
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(arr[1]);
            } else {
                ispost = false;
            }
        }
        if (!ispost) {
            xmlhttp.open("GET", file, true);
            xmlhttp.send();
        }
    }
}