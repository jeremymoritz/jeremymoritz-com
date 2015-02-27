function collatz(num) {
	if(num > 1) {
		return num % 2 ? (3 * num) + 1 : (num > 1 ? num / 2 : 1);
	} else {
		return 1;
	}
}

function countCollatz(num, collatzChains) {
	var count = 1;
	while(num > 1) {
		num = collatz(num);
		if(collatzChains[num + ""]) {
			count += collatzChains[num + ""];
			num = 1;
		} else {
			count++;
		}
	}
	return count;
}

function simpleCountCollatz(num) {
	var count = 1;
	while(num > 1) {
		num = collatz(num);
		count++;
	}
	return count;
}

function longestCollatz() {
	console.log("Starting...");
	var startTime = (new Date()).getTime();
	//var maxStartingNumber = 1000 * 1000;	//	max starting num = 1 Million
	var maxStartingNumber = 1000 * 1000;	//	max starting num = 1 Million
	var collatzChains = {};	//	{startingNumber: chainLength}
	
	for(var i = 1; i < maxStartingNumber; i++) {
		collatzChains[i + ""] = countCollatz(i, collatzChains);
	}
	
	var longestChain = 0;
	var answer;
	for(var key in collatzChains) {
		if(collatzChains[key] > longestChain) {
			longestChain = collatzChains[key];
			answer = key;
		}
	}

	document.getElementById("answer").value = answer;
	console.log("Time Elapsed: " + (((new Date()).getTime() - startTime) / 1000) + " seconds");
	
}