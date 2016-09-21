jQuery(document).on('click', '.action', function( e ) {
	if( jQuery(this).attr('data-action') == undefined || jQuery(this).attr('data-action') == null ) {
		console.warn('Could not find action (data-action attr missing');
		return true;
	}
	e.preventDefault();
	switch( jQuery(this).attr('data-action') ) {
		case 'addPerson':
			jQuery(document).trigger('innslag.loadView', ['addPerson', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
			break;
		case 'editPerson':
			jQuery(document).trigger('innslag.loadView', ['editPerson', jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this).attr('data-person-id')] );
			break;
		case 'showNewPerson':
			jQuery('#filter_persons_create').slideDown();
			break;
		default:
			console.warn('Unknown action '+ jQuery(this).attr('data-action') );
			break;
	}
})
jQuery(document).on('click', 'button[type="submit"]', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.resetBody', [jQuery(this).parents('li.innslag').attr('data-innslag-id'), true]);
});

jQuery(document).on('click', '.clickChildLink :not(a)', function(e) {
	e.preventDefault();
	console.warn('Should find and click .momClickMe');
	return true;
});

/********** GUI INTERACTIONS ************ */
jQuery(document).on('click', '.innslag .header', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.toggleBody', jQuery(this).parents('li.innslag').attr('data-innslag-id') );
});
jQuery(document).on('click', '.innslag .body .actionEdit', function(e) {
//	e.preventDefault();
	jQuery(document).trigger('innslag.loadView', ['edit', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
});
jQuery(document).on('click', '.innslagResetBody', function(e){
//	e.preventDefault();
	jQuery(document).trigger('innslag.resetBody', jQuery(this).attr('data-innslag-id'));
});
jQuery(document).on('click', '.innslagResetAndReloadBody', function(e){
//	e.preventDefault();
	jQuery(document).trigger('innslag.resetBody', [jQuery(this).attr('data-innslag-id'), true]);
});

/* Legg til i hendelse */
jQuery(document).on('click', '.actionEventAdd', function(e){
//	e.preventDefault();
	jQuery(document).trigger('innslag.loadView', ['addToEvent', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
});

/********** FILTER LISTS ***************** */
jQuery(document).on('loadedView.twigJSpersonadd', function(){
	console.info('initate filter');
	jQuery('#filter_persons').fastLiveFilter('#filter_persons_results', {
												callback: function(total) { 
													if( 0 == total ) {
														jQuery('#filter_persons_create').slideDown();
													} else {
														jQuery('#filter_persons_create').slideUp();
													}
												}
											  }
											);
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
jQuery(document).on('innslag.loadView', function(e, view, innslag_id, object_id) {

	var innslag = jQuery('#innslag_'+ innslag_id);
	var body = innslag.find('.body');
	var data = {
					'action':'UKMdeltakere_ajax',
					'do': 'renderView',
					'innslag': innslag_id,
					'view': view,
					'object_id': object_id
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
	jQuery(document).trigger('loadedView.'+ server_response.twigJS );
});