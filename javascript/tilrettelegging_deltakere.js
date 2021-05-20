var allergener_kulturelle = '{"vegan":{"id":"vegan","navn":"Vegan","beskrivelse":"","kategori":"kulturell"},"vegetarianer":{"id":"vegetarianer","navn":"Vegetar","beskrivelse":"","kategori":"kulturell"},"halal":{"id":"halal","navn":"Halal","beskrivelse":"","kategori":"kulturell"},"kosher":{"id":"kosher","navn":"Kosher","beskrivelse":"","kategori":"kulturell"},"storfe":{"id":"storfe","navn":"Storfe","beskrivelse":"","kategori":"kulturell"}}';
var allergener_standard = '{"gluten":{"id":"gluten","navn":"Gluten","beskrivelse":"Hvete, rug, bygg, havre, spelt, korasanhvete o.l","kategori":"standard"},"melk":{"id":"melk","navn":"Melk","beskrivelse":"Melk finner du i smør, ost, fløte, iskrem, desserter, melkepulver, yoghurt, bakverk, supper og sauser o.l.","kategori":"standard"},"laktose":{"id":"laktose","navn":"Laktose","beskrivelse":"Laktose, melkesukker, er et karbohydrat og en sukkerart som finnes i melk.","kategori":"standard"},"skalldyr":{"id":"skalldyr","navn":"Skalldyr","beskrivelse":"Dette inkluderer krabbe, hummer, reker, krill, kreps og scampi o.l.","kategori":"standard"},"egg":{"id":"egg","navn":"Egg","beskrivelse":"Egg finner du ofte i kaker, majones, sufflé, pasta, paier, noen kjøttprodukter, sauser, desserter og matvarer som er penslet med egg.","kategori":"standard"},"fisk":{"id":"fisk","navn":"Fisk","beskrivelse":"Fisk finner du ofte i skalldyrog fiskeretter, leverpostei, salatdressinger, tapenade, buljong og i Worcestersaus.","kategori":"standard"},"peanotter":{"id":"peanotter","navn":"Peanøtter","beskrivelse":"Peanøtter finner du ofte i kjeks, kaker, desserter, sjokolader, iskrem, peanøttolje, peanøttsmør, asiatiske og orientalske retter.","kategori":"standard"},"notter":{"id":"notter","navn":"Nøtter","beskrivelse":"","kategori":"standard"},"soya":{"id":"soya","navn":"Soya","beskrivelse":"Soya finner du i tofu, miso, tempeh, soyasaus, soyadrikker og soyamel o.l.","kategori":"standard"},"selleri":{"id":"selleri","navn":"Selleri","beskrivelse":"Dette inkluderer stangselleri (stilkselleri), samt blader, frø og rot (knoll) av selleriplanten.","kategori":"standard"},"sennep":{"id":"sennep","navn":"Sennep","beskrivelse":"Dette inkluderer sennep, sennepspulver og sennepsfrø.","kategori":"standard"},"sesamfro":{"id":"sesamfro","navn":"Sesamfrø","beskrivelse":"Sesamfrø finner ofte du i brød, vegetarretter, godteri, knekkebrød, kjeks, hummus, sesamolje, sesammel og tahini (sesampasta).","kategori":"standard"},"svoveldioksid_og_sulfitter":{"id":"svoveldioksid_og_sulfitter","navn":"Svoveldioksid og sulfitter","beskrivelse":"Sulfitt brukes ofte til konservering av frukt og grønnsaker (inklusive tomat), og i noen kjøttprodukter, så vel som i brus, juice, vin og øl.","kategori":"standard"},"lupin":{"id":"lupin","navn":"Lupin","beskrivelse":"Dette inkluderer lupinfrø og lupinmel, og kan finnes i noen typer brød, bakervarer, mel, vegetarprodukter og pasta.","kategori":"standard"},"blotdyr":{"id":"blotdyr","navn":"Bløtdyr","beskrivelse":"Dette inkluderer muslinger, snegler, blekksprut, blåskjell, kamskjell, østers, hjerteskjell, kråkeboller, akkar, kalamari, sjøsnegler o.l.","kategori":"standard"},"lok":{"id":"lok","navn":"Løk","beskrivelse":"","kategori":"standard"}}';


