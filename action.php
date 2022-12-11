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
    for ($i = 0; $i < $number; $i++)
    {
        if(trim($_POST["keywords"][$i] != ''))
        {
            $q = $_POST["keywords"][$i];

            $helper->fetchSearchResult($q);

            $kwrds[] = $q;
            $titles[] = $helper->getTilesArray();
            $links[] = $helper->getLinksArray();
            $contacts[] = $helper->getContactPagesArray();
            $all[] = $helper->getAllArray();
        }
    }

    $result = array('keywords'=>$kwrds, 'titles'=>$titles, 'links'=>$links, 'contacts'=>$contacts, 'all'=>$all);
    echo json_encode($result);
}