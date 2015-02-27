function getSumOfEvenFibonacciValues(maxNum) {
	var sum = 0;
	var prevNum = 0;
	var curNum = 1;

	while(curNum < maxNum) {
		sum += curNum % 2 ? 0 : curNum;

		var prevPlusCur = prevNum + curNum;
		prevNum = curNum;
		curNum = prevPlusCur;
	}

	return sum;
}