jQuery(document).on('click', '.intoleranse_update', function(e) {
    e.preventDefault();
    jQuery(this).html('Lagrer...').addClass('btn-primary').removeClass('btn-success');
    var person = jQuery(this).parents('li.person');

    var allergener = [];
    person.find('input[type="checkbox"]:checked').each(function() {
        allergener.push(jQuery(this).val());
    });
    
    kjorAjaxKall(person, allergener, true);
    
});

function tittellosBeforeSubmit(e) {
    var personId = jQuery(e.currentTarget).attr('person-id');
    
    var person = jQuery('li.person.single#'+personId);

    var allergener = [];
    person.find('input[type="checkbox"]:checked').each(function() {
        allergener.push(jQuery(this).val());
    });
    
    var empty = jQuery(person).parent().find('.har-valg .btn.selected').attr('funksjon') == 'nei'; 
    
    kjorAjaxKall(person, allergener, false, empty);
};

function kjorAjaxKall(person, allergener, handleUpdate, empty) {
    var data = {
        action: 'UKMVideresending_ajax',
        subaction: 'tilrettelegging',
        id: person.attr('data-id'),
        tekst: person.find('.intoleranse_tekst').val(),
        liste: allergener
    };

    if(empty) {
        data['liste'] = [];
        data['tekst'] = '';
    }

    jQuery.post(
        ajaxurl,
        data,
        function(response) {
            if(handleUpdate == false) return;

            if (response !== null && response !== undefined) {
                try {
                    response = JSON.parse(response);
                } catch (error) {
                    response = null;
                }
            }
                
            /* HANDLING GJENNOMFØRT. HÅNDTER RESPONS */
            if (response !== null && response.success) {
                handleTilretteleggUpdate(response);
            } else {
                alert('Beklager, kunne ikke hente informasjon fra server');
            }
        }
    );
}

function handleTilretteleggUpdate(response) {
    var person = jQuery('li.person#' + response.data.id);

    if (response.data.intoleranse_human.length == 0) {
        person.slideUp(
            function() {
                jQuery(this).remove();
            }
        );
        
        var intoleranseNySelect = jQuery(person).parent().parent().parent().find('.intoleranse_ny-select');
        jQuery(intoleranseNySelect).find('option[value="' + response.data.id + '"]').attr('hidden', false);
        
    } else {
        person.find('.header-person .status').html(response.data.intoleranse_human);
    }

    var knapp = person.find('.intoleranse_update');
    knapp.html('Lagret!').addClass('btn-success').removeClass('btn-primary');
    setTimeout(
        () => {
            knapp.html('Lagre');
        },
        2200
    );
}


jQuery(document).on('click', '#intoleranse_add', function(e) {
    e.preventDefault();
    var intoleranseSelect = jQuery(e.currentTarget).parent().find('.intoleranse_ny-select');
    
    if(jQuery(intoleranseSelect).find('option:selected').attr('hidden') == 'hidden') {
        return;
    }

    if (!jQuery(intoleranseSelect).val()) {
        alert('Velg en person fra listen før du trykker "legg til"');
        return false;
    }
    var data = {
        person: {
            id: jQuery(intoleranseSelect).val(),
            navn: jQuery(intoleranseSelect).find('option:selected').html(),
            mobil: jQuery(intoleranseSelect).find('option:selected').data('mobil'),
            intoleranse_liste: [],
            intoleranse_tekst: ''
        },
        allergener_kulturelle: JSON.parse(allergener_kulturelle),
        allergener_standard: JSON.parse(allergener_standard),
    };
    var intoleranseUl = intoleranseSelect.parent().parent().parent().find('ul.intoleranser-ul');
    jQuery(intoleranseUl).prepend(
        twigJS_intoleranserpameldte.render(data)
    );
    jQuery(intoleranseSelect).find('option[value="' + data.person.id + '"]').attr('hidden', true).attr('selected', false);
});

jQuery(document).on('click', '.har-allergi-intoleranse .har-valg .btn', function(e) {
    jQuery('.har-allergi-intoleranse .har-valg .btn').removeClass('selected btn-primary');
    var el = jQuery(e.currentTarget);
    el.addClass('selected btn-primary');

    var ja = el.attr('funksjon') == 'ja';
    jQuery(el).parents('.intoleranser-ul').find('li.person').addClass(ja ? '' : 'hide').removeClass(ja ? 'hide' : '');

    
});
