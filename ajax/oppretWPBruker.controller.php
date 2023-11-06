<?php
use UKMNorge\Wordpress\WriteUser;

if(is_super_admin()){
    $user = WriteUser::createParticipantUser('lisalausen1996', 'mhabtjgnpq_1667938457@tfbnw.net', 'LisaW', 'LausenW', 94078002, 37684);
    var_dump($user);
}
