jQuery(document).on('click', '.action', function( e ) {
	if( jQuery(this).attr('data-action') == undefined || jQuery(this).attr('data-action') == null ) {
		console.warn('Could not find action (data-action attr missing');
		return true;
	}
	e.preventDefault();

	switch( jQuery(this).attr('data-action') ) {

		// GENERISK LAGRINGSFUNKSJON
		case 'save':
			jQuery(document).trigger('save', [jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
			break;

		case 'simpleSave':
			jQuery(document).trigger('simpleSave', [jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this)] );
			break;


		// TITLER
		case 'addTitle':
			jQuery(document).trigger('innslag.loadView', ['addTitle', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
			break;
		case 'editTitle':
			jQuery(document).trigger('innslag.loadView', ['editTitle', jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this).attr('data-title-id')] );
			break;

		// PERSONER
		case 'addPerson':
			jQuery(document).trigger('innslag.loadView', ['addPerson', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
			break;
		case 'editPerson':
			jQuery(document).trigger('innslag.loadView', ['editPerson', jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this).attr('data-person-id')] );
			break;
		case 'showNewPerson':
			// TODO: Flytt til en funksjon, og støtt både fornavn og etternavn, eller mobil hvis det er bare tall?
			var form = jQuery(e.target).closest("form");
			var first_name = jQuery("#" + form.attr('id') + " #fornavn_sok").val();
			jQuery("#" + form.attr('id') + " #fornavn").val(first_name);
			jQuery('#'+ jQuery(this).attr('data-target')).fadeIn();
			break;
		case 'editContact':
			jQuery(document).trigger('innslag.loadView', ['changeContact', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
			break;
		case 'addExistingPerson':
			jQuery(document).trigger('innslag.loadView', ['addExistingPerson', jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this).attr('data-person-id')] );
			break;
		
		// NYTT INNSLAG
		case 'nyttInnslag':
			var btn = e.target;
			jQuery(document).trigger('innslag.showNew', btn);
			break;
		case 'addKontaktperson':
			var btn = e.target;
			if(btn.class != 'clickable') {
				// Det hender at man trykker på mobilnummeret og ikke boksen, så da finner vi boksen:
				btn = btn.closest('li');
			}
			jQuery(document).trigger('innslag.addKontaktperson', btn);
			break;
		case 'resetKontaktperson':
			var form = jQuery(e.target).closest('form');
			jQuery(document).trigger('innslag.resetKontaktperson', form);
			break;
		case 'saveNyttInnslag':
			var type = jQuery(this).attr('data-type');
			var form = jQuery("#nyttInnslagContainer_"+type);
			jQuery(document).trigger('innslag.saveNew', form);
			break;
		case 'closeNyttInnslag':
			var container = jQuery(e.target).attr('data-target');
			jQuery(document).trigger('innslag.resetNew', container);
			break;

		// INNSLAG
		case 'close':
			jQuery(document).trigger('innslag.hideBody', [jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
			jQuery(document).trigger('innslag.goToView', jQuery(this).parents('li.innslag').attr('data-innslag-id'));
			break;
		case 'meldPa':
			jQuery(document).trigger('innslag.loadView', ['meldPa', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
			break;
		default:
			console.warn('Unknown action '+ jQuery(this).attr('data-action') );
			break;
	}
});

jQuery(document).on('click', 'button[type="submit"]', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.resetBody', [jQuery(this).parents('li.innslag').attr('data-innslag-id'), true]);
});

/*jQuery(document).on('click', '.clickChildLink :not(a)', function(e) {
	e.preventDefault();
	console.warn('Should find and click .momClickMe');
	return true;
});*/

/********** GUI INTERACTIONS ************ */
jQuery(document).on('click', '.innslag .header', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.toggleBody', jQuery(this).parents('li.innslag').attr('data-innslag-id') );
});
jQuery(document).on('click', '.innslag .body .actionEdit', function(e) {
	e.preventDefault();
	jQuery(document).trigger('innslag.loadView', ['edit', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
});
jQuery(document).on('click', '.innslagResetBody', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.resetBody', jQuery(this).attr('data-innslag-id'));
});
jQuery(document).on('click', '.innslagResetAndReloadBody', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.resetBody', [jQuery(this).attr('data-innslag-id'), true]);
});
jQuery(document).on('innslag.goToView', function(e, innslag_id) {
	var pos = jQuery("#innslag_"+innslag_id).offset().top;
	pos = pos - 175; // Gi oss 175 px margin fra toppen.
    jQuery('html, body').animate({
        scrollTop: pos + 'px'
    }, 'fast');
});

/* Legg til i hendelse */
jQuery(document).on('click', '.actionEventAdd', function(e){
	e.preventDefault();
	jQuery(document).trigger('innslag.loadView', ['addToEvent', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
});


/********** FILTER LISTS ***************** */
jQuery(document).on('loadedView.twigJSpersonadd', function(){
	jQuery('.filter_personer').each(function() {
		jQuery(this).fastLiveFilter(jQuery('#' + jQuery(this).attr('data-results')), {
												callback: function(total, id) { 
													if( 0 == total ) {
														jQuery('#'+ id +'_create').fadeIn();
													} else {
														jQuery('#'+ id +'_create').fadeOut();
													}
												}
											  }
											);
									});
});

jQuery(document).ready(function(){
	jQuery('#filter_innslag').each(function() {
		jQuery(this).fastLiveFilter('.innslag_lister', {
												callback: function(total, id) { 
													jQuery('#filter_innslag_counter').fadeIn().html(''+ total +' innslag i listen nedenfor');
												}
											  }
									);
	});
});

jQuery(document).on('innslag.showNewFilter', function(){
	jQuery('.filter_personer').each(function() {
		jQuery(this).fastLiveFilter(jQuery('#' + jQuery(this).attr('data-results')), {
												callback: function(total, id) { 
													if( 0 == total ) {
														jQuery('#'+ id +'_create').fadeIn();
													} else {
														jQuery('#'+ id +'_create').fadeOut();
													}
												}
											  }
											);
									});
});


/********** NEW INNSLAG FUNCTIONS ******** */
jQuery(document).on('innslag.showNew', function(e, button) {
	var action = jQuery(button).attr('data-action');
	var type = jQuery(button).attr('data-type');
	var body = jQuery("#newInnslagBox_"+type);

	body.slideDown();

	jQuery(document).trigger('innslag.loadNew', [type, body]);
	
});
jQuery(document).on('click', '#kontaktpersonErMed', function(e) {
	var form = jQuery(e.target).closest("form");
	jQuery("#"+form.attr('id') + " #rolle_box").slideToggle();
});

jQuery(document).on('innslag.loadNew', function(e, type, body) {
	body = jQuery(body);
	var data = {
					'action':'UKMdeltakere_ajax',
					'do': 'renderView',
					'view': type
				}
	body.find(".body").html('<p>Vennligst vent, laster inn skjema...</p>').attr('data-load-state', 'false');

	jQuery.post(ajaxurl, data, function(response) {
		if( response.success === false ) {
			alert('Beklager, en feil oppsto på serveren! ' +"\r\n" + response.message );
			jQuery(document).trigger('innslag.resetNew', body);
		}
		else if( response.success ) {
			jQuery(document).trigger('innslag.renderNewForm', [body, response]);
			jQuery(document).trigger('innslag.showNewFilter');
		}
		else {
			alert('Beklager, klarte ikke å hente informasjon fra server!');
			console.log( response );
			jQuery(document).trigger('innslag.resetNew', body);
		}
	});
});

jQuery(document).on('innslag.resetNew', function(e, container) {
	if( !jQuery(container).is('ol') ) {
		container = container.closest("ol");
	}

	jQuery(container).slideUp();
	jQuery(container).find('.body').html = "";
});

jQuery(document).on('innslag.addKontaktperson', function(e, sel) {
	var form = jQuery(sel).closest("form");
	var formID = jQuery(form).attr('id');
	var person_id = jQuery(sel).attr('data-person-id');
	var person = jQuery(sel).html();

	jQuery("#"+formID + " #kontaktperson_felt").show();
	jQuery("#"+formID + " #kontaktperson_id").val(person_id);
	jQuery("#"+formID + " #kontaktperson_info").html(person);
	jQuery(document).trigger('innslag.lukkPersonliste', form);
});

jQuery(document).on('innslag.resetKontaktperson', function(e, form) {
	var formID = jQuery(form).attr('id');
	jQuery("#"+formID + " #kontaktperson_id").val('');
	jQuery("#"+formID + " #kontaktperson_info").html('');
	jQuery("#"+formID + " #kontaktperson_felt").hide();
	jQuery("#"+formID + " #sokefelt").show();
});
jQuery(document).on('innslag.lukkPersonliste', function(e, form) {
	var formID = jQuery(form).attr('id');
	jQuery("#"+formID + " #sokefelt").hide();
});

jQuery(document).on('innslag.saveNew', function(e, container) {
	var container = jQuery(container); // Nødvendig?
	var form = container;
	var data = {
					'action':'UKMdeltakere_ajax',
					'do': 'save', 
					'doSave': 'nyttInnslag',
					'formData': form.serializeArray()
				};

	container.html('<p>Vennligst vent, lagrer...</p>').attr('data-load-state', 'false');

	jQuery.post(ajaxurl, data, function(response) {
		if( response.success === false ) {
			alert('Beklager, en feil oppsto på serveren! ' +"\r\n" + response.message );
		}
		else if( response.success ) {
			// Skjul skjema
			jQuery(document).trigger('innslag.resetNew', container);
			// Trigger ny innlasting av listen.
			jQuery(document).trigger('innslag.newHeader', [response.innslag_id, response.type]);
		}
		else {
			alert('Beklager, klarte ikke å hente informasjon fra server!');
			console.log( response );
		}
	}).error( function(error) { 
		console.log("AJAX error: ");
		console.log(error);
	});
});

/**
 * innslag.renderBody
**/
jQuery(document).on('innslag.renderNewForm', function(e, body, server_response ) {
	if( undefined == server_response.view || null == server_response.view ) {
		alert('Beklager, en feil har oppstått. Ukjent view '+ server_response.view );
	}
	
	var rendered = eval( 'twigJS'+ server_response.twigJS + '.render( server_response )' );

	jQuery(body).find('.body').html( rendered );
});

/********** HEADER CONTAINER FUNCTIONS ***** */
jQuery(document).on('innslag.resetHeader', function(e, innslag_id ) {
	var container = jQuery("#innslag_" + innslag_id);
});

jQuery(document).on('innslag.newHeader', function(e, innslag_id, type) {
	//console.log('Adding new header...');
	var list = jQuery("#innslag_liste_"+type.key);
	var li = '<li id="innslag_'+innslag_id+'" class="list-group-item innslag">';
	li += '<div class="header clickable row"></div>';
	li += '<div class="body" data-load-state="false" style="display:none;">Vennligst vent... </div>';
	li += '<div class="clearfix"></div></div></li>';

	// Hvis vi har innslag i boksen, legg til ny boks, hvis ikke, bytt ut "Ingen påmeldte" med ny boks.
	if( list.children.length > 1 ) {
		list.append(li);
	}
	else {
		list.html(li);
	}

	// Hent inn data.
	jQuery(document).trigger('innslag.loadHeader', innslag_id);
});

jQuery(document).on('innslag.loadHeader', function(e, innslag_id) {
	//console.log('Loading header-data...');
	var container = jQuery("#innslag_" + innslag_id);
	var header = container.find('.header');
	var data = {
					'action':'UKMdeltakere_ajax',
					'do': 'renderView',
					'innslag': innslag_id,
					'view': 'header'
				}

	jQuery.post(ajaxurl, data, function(response) {
		if( response.success === false ) {
			alert('Beklager, en feil oppsto på serveren! ' +"\r\n" + response.message );
			//jQuery(document).trigger('innslag.resetBody', response.innslag_id );
		}
		else if( response.success ) {
			jQuery(document).trigger('innslag.renderHeader', [container, response]);
		}
		else {
			alert('Beklager, klarte ikke å hente informasjon fra server!');
			//jQuery(document).trigger('innslag.resetBody', response.innslag_id );
			console.log( response );
		}
	}).error( function(error) { 
		console.log("AJAX error: ");
		console.log(error);
	});
});

/**
 * innslag.renderHeader
**/
jQuery(document).on('innslag.renderHeader', function(e, container, server_response ) {
	var header = jQuery(container).find(".header");
	jQuery(header).html("<p>Vennligst vent...</p>");

	var rendered = eval( 'twigJS'+ server_response.twigJS + '.render( server_response )' );
	jQuery(header).html( rendered );

	jQuery(container).attr('data-innslag-id', server_response.innslag.id );
	jQuery(container).attr('data-innslag-type', server_response.innslag.type.key );
	jQuery(container).attr('data-filter', server_response.filter );

	jQuery(document).trigger('innslag.goToView', server_response.innslag_id);

	// Utvid skjema:
	jQuery(document).trigger('innslag.resetBody', server_response.innslag_id);
	jQuery(document).trigger('innslag.toggleBody', server_response.innslag_id);
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
 * Simple save for deleting stuff etc
 *
 */
jQuery(document).on('simpleSave', function(e, innslag_id, clicked){
	var innslag = jQuery('#innslag_'+ innslag_id);
	var body = innslag.find('.body');
	var data = {
					'action':'UKMdeltakere_ajax',
					'do': 'save',
					'doSave': clicked.attr('data-handle'),
					'innslag': innslag_id,
					'object_id': clicked.attr('data-object-id')
				}

	body.html('<p>Vennligst vent, lagrer...</p>').attr('data-load-state', 'false');
	
	jQuery.post(ajaxurl, data, function(response) {
		if( response.success === false ) {
			alert('Beklager, en feil oppsto på serveren! ' +"\r\n" + response.message );
			jQuery(document).trigger('innslag.resetBody', response.innslag_id );
		}
		else if( response.success ) {
			jQuery(document).trigger('innslag.resetBody', [response.innslag_id, true] );

			// Hvis dette var en avmeldingsforespørsel som gikk i orden, skjul hele elementet fra listen
			if ( 'meldAvInnslag' == clicked.attr('data-handle') ) {
				innslag.slideUp();
			}
		}
		else {
			alert('Beklager, klarte ikke å hente informasjon fra server!');
			jQuery(document).trigger('innslag.resetBody', response.innslag_id );
			console.log( response );
		}
	});
});


jQuery(document).on('save', function(e, innslag_id){
	var innslag = jQuery('#innslag_'+ innslag_id);
	var body = innslag.find('.body');
	var form = body.find('form');
	var data = {
					'action':'UKMdeltakere_ajax',
					'do': 'save',
					'doSave': form.attr('action'),
					'innslag': innslag_id,
					'formData': form.serializeArray(),
					'object_id': form.attr('data-object-id')
				}

	body.html('<p>Vennligst vent, lagrer...</p>').attr('data-load-state', 'false');
	
	jQuery.post(ajaxurl, data, function(response) {
		if( response.success === false ) {
			alert('Beklager, en feil oppsto på serveren! ' +"\r\n" + response.message );
			jQuery(document).trigger('innslag.resetBody', response.innslag_id );
		}
		else if( response.success ) {
			jQuery(document).trigger('innslag.resetBody', [response.innslag_id, true] );
		}
		else {
			alert('Beklager, klarte ikke å hente informasjon fra server!');
			jQuery(document).trigger('innslag.resetBody', response.innslag_id );
			console.log( response );
		}
	});
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
	console.log(server_response);
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