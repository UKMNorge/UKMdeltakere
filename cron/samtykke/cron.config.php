<?php

if( date('n') < 8) {
    define('SEASON', (int) date('Y') );
} else {
    define('SEASON', (int) date('Y') +1 );
}