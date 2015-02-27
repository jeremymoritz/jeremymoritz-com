var setup_form = {
	count: function( val ) {
		var count = $('#player-count');
		if ( typeof val !== 'undefined' ) {
			count.val( val );
			return;
		}
		return ( parseInt( count.val() ) + 1 );
	},
	init: function() {
		var that = this;
		$('#player-add').click(function(e) {
			e.preventDefault();
			var count = that.count();
			$('#players .player:last').after('<div id="player-' + count + '" class="player">\
	<a class="remove" href="">Remove</a>\
	<h3>Player ' + count + '</h3>\
	<div class="field">\
		<div class="label l_1"><label for="players-' + count + '-name">Name</label></div>\
		<div class="element e_1"><input type="text" class="input_text" name="players[' + count + '][name]" id="players-' + count + '-name" value="" /></div>\
		<div class="clear"></div>\
	</div>\
</div>');
			that.setup_last();
			$('#player-' + count + ' .remove').click(function(e) {
				e.preventDefault();
				that.remove( $(this) );
				return false;
			});
			that.count( count );
			return false;
		});
		$('#players .remove').click(function(e) {
			e.preventDefault();
			that.remove( $(this) );
			return false;
		});
	},
	remove: function( obj ) {
		obj.parent().remove();
		var ncount = 0;
		$('#players .player').each(function(i) {
			var $this = $(this);
			ncount = ( i + 1 );
			$this.attr('id','player-' + ncount);
			$this.find('h3').text('Player ' + ncount);
			$this.find('label').attr('for','players-' + ncount + '-name');
			$this.find('input[type="text"]').attr('name','players[' + ncount + '][name]').attr('id','players-' + ncount + '-name');
		});
		this.setup_last();
		this.count( ncount );
		return false;
	},
	setup_last: function() {
		$('#players .player').removeClass('last');
		$('#players .player:last').addClass('last');
	}
};
$(document).ready(function() {
	setup_form.init();
});