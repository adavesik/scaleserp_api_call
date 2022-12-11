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
    static $COUNT_OF_SEARCH_RESULT = '20';

    static $BLOG_CHECK_END         = '/license.txt';
    static $CONTACT_PAGE_VARIANTS  = ['/contact', '/contact-us', '/about-us'];

    private $contactPagesArray;
    private $domainsArray;
    private $linksArray;
    private $tilesArray;
    private $adsArray;
    private $allArray;

    /**
     * @param $queryString
     * @return void
     */
    public function fetchSearchResult($queryString)
    {
        $result = $this->runCURL(self::$SEARCH_RESULT_ENDPOINT, $queryString);
        $this->collectDataFromOrganicResult($result);
    }

    /**
     * @param $searchResult
     * @return void
     */
    public function collectDataFromOrganicResult($searchResult)
    {
        $this->contactPagesArray = array();
        $this->domainsArray = array();
        $this->linksArray = array();
        $this->tilesArray = array();
        $this->allArray = array();

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
                $slugCount = count(self::$CONTACT_PAGE_VARIANTS);
                foreach (self::$CONTACT_PAGE_VARIANTS as $slug)
                {
                    $slugCount--;
                    if ($this->checkIfHasContactPage($item['domain'], $slug))
                    {
                        $this->contactPagesArray[] = $item['domain'].$slug;
                        break; // If contact page has been found then we can terminate checking process
                    }
                    elseif($slugCount == 0)
                    {
                        $this->contactPagesArray[] = 'no link to contact page found';
                    }
                }
                $this->domainsArray[] = $item['domain']; // In general, we are not sing this anymore
                $this->linksArray[]   = $item['link'];
                $this->tilesArray[]   = $item['title'];
                // Double check by license.txt content
/*                if ($this->checkIfLicenseTXT($item['domain']))
                {
                }*/
            }

            $this->allArray[] = $item['domain'];
        }

        /* Code below is not necessary at this moment */
        if (!empty($adsResult))
        {
            foreach ($adsResult as $item)
            {
                $this->adsArray[] = $item['ads'];
            }
        }
        /*---Until here------------------------------*/
    }

    /**
     * @param $domain
     * @param array $failCodeList
     * @return bool
     */
    private function checkIfWordPress($domain, array $failCodeList = array(404))
    {
        $exists = false;
        $isWPinContent = false;

        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $domain.self::$BLOG_CHECK_END);
        curl_setopt($handle, CURLOPT_REFERER, $domain.self::$BLOG_CHECK_END);

        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($handle, CURLOPT_POST, FALSE);

        curl_setopt($handle, CURLOPT_HEADER, TRUE);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($handle, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($handle, CURLOPT_ENCODING, TRUE);

        curl_setopt($handle, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($handle, CURLOPT_COOKIEFILE, 'cookie.txt');

        curl_setopt($handle, CURLOPT_HTTPHEADER, ['text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9']);

        curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');

        $headers = curl_exec($handle);
        curl_close($handle);

        // At first check by content
        if (strpos($headers, "WordPress - Web publishing software") !== false) {
            $isWPinContent = true;
        }

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

        // If both are true then we can be almost sure this is a WP website
        if ($exists && $isWPinContent)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @param $domain
     * @param $slug
     * @return bool
     */
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

    /**
     * @return mixed
     */
    public function getAllArray()
    {
        return $this->allArray;
    }

    private function checkIfLicenseTXT($url)
    {

        $isWP = false;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url.self::$BLOG_CHECK_END);
        curl_setopt($curl, CURLOPT_REFERER, $url.self::$BLOG_CHECK_END);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, FALSE);

        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, TRUE);

        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie.txt');

        curl_setopt($curl, CURLOPT_HTTPHEADER, ['text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9']);

        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');

        $content = curl_exec($curl);
        curl_close($curl);

        if (strpos($content, "WordPress - Web publishing software") !== false) {
            $isWP = true;
        }

        return $isWP;
    }

    /**
     * @param $endpoint
     * @param $query
     * @return bool|string
     */
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