<?php

// HVIS INNSLAGET KAN VÆRE GRUPPE
if ($innslag->getType()->erGruppe()) {
    $JSON->twigJS = 'overview';

    // INFO OM INNSLAGET
    $JSON->innslag->kontaktperson   = $innslag->getKontaktperson();
    $JSON->innslag->beskrivelse     = $innslag->getBeskrivelse();

    // PERSONER I INNSLAGET
    $JSON->innslag->personer        = [];
    $snittalder                     = 0;

    // PERSONER PÅMELDT DETTE ARRANGEMENTET
    foreach ($innslag->getPersoner()->getAll() as $person) {
        $tmp                        = data_person($person);
        $tmp->rolle                 = $person->getRolle();
        $tmp->slettbar              = !$person->erPameldtAndre($monstring->getId());
        $JSON->innslag->personer[]  = $tmp;
        $snittalder                += (($tmp->alder_tall == '25+') ? 0 : $tmp->alder_tall);
    }
    $JSON->innslag->snittalder    = round($snittalder / ($innslag->getPersoner()->getAntall() > 0 ? $innslag->getPersoner()->getAntall() : 1), 1);

    // PERSONER SOM IKKE ER PÅMELDT DETTE ARRANGEMENTET
    $JSON->innslag->personer_ekstra = [];
    foreach ($innslag->getPersoner()->getAllIkkePameldte() as $person) {
        $tmp                                = data_person($person);
        $tmp->rolle                         = $person->getRolle();
        $JSON->innslag->personer_ekstra[]   = $tmp;
    }

    // INNSLAGETS TITLER (HVIS TYPEN HAR TITLER)
    if ($innslag->getType()->harTitler()) {
        // TITLER OG VARIGHET
        $JSON->innslag->titler          = [];
        $JSON->innslag->varighet        = $innslag->getTitler()->getVarighet();

        // TITLER PÅMELDT DETTE ARRANGEMENTET
        foreach ($innslag->getTitler()->getAll() as $tittel) {
            $tmp                        = data_tittel($tittel);
            $tmp->slettbar              = !$tittel->erPameldtAndre($monstring->getId());
            $JSON->innslag->titler[]    = $tmp;
        }
        // TITLER SOM TILHØRER INNSLAGET, MEN IKKE ER PÅMELDT DETTE ARRANGEMENTET
        $JSON->innslag->titler_ekstra = [];
        foreach ($innslag->getTitler()->getAllIkkePameldte() as $tittel) {
            $JSON->innslag->titler_ekstra[] = data_tittel($tittel);
        }
    }
}
// INNSLAGET ER ENKELTPERSON
else {
    $JSON->twigJS       = 'overviewtittellos';
    $person             = $innslag->getPersoner()->getSingle();
    $JSON->person       = data_person($person);
    $JSON->erfaring     = $innslag->getBeskrivelse();
}


// HENT UT PROGRAMMET FOR INNSLAGET PÅ DENNE MØNSTRINGEN
foreach ($innslag->getProgram()->getAllInkludertSkjulte() as $hendelse) {
    $tmp                = data_program($hendelse);
    $tmp->rekkefolge    = $innslag->getProgram()->getRekkefolge($hendelse);

    $JSON->innslag->hendelser[$hendelse->getId()] = $tmp;
}

// FINN LIGNENDE INNSLAG
if (!$innslag->erPameldt()) {
    $JSON->lignende = true;
    $JSON->alle_lignende = [];

    $alle_innslag = array_merge($monstring->getInnslag()->getAll(), $monstring->getInnslag()->getAllUfullstendige());

    foreach ($alle_innslag as $sammenlign_innslag) {
        // Hopp over seg selv
        if ($innslag->getId() == $sammenlign_innslag->getId()) {
            continue;
        }

        // Navnesjekk
        if ($innslag->getNavn() != 'Innslag uten navn' && !empty($innslag->getNavn())) {
            similar_text($innslag->getNavn(), $sammenlign_innslag->getNavn(), $likhet);
            if ($likhet > 60) {
                $data = data_innslag($sammenlign_innslag);
                $data->grunnlag = 'det finnes et innslag med et navn som ligner ' . floor($likhet) . '%';
                $JSON->alle_lignende[$sammenlign_innslag->getId()] = $data;
            }
        }

        // Samme kontaktperson
        if ($innslag->getKontaktpersonId() == $sammenlign_innslag->getKontaktpersonId()) {
            $data = data_innslag($sammenlign_innslag);
            $data->grunnlag = $innslag->getKontaktperson()->getFornavn() . ' ' . $innslag->getKontaktperson()->getEtternavn() . ' er kontaktperson';
            $JSON->alle_lignende[$sammenlign_innslag->getId()] = $data;
        }

        // Deltakere
        foreach ($innslag->getPersoner()->getAll() as $deltaker) {
            if ($deltaker->getId() == $sammenlign_innslag->getKontaktperson()->getId()) {
                $data = data_innslag($sammenlign_innslag);
                $data->grunnlag = $person->getFornavn() . ' ' . $person->getEtternavn() . ' er kontaktperson';
                $JSON->alle_lignende[$sammenlign_innslag->getId()] = $data;
            }
        }
    }
}
