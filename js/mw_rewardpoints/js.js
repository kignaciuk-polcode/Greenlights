var $j_mw = jQuery.noConflict();

$j_mw(function(){
	$j_mw("#transaction_box_hander").click(function(){
		$j_mw("#transaction_history_box").slideToggle();
		if(this.innerHTML =='Hide') this.innerHTML = 'Show';
		else this.innerHTML = 'Hide';
	});
});