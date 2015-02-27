/*!
 * Park Puzzle Solver - v0.1.0 
 * Build Date: 2014.10.05 
 * Docs: http://moritzcompany.com 
 * Coded @ Moritz Company 
 */ 
 
var app = angular.module('ParksApp', []);

app.run(function runApp($rootScope) {
	$rootScope._ = _;	//	allows lodash to be used in the view
});

app.controller('ParksController', [
	'$scope',
	'PuzzleFactory',
	function ParksController($s, PuzzleFactory) {
		function Cell(color, row, column, state, timestamp) {
			this.color = color;
			this.row = row;
			this.column = column;
			this.state = state || 'blank';
			this.timestamp = timestamp || new Date().getTime();

			return this;
		}

		function Park(cellsArray) {
			this.cells = cellsArray || [];

			this.getColor = function getColor() {
				return this.cells.length ? this.cells[0].color : undefined;	//	any cell in this park will have the same color
			};

			return this;
		}

		function Puzzle(id, cellsArray) {
			this.id = id;
			this.cells = cellsArray || [];
			this.rows = [];
			this.treeCount = 1;

			this.getCells = function getCells(stateOnly) {
				if (stateOnly) {
					if (_.isArray(stateOnly)) {	//	array of states (e.g. ['tree', 'note'])
						return _.filter(this.cells, function filterToOnlyThisState(cell) {
							return _.contains(stateOnly, cell.state);
						});
					}

					return _.filter(this.cells, {state: stateOnly});	//	assume it's a string (e.g. 'blank')
				}
				return this.cells;
			};

			this.getRows = function getRows() {
				if (this.rows.length) {
					return this.rows;
				}
				var rows = this.rows;

				_.forEach(this.cells, function eachCell(cell) {
					if (!rows[cell.row]) {
						rows[cell.row] = {cells: []};
					}

					rows[cell.row].cells.push(cell);
				});
				return rows;
			};

			this.getColumns = function getColumns() {
				var columns = [];

				_.forEach(this.cells, function eachCell(cell) {
					if (!columns[cell.column]) {
						columns[cell.column] = {cells: []};
					}

					columns[cell.column].cells.push(cell);
				});
				return columns;
			};

			this.getParks = function getParks(onlyBlanks, includeSolvedParks, sortFunction) {
				var parks = [];

				_.forEach(this.cells, function eachCell(cell) {
					var thisColorPark = _.find(parks, function matchColor(park) {
						return park.getColor() === cell.color;
					});
					if (!thisColorPark) {
						parks.push(new Park([cell]));
					} else {
						_.find(parks, function matchColor(park) {
							return park.getColor() === cell.color;
						}).cells.push(cell);	//	find the matching park and put the cell into it
					}
				});

				if (onlyBlanks) {
					_.forEach(parks, function eachPark(park) {
						park.cells = _.filter(park.cells, function filterOutNonBlanks(cell) {
							return isBlank(cell);
						});
					});
				}

				if (!includeSolvedParks) {
					parks = _.filter(parks, function eachPark(park) {
						return park.cells.length;
					});
				}

				if (!_.isFunction(sortFunction)) {
					sortFunction = function sortByNumberOfCells(park) {
						return park.cells.length;	//	sort parks in order of the number of cells of each color (smallest parks first);
					};
				}

				return _.sortBy(parks, sortFunction);
			};

			return this;
		}

		function logPuzzleState() {
			var icons = {
				tree: '¶',
				dot: '•',
				note: '¥'
			};

			logMessage = '>>>>>[' + (steps + 1) + '.] ' + logMessage;
			_.forEach($s.puzzle.getRows(), function eachRow(row) {
				logMessage += '\n';	//	logMessage is set outside
				_.forEach(row.cells, function eachCell(cell) {
					logMessage += icons[cell.state] ? icons[cell.state] : cell.color.slice(-1);
				});
			});

			console.log(logMessage);
			steps++;
			logMessage = '';	//	reset logMessage
		}

		function loadPuzzle(newPuzzle) {
			$s.puzzle = new Puzzle(newPuzzle.id);
			var gridSize = newPuzzle.puzzleColors.length;
			steps = 0;
			nrpLog = [];	//	Non-Repeatable Processes Log (avoid infinite loops)
			logMessage = '';
			$s.puzzle.treeCount = gridSize > 8 ? 2 : 1;

			//	Fill puzzle with cells and those cells' colors
			for (var i = 0; i < gridSize; i++) {
				for (var j = 0; j < gridSize; j++) {
					$s.puzzle.cells.push(new Cell('color' + newPuzzle.puzzleColors[i][j], i, j));
				}
			}
		}

		function identifyNeighbors(primaryCell, kindsOfNeighbors, relativeCoords, onlyBlanks) {
			var neighborsObj = {
				diagonallyAdjacent: _.where($s.puzzle.getCells(), function matchDiagonallyAdjacentNeighbors(cellToCheck) {
					return Math.abs(cellToCheck.row - primaryCell.row) === 1 && Math.abs(cellToCheck.column - primaryCell.column) === 1;
				}),
				orthogonallyAdjacent: _.where($s.puzzle.getCells(), function matchOrthogonallyAdjacentNeighbors(cellToCheck) {
					return (Math.abs(cellToCheck.row - primaryCell.row) === 1 && cellToCheck.column === primaryCell.column) ||
						(Math.abs(cellToCheck.column - primaryCell.column) === 1 && cellToCheck.row === primaryCell.row);
				}),
				relative: _.filter($s.puzzle.getCells(), function matchRelative(cellToCheck) {
					if (relativeCoords) {
						return cellToCheck.row === primaryCell.row + relativeCoords.y && cellToCheck.column === primaryCell.column + relativeCoords.x;
					}
				})
			};

			if ($s.puzzle.treeCount === 1) {
				_.assign(neighborsObj, {
					row: _.where($s.puzzle.getCells(), {row: primaryCell.row}),
					column: _.where($s.puzzle.getCells(), {column: primaryCell.column}),
					park: _.where($s.puzzle.getCells(), {color: primaryCell.color})
				});
			}

			var neighbors = [];
			if (kindsOfNeighbors === 'all') {
				_.forOwn(neighborsObj, function eachGroupOfNeighbors(group) {
					neighbors = _.union(neighbors, group);
				});
			} else if (typeof kindsOfNeighbors === 'string') {
				neighbors = neighborsObj[kindsOfNeighbors];
			}	else { //	kindsOfNeighbors is an array
				_.forEach(kindsOfNeighbors, function eachKind(kind) {
					neighbors = _.union(neighbors, neighborsObj[kind]);
				});
			}

			if (onlyBlanks) {
				neighbors = _.filter(neighbors, {state: 'blank'});
			}

			return _.pull(neighbors, primaryCell);
		}

		function isTreeOrNote(cell) {
			return _.contains(['tree', 'note'], cell.state);
		}

		function isBlank(cell) {
			return cell.state === 'blank';
		}

		function autoDotNeighbors(cell, kindsOfNeighbors, relativeCoords) {
			_.forEach(identifyNeighbors(cell, kindsOfNeighbors, relativeCoords, true), function eachCell(thisCell) {
				$s.changeState(thisCell, 'dot');
			});
		}

		function autoDotCommon(cells, property) {	//	place dots on all cells with the given property in common with the given cells (but not the given cells themselves)
			//	EXAMPLE: if 3 cells are on the same row (property), this will dot the other cells in that row
			var allBlankCells = $s.puzzle.getCells('blank');

			var diffCellsWithSameProperty = _.filter(allBlankCells, function filterByProperty(cell) {
				return cell[property] === cells[0][property] && !_.contains(cells, cell);
			});

			_.forEach(diffCellsWithSameProperty, function eachCell(cell) {
				$s.changeState(cell, 'dot');
			});
		}

		function findCommonalities(cells, ignoreList) {
			var commonalities = [];

			if (cells.length === 2) {
				if (cells[0].row === cells[1].row) {
					commonalities.push('row');

					if (Math.abs(cells[0].column - cells[1].column) === 1) {
						commonalities.push('orthogonallyAdjacent');
					}
				} else if (cells[0].column === cells[1].column) {
					commonalities.push('column');

					if (Math.abs(cells[0].row - cells[1].row) === 1) {
						commonalities.push('orthogonallyAdjacent');
					}
				} else if (Math.abs(cells[0].row - cells[1].row) + Math.abs(cells[0].column - cells[1].column) === 2) {
					commonalities.push('diagonallyAdjacent');
				}

				if (cells[0].color === cells[1].color) {
					commonalities.push('park');
				}

				return _.isArray(ignoreList) ? _.difference(commonalities, ignoreList) : commonalities;
			} else {
				var possibleCommonalities = ['row', 'column', 'park'];
				possibleCommonalities = _.isArray(ignoreList) ? _.difference(possibleCommonalities, ignoreList) : possibleCommonalities;
				commonalities = _.clone(possibleCommonalities);

				_.forEach(cells, function eachCell(cell) {
					_.forEach(possibleCommonalities, function eachPossibleCommonality(commonality) {
						if (cell[commonality] !== cells[0][commonality]) {
							_.pull(commonalities, commonality);
						}
					});
				});

				return commonalities;
			}
		}

		function findLonerCells(cells) {
			var lonerCells = [];

			function isAlone(cell, kindOfNeighbors) {
				return !identifyNeighbors(cell, kindOfNeighbors, undefined, true).length;
			}

			_.forEach(cells, function eachCell(cell) {
				if (isAlone(cell, 'park') || isAlone(cell, 'row') || isAlone(cell, 'column')) {
					logMessage += 'Loner (' + (isAlone(cell, 'row') ? 'Row' : (isAlone(cell, 'column') ? 'Column' : 'Park')) + ')...';
					lonerCells.push(cell);	//	this cell is all alone in its row, column, or park! (must be a tree!)
				}
			});

			return lonerCells;
		}

		function puzzleSolved(sanity) {
			if (sanity) {
				return $s.puzzle.getCells(['tree', 'note']).length === (Math.sqrt($s.puzzle.cells.length) * $s.puzzle.treeCount);
			}
			return false;
		}

		function sanityCheck() {
			var treeCount = $s.puzzle.treeCount;
			function isUnsolvable(cellCollections) {
				var unsolvable = false;	//	double-negative perhaps, but this is what we're actually testing for
				_.forEach(cellCollections, function eachCollection(cellCollection) {
					var blanks = _.where(cellCollection.cells, {state: 'blank'});
					var treesAndNotesCount = _.filter(cellCollection.cells, function matchTreesAndNotes(cell) {
						return isTreeOrNote(cell);
					}).length;
					if((treesAndNotesCount < treeCount && !blanks.length) || treesAndNotesCount > treeCount) {
						unsolvable = true;	//	must be an error somewhere
						return false;
					}
				});
				return unsolvable;
			}

			return !(isUnsolvable($s.puzzle.getParks()) || isUnsolvable($s.puzzle.getRows()) || isUnsolvable($s.puzzle.getColumns()));
		}

		var logMessage;
		var nrpLog;
		var steps;
		var startTime;

		$s.page = 'intro';	//	initially show the intro page;
		$s.switchPage = function switchPage(newPage) {
			$s.page = newPage;
		};

		$s.triggerClick = function triggerClick() {
			$('#solveBtn').trigger('click');
		};

		$s.clearPuzzle = function clearPuzzle() {
			var allCells = $s.puzzle.getCells();
			var notEmpty = _.find(allCells, {'state': 'dot'});
			if(notEmpty) {
				_.forEach(allCells, function(thisCell) {
					$s.changeState(thisCell, 'blank');
				});
			} else {
				_.forEach(allCells, function(thisCell) {
					thisCell.color = $s.selectedColor;
				});
			}
		};

		$s.createPuzzle = function createPuzzle() {
			$s.creatingPuzzle = true;
		};

		$s.solvePuzzle = function solvePuzzle() {
			$s.creatingPuzzle = false;
			if (steps === 0) {
				startTime = new Date().getTime();
			}
			(function loopThroughParks() {
				var repeatLoop = false;
				if ($s.puzzle.treeCount === 1) {
					_.forEach($s.puzzle.getParks(true), function eachPark(park) {
						var commonalities;
						var lonerCells = findLonerCells(park.cells);

						if (lonerCells.length) {
							$s.changeState(lonerCells[0], 'tree');
							repeatLoop = true;
							return false;
						} else {
							commonalities = findCommonalities(park.cells, ['park']);	//	what do ALL of these cells have in common (besides 'park')?

							if (_.contains(commonalities, 'row') && !_.contains(nrpLog, 'autoDotRow' + park.getColor())) {	//	if in same row, dot all other cells in that row
								logMessage = 'Single-row park...';
								autoDotCommon(park.cells, 'row');
								nrpLog.push('autoDotRow' + park.getColor());
								repeatLoop = true;
								return false;
							} else if (_.contains(commonalities, 'column') && !_.contains(nrpLog, 'autoDotColumn' + park.getColor())) {	//	if in same column, dot all other cells in that column
								logMessage = 'Single-column park...';
								autoDotCommon(park.cells, 'column');
								nrpLog.push('autoDotColumn' + park.getColor());
								repeatLoop = true;
								return false;
							}

							if (_.contains(commonalities, 'orthogonallyAdjacent') && !_.contains(nrpLog, 'duplex' + park.getColor())) {	//	only happens with duplexes
								logMessage = 'Duplex...';
								if (_.contains(commonalities, 'row')) {
									_.forEach(park.cells, function eachCell(cell) {
										autoDotNeighbors(cell, 'relative', {x: 0, y: 1});
										autoDotNeighbors(cell, 'relative', {x: 0, y: -1});
									});
								} else {	//	same column
									_.forEach(park.cells, function eachCell(cell) {
										autoDotNeighbors(cell, 'relative', {x: 1, y: 0});
										autoDotNeighbors(cell, 'relative', {x: -1, y: 0});
									});
								}

								nrpLog.push('duplex' + park.getColor());
								repeatLoop = true;
								return false;
							}
						}
					});
				} else if ($s.puzzle.treeCount === 2) {
					var hasTwoTrees = function hasTwoTrees(cellCollection) {
						return _.filter(cellCollection.cells, isTreeOrNote).length === 2;
					};
					var dotOthersInTwoTreeCollection = function dotOthersInTwoTreeCollection(cellCollection) {
						var blankCells = _.filter(cellCollection.cells, isBlank);

						if (blankCells.length) {
							logMessage = 'Two Trees in a Row, Column, or Park. Dot the rest...';
							_.forEach(blankCells, function eachBlankCell(cell) {
								$s.changeState(cell, 'dot');
							});

							return true;	//	making new dots
						}
						return false;	//	no new dots
					};

					var twoTreeCollectionFound = false;
					_.forEach($s.puzzle.getRows(), function eachRow(row) {
						if (hasTwoTrees(row)) {
							if (dotOthersInTwoTreeCollection(row)) {
								twoTreeCollectionFound = true;
								return false;
							}
						}
					});

					if (!twoTreeCollectionFound) {
						_.forEach($s.puzzle.getColumns(), function eachColumn(column) {
							if (hasTwoTrees(column)) {
								if (dotOthersInTwoTreeCollection(column)) {
									twoTreeCollectionFound = true;
									return false;
								}
							}
						});
					}

					if (!twoTreeCollectionFound) {
						_.forEach($s.puzzle.getParks(), function eachPark(park) {
							if (hasTwoTrees(park)) {
								if (dotOthersInTwoTreeCollection(park)) {
									return false;
								}
							}
						});
					}

					if (twoTreeCollectionFound) {
						repeatLoop = true;
					}
				}

				if (!repeatLoop) {
					var sanity = sanityCheck();
					if (puzzleSolved(sanity)) {
						_.forEach(_.filter($s.puzzle.getCells(), {state: 'note'}), function eachNoteCell(cell) {
							$s.changeState(cell, 'tree');
						});
						logMessage = 'Convert Notes to Trees...';
						logPuzzleState();
						console.log('\nPuzzle is solved! Hooray!\n');
						console.log('Time Elapsed: ' + ((new Date().getTime() - startTime) / 1000) + ' seconds');
						console.log('Steps taken: ' + steps);
						steps = 0;
						$s.solveMessage = 'We Did it!';
					} else {
						if (sanity) {
							logMessage = 'Guess...';
							$s.changeState($s.puzzle.getParks(true)[0].cells[0], 'note');
							repeatLoop = true;
						} else {
							logMessage = 'Pull the latest note...';
							//	puzzle is in error, our last note is wrong.  Let's fix it.
							var latestNote = _.max($s.puzzle.getCells('note'), 'timestamp');
							var cellsAfterLastNote = _.filter($s.puzzle.getCells(), function cellFilter(cell) {
								return cell.timestamp > latestNote.timestamp;
							});
							_.forEach(cellsAfterLastNote, function eachCell(cell) {
								$s.changeState(cell, 'blank');
							});
							$s.changeState(latestNote, 'dot');
							repeatLoop = true;
						}
					}
				}

				if (repeatLoop) {
					logPuzzleState();
					if (steps % 20 === 0) {
						setTimeout($s.triggerClick, 1);
						return false;
					} else {
						loopThroughParks();
					}
				}
			})();
		};

		$s.availablePuzzles = PuzzleFactory.getAllPuzzles();
		$s.puzzleChose = $s.availablePuzzles[0];

		$s.choosePuzzle = function choosePuzzle() {
			loadPuzzle($s.puzzleChose);
		};

		$s.puzzle = {};

		$s.selectedAction = 'rotate';
		$s.cellStates = ['blank', 'dot', 'tree', 'note'];
		$s.actions = ['rotate'].concat($s.cellStates);
		$s.actionIcons = {
			rotate: 'refresh',
			blank: 'square-o',
			tree: 'tree',
			dot: 'circle',
			note: 'flag'
		};
		$s.creatingPuzzle = false;
		$s.selectedColor = 'color1';
		$s.colorOptions = ['color1', 'color2', 'color3', 'color4', 'color5', 'color6', 'color7', 'color8', 'color9', 'color10', 'color11', 'color12'];
		//$s.colors = ['color1'].concat($s.colorOptions);

		$s.selectThisAction = function selectThisAction(action) {
			$s.selectedAction = action;
		};

		$s.selectThisColor = function selectThisColor(color) {
			$s.selectedColor = color;
			console.log($s.selectedColor);
		};

		$s.changeState = function changeState(cell, state, onlyIfBlank) {
			if($s.creatingPuzzle) {
				cell.color = $s.selectedColor;
			} else {
				if (state) {	//	this is set via the solve button only
					if (onlyIfBlank && !isBlank(cell.state)) {
						return;
					}
					cell.state = state;
					var time = new Date().getTime();
					cell.timestamp = time;

					switch(state) {
					case 'note':
						while(time === new Date().getTime()) {} // wait up to one millisecond, then
							/* fall through */
					case 'tree':
						autoDotNeighbors(cell, 'all');
						break;
					}
				} else if (_.contains($s.cellStates, $s.selectedAction)) {	//	human only
					cell.state = $s.selectedAction;
				} else {	//	the rotate option is on (human only)
					cell.state = (cell.state === 'dot' ? 'tree' : (cell.state === 'tree' ? 'blank' : 'dot'));
				}
			}
		};

		loadPuzzle($s.puzzleChose);
	}
]);


