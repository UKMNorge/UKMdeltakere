<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Wordpress\WriteUser;
use UKMNorge\Wordpress\User;
use UKMNorge\Wordpress\Blog;


$arrangement = new Arrangement(get_option('pl_id'));
$innslag_id = $_POST['innslag_id'];
$innslag = $arrangement->getInnslag()->get($innslag_id);

if($innslag && is_super_admin()) {
    if($innslag->getType()->getKey() == 'arrangor' || $innslag->getType()->getKey() == 'nettredaksjon') {
        $person = $innslag->getPerson();
        $username = "deltaker_" . $person->getId();
        $user = WriteUser::createParticipantUser($username, $person->getEpost(), $person->getFornavn(), $person->getEtternavn(), $person->getMobil(), $person->getId());

        Blog::leggTilBruker($blog_id, $user->getId(), $role);

        // Oppgraderer rollen til brukeren
        WriteUser::oppgraderBruker(
            $user,
            get_current_blog_id(),
            User::getOppgradertRolleForInnslagType($innslag->getType())
        );
    }
}