jQuery(document).ready(function($) {
	// settings tabs
	//When page loads…
	$(".tab_content").hide(); //Hide all content
	$("h2.nav-tab-wrapper a:first").addClass("nav-tab-active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	$('h2.nav-tab-wrapper a').click(function(e) {				
		e.preventDefault();
		var tab = $(this).attr('href');
		$( 'h2.nav-tab-wrapper a' ).removeClass( 'nav-tab-active' );
		$(this).addClass( 'nav-tab-active' );
		$(".tab_content").hide();		
		$("#tab_container " + tab).fadeIn();	
	});
	
	$('#media-items').bind('DOMNodeInserted',function(){
		$('input[value="Insert into Post"]').each(function(){
				$(this).attr('value','Use This Image');
		});
	});

	$('.repeatable-add').click(function() {
		field = $(this).closest('td').find('.custom_repeatable li:last').clone(true);
		fieldLocation = $(this).closest('td').find('.custom_repeatable li:last');
		$('input', field).val('').attr('name', function(index, name) {
			return name.replace(/(\d+)/, function(fullMatch, n) {
				return Number(n) + 1;
			});
		})
		field.insertAfter(fieldLocation, $(this).closest('td'))
		return false;
	});
	
	$('.repeatable-remove').click(function(){
		$(this).parent().remove();
		return false;
	});

	jQuery('.custom_repeatable').sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.sort'
	});
	
	
});

