<?php
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

use App\Database\DataAccess;

/**
 * Class Books
 */
class Books
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \App\DataAccess
     */
    protected $dataaccess;

    public function __construct(LoggerInterface $logger, DataAccess $dataaccess)
    {
        $this->logger = $logger;
        $this->dataaccess = $dataaccess;
    }

    public function getBooks(Request $request, Response $response, $args)
    {
        $path = explode('/', $request->getUri()
            ->getPath()) [1];
        $arrparams = $request->getParams();

        $page = (isset($arrparams['page']) && $arrparams['page'] > 0) ? $arrparams['page'] : 1;
        $limit = isset($arrparams['limit']) ? $arrparams['limit'] : 10;
        $offset = (--$page) * $limit;

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        return $response->write(json_encode($this
            ->dataaccess
            ->getBooksQuery($path, $arrparams, $offset)));
    }

    public function getBook(Request $request, Response $response, $args)
    {
        $path = explode('/', $request->getUri()
            ->getPath()) [1];
        $arrparams = $request->getParams();

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        $id = $request->getAttribute('id');

        return $response->write(json_encode($this
            ->dataaccess
            ->getBookQuery($path, $arrparams, $id)));

    }

    public function addBooks(Request $request, Response $response, $args)
    {
        $path = explode('/', $request->getUri()
            ->getPath()) [1];

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        $request_items = array(
            "book_name" => $request->getParsedBody() ['book_name'],
            "book_isbn" => $request->getParsedBody() ['book_isbn'],
            "book_category" => $request->getParsedBody() ['book_category']
        );

        return $response->write(json_encode($this
            ->dataaccess
            ->addBookQuery($path, $request_items)));
    }

    public function updateBook(Request $request, Response $response, $args)
    {
        $path = explode('/', $request->getUri()
            ->getPath()) [1];
        $id = $request->getAttribute('id');

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        $request_items = array(
            "book_name" => $request->getParsedBody() ['book_name'],
            "book_isbn" => $request->getParsedBody() ['book_isbn'],
            "book_category" => $request->getParsedBody() ['book_category']
        );

        return $response->write(json_encode($this
            ->dataaccess
            ->updateBookQuery($path, $request_items, $id)));
    }

    public function deleteBook(Request $request, Response $response, $args)
    {
        $path = explode('/', $request->getUri()
            ->getPath()) [1];
        $id = $request->getAttribute('id');

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        return $response->write(json_encode($this
            ->dataaccess
            ->deleteBookQuery($path, $arrparams, $id)));
    }

    public function searchBooks(Request $request, Response $response, $args)
    {

        $path = explode('/', $request->getUri()
            ->getPath()) [1];
        $arrparams = $request->getParams();
        $query = $request->getAttribute('query');

        $page = (isset($arrparams['page']) && $arrparams['page'] > 0) ? $arrparams['page'] : 1;
        $limit = isset($arrparams['limit']) ? $arrparams['limit'] : 10;
        $offset = (--$page) * $limit;

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        return $response->write(json_encode($this
            ->dataaccess
            ->searchByNameQuery($path, $arrparams, $query, $offset)));
    }

    public function searchISBN(Request $request, Response $response, $args)
    {

        $path = explode('/', $request->getUri()
            ->getPath()) [1];
        $arrparams = $request->getParams();
        $query = $request->getAttribute('query');

        $page = (isset($arrparams['page']) && $arrparams['page'] > 0) ? $arrparams['page'] : 1;
        $limit = isset($arrparams['limit']) ? $arrparams['limit'] : 10;
        $offset = (--$page) * $limit;

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        return $response->write(json_encode($this
            ->dataaccess
            ->searchByISBNQuery($path, $arrparams, $query, $offset)));
    }

    public function searchCategory(Request $request, Response $response, $args)
    {

        $path = explode('/', $request->getUri()
            ->getPath()) [1];
        $arrparams = $request->getParams();
        $query = $request->getAttribute('query');

        $page = (isset($arrparams['page']) && $arrparams['page'] > 0) ? $arrparams['page'] : 1;
        $limit = isset($arrparams['limit']) ? $arrparams['limit'] : 10;
        $offset = (--$page) * $limit;

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        return $response->write(json_encode($this
            ->dataaccess
            ->searchByCategoryQuery($path, $arrparams, $query, $offset)));
    }
}

