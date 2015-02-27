<?php

if ( !defined('WAR') ) {
	die('Direct script access is not allowed');
}

class war {

	private static $instance = null;

	const STATUS_SETUP = 1;
	const STATUS_PLAYING = 2;

	const WON     = 1;
	const LOST    = 2;
	const PLAYING = 3;

	private $deck = null;
	private $stack = null;
	private $players = array();
	private $player_count = 0;
	private $config = array();
	private $war = false;
	private $war_idx = null;
	private $war_level = 1;
	private $wars = array();
	private $battles = array();
	private $stats = array();

	public static function init() {
		$session_path = config_get('SESSION_SAVE_PATH');
		if ( !is_writeable( $session_path ) ) {
			error::trigger('Session save path is not writable');
		}
		session_save_path( $session_path );
		session_regenerate_id(true);
		session_start();
		self::$instance = new war;
	}

	public function __construct() {
		foreach( array('config','status','players','battles','stats') as $key ) {
			if ( !isset( $_SESSION[$key] ) ) {
				switch( $key ) {
					case 'status':
						$_SESSION[$key] = self::STATUS_SETUP;
					break;
					default:
						$_SESSION[$key] = array();
					break;
				}
			}
			$this->{$key} =& $_SESSION[$key];
		}
		switch( $this->status ) {
			case self::STATUS_SETUP:
				$this->setup_form();
			break;
			case self::STATUS_PLAYING:
				$this->play();
			break;
		}
	}

	public function setup_form() {
		$face_down_cards = array(
			'1' => '1 Card',
			'3' => '3 Cards'
		);
		/*$use_jokers = array(
			'no'  => 'No',
			'yes' => 'Yes'
		);*/
		$player_count = ( isset( filter_input(INPUT_POST, 'players') ) && count( filter_input(INPUT_POST, 'players') ) > 0 ? count( filter_input(INPUT_POST, 'players') ) : 1 );
		$errors = array();
		if ( isset( filter_input(INPUT_POST, 'submit') ) ) {
			$players = array();
			if ( isset( filter_input(INPUT_POST, 'players') ) ) {
				foreach( filter_input(INPUT_POST, 'players') as $i => $player ) {
					if ( strlen( trim( $player['name'] ) ) == '' ) {
						$errors[] = "Player {$i} is required";
						continue;
					}
					$players[] = $player;
				}
			}
			if ( count( $errors ) == 0 ) {
				if ( count( $players ) < 2 ) {
					$errors[] = 'At least two players are required to play this game';
				}
			}
			foreach( array('face_down_cards'=>'Face down cards'/*,'use_jokers'=>'Use Jokers'*/) as $key => $label ) {
				if ( !isset( filter_input(INPUT_POST, $key) ) || trim( filter_input(INPUT_POST, $key) ) == '' ) {
					$errors[] = "{$label} selection is required";
				}
				elseif ( !array_key_exists( filter_input(INPUT_POST, $key),${$key} ) ) {
					$errors[] = "{$label} is not a valid selection";
				}
			}
			if ( count( $errors ) == 0 ) {
				foreach( $players as $_player ) {
					$player = new player;
					$player->set_name( $_player['name'] );
					$this->add_player( $player );
				}
				$this->config['face_down_cards'] = (int) filter_input(INPUT_POST, 'face_down_cards');
				//$this->config['use_jokers']      = ( filter_input(INPUT_POST, 'use_jokers') == 'yes' ? true : false );
				$this->status = self::STATUS_PLAYING;
				header('Location: ' . config_get('URL') . '/index.php');
				exit;
			}
		}
		template::output('main',array(
			'css'  => array('main','setup_form'),
			'js'   => array('jquery','setup_form'),
			'body' => template::fetch('setup_form',array(
				'player_count'    => $player_count,
				'face_down_cards' => $face_down_cards,
				//'use_jokers'      => $use_jokers,
				'errors'          => $errors
			))
		));
	}

