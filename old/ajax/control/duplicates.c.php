<?php
function UKMdeltakere_ajax_controller($b_id){
# SAMME NAVN
# SAMME KONTAKTPERSON
# NOEN I INNSLAGET ER KONTAKTPERSON

	#######################################################
	# INNSLAGET SOM INSPISERES
	$i_inn = new innslag($b_id,false);
	
	# ALLE PERSONER I INNSLAGET
	$i_personer = $i_inn->personer();
	foreach($i_personer as $inspect_person) {
		$sjekk[] = $inspect_person['p_id'];
		$i_pers[$inspect_person['p_id']] = new person($inspect_person['p_id']);
	}
	$sjekk[] = $i_inn->g('b_contact');
	$i_pers[$i_inn->g('b_contact')] = new person($i_inn->g('b_contact'));	
	
	# BYGGET ARRAY SJEKK SOM INNEHOLDER ALLE PERSONOBJEKTER
	

	#######################################################
	# ALLE ANDRE INNSLAG
	$place = new monstring(get_option('pl_id'));
	$innslagene = array();
	for($i=1;$i<9;$i++) {
		$innslagene = array_merge($innslagene, $place->innslag_etter_status($i));
	}
	
	#######################################################
	# LOOP OG SJEKK ALLE INNSLAG PÅ MØNSTRINGEN
	foreach($innslagene as $innslag){
		$inn = new innslag($innslag['b_id'],false);
		# HVIS IKKE DET SOM INSPISERES
		if($inn->g('b_id')!==$i_inn->g('b_id')) {
			# LIKT NAVN SOM ET ANNET?
			similar_text($inn->g('b_name'),$i_inn->g('b_name'), $likhet);
			if($likhet > 60)
				$inn_like[gs($inn->g('b_status'))] = array('status'=>gst($inn->g('b_status')), 
														   'navn'=>$inn->g('b_name'));
			
			# SJEKK OM ALLE PERSONER ER KONTAKTPERSON I ANNET INNSLAG
			foreach($sjekk as $pid) {
				if($inn->g('b_contact')==$pid) {
					$inn_lik_kp[gs($inn->g('b_status'))] = array('status'=>gst($inn->g('b_status')),
																 'navn' => $inn->g('b_name'),
																 'kontaktperson'=>$i_pers[$pid]->g('name'));
				}
			}
		}
	}
	
	#######################################################
	### ANBEFALINGER
	# INGEN ANDRE LIGNENDE
	if(sizeof($inn_like)==0 && sizeof($inn_lik_kp)==0) {
		if($inn->g('b_name')=='Innslag uten navn' && $i_inn->editable())
			$tips = 'Dette innslaget <strong>ser ut som</strong> om det skal slettes..';
		else
			$tips = 'Innslaget har ingen andre påmeldinger, og trenger kanskje hjelp';
	} elseif(sizeof($inn_like)==0 && sizeof($inn_lik_kp['a']) > 0) {
		$tips = 'Det er ingen andre innslag med likt navn, men deltakerne i innslaget deltar til sammen med '.sizeof($inn_lik_kp['a']).' innslag.'
			.' Dette kan kanskje slettes?'; 
	} elseif(sizeof($inn_like)==0 && sizeof($inn_lik_kp['a'])==0 && sizeof($inn_lik_kp['b']) > 0) {
		$tips = 'Det er ingen andre innslag med likt navn, men deltakerne i innslaget har innslag i listen "ufullstendige påmeldinger". '
			.  'Dette kan kanskje slettes?'; 
	} elseif(sizeof($inn_like['a'])>0) {
			$tips = 'Det finnes '.sizeof($inn_like['a']).' innslag med lignende navn som allerede deltar. Dette kan kanskje slettes?';
	} elseif(sizeof($inn_like['b'])>0) {
			$tips = 'Det finnes '.sizeof($inn_like['b']).' innslag med lignende navn i listen "ufullstendige påmeldinger". Dette kan kanskje slettes?';
	} else {
		$tips = 'Dette innslaget ser ut til at det trenger litt hjelp, da det ikke finnes noen relaterte påmeldte innslag';
	}
	if(!$i_inn->editable())
		$tips .= '<br /><strong>OBS:</strong> Det er mindre enn 7 dager siden innslaget først startet sin påmelding, og påmeldingen kan derfor være underveis';
	
	if(!empty($tips))
		$tips = '<div id="message" class="updated">TIPS: '.$tips.'</div>';
	
	if(!is_array($inn_lik_kp))
		$inn_lik_kp = array();
	if(!is_array($inn_like))
		$inn_like = array();

	ksort($inn_lik_kp);
	return array('like'=>$inn_like, 'like_kp'=>$inn_lik_kp, 'tips'=>$tips);
}


function gs($status){
	if($status == 8)
		return 'a';
	elseif($status < 8 && $status > 5)
		return 'b';
	return 'c';
}

function gst($status) {
	switch(gs($status)) {
		case 'a':	return	'Påmeldte innslag';
		case 'b':	return	'Ufullstendige påmeldinger';
	}
	return 'Så vidt påbegynte påmeldinger';
}
?>