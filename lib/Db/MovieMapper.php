<?php
namespace OCA\MoviesCollection\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class MovieMapper extends QBMapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'movies_collection', Movie::class);
    }

    public function find(int $id, string $userId) {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
            ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ;

        return $this->findEntity($qb);
    }

    public function findAll(string $userId, int $page) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('id`, title, year, poster_url, rating, `listed', 'created')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->orderBy('created', 'DESC')
           ->setFirstResult(30*$page)
           ->setMaxResults(30);

        return $this->findEntities($qb);
    }

    public function findListed(string $userId, int $page) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('id`, title, year, poster_url, rating, `listed', 'created')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('listed', $qb->createNamedParameter('1')))
           ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->orderBy('created', 'DESC')
           ->setFirstResult(30*$page)
           ->setMaxResults(30);

        return $this->findEntities($qb);
    }

    public function findBestOrder(string $userId, int $page) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('id`, title, year, poster_url, rating, `listed', 'created')
           ->from($this->getTableName())
           ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->orderBy('rating', 'DESC')
           ->setFirstResult(30*$page)
           ->setMaxResults(30);

        return $this->findEntities($qb);
    }

    public function findAllByGenre(string $userId, string $genre, int $page) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('id`, title, year, poster_url, rating, `listed', 'created')
           ->from($this->getTableName())
           ->where('LOWER(genre) LIKE LOWER(:genre)')
           ->setParameter('genre', '%' . $genre . '%')
           ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->orderBy('created', 'DESC')
           ->setFirstResult(30*$page)
           ->setMaxResults(30);

        return $this->findEntities($qb);
    }
    
    public function search(string $userId, string $search) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('id`, title, year, poster_url, rating, `listed', 'created')
           ->from($this->getTableName())
           ->where('LOWER(title) LIKE LOWER(:title)')
           ->setParameter('title', '%' . $search . '%')
           ->orWhere('LOWER(original_title) LIKE LOWER(:original_title)')
           ->setParameter('original_title', '%' . $search . '%')
           ->orWhere('LOWER(cast) LIKE LOWER(:cast)')
           ->setParameter('cast', '%' . $search . '%')
           ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->setMaxResults(20);

        return $this->findEntities($qb);
    }

}