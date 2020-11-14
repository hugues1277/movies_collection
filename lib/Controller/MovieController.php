<?php

namespace OCA\MoviesCollection\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\MoviesCollection\Db\Movie;
use OCA\MoviesCollection\Db\MovieMapper;

class MovieController extends Controller {

    private $mapper;
    private $userId;

    public function __construct(string $AppName, IRequest $request, MovieMapper $mapper, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->mapper = $mapper;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     */
    public function index()
    {
        return new DataResponse($this->mapper->findAll($this->userId));
    }

    /**
     * @NoAdminRequired
     * 
     * @param string $genre
     * @param string $page
     */
    public function list(string $genre, int $page)
    {
        if($genre=="all"){
            return new DataResponse($this->mapper->findAll($this->userId, $page));
        }else if($genre=="listed"){
            return new DataResponse($this->mapper->findListed($this->userId, $page));
        }else if($genre=="best"){
            return new DataResponse($this->mapper->findBestOrder($this->userId, $page));
        }else{
            return new DataResponse($this->mapper->findAllByGenre($this->userId, $genre, $page));
        }
                            try {
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     * 
     * @param string $search
     */
    public function search(string $search)
    {
        return new DataResponse($this->mapper->search($this->userId, $search));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show(int $id)
    {
        try {
            return new DataResponse($this->mapper->find($id, $this->userId));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param string $imdbId
     * @param string $title
     * @param string $originalTitle
     * @param string $director
     * @param string $genre
     * @param string $year
     * @param string $runtime
     * @param string $posterUrl
     * @param string $trailerUrl
     * @param string $synopsis
     * @param string $cast
     * @param int $rating
     * @param string $comment
     * @param int $listed
     */
    public function create( string $imdbId,
                            string $title,
                            string $originalTitle,
                            string $director,
                            string $genre,
                            string $year,
                            string $runtime,
                            string $posterUrl,
                            string $trailerUrl,
                            string $synopsis,
                            string $cast,
                            int $rating,
                            string $comment,
                            int $listed)
    {
        if(strlen($title) < 1 || strlen($posterUrl) < 5){
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }

        $date = new \DateTime();

        $movie = new Movie();
        $movie->setImdbId($imdbId);
        $movie->setTitle($title);
        $movie->setOriginalTitle($originalTitle);
        $movie->setDirector($director);
        $movie->setGenre($genre);
        $movie->setYear($year);
        $movie->setRuntime($runtime);
        $movie->setPosterUrl($posterUrl);
        $movie->setTrailerUrl($trailerUrl);
        $movie->setSynopsis($synopsis);
        $movie->setCast($cast);
        $movie->setRating($rating);
        $movie->setComment($comment);
        $movie->setListed($listed);
        $movie->setCreated($date->getTimestamp());
        $movie->setUserId($this->userId);

        try {
            return new DataResponse($this->mapper->insert($movie));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $imdbId
     * @param string $title
     * @param string $originalTitle
     * @param string $director
     * @param string $genre
     * @param string $year
     * @param string $runtime
     * @param string $posterUrl
     * @param string $trailerUrl
     * @param string $synopsis
     * @param string $cast
     * @param int $rating
     * @param string $comment
     * @param int $listed
     */
    public function update( int $id,
                            string $imdbId,
                            string $title,
                            string $originalTitle,
                            string $director,
                            string $genre,
                            string $year,
                            string $runtime,
                            string $posterUrl,
                            string $trailerUrl,
                            string $synopsis,
                            string $cast,
                            int $rating,
                            string $comment,
                            int $listed)
    {
        if(strlen($title) < 1 || strlen($posterUrl) < 5){
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
        
        try {
            $movie = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $movie->setImdbId($imdbId);
        $movie->setTitle($title);
        $movie->setOriginalTitle($originalTitle);
        $movie->setDirector($director);
        $movie->setGenre($genre);
        $movie->setYear($year);
        $movie->setRuntime($runtime);
        $movie->setPosterUrl($posterUrl);
        $movie->setTrailerUrl($trailerUrl);
        $movie->setSynopsis($synopsis);
        $movie->setCast($cast);
        $movie->setRating($rating);
        $movie->setComment($comment);
        $movie->setListed($listed);
        $movie->setUserId($this->userId);
        return new DataResponse($this->mapper->update($movie));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function updateListed(int $id)
    {
        try {
            $movie = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $movie->setListed($movie->getListed()?0:1);
        return new DataResponse($this->mapper->update($movie));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        try {
            $movie = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->mapper->delete($movie);
        return new DataResponse($movie);
    }
}
