<?php
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

use App\Database\DataAccess;

/**
 * Class Rates
 */
class Rates
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger, DataAccess $dataaccess)
    {
        $this->logger = $logger;
        $this->dataaccess = $dataaccess;
    }

    public function checkRateLimit(Request $request, Response $response, $args)
    {
        $path = explode('/', $request->getUri()
            ->getPath()) [1];
        $arrparams = $request->getParams();

        // log route was hit
        $route = $request->getAttribute('route');
        $this
            ->logger
            ->info($route->getName() . ' hit.');

        // get user ip address
        $ipAddress = $request->getAttribute('ip_address');

        return $response->write(json_encode($this
            ->dataaccess
            ->checkRateLimitQuery($path, $arrparams, $ipAddress)));
    }
}

