<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Wordpress\WriteUser;
use UKMNorge\Wordpress\User;
use UKMNorge\Wordpress\Blog;


$arrangement = new Arrangement(get_option('pl_id'));
$innslag_id = $_POST['innslag_id'];
$innslag = $arrangement->getInnslag()->get($innslag_id);

if(false || hasUserRole(get_current_user_id(), 'administrator') || hasUserRole(get_current_user_id(), 'editor') || is_super_admin()) {
    if($innslag->getType()->getKey() == 'arrangor' || $innslag->getType()->getKey() == 'nettredaksjon') {
        $person = $innslag->getPerson();
        $username = "deltaker_" . $person->getId();
        $user = WriteUser::createParticipantUser($username, $person->getEpost(), $person->getFornavn(), $person->getEtternavn(), $person->getMobil(), $person->getId());
    
        $JSON->twigJS = 'overviewtittellos';
        $JSON->twigJS = 'overview';
        $JSON->innslag_id = strval($innslag->getId());
        $JSON->erfaring		        = $innslag->getBeskrivelse();
        $JSON->innslag->kommune_id  = $innslag->getKommune()->getId();
    
        $role = User::getOppgradertRolleForInnslagType($innslag->getType());
        Blog::leggTilBruker(get_current_blog_id(), $user->getId(), $role);
    }
}
else {
    throw new Exception('Du har ikke tigang!');
}


function hasUserRole($user_id, $role_name) {
    $user_meta = get_userdata($user_id);
    $user_roles = $user_meta->roles;
    return in_array($role_name, $user_roles);
}