function lowestDivisible() {
	var answer = 0;
	
	outerLoop:
	for(var i = 10; i <= (2522); i++) {
		innerLoop:
		for(var j = 6; j <= 10; j++) {
			if(i % j > 0) {
				continue outerLoop;
			}
		}
		console.log("answer is " + i);
		answer = i;
		//return i;
		break;
	}
	
	

	document.getElementById("answer").value = answer;
	
	return false;
}