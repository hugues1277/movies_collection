<?php

namespace OCA\MoviesCollection\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;

use OCA\MoviesCollection\Service\IMDB;
use OCA\MoviesCollection\Service\IMDBHelper;

class ImdbController extends Controller
{

    public function __construct(string $AppName, IRequest $request)
    {
        parent::__construct($AppName, $request);
    }

    /**
     * @NoAdminRequired
     *
     * @param string $search
     */
    public function imdb(string $search)
    {
        try {
            $IMDB = new IMDB($search);
            $value = [];
            if ($IMDB->isReady) {
                foreach ($IMDB->getAll() as $aItem) {
                    if ($IMDB::$sNotFound !== $aItem['value']) {
                        $value[lcfirst($aItem['name'])] = $aItem['value'];
                    }
                }
            }
            return new JSONResponse($value);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    
    /**
     * @NoAdminRequired
     *
     * @param string $url
     * @param string $imdbId
     */
    public static function getImage(string $url)
    {
        return new DataResponse(self::imageToBase64($url));
    }
    private static function imageToBase64($image){
        $imageData = base64_encode(self::curl_get_contents($image));
        $mime_types = array(
        'gif' => 'image/gif',
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'bmp' => 'image/bmp'
        );
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        if (array_key_exists($ext, $mime_types)) {
            $a = $mime_types[$ext];
        }
        return 'data: '.$a.';base64,'.$imageData;
    }
    private static function curl_get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
