<form action="<?php echo home_url(); ?>/" id="searchform" class="searchform" method="get">
	<input type="text" id="s" name="s" value="<?php _e('Search', 'envirra') ?>" onfocus="if(this.value=='<?php _e('Search', 'envirra') ?>')this.value='';" onblur="if(this.value=='')this.value='<?php _e('Search', 'envirra') ?>';" autocomplete="off" />
	<button class="search-button"><i class="icon-entypo-search"></i></button>
</form>