<?php

if ( !defined('WAR') ) {
	die('Direct script access is not allowed');
}

class deck extends stack {

	const SIZE = 52;

	public function __construct() {
		$suits = card::get_suits();
		$ranks = card::get_ranks();
		foreach( $suits as $suit => $suit_name ) {
			foreach( $ranks as $rank => $rank_data ) {
				$card = new card;
				$card->set_suit( $suit );
				$card->set_rank( $rank );
				$this->add_card( $card );
			}
		}
	}

}

?>