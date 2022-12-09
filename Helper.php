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
    static $API_KEY                = 'DE8AB7A9185E4908B044BE644453C2FA';
    static $COUNT_OF_SEARCH_RESULT = '10';

    static $BLOG_CHECK_END         = '/license.txt';
    static $CONTACT_PAGE_VARIANTS  = ['/contact', '/contact-us', '/about-us'];

    private $contactPagesArray;
    private $domainsArray;
    private $linksArray;
    private $tilesArray;
    private $adsArray;

    public function fetchSearchResult($queryString)
    {
        $result = $this->runCURL(self::$SEARCH_RESULT_ENDPOINT, $queryString);
        $this->collectDataFromOrganicResult($result);
    }

    public function collectDataFromOrganicResult($searchResult)
    {
        $this->contactPagesArray = array();
        $this->domainsArray = array();
        $this->linksArray = array();
        $this->tilesArray = array();

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
                // If WordPress then try to find contact page
                foreach (self::$CONTACT_PAGE_VARIANTS as $slug)
                {
                    if ($this->checkIfHasContactPage($item['domain'], $slug))
                    {
                        $this->contactPagesArray[] = $item['domain'].$slug;
                        break; // If contact page has been found then we can terminate checking process
                    }
                    else
                    {
                        $this->contactPagesArray[] = 'no link to contact page found';
                    }
                }
                $this->domainsArray[] = $item['domain'];
                $this->linksArray[] = $item['link'];
                $this->tilesArray[] = $item['title'];
            }
        }

        /* Code below is not necessary at this moment */
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
        $exists = false;
        $handle = curl_init($domain);

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($handle, CURLOPT_HEADER, true);

        curl_setopt($handle, CURLOPT_NOBODY, true);

        curl_setopt($handle, CURLOPT_USERAGENT, true);

        $headers = curl_exec($handle);
        curl_close($handle);

        if (empty($failCodeList) or !is_array($failCodeList)){

            $failCodeList = array(404);
        }

        if (!empty($headers)){

            $exists = true;

            $headers = explode(PHP_EOL, $headers);

            foreach($failCodeList as $code){

                if (is_numeric($code) and strpos($headers[0], strval($code)) !== false){

                    $exists = false;

                    break;
                }
            }
        }

        return $exists;
    }

    private function checkIfHasContactPage($domain, $slug)
    {
        $exists = false;
        $handle = curl_init($domain.$slug);

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($handle, CURLOPT_HEADER, true);

        curl_setopt($handle, CURLOPT_NOBODY, true);

        curl_setopt($handle, CURLOPT_USERAGENT, true);

        $headers = curl_exec($handle);
        curl_close($handle);

        if (empty($failCodeList) or !is_array($failCodeList)){

            $failCodeList = array(404);
        }

        if (!empty($headers)){

            $exists = true;

            $headers = explode(PHP_EOL, $headers);

            foreach($failCodeList as $code){

                if (is_numeric($code) and strpos($headers[0], strval($code)) !== false){

                    $exists = false;

                    break;
                }
            }
        }

        return $exists;
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

    /**
     * @return mixed
     */
    public function getContactPagesArray()
    {
        return $this->contactPagesArray;
    }

}