	public function play() {
		if ( isset( filter_input(INPUT_GET, 'action') ) ) {
			switch( filter_input(INPUT_GET, 'action') ) {
				case 'reset':
					$this->config = array();
					$this->status = self::STATUS_SETUP;
					$this->players = array();
					$this->battles = array();
				break;
				case 'new-war':
					$this->battles = array();
					foreach( $this->players as $player ) {
						$player->set_status( self::PLAYING );
						$player->clear_cards();
					}
				break;
			}
			header('Location: ' . config_get('URL') . '/index.php');
			exit;
		}
		if ( count( $this->battles ) == 0 ) {
			$this->generate_battles();
		}
		$vars = array();
		$vars['show'] = ( isset( filter_input(INPUT_GET, 'show') ) ? filter_input(INPUT_GET, 'show') : 'battle' );
		switch( $vars['show'] ) {
			case 'battle':
				$battle_id = 1;
				if ( isset( filter_input(INPUT_GET, 'id') ) && is_numeric( filter_input(INPUT_GET, 'id') ) && isset( $this->battles[filter_input(INPUT_GET, 'id')] ) ) {
					$battle_id = (int) filter_input(INPUT_GET, 'id');
				}
				$vars['battle_id'] = $battle_id;
				$vars['battles'] = array( $battle_id=>$this->battles[$battle_id] );
				$vars['scores'] = ( isset( $this->battles[$battle_id]['score'] ) ? $this->battles[$battle_id]['score'] : false );
				$vars['prev_battle'] = ( $battle_id > 1 ? ( $battle_id - 1 ) : false );
				$vars['next_battle'] = ( $battle_id < count( $this->battles ) ? ( $battle_id + 1 ) : false );
			break;
			case 'battle-all':
				$vars['battles'] = $this->battles;
			break;
			case 'stats':
				$vars['winner'] = $this->stats['winner'];
				$vars['battles'] = count( $this->battles );
				$vars['wars'] = $this->stats['war_levels'];
			break;
		}
		$vars['players'] = $this->players;
		$vars['suit_directories'] = array(
			card::SUIT_SPADES   => 'spades',
			card::SUIT_HEARTS   => 'hearts',
			card::SUIT_DIAMONDS => 'diamonds',
			card::SUIT_CLUBS    => 'clubs'
		);
		$vars['rank_files'] = array(
			card::ACE => 'ace',
			2  => '2',
			3  => '3',
			4  => '4',
			5  => '5',
			6  => '6',
			7  => '7',
			8  => '8',
			9  => '9',
			10 => '10',
			card::JACK  =>'jack',
			card::QUEEN => 'queen',
			card::KING  => 'king'
		);
		template::output('main',array(
			'css'  => array('main','game'),
			'body' => template::fetch( 'game',$vars )
		));
	}

	public function add_player( player $player ) {
		$id = count( $this->players );
		$player->set_id( $id );
		$this->players[$id] = $player;
		return $this;
	}

	public function get_players() {
		return $this->players;
	}

	public function generate_battles() {
		set_time_limit(0);
		$this->player_count = count( $this->players );
		if ( $this->player_count === 0 ) {
			error::trigger('Players are required to play this game');
		}
		elseif ( $this->player_count === 1 ) {
			error::trigger('At least two players are required to play this game');
		}
		$this->deck = new deck;
		if ( ( $remove = ( deck::SIZE % $this->player_count ) ) !== 0 ) {
			$this->deck->remove_cards( $remove );
		}
		$this->deck->shuffle();
		$deck_chunks = array_chunk( $this->deck->get_cards(),$this->player_count );
		foreach( $deck_chunks as $cards ) {
			foreach( $cards as $i => $card ) {
				$this->players[$i]->add_card( $card );
			}
		}
		$this->stack = new stack;
		$i = $b = 1;
		$battles = array();
		$stats = array(
			'wars' => 0,
			'war_levels' => array()
		);
		while(true) {
			if ( $i++ > 2000 ) { //prevent infinite loop (which is a possibility with this game if cards are not shuffled upon putting them back in the deck)
				break;
			}
			$cards = array();
			foreach( $this->players as $p_i => $player ) {
				$card = $player->get_card( stack::POSITION_TOP );
				if ( $card === false ) {
					continue;
				}
				$this->stack->add_card( $card,stack::POSITION_TOP );
				$cards[$p_i] = $card;
			}
			$battles[$b]['cards'][] = $cards;
			if ( $this->war == true ) {
				$this->war_idx++;
				if ( $this->war_idx < $this->config['face_down_cards'] ) {
					continue;
				}
				if ( $this->war_idx == $this->config['face_down_cards'] ) {
					$this->stack->clear_weights();
					continue;
				}
			}
			list( $war,$winner ) = $this->stack->compare();
			if ( $war == true ) {
				if ( $this->war == true ) {
					$this->war_idx = 0;
					$this->war_level++;
					continue;
				}
				$this->war = true;
				$this->war_idx = 0;
				continue;
			}
			if ( $this->war == true ) {
				$stats['wars']++;
				$battles[$b]['war'] = $stats['wars'];
				if ( !isset( $stats['war_levels'][$this->war_level] ) ) {
					$stats['war_levels'][$this->war_level] = 0;
				}
				$stats['war_levels'][$this->war_level]++;
				$this->war = false;
				$this->war_idx = 0;
				$this->war_level = 1;
			}
			$battles[$b]['winner'] = $winner;
			$this->stack->shuffle(); //prevents infinite loop, maybe just adding one card from the top of the stack to the bottom might solve this problem, but i didn't try it
			$cards = $this->stack->get_cards();
			foreach( $cards as $card ) {
				$winner->add_card( $card );
			}
			$this->stack->clear_cards();
			$t = 0;
			$last = null;
			foreach( $this->players as $player ) {
				$battles[$b]['score'][$player->get_id()] = $player->card_count();
				if ( $player->get_status() !== self::PLAYING ) {
					continue;
				}
				if ( $player->card_count() == 0 && $player->get_id() !== $winner->get_id() ) {
					$this->player_count--;
					$player->set_status( self::LOST );
					$battles[$b]['lost'][] = $player;
					continue;
				}
				$last = $player;
				$t++;
			}
			if ( $t === 1 ) {
				$battles[$b]['won'] = $last;
				$stats['winner'] = $last;
				break;
			}
			$b++;
		}
		$this->battles = $battles;
		$this->stats = $stats;
		unset( $battles );
		unset( $stats );
	}

}
