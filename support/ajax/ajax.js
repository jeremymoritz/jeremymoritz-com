$('#search').keyup(function() {
	var searchField = $('#search').val();
	var myExp = new RegExp(searchField, "i");
	$.getJSON('data.json',function(data) {
		var output = '<ul class="searchResults">';
		$.each(data, function(key, val) {
			if ((val.bio.search(myExp) != -1) || (val.name.search(myExp) != -1)) {
				output += '<li>';
				output += '<h2>' + val.name + '</h2>';
				output += '<img src="http://www.moviemink.com/_img/mink_0200.png" alt="" />';
				output += '<p>' + val.bio + '</p>';
				output += '</li>';
			}
		});
		output += "</ul>";
		$('#update').html(output);
	});
});






// $.getJSON('data.json',function(data) {
	// var output = "<ul>";
	// $.each(data, function(key, val) {
		// output += "<li>" + val.name + "</li>"; 
	// });
	// output += "</ul>";
	// $('#googleResults').append(output);
// 
// })
// 



// function googleSearch() {
	// var request;
	// if (window.XMLHttpRequest) {
		// request = new XMLHttpRequest();
	// } else {
		// request = new ActiveXObject('Microsoft.XMLHTTP');
	// }
	// // request.open('GET', 'data.xml');
	// // request.onreadystatechange = function() {
		// // if ((request.readyState===4) && (request.status===200)) {
			// // console.log(request.responseXML.getElementsByTagName('name')[1]);
			// // var items = request.responseXML.getElementsByTagName('name');
			// // console.log(items);
			// // var output = "<ul>";
				// // for (var i = 0; i < items.length; i++) {
					// // output += "<li>" + items[i].firstChild.nodeValue + "</li>";
				// // }
			// // output += "</ul>";
			// // document.getElementById('googleResults').innerHTML = output;
// // 	
// // 	
		// // }
	// // }
	// request.open('GET', 'data.json');
	// request.onreadystatechange = function() {
		// if ((request.readyState===4) && (request.status===200)) {
			// var items = JSON.parse(request.responseText);
			// console.log(items);
			// var output = "<ul>";
				// for (var key in items) {
					// output += "<li>" + items[key].name + "</li>";
				// }
			// output += "</ul>";
			// document.getElementById('googleResults').innerHTML = output;
// 
		// }
	// }
// 
	// request.send();
// 
// }
// 	
// var myButton = document.getElementById('loadButton');
// myButton.onclick = googleSearch;

// function googleSearch() {
	// $.ajax({
	  // url: "test.html",
	  // cache: false
	// }).done(function( html ) {
	  // $("#googleResults").append(html);
	// });
	// console.log('it works');
// }