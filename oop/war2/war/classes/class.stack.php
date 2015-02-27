<?php

class stack {

	const POSITION_TOP    = 1;
	const POSITION_BOTTOM = 2;

	protected $cards = array();

	public function get_cards() {
		return $this->cards;
	}

	public function get_card( $position ) {
		if ( count( $this->cards ) === 0 ) {
			return false;
		}
		switch( $position ) {
			case self::POSITION_TOP:
				$card = array_shift( $this->cards );
			break;
			case self::POSITION_BOTTOM:
				$card = array_pop( $this->cards );
			break;
		}
		return $card;
	}

	public function has_cards() {
		return ( $this->card_count() === 0 ? false : true );
	}

	public function card_count() {
		return count( $this->cards );
	}

	public function add_card( card $card,$position=self::POSITION_BOTTOM ) {
		$card->set_comparable_status(true);
		switch( $position ) {
			case self::POSITION_TOP:
				array_unshift( $this->cards,$card );
			break;
			case self::POSITION_BOTTOM:
				$this->cards[] = $card;
			break;
		}
		return $this;
	}

	public function shuffle() {
		if ( count( $this->cards ) === 0 ) {
			error::trigger('No cards available in stack to shuffle');
		}
		$keys = array_keys( $this->cards );
		shuffle( $keys );
		$cards = array();
		foreach( $keys as $key ) {
			$cards[$key] = $this->cards[$key];
		}
		$this->cards = $cards;
	}

	public function remove_cards( $num ) {
		for( $i=1;$i <= $num;$i++ ) {
			$c = ( count( $this->cards ) - 1 );
			unset( $this->cards[$c] );
		}
	}

	public function compare() {
		$weights = array();
		foreach( $this->cards as $i => $card ) {
			if ( $card->get_comparable_status() == false ) {
				continue;
			}
			$weights[$i] = $card->get_weight();
		}
		arsort( $weights,SORT_NUMERIC );
		$highest = array_values( $weights );
		$highest = array_shift( $highest );
		$counts = array_count_values( $weights );
		$same = $winner = false;
		if ( $counts[$highest] > 1 ) {
			$same = true;
		}
		else {
			$card = array_keys( $weights );
			$card = array_shift( $card );
			$winner = $this->cards[$card]->get_player();
		}
		return array( $same,$winner );
	}

	public function clear_cards() {
		$this->cards = array();
	}

	public function clear_weights() {
		foreach( $this->cards as $card ) {
			$card->set_comparable_status(false);
		}
	}

}

?>