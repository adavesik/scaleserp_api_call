<?php
/*
 * Copyright (C) 2022 Sevada Ghazaryan. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */


require('Helper.php');

$helper = new Helper();

// Get number of submitted keywords
$number = count($_POST["keywords"]);

// Check if there is one keyword has benn submitted at least
if($number > 0)
{
    if(trim($_POST["keywords"][0] != ''))
    {
        $q = $_POST["keywords"][0];

        $helper->fetchSearchResult($q);

        $result = array('keyword'=>$q, 'titles'=>$helper->getTilesArray(), 'links'=>$helper->getLinksArray(), 'contacts'=>$helper->getDomainsArray());
        echo json_encode($result);

/*        print_r($helper->getDomainsArray());
        print_r($helper->getLinksArray());
        print_r($helper->getTilesArray());
        print_r($helper->getAdsArray());*/
    }
}