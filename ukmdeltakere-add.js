

jQuery(document).on('innslag.showTittellosForm', function(e, sel) {
	if( !jQuery(sel).is('ol') ) {
		sel = sel.closest('ol');
	}

	// Finn ut om vi er i arrangør eller media-modus:
	var id = jQuery(sel).attr('id').split("_");
	var type = id[id.length-1];
	
	jQuery("#newInnslagForm_"+type).slideDown();
});

jQuery(document).on('innslag.hideTittellosForm', function(e, sel) {
	if( !jQuery(sel).is('form') ) {
		sel = sel.closest('form');
	}
	// Finn ut om vi er i arrangør eller media-modus:
	var id = jQuery(sel).attr('id').split("_");
	var type = id[id.length-1];
	jQuery("#newInnslagForm_"+type).hide();
});





/********** HEADER CONTAINER FUNCTIONS ***** */
jQuery(document).on('innslag.resetHeader', function(e, server_response ) {
	var rendered = eval( 'twigJSoverviewlistItem.render( server_response )' );
	jQuery("#innslag_" + innslag_id).html( rendered );
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
			jQuery(document).trigger('innslag.goToView', response.innslag_id);
			// Utvid skjema:
			jQuery(document).trigger('innslag.resetBody', response.innslag_id);
			jQuery(document).trigger('innslag.toggleBody', response.innslag_id);
		}
		else {
			alert('Beklager, klarte ikke å hente informasjon fra server!');
			//jQuery(document).trigger('innslag.resetBody', response.innslag_id );
			//console.log( response );
		}
	}).error( function(error) { 
		//console.log("AJAX error: ");
		//console.log(error);
	});
});

/**
 * innslag.renderHeader
**/
jQuery(document).on('innslag.renderHeader', function(e, server_response ) {
	var header = jQuery('#'+ server_response.innslag.id).find(".header");
	jQuery(header).html("<p>Vennligst vent...</p>");

	var rendered = eval( 'twigJS'+ server_response.twigJS + '.render( server_response )' );
	jQuery(header).html( rendered );

	jQuery(container).attr('data-innslag-id', server_response.innslag.id );
	jQuery(container).attr('data-innslag-type', server_response.innslag.type.key );
	jQuery(container).attr('data-filter', server_response.filter );

});









		jQuery(document).on('innslag.showNewPerson', function(e) {
			/*console.log("showNewPerson:");*/
			// TODO: Flytt til en funksjon
			var form = jQuery(e.target).closest("form");
			// Først trigge visning av skjema:
			jQuery(document).trigger('innslag.showTittellosForm', form);

			var input = jQuery("#" + form.attr('id') + " #fornavn_sok").val();
			// Er det mobilnummer eller navn vi søker på?
			if( jQuery.isNumeric( input ) ) {
				//console.log('Numeric!');
				jQuery("#"+ form.attr('id') + " #mobil").val(input);
			}
			else {
				//console.log('NotNum');
				var name = input.split(" ");
				var first_name = '';
				var last_name = '';
				if( name.length == 3 ) {
					first_name = name[0];
					last_name = name.splice(1,2).join(" ");
					/*console.log("First name: "+first_name);
					console.log("Lastname: "+last_name);*/
				} else {
					//console.log("Math.floor(name.length/2)= "+Math.floor(name.length/2));
					first_name = name.splice(0, Math.floor(name.length/2)).join(" ");
					last_name = name.splice(Math.floor(name.length/2), name.length).join(" ");
					/*console.log("First name: "+first_name);
					console.log("Lastname: "+last_name);*/
				}
				jQuery("#" + form.attr('id') + " #fornavn").val(first_name);
				jQuery("#" + form.attr('id') + " #etternavn").val(last_name);
				
			}
			jQuery('#'+ jQuery(this).attr('data-target')).fadeIn();	
		});