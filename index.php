<?php
require_once("service-discovery.php");

// Get our Catalog API endpoint from Service Discovery
$catalogRoute = getAPIRoute("Catalog-API");

// Get the products from our Catalog API
$result = request("GET", $catalogRoute . "/items");
?>

<script>
// Set "items" to the response from our Catalog API request.
var items = <?php echo $result;?>

// Take the item JSON received from the catalog API and format it nicely
function loadItems(){
	if(items.rows == undefined){
		document.getElementById("loading").innerHTML = "";
		return alert("Items is undefined. Please check that your catalog application is running. " + items);
	}

	for(var i = 0; i < items.rows.length; ++i){
		addItem(items.rows[i].doc);
	}

	document.getElementById("loading").innerHTML = "";
}

// This function formats each item (product) using the JSON received from the catalog API app
function addItem(item){
	var div = document.createElement('div');
	div.className = 'column';
	div.innerHTML = "<a class='th' href = '"+item.imgsrc+"'><img src = '"+item.imgsrc+"'/></a></div><h5>"+item.name+"</h5><p>$"+item.usaDollarPrice.toLocaleString() + " USD</p><p>"+item.description+"</p><a class='button expanded' onclick='orderItem(\""+item._id+"\")'>Buy</a>";
	if(item.isNew)
		document.getElementById('newItemWell').appendChild(div);
	else
		document.getElementById('itemWell').appendChild(div);
}

function orderItem(itemID){
	// Create a random customer ID and count
	var custID = Math.floor((Math.random() * 999) + 1); 
	var count = Math.floor((Math.random() * 9999) + 1); 
	var order = {"itemid": itemID, "customerid":custID, "count":count};

	$.ajax ({
		type: "POST",
		contentType: "application/json",
		url: "submit-orders.php",
		data: JSON.stringify(order),
		dataType: "json",
		success: function( result ) {
			if(result.httpCode != "201" && result.httpCode != "200"){
				alert("Failure: check that your JavaOrders API App is running and your user-provided service has the correct URL.");
			}
			else{
				alert("Order Submitted! Check your Java Orders API to see your orders: \n" + result.ordersURL);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) { 
			alert("Error");
			console.log("Status: " , textStatus); console.log("Error: " , errorThrown); 
		}  
	});

}
</script>

<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Microservices Store | Demo</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/foundation/6.2.2/foundation.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<link rel="shortcut icon" href="network-chart_32.png"/>
</head>

<body onload='loadItems()'>
	<div id='loading'>Loading...</div>
	<div class="top-bar">
		<div class="top-bar-left">	
			<ul class="menu">
				<li class="menu-text">Microservices Store Demo</li>
			</ul>
		</div>
	</div>

	<div class="row column text-center">
		<h2>Our Newest Products</h2>
		<hr>
	</div>
	<div id='newItemWell' class="row small-up-2 large-up-4">

	</div>
	<hr>
	<div class="row column text-center">
		<h2>Some Other Neat Products</h2>
		<hr>
		<div id='itemWell' class="row small-up-2 large-up-4">
	</div>
	<div class="callout large secondary">
		<div class="row">
				<h5>Microservices Store Demo</h5>
				<p>You can find the blog post associated with this demo <a href="https://developer.ibm.com/bluemix/2015/03/16/sample-application-using-microservices-bluemix/" target="_blank">here</a></p>
		</div>
	</div>
</body>
</html>
