<?php
	$mysql_host = "localhost";
	$mysql_database = "keweizho_dankmeet";
	$mysql_user = "keweizho_dm";
	$mysql_password = "6b3p34";
	
	$db_handle = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
	if (!$db_handle) {
		die("Connection Error: ".mysqli_connect_errno());
	}
	session_start();
	$s = session_id();
	$query = "SELECT * FROM users WHERE session='{$s}'";
	$result = mysqli_query($db_handle, $query);
	$user_id; $user_last_active; $user_nick; $user_info; $is_logged;
	if ($row = $result->fetch_assoc()) {
		// User has logged in before, use existing records
		$is_logged = true;
		$user_id = $row["id"];
		$user_last_active = $row["last_active"];
		$user_nick = $row["nick"];
		$user_info = $row["info"];
		$_SESSION['username'] = $user_nick;
	} else {
		// Require sign on
		$is_logged = false;
	}
	function detect_mobile()
	{
    	if(preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT']))
     	   return true;
   	 	else
       		 return false;
    }
    $isMobile = detect_mobile();
?><!DOCTYPE html>
<html>
	<head>
		<title>DankMeet</title>
		<script src="js/jquery-1.11.1.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
	<?php if (!$isMobile) echo '<link rel="stylesheet" type="text/css" href="chatfiles/chatstyle.css" />'; ?>
		<style type="text/css">
		#popupbox {
 			padding: 15px 25px 25px 25px;
  			width: 50%; 
  			height: 320px;
  			top: 20%;
  			left: 25%;
  			position: absolute; 
  			background: #F6F6F9; 
  			z-index: 9; 
  			visibility: <?php if ($is_logged) echo "hidden"; else echo "visible"; ?>; 
  		}
  		#popupcover {
 			margin: 0; 
  			padding-top: 20%;
  			width: 100%; 
  			height: 100%; 
  			position: absolute; 
  			top: 0px;
  			left: 0px;
  			background: #666699;
  			font-size: 200%;
  			border: 0px;
  			z-index: 8; 
  			font-family: arial; 
  			text-align: center;
  			visibility: visible;
  			opacity: 0.3;
  		}
		
		
		</style>
		<link href='http://fonts.googleapis.com/css?family=Oswald:300' rel='stylesheet' type='text/css'>
      	<?php if (!$isMobile) echo '<link href="stylesheet.css" rel="stylesheet" type="text/css">';
      		else echo '<link href="stylesheet_m.css" rel="stylesheet" type="text/css">'; ?>

      	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
      		
      	<script>
    var lastMarker = null;
	var lastWindow = null;
	var user = [];
	var posX = 59.32522;
	var posY = 18.07002;
	var positionList = [];
	var nameList = [];
	var markerList = [];
	var myMarker = null;
	var idToMarker = {};
	var map;
	var complete = 0;
	var cLong = 0;
	var cLat = 0;
	var isOnProfile = false;
	function getLocation() {
   		if (navigator.geolocation) {
        	navigator.geolocation.getCurrentPosition(showPosition);
    	} else {
       		// Require geolocation to be on
       		$("#popupcover").css('visibility', 'visible');
    	}
	}
	function showPosition(position) {
    	cLat = position.coords.latitude;
    	cLong = position.coords.longitude;
    	var req = "get.php?long="+cLong+"&lat="+cLat;
		$.ajax({
     		async: true,
    		type: 'GET',
     		url: req,
    		success: function(data) {
     		}});
     	createMyMarker(new google.maps.LatLng(cLat, cLong), map, "Me");
     	if (!isOnProfile)
     		$("#popupcover").css('visibility', 'hidden');
	}
	
	function initialize() {
		var mapOptions;
		var style = [
    {
        "featureType": "landscape.natural",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "color": "#e0efef"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "hue": "#1900ff"
            },
            {
                "color": "#c0e8e8"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [
            {
                "lightness": 100
            },
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "transit.line",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "lightness": 700
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "all",
        "stylers": [
            {
                "color": "#7dcdcd"
            }
        ]
    }
];

		mapOptions = {
			center: new google.maps.LatLng(47.8486728,-89.3367649),
			zoom: 5,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			styles: style
		};
		var mapCanvas = document.getElementById('map-canvas');
		map = new google.maps.Map(mapCanvas, mapOptions);
		<?php if ($is_logged) echo "getLocation(); setInterval(function(){ getLocation(); }, 5000); refreshMarkers();"; ?>
	}
	
	function refreshMarkers ()
	{
		for (var i=0; i<markerList.length; i++)
			markerList[i].setMap(null);
		markerList = [];
		positionList = [];
		nameList = [];
		
		var pos;
		$.ajax({
     		async: false,
    		type: 'GET',
     		url: 'get.php',
    		success: function(data) {
          		pos = data;
     		}});
		var pl = pos.split("!*JIu03A9");

		for (i=0;i<pl.length && (pl.length >= 4);i+=4){
			createMarker(new google.maps.LatLng(pl[i+1], pl[i]), map, pl[i+2], pl[i+3]);
		}
	}
	function formatBox (name, info)
	{
		return "<h3>"+name+"</h3>"+"<p>"+info+"</p>";
	}
	function createMyMarker(latlng, address, name){
		var contentString = formatBox (<?php echo '"'.$user_nick.'"';?>, <?php echo '"'.$user_info.'"';?>);
		if (!myMarker) {
			myMarker = new google.maps.Marker({
				map: address,
				position: latlng,
				title: name,
				animation: google.maps.Animation.DROP
			});
			var infowindow = new google.maps.InfoWindow({
			content: contentString
			});
			google.maps.event.addListener(myMarker, 'click', function() {
				if (myMarker.getAnimation() != null) {
					lastMarker = null;
					lastWindow = null;
					myMarker.setAnimation(null);
					infowindow.close(address, myMarker);
				} else {
					if (lastMarker!=null){
						lastMarker.setAnimation(null);
						lastWindow.close(address,lastMarker);
					}
					lastMarker = myMarker;
					lastWindow = infowindow;
					address.panTo(myMarker.getPosition())
					infowindow.open(address,myMarker);
					myMarker.setAnimation(google.maps.Animation.BOUNCE);
				}
			});
			google.maps.event.addListener(infowindow, 'closeclick', function(){
				myMarker.setAnimation(null);
				infowindow.close(address,myMarker);
			});
			address.panTo(myMarker.getPosition());
			address.setZoom(15);
		} else {
			myMarker.setPosition(latlng);
		}
		
	}
	
	
	function createMarker(latlng, address, name, info){
		var contentString = formatBox (name, info);
		var marker = new google.maps.Marker({
			map: address,
			position: latlng,
			title: name,
			animation: google.maps.Animation.DROP
		});
		markerList.push(marker);
		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		google.maps.event.addListener(marker, 'click', function() {
			if (marker.getAnimation() != null) {
				lastMarker = null;
				lastWindow = null;
				marker.setAnimation(null);
				infowindow.close(address, marker);
			} else {
				if (lastMarker!=null){
					lastMarker.setAnimation(null);
					lastWindow.close(address,lastMarker);
				}
				lastMarker = marker;
				lastWindow = infowindow;
				address.panTo(marker.getPosition())
				infowindow.open(address,marker);
				marker.setAnimation(google.maps.Animation.BOUNCE);
			}
		});
		google.maps.event.addListener(infowindow, 'closeclick', function(){
			marker.setAnimation(null);
		});
	}

	google.maps.event.addDomListener(window, 'load', initialize);
      </script>

	</head>
	<body>
	
		
		<div class="header-cont">
		    <div class="header">
		    	<img src="logo.png" style="max-height:100%;">

					<?php
						if ($is_logged)
							echo '<a href="#" class="btn btn-logout">Log Out</a>';
						else
							echo '<a href="#" class="btn btn-login">Log In</a>';
						?>
					<a href="#" class="btn btn-login">DankFile</a>
					<a href="#" class="btn btn-refresh">Refresh</a>
		    </div>
		</div>
		<div id="map-canvas"></div>
	<!-- Chat box -->
	<?php if ($isMobile) echo '<!--'; ?>
	<div class="chatbox">
    	<iframe src="chat.php" width="100%" height="100%"
       scrolling="no" frameborder="0" name="frame_chat"> </iframe>
    </div>
    <?php if ($isMobile) echo '-->'; ?>
    <div id="popupbox"> 
			<form>
  <div class="form-group">
  	<h2><?php if($is_logged) echo "Update "; else echo "Create "; ?>your DankFile</h2>
  	<br />
    <label for="exampleInputUsername">Nickname</label>
    <input type="username" class="form-control" id="inputUsername" placeholder="Give a name for yourself">
  </div>
  <div class="form-group">
    <label for="exampleInputActivity">Interests</label>
    <input type="activity" class="form-control" id="inputInfo" placeholder="What do you want to do?">
  </div>
  
  <button type="submit" id="loginButton" class="btn btn-default">Submit</button>
</form>
	<?php if($is_logged) echo '<a href="#" style="top:10px;left:20px;" id="closeLogin">Close</a>'; ?>
		</div>
		<div id="popupcover"><?php if($is_logged) echo "<h1>Determining your location...</h1>"; ?></div>
		
		<script>
		
			$(".btn-refresh").click(function() {
				refreshMarkers();
			});
			$(".btn-login").click(function() {
				$("#popupbox").css('visibility', 'visible');
				$("#popupcover").css('visibility', 'visible');
				isOnProfile = true;
			});
			$(".btn-logout").click(function() {
				$.ajax({
     				async: false,
    				type: 'GET',
     				url: 'get.php?logout',
    				success: function(data) {
     				}
     			});
     			window.location.replace("index.php");
			});
			$("#closeLogin").click(function() {
				$("#popupbox").css('visibility', 'hidden');
				$("#popupcover").css('visibility', 'hidden');
			});
			
			$("#loginButton").click(function() {
				
				$('#loginButton').attr("disabled", true);
				$.ajax({
     				async: false,
    				type: 'POST',
     				url: 'get.php',
     				data: {sid: <?php echo '"'.$s.'"';?>,nick:$("#inputUsername").val(),info:$("#inputInfo").val()},
    				success: function(data) {
     				}
     			});
     			window.location.replace("index.php");
			});
			
			$(document).ready(function() {
				<?php if(!$is_logged) echo "/*"; ?>
					$("#chatuser").val(<?php echo '"'.$user_nick.'"'; ?>);
					$("#form_chat").submit();
				<?php if(!$is_logged) echo "*/"; ?>
			});
		</script>
	</body>
</html>