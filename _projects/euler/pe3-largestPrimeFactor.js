function getLargestPrimeFactorMethod1(bigNum) {
	var startTime = (new Date()).getTime();
	function getFactors(n, stopAtFirstFactor) {
		var factors = [];
		for(var i = 3; i < (n / 2); i += 2) {
			if(n % i == 0) {
				factors.push(i);
				if(stopAtFirstFactor) {
					break;
				}
			}
		}
		return factors;
	}
	var answer = ':-(';

	var factors = getFactors(bigNum);
	console.log("factors of " + bigNum + ": " + factors);
	factors.reverse();
	for(var i = 0; i < factors.length; i++) {
		if(!getFactors(factors[i], true).length) {
			answer = factors[i];
			break;
		}
	}

	console.log("Method 1 is done! Time Elapsed: " + (((new Date()).getTime() - startTime) / 1000) + " seconds");
	
	document.getElementById("answer").value = answer;
	
	return false;
}

function getLargestPrimeFactorMethod2(bigNum) {
	var startTime = (new Date()).getTime();
	function findAllPrimes(n) {
		var sieve = [];
		var primes = [];
		for(var i = 3; i < n; i += 2) {
			if(sieve.indexOf(i) == -1) {
				primes.push(i);
				for(var j = 2; j < n; j++) {
					var mult = j * i;
					if(mult > n) {
						break;
					} else if(sieve.indexOf(mult) == -1) {
						sieve.push(mult);
					}
				}
			}
		}

		return primes;
	}

	var primes = findAllPrimes(bigNum / 2);
	// console.log(primes);

	primes.reverse();
	for(var i = 0; i < primes.length; i++) {
		if(bigNum % primes[i] == 0) {
			largestPrimeFactor = primes[i];
			break;
		}
	}

	console.log("Method 2 is done! Time Elapsed: " + (((new Date()).getTime() - startTime) / 1000) + " seconds");
	return largestPrimeFactor;
}