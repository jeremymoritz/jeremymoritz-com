<!DOCTYPE html>
<html lang='en'>
	<head>
		<meta charset='UTF-8'>
		<title>JS Test</title>
	</head>
	<body>
		<p><a href='javascript:orderList("orderMe")'>Order this list!</a></p>
		<ul id='orderMe'>
			<li>Item B</li>
			<li>Item D</li>
			<li>Item A</li>
			<li>Item C</li>
		</ul>
		
		<script>
			//	A NON-jQuery way to order the list:
			function orderList(listToOrder) {
				var theUL = document.getElementById(listToOrder);	//	reference the ul
				var theLIs = theUL.getElementsByTagName('li');	//	get the children of the ul as an array
				
				var liArray = new Array();	//	new array (for list items)
				
				for(var j = 0; j < theLIs.length; j++) {	//	iterate through the current LIs
					liArray.push(theLIs[j].innerHTML);	//	add their content to the new array
				}
				
				liArray.sort();	//	sort the array of list items
				theUL.innerHTML = "";	//	clear out the ul (it's now empty)
				
				for(var k = 0; k < liArray.length; k++) {	//	for as many elements as are in the sorted array...
					var newLI = document.createElement("li");	//	create a new element for the LI
					var text = document.createTextNode(liArray[k]);	//	add the text node of the sorted li item to the new element
					newLI.appendChild(text);	//	append the text to the newly created LI element
					
					theUL.appendChild(newLI);	//	add the LI to the UL
				}
			}
		</script>
	</body>
</html>
