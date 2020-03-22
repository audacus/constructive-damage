<!DOCTYPE html>
<html lang="en">
	<head>
		<title>ConstructiveDamageClient</title>
		<meta charset="utf-8">
		<style>
			body 
			{
				margin: 0px;
				background-color: #eeeeee;
				overflow: hidden;
			}
		</style>
		<script src="three.js"></script>
		<script src="stats.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
	</head>
	<body onscroll="onScroll()">	
		<script src="cdc.js"></script>
		<div style="position:absolute;top:0px;right:0px">
			<button onclick="jsonUpdate()">Manual Refresh</button>
			<button onclick="startRefreshTimer()">Start refresh timer</button>
			<button onclick="stopRefreshTimer()">Stop refresh timer</button>
		</div>
	</body>
</html>
