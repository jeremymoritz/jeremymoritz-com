<?php

class player extends stack {

	private $id = null;
	private $name = '';
	private $status = war::PLAYING;

	public function get_id() {
		return $this->id;
	}

	public function set_id( $id ) {
		$this->id = $id;
	}

	public function get_name() {
		return $this->name;
	}

	public function set_name( $name ) {
		$this->name = $name;
	}

	public function get_status() {
		return $this->status;
	}

	public function set_status( $status ) {
		$this->status = $status;
	}

	public function add_card( card $card,$position=stack::POSITION_BOTTOM ) {
		$card->set_player( $this );
		$card->set_comparable_status(true);
		switch( $position ) {
			case stack::POSITION_TOP:
				array_unshift( $this->cards,$card );
			break;
			case stack::POSITION_BOTTOM:
				$this->cards[] = $card;
			break;
		}
		return $this;
	}

}

?>