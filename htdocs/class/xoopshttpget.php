<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * XoopsHttpGet - return response to a http get request
 *
 * @category  HttpGet
 * @package   Xoops
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2020 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
class XoopsHttpGet
{
    protected $useCurl = true;
    protected $url;
    protected $error;

    /**
     * XoopsHttpGet constructor.
     *
     * @param string $url the url to process
     *
     * @throws \RuntimeException if neither curl extension nor stream wrappers (allow_url_fopen) is available
     */
    public function __construct($url)
    {
        $this->url = $url;
        if (!function_exists('curl_init')) {
            $this->useCurl = false;
            $urlFopen = (int) ini_get('allow_url_fopen');
            if ($urlFopen === 0) {
                throw new \RuntimeException("CURL extension or allow_url_fopen ini setting is required.");
            }
        }
    }

    /**
     * Return the response from a GET to the specified URL.
     *
     * @return string|bool response or false on error
     */
    public function fetch()
    {
        return ($this->useCurl) ? $this->fetchCurl() : $this->fetchFopen();
    }

    /**
     * Use curl to GET the specified URL.
     *
     * @return string|bool response or false on error
     */
    protected function fetchCurl()
    {
        $curlHandle = curl_init($this->url);
        if (false === $curlHandle) {
            $this->error = 'curl_init failed';
            return false;
        }
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER         => 0,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_MAXREDIRS      => 4,
        );
        curl_setopt_array($curlHandle, $options);

        $response = curl_exec($curlHandle);
        if (false === $response) {
            $this->error = curl_error($curlHandle);
        } else {
            $httpcode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
            if (200 != $httpcode) {
                $this->error = $response;
                $response = false;
            }
        }
        curl_close($curlHandle);
        return $response;
    }

    /**
     * Use stream wrapper to GET the specified URL.
     *
     * @return string|false response or false on error
     */
    protected function fetchFopen()
    {
        $response = file_get_contents($this->url);
        if (false === $response) {
            $this->error = 'file_get_contents() failed.';
        }
        return $response;
    }

    /**
     * Return any error set during processing of fetch()
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }
}
