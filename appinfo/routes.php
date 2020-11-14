<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\MoviesCollection\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'movie#list', 'url' => '/list/{genre}', 'verb' => 'GET'],
        ['name' => 'movie#search', 'url' => '/search/{search}', 'verb' => 'GET'],

        ['name' => 'movie#show', 'url' => '/movie/{id}', 'verb' => 'GET'],
        ['name' => 'movie#create', 'url' => '/movie', 'verb' => 'POST'],
        ['name' => 'movie#updateListed', 'url' => '/movie/listed/{id}', 'verb' => 'PUT'],
        ['name' => 'movie#update', 'url' => '/movie/{id}', 'verb' => 'PUT'],
        ['name' => 'movie#destroy', 'url' => '/movie/{id}', 'verb' => 'DELETE'],

        ['name' => 'imdb#imdb', 'url' => '/imdb/{search}', 'verb' => 'GET'],
        ['name' => 'imdb#getImage', 'url' => '/getImage', 'verb' => 'GET'],
    ]
];
