// jQuery-fallback for HTML5-placeholder.
jQuery(function(){
	
    // check placeholder browser support
    if (!Modernizr.input.placeholder){
    
        // set placeholder values
        jQuery(this).find('[placeholder]').each(function(){
            if (jQuery(this).val() == ''){ // if field is empty
                jQuery(this).val(jQuery(this).attr('placeholder') );
            }
        });
        
        // focus and blur of placeholders
        jQuery('[placeholder]').focus(function(){
            if (jQuery(this).val() == jQuery(this).attr('placeholder')){
                jQuery(this).val('');
                jQuery(this).removeClass('placeholder');
            }
        }).blur(function(){
            if (jQuery(this).val() == '' || jQuery(this).val() == jQuery(this).attr('placeholder')){
                jQuery(this).val(jQuery(this).attr('placeholder'));
                jQuery(this).addClass('placeholder');
            }
        });
        
        // remove placeholders on submit
        jQuery('[placeholder]').closest('form').submit(function(){
            jQuery(this).find('[placeholder]').each(function(){
                if (jQuery(this).val() == jQuery(this).attr('placeholder')){
                    jQuery(this).val('');
                }
            })
        });
    }
    
});

function getHideFunc(htmlelement){
	if (jQuery(htmlelement).attr('data-slide') == 'true'){
		var hide = function(html){jQuery(html).slideUp()}
	} else {
		var hide = function(html){jQuery(html).hide()}
	}	
	return hide;
}

function getShowFunc(htmlelement){
	if (jQuery(htmlelement).attr('data-slide') == 'true'){
		var show = function(html){jQuery(html).slideDown()}
	} else {
		var show = function(html){jQuery(html).show()}
	}
	return show;	
}

// Filter the elements of attr "data-children" by the value of the input field. 
// If a grouping parent is given by the attr "data-parent", they will be hidden if they have no visible items.
// The function must be binded to a input field.
function filter_list(){
	var parent = jQuery(this).attr('data-parent');
	var children = jQuery(this).attr('data-children');
	var searchText = jQuery(this).val().toLowerCase();
	var isEmpty = true;
	var show = getShowFunc(this);
	var hide = getHideFunc(this);
	if (parent !== undefined){
		jQuery(parent).each(function(){
			var hasAtLeastOne = false;
			jQuery(this).find(children).each(function(){
				var searchData = getSearchData(this);					
				if (searchData.indexOf(searchText) != -1){
					show(this);
					hasAtLeastOne = true;
					isEmpty = false;
				} else {
					hide(this);
				}
			});
			if (hasAtLeastOne){
				show(this);
				isEmpty = false;
			} else {
				hide(this);
			}
		});
	} else {
		jQuery(children).each(function(){
			var searchData = getSearchData(this); 
			if (searchData.indexOf(searchText) != -1){
				show(this);
				isEmpty = false;
			} else {
				hide(this);
			}	
		});
	}
	if (isEmpty){
		show('#' + this.id + '_empty');
	} else {
		hide('#' + this.id + '_empty');
	}
		
}

// Fetch the data to search in. If an attr "data-filter" is defined, the data there is returned. Otherwise, the html of the element is retuned.
function getSearchData(selector){
	if (jQuery(selector).attr("data-filter") !== undefined){
		var searchData = jQuery(selector).attr('data-filter').toLowerCase();
	} else {
		var searchData = jQuery(selector).html().toLowerCase();
	}	
	return searchData;	
}

// Show all children and parents defined in data-children and data-parent of the HTMLElement
function clear_filter(htmlelement){
	var show = getShowFunc(htmlelement);
	jQuery(htmlelement).val('');
	jQuery(jQuery(htmlelement).attr('data-children')).each(function(){
		show(this);
	});
	if (jQuery(htmlelement).attr('data-parent') != undefined){
		jQuery(jQuery(htmlelement).attr('data-parent')).each(function(){
			show(this);
		});
	}
}

function disable_old(){
	jQuery('.wrapper_old_person').slideUp();
}

function enable_old(){
	jQuery('.wrapper_old_person').slideDown();
}

function disable_new(){
	jQuery('.wrapper_new_person').slideUp();
	jQuery('.role_label').slideDown();
	jQuery('.role_input').slideDown();
}

function enable_new(){
	jQuery('.wrapper_new_person').slideDown();
}
