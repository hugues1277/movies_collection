<?php

namespace OCA\MoviesCollection\Service;

use OCA\MoviesCollection\Service\IMDB;

class IMDBHelper extends IMDB
{
    /**
     * Regular expression helper.
     *
     * @param string $sContent The content to search in.
     * @param string $sPattern The regular expression.
     * @param null   $iIndex   The index to return.
     *
     * @return bool   If no match was found.
     * @return string If one match was found.
     * @return array  If more than one match was found.
     */
    public static function matchRegex($sContent, $sPattern, $iIndex = null)
    {
        preg_match_all($sPattern, $sContent, $aMatches);
        if ($aMatches === false) {
            return false;
        }
        if ($iIndex !== null && is_int($iIndex)) {
            if (isset($aMatches[$iIndex][0])) {
                return $aMatches[$iIndex][0];
            }

            return false;
        }

        return $aMatches;
    }

    /**
     * Preferred output in responses with multiple elements
     *
     * @param bool   $bArrayOutput Native array or string with separators.
     * @param string $sSeparator   String separator.
     * @param string $sNotFound    Not found text.
     * @param array  $aReturn      Original input.
     * @param bool   $bHaveMore    Have more elements indicator.
     *
     * @return string|array Multiple results separated by selected separator string, or enclosed into native array.
     */
    public static function arrayOutput($bArrayOutput, $sSeparator, $sNotFound, $aReturn = null, $bHaveMore = false)
    {
        if ($bArrayOutput) {
            if ($aReturn == null || !is_array($aReturn)) {
                return [];
            }

            if ($bHaveMore) {
                $aReturn[] = '…';
            }

            return $aReturn;
        } else {
            if ($aReturn == null || !is_array($aReturn)) {
                return $sNotFound;
            }

            foreach ($aReturn as $i => $value) {
                if (is_array($value)) {
                    $aReturn[$i] = implode($sSeparator, $value);
                }
            }

            return implode($sSeparator, $aReturn) . (($bHaveMore) ? '…' : '');
        }
    }

    /**
     * @param string $sInput Input (eg. HTML).
     *
     * @return string Cleaned string.
     */
    public static function cleanString($sInput)
    {
        $aSearch  = [
            'Full summary &raquo;',
            'Full synopsis &raquo;',
            'Add summary &raquo;',
            'Add synopsis &raquo;',
            'See more &raquo;',
            'See why on IMDbPro.',
            "\n",
            "\r"
        ];
        $aReplace = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        $sInput   = str_replace('</li>', ' | ', $sInput);
        $sInput   = strip_tags($sInput);
        $sInput   = str_replace('&nbsp;', ' ', $sInput);
        $sInput   = str_replace($aSearch, $aReplace, $sInput);
        $sInput   = html_entity_decode($sInput, ENT_QUOTES | ENT_HTML5);
        $sInput   = preg_replace('/\s+/', ' ', $sInput);
        $sInput   = trim($sInput);
        $sInput   = rtrim($sInput, ' |');

        return ($sInput ? trim($sInput) : self::$sNotFound);
    }

    /**
     * @param string $sText   The long text.
     * @param int    $iLength The maximum length of the text.
     *
     * @return string The shortened text.
     */
    public static function getShortText($sText, $iLength = 100)
    {
        if (mb_strlen($sText) <= $iLength) {
            return $sText;
        }

        list($sShort) = explode("\n", wordwrap($sText, $iLength - 1));

        if (substr($sShort, -1) !== '.') {
            return $sShort . '…';
        }

        return $sShort;
    }

    /**
     * @param string $sUrl      The URL to fetch.
     * @param bool   $bDownload Download?
     *
     * @return bool|mixed Array on success, false on failure.
     */
    public static function runCurl($sUrl, $bDownload = false)
    {
        $oCurl = curl_init($sUrl);
        curl_setopt_array(
            $oCurl,
            [
                CURLOPT_BINARYTRANSFER => ($bDownload ? true : false),
                CURLOPT_CONNECTTIMEOUT => self::IMDB_TIMEOUT,
                CURLOPT_ENCODING       => '',
                CURLOPT_FOLLOWLOCATION => 0,
                CURLOPT_FRESH_CONNECT  => 0,
                CURLOPT_HEADER         => ($bDownload ? false : true),
                CURLOPT_HTTPHEADER     => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Charset: utf-8, iso-8859-1;q=0.5',
                    'Accept-Language: ' . self::IMDB_LANG
                ],
                CURLOPT_REFERER        => 'https://www.imdb.com',
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_TIMEOUT        => self::IMDB_TIMEOUT,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0',
                CURLOPT_VERBOSE        => 0
            ]
        );
        $sOutput   = curl_exec($oCurl);
        $aCurlInfo = curl_getinfo($oCurl);
        curl_close($oCurl);
        $aCurlInfo['contents'] = $sOutput;

        if (200 !== $aCurlInfo['http_code'] && 302 !== $aCurlInfo['http_code']) {
            if (true === self::IMDB_DEBUG) {
                echo '<pre><b>cURL returned wrong HTTP code “' . $aCurlInfo['http_code'] . '”, aborting.</b></pre>';
            }

            return false;
        }

        return $aCurlInfo;
    }
}
