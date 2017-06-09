/********** GUI-INTERAKSJONER ************ */
	/* Knyttet til liste-visningen			   */
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

	/* Knyttet til knapper					   */
		// Legg til i hendelse
		jQuery(document).on('click', '.actionEventAdd', function(e){
			e.preventDefault();
			jQuery(document).trigger('innslag.loadView', ['addToEvent', jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
		});

	/* Knyttet til .action-knapper			   */
	jQuery(document).on('click', '.action', function( e ) {
		if( jQuery(this).attr('data-action') == undefined || jQuery(this).attr('data-action') == null ) {
			console.warn('Could not find action (data-action attr missing');
			return true;
		}
		e.preventDefault();
	
		switch( jQuery(this).attr('data-action') ) {
			// LAGRING AV INNHOLD
			case 'save':
				jQuery(document).trigger('save', [jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
				break;
			// SLETTING AV INNHOLD
			case 'simpleSave':
				jQuery(document).trigger('simpleSave', [jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this)] );
				break;
	
			// HANDLINGER SOM KUN RELATERER SEG TIL INNSLAGET
			case 'addTitle':
			case 'addPerson':
			case 'changeContact':
			case 'meldPa':
				jQuery(document).trigger('innslag.loadView', [jQuery(this).attr('data-action'), jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
				break;
			
			// HANDLINGER SOM RELATERER SEG TIL ET SUB-OBJEKT (REDIGERING AV TITLER OSV)
			case 'editTitle':
				jQuery(document).trigger('innslag.loadView', ['editTitle', jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this).attr('data-title-id')] );
				break;
			case 'editPerson':
				jQuery(document).trigger('innslag.loadView', ['editPerson', jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this).attr('data-person-id')] );
				break;
			case 'addExistingPerson':
				jQuery(document).trigger('innslag.loadView', ['addExistingPerson', jQuery(this).parents('li.innslag').attr('data-innslag-id'), jQuery(this).attr('data-person-id')] );
				break;	
			
				// Vis skjema for en å opprette en ny person
			case 'showNewPerson':
				var sokefeltId = 'filter_persons_innslag_' + jQuery(this).parents('li.innslag').attr('data-innslag-id');
				jQuery(document).trigger('person.create.show', [sokefeltId, false]);
				break;
		
			// LUKK INNSLAGSBOKSEN
			case 'close':
				if( jQuery(this).parents('li.innslag').attr('data-innslag-id').substring(0,4) ==  'new_' ) {
					jQuery(this).parents('li.innslag').slideUp();
				}
				jQuery(document).trigger('innslag.hideBody', [jQuery(this).parents('li.innslag').attr('data-innslag-id')] );
				jQuery(document).trigger('innslag.goToView', jQuery(this).parents('li.innslag').attr('data-innslag-id'));
				break;
			
	
			// HANDLINGER RELATERT TIL NYE INNSLAG
			case 'nyttInnslag':
				var btn = e.target;
				jQuery(document).trigger('innslag.showNew', btn);
				break;
			case 'createPerson':
				jQuery(document).trigger('innslag.createPerson', jQuery(this).parents('li.innslag').attr('data-innslag-id'));
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
				jQuery(document).trigger('innslag.resetKontaktperson', jQuery(this).parents('li.innslag').attr('data-innslag-id'));
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
	
			default:
				console.warn('Unknown action '+ jQuery(this).attr('data-action') );
				break;
		}
	});
	/* Ved submit skal innslagsboksen reloades */
	/* MÅ skje etter .action-triggeren slik at formet fortsatt er der on submit */
	jQuery(document).on('click', 'button[type="submit"]', function(e){
		e.preventDefault();
		jQuery(document).trigger('innslag.resetBody', [jQuery(this).parents('li.innslag').attr('data-innslag-id'), true]);
	});

/********** FILTER-LISTS ***************** */
	// LEGG TIL PERSON (SØK)
	jQuery(document).on('loadedView.twigJSpersonadd', function(){
		jQuery('.filter_personer').each(function() {
			jQuery(this).fastLiveFilter(jQuery('#' + jQuery(this).attr('data-results')), {
													callback: function(total, id) { 
														if( 0 == total ) {
															jQuery(document).trigger('person.create.show', [id, true]);
														} else {
															jQuery(document).trigger('person.create.hide', [id, true]);
														}
													}
												  }
												);
										});
	});
	// FILTRER INNSLAGSLISTEN
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
	
	jQuery(document).on('person.create.show', function(e, id, noResults) {
		if( noResults ) {
			jQuery('#'+ id +'_noResults').show();
		}
		jQuery('#'+ id +'_create').fadeIn();
		jQuery('#'+ id +'_notInList').hide();
		
		// Sett inn data fra søkefeltet
		var input = jQuery('#'+ id).val();
	
		if( jQuery.isNumeric( input ) ) {
			jQuery('#'+ id +'_create .inputmobil').val( input );
		}
		else {
			var name = input.split(" ");
			var first_name = '';
			var last_name = '';
			if( name.length == 1 ) {
				first_name = name[0];
			}
			else if( name.length == 3 ) {
				first_name = name[0];
				last_name = name.splice(1,2).join(" ");
			}
			else {
				first_name = name.splice(0, Math.ceil(name.length/2)).join(" ");
				last_name = name.splice(name.length-Math.ceil(name.length/2), name.length).join(" ");
			}
			jQuery('#'+ id +'_create .inputfornavn').val(first_name);
			jQuery('#'+ id +'_create .inputetternavn').val(last_name);
			
		}
	});	
	
	jQuery(document).on('person.create.hide', function(e, id, noResults) {
		jQuery('#'+ id +'_noResults').hide();
		jQuery('#'+ id +'_create').fadeOut();
		jQuery('#'+ id +'_notInList').fadeIn();
	});
/********** HJELPE-FUNKSJONER ************ */
	/**
	 * innslag.goToView
	 * scroll til innslagsposisjon
	**/
	jQuery(document).on('innslag.goToView', function(e, innslag_id) {
		var innslag = jQuery("#innslag_"+innslag_id);
		if( null == innslag || null == innslag.offset() ) {
			return;
		}
		pos = innslag.offset().top - 175; // Gi oss 175 px margin fra toppen.
	    jQuery('html, body').animate({
	        scrollTop: pos + 'px'
	    }, 'fast');
	});
	/**
	 * clickChildLink
	 * Klikk på child-lenke inni på-klikket container
	**/
	/*jQuery(document).on('click', '.clickChildLink :not(a)', function(e) {
		e.preventDefault();
		console.warn('Should find and click .momClickMe');
		return true;
	});*/



/*** INNSLAG BODY-CONTAINER FUNKSJONER *** */
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
		
		// Hvis ikke lastet inn redigeringsview før, gjør det nå.
		if( 'false' == body.attr('data-load-state') ) {
			jQuery(document).trigger('innslag.loadView', 
								['overview', innslag.attr('data-innslag-id')]);
		} 
	});
	/**
	 * innslag.renderBody
	**/
	jQuery(document).on('innslag.renderBody', function(e, server_response ) {
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
	
	/**
	 * innslag.reloadHeader
	**/
	jQuery(document).on('innslag.reloadHeader', function(e, innslag_id){
		var data = {
						'action':'UKMdeltakere_ajax',
						'do': 'renderView',
						'innslag': innslag_id,
						'view': 'header',
					}	
		jQuery.post(ajaxurl, data, function(response) {
			if(response.success) {
				console.log(response);
				// TODO: RENDER VIEW IN CONTAINER HEADER
			}
		});
		
	});	


/******** INNSLAG VIEW-FUNKSJONER ******** */
	/**
	 * innslag.loadView
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

/********** LAGRINGS-FUNKSJONER ********** */
	/**
	 * save
	 * Håndterer lagring av skjema
	**/
	jQuery(document).on('save', function(e, innslag_id){
		console.info( innslag_id );
		var form = jQuery('#innslag_'+ innslag_id).find('.body').find('form');
		var data = {
						'action':'UKMdeltakere_ajax',
						'do': 'save',
						'doSave': form.attr('action'),
						'innslag': innslag_id,
						'formData': form.serializeArray(),
						'object_id': form.attr('data-object-id')
					}
		jQuery(document).trigger('saveAjax', [innslag_id, data]);
	
	});
	
	/**
	 * Simplesave 
	 * Håndterer lagring fra knapper (ikke skjema)
	 */
	jQuery(document).on('simpleSave', function(e, innslag_id, clicked) {
		var warning = clicked.attr('data-warning');
		if( null != warning) {
			if( !confirm(warning) ) {
				return;
			}	
		}
		var data = {
						'action':'UKMdeltakere_ajax',
						'do': 'save',
						'doSave': clicked.attr('data-handle'),
						'innslag': innslag_id,
						'object_id': clicked.attr('data-object-id')
					}
		jQuery(document).trigger('saveAjax', [innslag_id, data]);
	});
	
	/**
	 * saveAjax
	 * Faktisk håndterer lagring og respons
	 * Samme handler for save og simpleSave
	**/
	jQuery(document).on('saveAjax', function(e, innslag_id, data) {
		var innslag = jQuery('#innslag_'+ innslag_id);
		innslag.find('.body').html('<p>Vennligst vent, lagrer...</p>').attr('data-load-state', 'false');
		jQuery.post(ajaxurl, data, function(response) {
			if( response.success === false ) {
				alert('Beklager, en feil oppsto på serveren! ' +"\r\n" + response.message );
				jQuery(document).trigger('innslag.resetBody', response.innslag_id );
			}
			else if( response.success ) {
				if( null != response.redirect && response.redirect ) {
					window.location.href = response.redirect;
					return;
				}
				// Hvis dette er skjema for et nytt innslag
				if( response.innslag_id.substring(0,4) ==  'new_' ) {
					var rendered = eval( 'twigJS'+ response.twigJS + '.render( response )' );
					jQuery('#innslag_'+ response.innslag_id).find('.body').html( rendered );
					jQuery('#innslag_'+ response.innslag_id).find('.body').slideDown();
					jQuery(document).trigger('loadedView.twigJSpersonadd'); // Start filter-funksjonen
				} else {
					// Hvis dette var en avmeldingsforespørsel som gikk i orden, skjul hele elementet fra listen
					if( null != response.meldtAv ) {
						jQuery('#innslag_'+ response.innslag_id).slideUp();
					}
					jQuery(document).trigger('innslag.resetBody', [response.innslag_id, true] );
					jQuery(document).trigger('innslag.reloadHeader', [response.innslag_id]);
				}
			}
			else {
				alert('Beklager, klarte ikke å hente informasjon fra server!');
				jQuery(document).trigger('innslag.resetBody', response.innslag_id );
				console.log( response );
			}
		});
	});

/******** NYTT INNSLAG-FUNKSJONER ******** */
	/**
	 * GUI: Klikk på knappen for å legge til nytt innslag
	**/
	jQuery(document).on('innslag.showNew', function(e, button) {
		var action = jQuery(button).attr('data-action');
		var type = jQuery(button).attr('data-type');
		var innslag_id = 'new_' + type;
		var innslag = jQuery('#innslag_'+ innslag_id);
		
		innslag.find(".body").html('<p>Vennligst vent, laster inn skjema...</p>').attr('data-load-state', 'false');
		innslag.slideDown();
	
		var data = {
					'action':'UKMdeltakere_ajax',
					'do': 'renderView',
					'view': 'innslag_new',
					'type': type,
					'innslag': 'new_'+ type
				}
	
		jQuery(document).trigger('saveAjax', [innslag_id, data]);
	});
	
	/**
	 * Velg kontaktpersonen
	**/
	jQuery(document).on('innslag.addKontaktperson', function(e, selected) {
		var innslag = jQuery( selected ).parents("li.innslag");
		var person = jQuery( selected );
		
		// Sett verdier
		innslag.find('.kontaktperson').val( person.attr('data-person-id') );
		innslag.find('.kontaktpersonNavn').html( person.html() );
		
		// Skjul søkeside og opprett person-skjema
		innslag.find('.searchPerson').slideUp();
		innslag.find('.createPerson').slideUp();
		
		// Vis valgt person
		innslag.find('.selectedPerson').slideDown();

		// Vis rolle-spørsmål
		jQuery(document).trigger('kontaktperson.valgt', innslag.attr('data-innslag-id'));
	});
	
	/**
	 * Ombestem deg og velg kontaktperson på nytt
	**/
	jQuery(document).on('innslag.resetKontaktperson', function(e, innslag_id){
		var innslag = jQuery('#innslag_'+ innslag_id );
		// Skjul valgt person
		innslag.find('.selectedPerson').slideUp();
		// Vis søkeliste
		innslag.find('.searchPerson').slideDown();
		// Skjul rolle-spørsmål
		jQuery(document).trigger('kontaktperson.angreValgt', innslag.attr('data-innslag-id'));
	});
	
	/**
	 * Opprett en kontaktperson
	**/
	jQuery(document).on('innslag.createPerson', function(e, innslag_id) {
		var innslag = jQuery('#innslag_'+ innslag_id );

		innslag.find('.searchPerson').slideUp();
		innslag.find('.selectedPerson').slideUp();
		innslag.find('.createPerson').slideDown();
		
		// Vis rolle-spørsmål
		jQuery(document).trigger('kontaktperson.valgt', innslag.attr('data-innslag-id'));
	});
	
	/**
	 * Vis rolle-spørsmål for kontaktperson
	**/
	jQuery(document).on('kontaktperson.valgt', function(e, innslag_id){
		jQuery('.kontaktpersonRolleValg').slideDown();
	});
	/**
	 * Skjul rolle-spørsmål for kontaktperson
	**/
	jQuery(document).on('kontaktperson.angreValgt', function(e, innslag_id){
		jQuery('.kontaktpersonRolleValg').slideUp();
	});
	
	/**
	 * Vis / skjul selve rolle-feltet avhengig av checkbox i rolle-spørsmål
	**/
	jQuery(document).on('click', '.kontaktpersonErMed', function(e){
		var innslag = jQuery(this).parents('li.innslag');
		
		if( innslag.find('input[type=checkbox]').is(':checked') ) {
			innslag.find('.kontaktpersonRolle').slideDown();
		} else {
			innslag.find('.kontaktpersonRolle').slideUp();
		}
	});
	
	
	
	
	
	
	