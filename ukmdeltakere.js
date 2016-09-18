/********** GUI INTERACTIONS ************ */
jQuery(document).on('click', '.innslag .header', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.toggleBody', jQuery(this).parents('li').attr('data-innslag-id') );
});
jQuery(document).on('click', '.innslag .body .actionEdit', function(e) {
	e.preventDefault();
	jQuery(document).trigger('innslag.loadView', ['edit', jQuery(this).parents('li').attr('data-innslag-id')] );
});
jQuery(document).on('click', '.innslagResetBody', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.resetBody', jQuery(this).attr('data-innslag-id'));
});
jQuery(document).on('click', '.innslagResetAndReloadBody', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.resetBody', [jQuery(this).attr('data-innslag-id'), true]);
});

/********** BODY CONTAINER FUNCTIONS ***** */
/**
 * innslag.toggleBody
**/
jQuery(document).on('innslag.toggleBody', function(e, innslag_id ) {
	var body = jQuery( '#innslag_'+ innslag_id ).find('.body');
	if( body.is(':visible') ) {
		jQuery(document).trigger('innslag.hideBody', innslag_id);
	} else {
		jQuery(document).trigger('innslag.showBody', innslag_id);
	}
});
/**
 * innslag.hideBody
**/
jQuery(document).on('innslag.hideBody', function(e, innslag_id) {
	jQuery( '#innslag_'+ innslag_id ).find('.body').slideUp();
	jQuery( '#innslag_'+ innslag_id ).removeClass('selected');
	
});
/**
 * innslag.resetBody
**/
jQuery(document).on('innslag.resetBody', function(e, innslag_id, reload) {
	jQuery( '#innslag_'+ innslag_id ).find('.body').attr('data-load-state', 'false');
	if( undefined == reload || null == reload || false == reload ) {
		jQuery(document).trigger('innslag.hideBody', innslag_id);
	} else {
		jQuery(document).trigger('innslag.showBody', innslag_id);
	}
});
/**
 * innslag.showBody
**/
jQuery(document).on('innslag.showBody', function(e, innslag_id) {
	var innslag = jQuery('#innslag_'+ innslag_id);
	var body = innslag.find('.body');
	
	innslag.addClass('selected');
	body.slideDown();
	
	// Hvis ikke lastet inn redigeringsvisning før, gjør det nå.
	if( 'false' == body.attr('data-load-state') ) {
		jQuery(document).trigger('innslag.loadView', 
							['overview', innslag.attr('data-innslag-id')]);
	} 
});
/**
 * innslag.loadBody
 * called by innslag.showBody if body attr(data-load-state') == 'false'
**/
jQuery(document).on('innslag.loadView', function(e, view, innslag_id) {

	var innslag = jQuery('#innslag_'+ innslag_id);
	var body = innslag.find('.body');
	var data = {
					'action':'UKMdeltakere_ajax',
					'do': 'renderView',
					'innslag': innslag_id,
					'view': view
				}
	body.html('<p>Vennligst vent, laster inn...</p>').attr('data-load-state', 'false');

	jQuery.post(ajaxurl, data, function(response) {
		if( response.success === false ) {
			alert('Beklager, en feil oppsto på serveren! ' +"\r\n" + response.message );
			jQuery(document).trigger('innslag.resetBody', response.innslag_id );
		}
		else if( response.success ) {
			jQuery(document).trigger('innslag.renderBody', [response])
		}
		else {
			alert('Beklager, klarte ikke å hente informasjon fra server!');
			jQuery(document).trigger('innslag.resetBody', response.innslag_id );
			console.log( response );
		}
	});
});
/**
 * innslag.renderBody
**/
jQuery(document).on('innslag.renderBody', function(e, server_response ) {
	console.log( server_response );
	if( undefined == server_response.view || null == server_response.view ) {
		alert('Beklager, en feil har oppstått. Ukjent view '+ server_response.view );
	}
	if( undefined == server_response.innslag_id || null == server_response.innslag_id ) {
		alert('Beklager, en feil har oppstått. Ukjent innslag '+ server_response.innslag_id );
	}
	
	var rendered = eval( 'twigJS'+ server_response.twigJS + '.render( server_response )' );
	jQuery('#innslag_'+ server_response.innslag_id).find('.body').attr('data-load-state','true').html( rendered );
});