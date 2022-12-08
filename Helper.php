<?php
/*
 * Copyright (C) 2022 Sevada Ghazaryan. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

class Helper
{
    static $SEARCH_RESULT_ENDPOINT = 'https://api.scaleserp.com/search';
    static $API_KEY = 'BA003D57E568437E94047B4699C48E6B';
    static $COUNT_OF_SEARCH_RESULT = '30';

    static $BLOG_CHECK_END = '/license.txt';
    static $CONTACT_PAGE_VARIANTS = '/contact';

    private $domainsArray;
    private $linksArray;
    private $tilesArray;
    private $adsArray;

    public function fetchSearchResult($queryString)
    {
        $result = $this->runCURL(self::$SEARCH_RESULT_ENDPOINT, $queryString);
        //return $result;
        $this->collectDataFromOrganicResult($result);
    }

    public function collectDataFromOrganicResult($searchResult)
    {
        // Make it as array for easy access
        $result_array = json_decode($searchResult, true);

        // Get organic result as an array
        $organicResult = $result_array['organic_results'];
        $adsResult = $result_array['ads'];

        // Loop via result and collect data
        foreach ($organicResult as $item)
        {
            // Check for WordPress
            if($this->checkIfWordPress($item['domain']))
            {
                $this->domainsArray[] = $item['domain'];
                $this->linksArray[] = $item['link'];
                $this->tilesArray[] = $item['title'];
            }
        }

        if (!empty($adsResult))
        {
            foreach ($adsResult as $item)
            {
                $this->adsArray[] = $item['ads'];
            }
        }
    }

    private function checkIfWordPress($domain)
    {
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $domain.self::$BLOG_CHECK_END);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        // grab URL and pass it to the browser
        curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        // close cURL resource, and free up system resources
        curl_close($ch);
        if ( $httpStatus == 200 ) {
            return true;
        }
        return false;
    }

    private function runCURL($endpoint, $query)
    {
        # set up the request parameters
        $queryString = http_build_query([
            'api_key' => self::$API_KEY,
            'q' => $query,
            'num' => self::$COUNT_OF_SEARCH_RESULT,
            'output' => 'json'
        ]);

# make the http GET request to Scale SERP
        $ch = curl_init(sprintf('%s?%s', $endpoint, $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
# the following options are required if you're using an outdated OpenSSL version
# more details: https://www.openssl.org/blog/blog/2021/09/13/LetsEncryptRootCertExpire/
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_TIMEOUT, 180);

        $api_result = curl_exec($ch);
        curl_close($ch);

        //return $api_result;
# print the JSON response from Scale SERP
        return $api_result;
    }

    /**
     * @return mixed
     */
    public function getDomainsArray()
    {
        return $this->domainsArray;
    }

    /**
     * @return mixed
     */
    public function getLinksArray()
    {
        return $this->linksArray;
    }

    /**
     * @return mixed
     */
    public function getTilesArray()
    {
        return $this->tilesArray;
    }

    /**
     * @return mixed
     */
    public function getAdsArray()
    {
        return $this->adsArray;
    }

}