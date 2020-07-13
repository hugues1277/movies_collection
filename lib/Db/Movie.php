<?php
namespace OCA\MoviesCollection\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Movie extends Entity implements JsonSerializable {

    public $id;
    protected $imdbId;
    protected $title;
    protected $originalTitle;
    protected $director;
    protected $genre;
    protected $year;
    protected $runtime;
    protected $posterUrl;
    protected $trailerUrl;
    protected $synopsis;
    protected $cast;
    protected $userId;
    protected $rating;
    protected $comment;
    protected $listed;
    protected $created;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'imdbId' => $this->imdbId,
            'title' => $this->title,
            'originalTitle' => $this->originalTitle,
            'director' => $this->director,
            'genre' => $this->genre,
            'year' => $this->year,
            'runtime' => $this->runtime,
            'posterUrl' => $this->posterUrl,
            'trailerUrl' => $this->trailerUrl,
            'synopsis' => $this->synopsis,
            'cast' => $this->cast,
            'userId' => $this->userId,
            'rating' => $this->rating,
            'comment' => $this->comment,            
            'listed' => $this->listed,
            'created' => $this->created,
        ];
    }
}