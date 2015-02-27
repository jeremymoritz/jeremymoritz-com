function largestPalindrome() {
	var startTime = (new Date()).getTime();
	var palindromes = [0];	//	start with 0 just to have SOMETHING in the array
	var numbers = [];
	
	var numDigits = parseInt(document.getElementById("numDigits").value);
	numDigits = numDigits <= 8 ? (numDigits >= 1 ? numDigits : 3) : 3;
	
	var maxDigits = Math.pow(10, numDigits) - 1;
	var minDigits = Math.pow(10, numDigits - 1) + 1;

	outerLoop:
	for(var i = maxDigits; i >= minDigits; i--) {
		innerLoop:
		for(var j = maxDigits; j >= i; j--) {
			var prod = i * j;
			
			if(prod < palindromes[0]) {
				continue outerLoop;
			}
			
			if(prod == (prod + "").split("").reverse().join("")) {
				prod = parseInt(prod);	//	turn it back into an integer
				if(prod > palindromes[0] || palindromes.length == 0) {
					palindromes.unshift(prod);	//	add to BEGINNING of array
				} else {
					continue outerLoop;
				}
			}
		}
	}

	document.getElementById("answer").value = palindromes[0];
	console.log("Time Elapsed: " + (((new Date()).getTime() - startTime) / 1000) + " seconds");
	
}