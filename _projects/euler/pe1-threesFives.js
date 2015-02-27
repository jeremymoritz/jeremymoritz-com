function threesFives(maxNum) {
	var sum = 0;

	for(var i = 0; i < maxNum; i++) {
		sum += (i % 3 ? (i % 5 ? 0 : i) : i);
	}

	return sum;
}