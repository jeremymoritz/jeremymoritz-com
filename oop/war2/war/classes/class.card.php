<?php

if ( !defined('WAR') ) {
	die('Direct script access is not allowed');
}

class card {

	const SUIT_SPADES   = 1;
	const SUIT_HEARTS   = 2;
	const SUIT_DIAMONDS = 3;
	const SUIT_CLUBS    = 4;

	const KING  = 11;
	const QUEEN = 12;
	const JACK  = 13;

	const ACE = 1;

	private $suit  = null;
	private $rank  = null;
	private $player = null;
	private $prev_player = null;
	private $compare = true;

	public static function get_suits() {
		return array(
			self::SUIT_SPADES   => 'Spades',
			self::SUIT_HEARTS   => 'Hearts',
			self::SUIT_DIAMONDS => 'Diamonds',
			self::SUIT_CLUBS    => 'Clubs'
		);
	}

	public static function get_ranks() {
		return array(
			self::ACE => array(
				'title' => 'Ace',
				'weight' => 13
			),
			2 => array(
				'title' => '2',
				'weight' => 1
			),
			3 => array(
				'title' => '3',
				'weight' => 2
			),
			4 => array(
				'title' => '4',
				'weight' => 3
			),
			5 => array(
				'title' => '5',
				'weight' => 4
			),
			6 => array(
				'title' => '6',
				'weight' => 5
			),
			7 => array(
				'title' => '7',
				'weight' => 6
			),
			8 => array(
				'title' => '8',
				'weight' => 7
			),
			9 => array(
				'title' => '9',
				'weight' => 8
			),
			10 => array(
				'title' => '10',
				'weight' => 9
			),
			self::JACK => array(
				'title' => 'Jack',
				'weight' => 10
			),
			self::QUEEN => array(
				'title' => 'Queen',
				'weight' => 11
			),
			self::KING => array(
				'title' => 'King',
				'weight' => 12
			)
		);
	}

	public function set_suit( $suit ) {
		$this->suit = $suit;
		return $this;
	}

	public function get_suit() {
		if ( is_null( $this->suit ) ) {
			return false;
		}
		return $this->suit;
	}

	public function set_rank( $rank ) {
		if ( !is_integer( $rank ) ) {
			error::trigger('Rank must be an integer');
		}
		$this->rank = $rank;
	}

	public function get_rank() {
		if ( is_null( $this->rank ) ) {
			return false;
		}
		return $this->rank;
	}

	public function get_name() {
		$suits = self::get_suits();
		$ranks = self::get_ranks();
		return "{$ranks[$this->rank]['title']} of {$suits[$this->suit]}";
	}

	public function get_weight() {
		$ranks = self::get_ranks();
		return $ranks[$this->rank]['weight'];
	}

	public function get_player() {
		return $this->player;
	}

	public function set_player( player $player ) {
		if ( !is_null( $this->player ) ) {
			$this->prev_player = $this->player;
		}
		$this->player = $player;
	}

	public function get_comparable_status() {
		return $this->compare;
	}

	public function set_comparable_status( $bool ) {
		$this->compare = $bool;
	}

}

?>