//	BLANK PUZZLE TEMPLATE
// {
// 	id: #,
// 	puzzleColors: [
// 		[, , , , ],
// 		[, , , , ],
// 		[, , , , ],
// 		[, , , , ],
// 		[, , , , ]
// 	]
// }
app.factory('PuzzleFactory', [function PuzzleFactory() {
	return {
		getAllPuzzles: function getAllPuzzles() {
			return [
				{
					id: 1,
					puzzleColors: [
						[1, 1, 1, 1, 2],
						[2, 2, 2, 2, 2],
						[2, 2, 3, 4, 4],
						[3, 3, 3, 3, 3],
						[3, 3, 5, 3, 3]
					]
				}, {
					id: 2,
					puzzleColors: [
						[1, 1, 1, 2, 2],
						[1, 1, 1, 1, 3],
						[1, 1, 3, 3, 3],
						[1, 1, 4, 4, 5],
						[5, 5, 5, 5, 5]
					]
				}, {
					id: 3,
					puzzleColors: [
						[1, 1, 1, 2, 2],
						[1, 1, 1, 2, 2],
						[1, 1, 1, 3, 4],
						[5, 5, 5, 3, 4],
						[5, 3, 3, 3, 4]
					]
				}, {
					id: 4,
					puzzleColors: [
						[1, 2, 2, 2, 2],
						[1, 1, 2, 3, 2],
						[4, 1, 2, 3, 5],
						[4, 1, 1, 3, 5],
						[4, 4, 3, 3, 3]
					]
				}, {
					id: 5,
					puzzleColors: [
						[1, 1, 2, 2, 2, 2],
						[1, 1, 1, 2, 2, 2],
						[1, 1, 1, 1, 3, 2],
						[4, 1, 1, 3, 3, 3],
						[4, 5, 5, 5, 6, 3],
						[4, 4, 5, 5, 6, 6]
					]
				}, {
					id: 6,
					puzzleColors: [
						[1, 1, 2, 2, 3, 3, 4],
						[1, 1, 2, 2, 3, 3, 3],
						[5, 5, 5, 2, 3, 6, 6],
						[5, 5, 5, 2, 3, 6, 6],
						[5, 5, 3, 3, 3, 6, 6],
						[5, 5, 7, 7, 7, 6, 6],
						[7, 7, 7, 7, 7, 7, 7]
					]
				}, {
					id: 7,
					puzzleColors: [
						[1, 1, 1, 1, 1, 2, 2],
						[3, 1, 1, 1, 2, 2, 4],
						[3, 1, 5, 5, 6, 4, 4],
						[3, 1, 5, 6, 6, 7, 4],
						[3, 1, 5, 3, 7, 7, 4],
						[3, 3, 3, 3, 7, 4, 4],
						[3, 3, 3, 4, 4, 4, 4]
					]
				}, {
					id: 8,
					puzzleColors: [
						[1, 1, 1, 1, 1, 1, 1, 2],
						[1, 3, 3, 4, 4, 4, 1, 2],
						[1, 3, 3, 4, 3, 4, 2, 2],
						[1, 3, 3, 3, 3, 3, 2, 5],
						[3, 3, 3, 6, 3, 3, 3, 5],
						[3, 3, 6, 6, 3, 3, 5, 5],
						[3, 6, 6, 6, 7, 8, 5, 8],
						[3, 6, 6, 7, 7, 8, 8, 8]
					]
				}, {
					id: 9,
					puzzleColors: [
						[1, 1, 1, 1, 2, 2, 2, 2],
						[1, 1, 2, 1, 2, 2, 2, 3],
						[1, 1, 2, 2, 2, 2, 3, 3],
						[1, 1, 1, 1, 4, 3, 3, 5],
						[6, 1, 4, 4, 4, 5, 5, 5],
						[6, 4, 4, 4, 4, 4, 7, 5],
						[6, 6, 8, 8, 4, 4, 7, 5],
						[6, 8, 8, 7, 7, 7, 7, 5]
					]
				}, {
					id: 10,
					puzzleColors: [
						[1, 1, 1, 1, 1, 1, 1, 2, 2],
						[1, 1, 1, 1, 3, 3, 4, 4, 2],
						[5, 1, 1, 1, 3, 4, 4, 2, 2],
						[5, 1, 1, 3, 3, 6, 4, 4, 4],
						[5, 5, 1, 1, 1, 6, 6, 6, 4],
						[5, 7, 6, 6, 6, 6, 4, 4, 4],
						[7, 7, 7, 6, 8, 8, 8, 4, 4],
						[7, 6, 6, 6, 8, 9, 9, 9, 4],
						[7, 6, 8, 8, 8, 8, 8, 9, 9]
					]
				}, {
					id: 11,
					puzzleColors: [
						[1, 1, 1, 1, 2, 2, 2, 2, 2, 2 ],
						[1, 3, 1, 1, 2, 2, 2, 2, 2, 2 ],
						[3, 3, 3, 3, 4, 2, 2, 5, 5, 2 ],
						[3, 3, 3, 3, 4, 4, 2, 6, 5, 5 ],
						[3, 4, 4, 4, 4, 4, 4, 6, 5, 5 ],
						[3, 3, 7, 7, 4, 7, 6, 6, 5, 5 ],
						[3, 7, 7, 7, 7, 7, 7, 6, 5, 5 ],
						[3, 8, 8, 9, 7, 7, 6, 6, 10,5 ],
						[8, 8, 9, 9, 9, 10,10,10,10,5 ],
						[8, 8, 9, 9, 10,10,10,10,10,10]
					]
				}, {
					id: 12,
					puzzleColors: [
						[1, 1, 1, 1, 2, 2, 3, 3, 3, 3, 3],
						[1, 1, 1, 2, 2, 2, 3, 3, 3, 3,10],
						[1, 1, 5, 5, 5, 2, 2, 2, 3,10,10],
						[1, 5, 5, 5, 6, 6, 2, 2, 3,10,10],
						[8, 5, 5, 9, 6, 6, 6,11,11,10,10],
						[8, 8, 8, 9, 9, 6, 6, 4,11,11,10],
						[8, 7, 7, 9, 9, 4, 4, 4,11,11,11],
						[8, 7, 9, 9, 9, 9, 4, 4,11,11,11],
						[8, 7, 7, 7, 9, 4, 4,11,11,11,11],
						[8, 8, 7, 7, 7, 7,11,11,11,11,11],
						[8, 8, 7, 7, 7, 7, 7,11,11,11,11]
					]
				}, {
					id: 13,
					puzzleColors: [
						[1, 1, 2, 2, 3, 3, 3, 4, 4, 4, 4 ],
						[1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4 ],
						[1, 2, 2, 2, 5, 5, 5, 5, 4, 4, 4 ],
						[1, 1, 2, 2, 5, 5, 5, 4, 4, 4, 6 ],
						[1, 7, 5, 5, 5, 8, 5, 8, 4, 6, 6 ],
						[7, 7, 7, 9, 9, 8, 8, 8, 8, 6, 10],
						[7, 7, 7, 9, 9, 9, 8, 8, 8, 6, 10],
						[11,7, 9, 9, 9, 9, 9, 8, 6, 6, 10],
						[11,11,9, 9, 9, 9, 10,10,10,10,10],
						[11,11,11,11,9, 9, 10,10,10,10,10],
						[11,11,11,11,9, 10,10,10,10,10,10]
					]
				}, {
					id: 14,
					puzzleColors: [
						[1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2],
						[1, 1, 1, 2, 2, 2, 2, 3, 4, 4, 2],
						[1, 1, 1, 1, 2, 2, 2, 3, 3, 4, 5],
						[6, 1, 1, 1, 2, 3, 3, 3, 4, 4, 5],
						[6, 1, 1, 3, 3, 3, 3, 4, 4, 5, 5],
						[6, 6, 6, 6, 6, 6, 3, 3, 3, 5, 5],
						[6, 6, 6, 6, 7, 6, 7, 3, 5, 5, 8],
						[6, 6, 9, 9, 7, 7, 7, 3, 3, 8, 8],
						[10,6, 6, 9, 9, 7, 3, 3, 11,8, 8],
						[10,6, 10,10,9, 9, 3, 11,11,11,8],
						[10,10,10,3, 3, 3, 3, 11,11,8, 8]
					]
				}, {
					id: 'Christmas 149',
					puzzleColors: [
						[1, 1, 1, 2, 2, 3, 3, 3, 3, 3, 3, 3],
						[1, 1, 1, 2, 2, 3, 3, 3, 3, 3, 3, 3],
						[4, 1, 1, 2, 2, 3, 3, 3, 3, 3, 3, 5],
						[4, 1, 1, 1, 3, 3, 3, 3, 3, 3, 5, 5],
						[4, 1, 1, 1, 6, 6, 6, 6, 6, 5, 5, 5],
						[7, 1, 1, 1, 6, 6, 6, 6, 6, 5, 5, 5],
						[7, 7, 7, 8, 8, 8, 8, 8, 9, 5, 5, 5],
						[7, 7, 7, 7, 8, 8, 8, 8, 9,10, 5, 5],
						[7, 7, 7, 8, 8,11,11, 9, 9,10, 5, 5],
						[7, 7, 7,11,11,11,11,11,10,10,10,10],
						[7, 7, 7,12,12,12,12,11,10,10,10,10],
						[7, 7, 7, 7,12,12,12,12,10,10,10,10]
					]
				}
			];
		}
	};
}]);

