<?php	//	classes

class deck {
	//	PROPERTIES
	public $cards = array();
	
	//	CONSTRUCTOR
	function __construct() {
		$this->fillDeck();
	}
		
	//	GETTERS
	public function get_cards() {
		return $this->cards;
	}
	public function get_numberOfCards() {
		return count($this->cards);
	}
	
	//	METHODS
	public function fillDeck() {
		for($suit = 0; $suit <= 3; $suit++) {
			for($value = 2; $value <= 14; $value++) {
				$this->cards[] = new card($value,$suit);
			}
		}
		shuffle($this->cards);
	}
	
	public function dealCards($player1, $player2) {	//	$player1 & $player2 are instances of 'player'
		while($this->get_numberOfCards() > 0) {
			$player1->addToPile(array_pop($this->cards));
			$player2->addToPile(array_pop($this->cards));
		}
	}
}

class card {
	//	PROPERTIES
	public $value;	//	2-14 (2-10, Jack, Queen, King, Ace)
	public $suit;	//	0-3 (clubs, diamonds, hearts, spades)
	protected $suitNames = array(
		'clubs',
		'diamonds',
		'hearts',
		'spades'
	);
	
	//	CONSTRUCTOR (to be run at instantiation time)
	function __construct($new_value, $new_suit) {	//	must set properties at time of instantiation
		$this->value = $new_value;
		$this->suit = $new_suit;
	}
	
	//	SETTERS
	public function set_value($new_value) {
		$this->value = $new_value;
	}
	public function set_suit($new_suit) {
		$this->suit = $new_suit;
	}
	
	//	GETTERS
	public function get_value() {
		return $this->value;
	}
	public function get_suit() {
		return $this->suit;
	}
	public function get_suitName() {
		return $this->suitNames[$this->get_suit()];
	}
	
	//	METHODS
	public function displayValue($numVal = 2, $showFullName = false) {	//	show Ace, King, Queen, & Jack
		switch($numVal) {
			case 14:
				return $showFullName ? "Ace" : "A"; break;
			case 13:
				return $showFullName ? "King" : "K"; break;
			case 12:
				return $showFullName ? "Queen" : "Q"; break;
			case 11:
				return $showFullName ? "Jack" : "J"; break;
			default:
				return $numVal;
		}
	}
	public function cardImg() {
		return "<img src='img/cards/" . strtolower("{$this->displayValue($this->get_value(),true)}_of_{$this->get_suitName()}") . ".jpg' alt='{$this->displayValue($this->get_value(), true)} of {$this->get_suitName()}'>";
	}
	public function showCard() {
		//return "<span class='cards " . $this->get_suitName() . "'>{$this->displayValue($this->get_value())}<img src='img/{$this->get_suitName()}.png' alt='{$this->get_suitName()}'></span>";
		return "<span class='cards " . $this->get_suitName() . "'>{$this->cardImg()}</span>";
	}
}

class player {
	//	PROPERTIES
	public $name;
	public $pile = array();
	
	//	CONSTRUCTOR
	function __construct($new_name = null) {
		$this->set_name($new_name);
	}
		
	//	SETTERS
	public function set_name($new_name) {
		$this->name = $new_name;
	}
		
	//	GETTERS
	public function get_name() {
		return $this->name;
	}
	public function get_pile($index = null) {
		if(is_numeric($index)) {
			return $this->pile[$index];	//	specific card
		} else {
			return $this->pile;	//	array
		}
	}
	public function get_topCard() {
		return $this->get_pile(0);	//	get top card from pile
	}
	public function get_pileCount() {
		return count($this->pile);
	}
	
	//	METHODS
	public function addToPile($card) {
		$this->pile[] = $card;
	}
	public function showPile() {
		foreach($this->pile as $c) {
			echo $c->showCard() . " | \n";
		}
	}
	public function grabEscrow($escrowArray) {	//	grab all the cards in Escrow and add to pile
		shuffle($escrowArray);
		foreach($escrowArray as $escrowCard) {
			$this->addToPile($escrowCard);
		}
	}
}

class game {
	//	PROPERTIES
	public $winner;
	public $loser;
	public $escrow = array();
	
	//	CONSTRUCTOR
	function __construct() {
	}
		
	//	SETTERS
	public function set_winner($new_winner) {
		$this->winner = $new_winner;
	}
	public function set_loser() {
		$this->loser = $new_loser;
	}
		
	//	GETTERS
	public function get_winner() {
		return $this->winner;
	}
	public function get_loser() {
		return $this->loser;
	}
	public function get_escrow() {
		return $this->escrow;
	}
	public function get_escrowCount() {
		return count($this->escrow);
	}
	
	//	METHODS
	public function emptyEscrow() {
		$this->escrow = array();
	}
	public function addToEscrow($card) {
		$this->escrow[] = $card;
	}
	public function battle($player1, $player2) {
		$p1Card = array_shift($player1->pile);
		$p2Card = array_shift($player2->pile);
		
		$this->addToEscrow($p1Card);
		$this->addToEscrow($p2Card);
		
		if($p1Card->value > $p2Card->value) {	//	player 1 wins!
			$player1->grabEscrow($this->get_escrow());
			$this->emptyEscrow();	//	empty the escrow pile
		} elseif($p1Card->value < $p2Card->value) {	//	player 2 wins!
			$player2->grabEscrow($this->get_escrow());
			$this->emptyEscrow();	//	empty the escrow pile
		} else {	//	tie
			for($i = 1; $i<=3; $i++) {
				if($player1->get_pileCount() > LOSING_SCORE) {	//	if player has no cards, he doesn't give any (he'll lose immediately on the next attempt at iteration of the battle function
					$this->addToEscrow(array_shift($player1->pile));
				}
				
				if($player2->get_pileCount() > LOSING_SCORE) {	//	if player has no cards, he doesn't give any (he'll lose immediately on the next attempt at iteration of the battle function
					$this->addToEscrow(array_shift($player2->pile));
				}
			}
		}
	}
}
?>