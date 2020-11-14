<?php

namespace OCA\MoviesCollection\Service;

use Exception;
use OCA\MoviesCollection\Service\IMDBHelper;

class IMDB
{
    /**
     * Set this to true if you run into problems.
     */
    const IMDB_DEBUG = false;

    /**
     * Set the preferred language for the User Agent.
     */
    const IMDB_LANG = 'en-US,en;q=0.9';

    /**
     * Define the timeout for cURL requests.
     */
    const IMDB_TIMEOUT = 15;

    /**
     * These are the regular expressions used to extract the data.
     * If you don’t know what you’re doing, you shouldn’t touch them.
     */
    const IMDB_AKA           = '~<td[^>]*>\s*Also\s*Known\s*As\s*</td>\s*<td>(.+)</td>~Uis';
    const IMDB_ASPECT_RATIO  = '~<td[^>]*>Aspect\s*Ratio</td>\s*<td>(.+)</td>~Uis';
    const IMDB_AWARDS        = '~<div\s*class="titlereference-overview-section">\s*Awards:(.+)</div>~Uis';
    const IMDB_BUDGET        = '~<td[^>]*>Budget<\/td>\s*<td>\s*(.*)(?:\(estimated\))\s*<\/td>~Ui';
    const IMDB_CAST          = '~<td[^>]*itemprop="actor"[^>]*>\s*<a\s*href="/name/([^/]*)/\?[^"]*"[^>]*>\s*<span.+>(.+)</span~Ui';
    const IMDB_CAST_IMAGE    = '~(loadlate="(.*)"[^>]*><\/a>\s+<\/td>\s+)?<td[^>]*itemprop="actor"[^>]*>\s*<a\s*href="\/name\/([^/]*)\/\?[^"]*"[^>]*>\s*<span.+>(.+)<\/span+~Uis';
    const IMDB_CERTIFICATION = '~<td[^>]*>\s*Certification\s*</td>\s*<td>(.+)</td>~Ui';
    const IMDB_CHAR          = '~<td class="character">(?:\s+)<div>(.*)(?:\s+)(?: /| \(.*\)|<\/div>)~Ui';
    const IMDB_COLOR         = '~<a href="\/search\/title\?colors=(?:.*)">(.*)<\/a>~Ui';
    const IMDB_COMPANY       = '~href="[^"]*update=[t0-9]+:production_companies[^"]*">Edit</a>\s*</header>\s*<ul\s*class="simpleList">.+<a href="\/company\/(.*)\/">(.*)</a>~Ui';
    const IMDB_COUNTRY       = '~<a href="/country/(\w+)">(.*)</a>~Ui';
    const IMDB_CREATOR       = '~<div[^>]*>\s*(?:Creator|Creators)\s*:\s*<ul[^>]*>(.+)</ul>~Uxsi';
    const IMDB_DIRECTOR      = '~<div[^>]*>\s*(?:Director|Directors)\s*:\s*<ul[^>]*>(.+)</ul>~Uxsi';
    const IMDB_GENRE         = '~href="/genre/([a-zA-Z_-]*)/?">([a-zA-Z_ -]*)</a>~Ui';
    const IMDB_GROSS         = '~pl-zebra-list__label">Cumulative Worldwide Gross<\/td>\s+<td>\s+(.*)\s+<~Uxsi';
    const IMDB_ID            = '~((?:tt\d{6,})|(?:itle\?\d{6,}))~';
    const IMDB_LANGUAGE      = '~<a href="\/language\/(\w+)">(.*)<\/a>~Ui';
    const IMDB_LOCATION      = '~href="\/search\/title\?locations=(.*)">(.*)<\/a>~Ui';
    const IMDB_LOCATIONS     = '~href="\/search\/title\?locations=[^>]*>\s?(.*)\s?<\/a>[^"]*<dd>\s?(.*)\s<\/dd>~Ui';
    const IMDB_MPAA          = '~<li class="ipl-inline-list__item">(?:\s+)(TV-Y|TV-Y7|TV-G|TV-PG|TV-14|TV-MA|G|PG|PG-13|R|NC-17|NR|UR)(?:\s+)<\/li>~Ui';
    const IMDB_NAME          = '~href="/name/(.+)/?(?:\?[^"]*)?"[^>]*>(.+)</a>~Ui';
    const IMDB_DESCRIPTION   = '~<section class="titlereference-section-overview">\s+<div>(.*)</div>\s+<hr>~Ui';
    const IMDB_NOT_FOUND     = '~<h1 class="findHeader">No results found for ~Ui';
    const IMDB_PLOT          = '~<td[^>]*>\s*Plot\s*Summary\s*</td>\s*<td>\s*<p>(.+)</p>~Ui';
    const IMDB_PLOT_KEYWORDS = '~<td[^>]*>Plot\s*Keywords</td>\s*<td>(.+)(?:<a\s*href="/title/[^>]*>[^<]*</a>\s*</li>\s*</ul>\s*)?</td>~Ui';
    const IMDB_POSTER        = '~<link\s*rel=\'image_src\'\s*href="(.*)">~Ui';
    const IMDB_RATING        = '~class="ipl-rating-star__rating">(.*)<~Ui';
    const IMDB_RELEASE_DATE  = '~href="/title/[t0-9]*/releaseinfo">(.*)<~Ui';
    const IMDB_RUNTIME       = '~<td[^>]*>\s*Runtime\s*</td>\s*<td>(.+)</td>~Ui';
    const IMDB_SEARCH        = '~<td class="result_text"> <a href="\/title\/(tt\d{6,})\/(?:.*)"(?:\s*)>(?:.*)<\/a>~Ui';
    const IMDB_SEASONS       = '~episodes\?season=(?:\d+)">(\d+)<~Ui';
    const IMDB_SOUND_MIX     = '~<td[^>]*>\s*Sound\s*Mix\s*</td>\s*<td>(.+)</td>~Ui';
    const IMDB_TAGLINE       = '~<td[^>]*>\s*Taglines\s*</td>\s*<td>(.+)</td>~Ui';
    const IMDB_TITLE         = '~itemprop="name">(.*)(<\/h3>|<span)~Ui';
    const IMDB_TITLE_ORIG    = '~</h3>(?:\s+)(.*)(?:\s+)<span class=\"titlereference-original-title-label~Ui';
    const IMDB_TRAILER       = '~href="videoplayer/(vi[0-9]*)"~Ui';
    const IMDB_URL           = '~https?://(?:.*\.|.*)imdb.com/(?:t|T)itle(?:\?|/)(..\d+)~i';
    const IMDB_USER_REVIEW   = '~href="/title/[t0-9]*/reviews"[^>]*>([^<]*)\s*User~Ui';
    const IMDB_VOTES         = '~"ipl-rating-star__total-votes">\s*\((.*)\)\s*<~Ui';
    const IMDB_WRITER        = '~<div[^>]*>\s*(?:Writer|Writers)\s*:\s*<ul[^>]*>(.+)</ul>~Ui';
    const IMDB_YEAR          = '~og:title\' content="(?:.*)\((?:.*)(\d{4})(?:.*)\)~Ui';

    /**
     * @var string The string returned, if nothing is found.
     */
    public static $sNotFound = 'n/A';

    /**
     * @var null|int The ID of the movie.
     */
    public $iId = null;

    /**
     * @var bool Is the content ready?
     */
    public $isReady = false;

    /**
     * @var string Char that separates multiple entries.
     */
    public $sSeparator = ', ';

    /**
     * @var null|string The URL to the movie.
     */
    public $sUrl = null;

    /**
     * @var bool Return responses enclosed in array
     */
    public $bArrayOutput = false;

    /**
     * @var null|string Holds the source.
     */
    private $sSource = null;

    /**
     * @var string What to search for?
     */
    private $sSearchFor = 'movie';

    /**
     * @param string $sSearch    IMDb URL or movie title to search for.
     * @param string $sSearchFor What to search for?
     *
     * @throws Exception
     */
    public function __construct($sSearch, $sSearchFor = 'movie')
    {
        if (!function_exists('curl_init')) {
            throw new Exception('You need to enable the PHP cURL extension.');
        }
        if (in_array(
            $sSearchFor,
            [
                'movie',
                'tv',
                'episode',
                'game',
                'all'
            ]
        )) {
            $this->sSearchFor = $sSearchFor;
        }
        if (true === self::IMDB_DEBUG) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(-1);
            echo '<pre><b>Running:</b> fetchUrl("' . $sSearch . '")</pre>';
        }
        $this->fetchUrl($sSearch);
    }

    /**
     * @param string $sSearch IMDb URL or movie title to search for.
     *
     * @return bool True on success, false on failure.
     */
    private function fetchUrl($sSearch)
    {
        $sSearch = trim($sSearch);

        // Try to find a valid URL.
        $sId = IMDBHelper::matchRegex($sSearch, self::IMDB_ID, 1);
        if (false !== $sId) {
            $this->iId  = preg_replace('~[\D]~', '', $sId);
            $this->sUrl = 'https://www.imdb.com/title/tt' . $this->iId . '/reference';
            $bSearch    = false;
        } else {
            switch (strtolower($this->sSearchFor)) {
                case 'movie':
                    $sParameters = '&s=tt&ttype=ft';
                    break;
                case 'tv':
                    $sParameters = '&s=tt&ttype=tv';
                    break;
                case 'episode':
                    $sParameters = '&s=tt&ttype=ep';
                    break;
                case 'game':
                    $sParameters = '&s=tt&ttype=vg';
                    break;
                default:
                    $sParameters = '&s=tt';
            }

            $this->sUrl = 'https://www.imdb.com/find?q=' . rawurlencode(str_replace(' ', '+', $sSearch)) . $sParameters;
            $bSearch    = true;
        }

        // Run cURL on the URL.
        if (true === self::IMDB_DEBUG) {
            echo '<pre><b>Running cURL:</b> ' . $this->sUrl . '</pre>';
        }

        $aCurlInfo = IMDBHelper::runCurl($this->sUrl);
        $sSource   = $aCurlInfo['contents'];

        if (false === $sSource) {
            if (true === self::IMDB_DEBUG) {
                echo '<pre><b>cURL error:</b> ' . var_dump($aCurlInfo) . '</pre>';
            }

            return false;
        }

        // Was the movie found?
        $sMatch = IMDBHelper::matchRegex($sSource, self::IMDB_SEARCH, 1);
        if (false !== $sMatch) {
            $sUrl = 'https://www.imdb.com/title/' . $sMatch . '/reference';
            $this->sSource = null;
            self::fetchUrl($sUrl);
            return true;
        }
        $sMatch = IMDBHelper::matchRegex($sSource, self::IMDB_NOT_FOUND, 0);
        if (false !== $sMatch) {
            if (true === self::IMDB_DEBUG) {
                echo '<pre><b>Movie not found:</b> ' . $sSearch . '</pre>';
            }

            return false;
        }

        $this->sSource = str_replace(
            [
                "\n",
                "\r\n",
                "\r"
            ],
            '',
            $sSource
        );
        $this->isReady = true;

        return true;
    }

    /**
     * @return array All data.
     */
    public function getAll()
    {
        $aData = [];
        foreach (get_class_methods(__CLASS__) as $method) {
            if (substr($method, 0, 3) === 'get' && $method !== 'getAll' && $method !== 'getCastImages') {
                $aData[$method] = [
                    'name'  => ltrim($method, 'get'),
                    'value' => $this->{$method}()
                ];
            }
        }
        array_multisort($aData);

        return $aData;
    }

    /**
     * @param int  $iLimit How many cast members should be returned?
     * @param bool $bMore  Add … if there are more cast members than printed.
     *
     * @return string A list with cast members or $sNotFound.
     */
    public function getCast($iLimit = 5, $bMore = true)
    {
        if (true === $this->isReady) {
            $aMatch  = IMDBHelper::matchRegex($this->sSource, self::IMDB_CAST);
            $aReturn = [];
            if (count($aMatch[2])) {
                foreach ($aMatch[2] as $i => $sName) {
                    if (0 !== $iLimit && $i >= $iLimit) {
                        break;
                    }
                    $aReturn[] = IMDBHelper::cleanString($sName);
                }

                $bMore = (0 !== $iLimit && $bMore && (count($aMatch[2]) > $iLimit) ? '…' : '');

                $bHaveMore = ($bMore && (count($aMatch[2]) > $iLimit));

                return IMDBHelper::arrayOutput(
                    $this->bArrayOutput,
                    $this->sSeparator,
                    self::$sNotFound,
                    $aReturn,
                    $bHaveMore
                );
            }
        }

        return IMDBHelper::arrayOutput($this->bArrayOutput, $this->sSeparator, self::$sNotFound);
    }

    /**
     * @return string imdb id.
     */
    public function getImdbId()
    {
        if (true === $this->isReady) {
            return $this->iId;
        }

        return self::$sNotFound;
    }

    /**
     * @return string A list with the directors or $sNotFound.
     */
    public function getDirector($sTarget = '')
    {
        if (true === $this->isReady) {
            $sMatch  = IMDBHelper::matchRegex($this->sSource, self::IMDB_DIRECTOR, 1);
            $aMatch  = IMDBHelper::matchRegex($sMatch, self::IMDB_NAME);
            $aReturn = [];
            if (count($aMatch[2])) {
                foreach ($aMatch[2] as $i => $sName) {
                    $aReturn[] =
                        '<a href="https://www.imdb.com/name/' .
                        IMDBHelper::cleanString($aMatch[1][$i]) .
                        '/"' .
                        ($sTarget ? ' target="' . $sTarget . '"' : '') .
                        '>' .
                        IMDBHelper::cleanString($sName) .
                        '</a>';
                }

                $sMatch = IMDBHelper::arrayOutput($this->bArrayOutput, $this->sSeparator, self::$sNotFound, $aReturn);
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }


    /**
     * @return string The description of the movie or $sNotFound.
     */
    public function getSynopsis()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_DESCRIPTION, 1);
            if (false !== $sMatch) {
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }

    /**
     * @return string A list with the genres or $sNotFound.
     */
    public function getGenre($sTarget = '')
    {
        if (true === $this->isReady) {
            $aMatch  = IMDBHelper::matchRegex($this->sSource, self::IMDB_GENRE);
            $aReturn = [];
            if (count($aMatch[2])) {
                foreach (array_unique($aMatch[2]) as $i => $sName) {
                    $aReturn[] =
                        '<a href="https://www.imdb.com/search/title?genres=' .
                        IMDBHelper::cleanString($aMatch[1][$i]) .
                        '"' .
                        ($sTarget ? ' target="' . $sTarget . '"' : '') .
                        '>' .
                        IMDBHelper::cleanString($sName) .
                        '</a>';
                }

                return IMDBHelper::cleanString(IMDBHelper::arrayOutput($this->bArrayOutput, $this->sSeparator, self::$sNotFound, $aReturn));
            }
        }

        return self::$sNotFound;
    }

    /**
     * @return string A list with the plot keywords or $sNotFound.
     */
    public function getPlotKeywords()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_PLOT_KEYWORDS, 1);
            if (false !== $sMatch) {
                $aReturn = explode('|', IMDBHelper::cleanString($sMatch));

                return IMDBHelper::arrayOutput($this->bArrayOutput, $this->sSeparator, self::$sNotFound, $aReturn);
            }
        }

        return IMDBHelper::arrayOutput($this->bArrayOutput, $this->sSeparator, self::$sNotFound);
    }

    /**
     * @param int $iLimit The limit.
     *
     * @return string The plot of the movie or $sNotFound.
     */
    public function getPlot($iLimit = 0)
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_PLOT, 1);
            if (false !== $sMatch) {
                if ($iLimit !== 0) {
                    return IMDBHelper::getShortText(IMDBHelper::cleanString($sMatch), $iLimit);
                }

                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }

    /**
     * @param string $sSize     Small or big poster?
     * @param bool   $bDownload Return URL to the poster or download it?
     *
     * @return bool|string Path to the poster.
     */
    public function getPosterUrl($sSize = 'small')
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_POSTER, 1);
            if (false !== $sMatch) {
                if ('big' === strtolower($sSize) && false !== strstr($sMatch, '@._')) {
                    $sMatch = substr($sMatch, 0, strpos($sMatch, '@._')) . '@.jpg';
                } else {
                    $sMatch = preg_replace('/_V1_([^@]+)$/', "_V1_UX182_CR0,0,182,268_AL_.jpg", $sMatch);
                }
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }

    /**
     * Release date doesn't contain all the information we need to create a media and
     * we need this function that checks if users can vote target media (if can, it's released).
     *
     * @return  true If the media is released
     */
    public function isReleased()
    {
        $strReturn = $this->getReleaseDate();
        if ($strReturn == self::$sNotFound || $strReturn == 'Not yet released') {
            return false;
        }

        return true;
    }

    /**
     * @return string The release date of the movie or $sNotFound.
     */
    public function getReleaseDate()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_RELEASE_DATE, 1);
            if (false !== $sMatch) {
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }

    /**
     * @return string The runtime of the movie or $sNotFound.
     */
    public function getRuntime()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_RUNTIME, 1);
            if (false !== $sMatch) {
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }

    /**
     * @param bool $bForceLocal Try to return the original name of the movie.
     *
     * @return string The title of the movie or $sNotFound.
     */
    public function getTitle()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_TITLE, 1);
            $sMatch = preg_replace('~\(\d{4}\)$~Ui', '', $sMatch);
            if (false !== $sMatch && "" !== $sMatch) {
                return IMDBHelper::cleanString($sMatch);
            }
        }
        return self::$sNotFound;
    }

    public function getOriginalTitle()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_TITLE_ORIG, 1);
            if (false !== $sMatch && "" !== $sMatch) {
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }

    /**
     * @param bool $bEmbed Link to player directly?
     *
     * @return string The URL to the trailer of the movie or $sNotFound.
     */
    public function getTrailerUrl($bEmbed = false)
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_TRAILER, 1);
            if (false !== $sMatch) {
                $sUrl = 'https://www.imdb.com/video/imdb/' . $sMatch . '/' . ($bEmbed ? 'player' : '');

                return IMDBHelper::cleanString($sUrl);
            }
        }

        return self::$sNotFound;
    }

    /**
     * @return string The IMDb URL.
     */
    public function getUrl()
    {
        if (true === $this->isReady) {
            return IMDBHelper::cleanString(str_replace('reference', '', $this->sUrl));
        }

        return self::$sNotFound;
    }

    /**
     * @return string The user review of the movie or $sNotFound.
     */
    public function getUserReview()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_USER_REVIEW, 1);
            if (false !== $sMatch) {
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }

    /**
     * @return string The votes of the movie or $sNotFound.
     */
    public function getVotes()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_VOTES, 1);
            if (false !== $sMatch) {
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }

    /**
     * @return string A list with the writers or $sNotFound.
     */
    public function getWriter($sTarget = '')
    {
        if (true === $this->isReady) {
            $sMatch  = IMDBHelper::matchRegex($this->sSource, self::IMDB_WRITER, 1);
            $aMatch  = IMDBHelper::matchRegex($sMatch, self::IMDB_NAME);
            $aReturn = [];
            if (count($aMatch[2])) {
                foreach ($aMatch[2] as $i => $sName) {
                    $aReturn[] =
                        '<a href="https://www.imdb.com/name/' .
                        IMDBHelper::cleanString($aMatch[1][$i]) .
                        '/"' .
                        ($sTarget ? ' target="' . $sTarget . '"' : '') .
                        '>' .
                        IMDBHelper::cleanString($sName) .
                        '</a>';
                }
                return IMDBHelper::cleanString(IMDBHelper::arrayOutput($this->bArrayOutput, $this->sSeparator, self::$sNotFound, $aReturn));
            }
            return self::$sNotFound;
        }
    }

    /**
     * @return string The year of the movie or $sNotFound.
     */
    public function getYear()
    {
        if (true === $this->isReady) {
            $sMatch = IMDBHelper::matchRegex($this->sSource, self::IMDB_YEAR, 1);
            if (false !== $sMatch) {
                return IMDBHelper::cleanString($sMatch);
            }
        }

        return self::$sNotFound;
    }
}
