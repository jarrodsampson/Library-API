<?php
use Slim\Middleware\TokenAuthentication;

// caching options
$app->add(new \Slim\HttpCache\Cache('public', 86400));

// Add Lazy CORS
$app->options('/{routes:.+}', function ($request, $response, $args)
{
    return $response;
});
$app->add(function ($req, $res, $next)
{
    $response = $next($req, $res);
    return $response->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', $this->get('settings') ['cors'])
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Rate Limiter
$app->add(function ($request, $response, $next)
{

    $responsen = $response->withHeader('X-Powered-By', $this->settings['PoweredBy']);

    $APIRateLimit = new App\Utils\APIRateLimiter($this);
    $mustbethrottled = $APIRateLimit();

    if ($mustbethrottled == false)
    {
        $responsen = $next($request, $responsen);
    }
    else
    {
        $responsen = $responsen->withStatus(429)
            ->withHeader('RateLimit-Limit', $this->settings['api_rate_limiter']['requests'])
            ->withJson(array(
            'status' => 'error',
            'message' => 'You have reached your daily limit of requests.',
            'dailyLimit' => $this->settings['api_rate_limiter']['requests']
        ));
    }
    return $responsen;
});

// ip address checker
$checkProxyHeaders = true;
$trustedProxies = [];
$app->add(new RKA\Middleware\IpAddress($checkProxyHeaders, $trustedProxies));

/**
 * Token authentication middleware
 */
$authenticator = function ($request, TokenAuthentication $tokenAuth)
{
    /**
     * Try find authorization token via header, parameters, cookie or attribute
     * If token not found, return response with status 401 (unauthorized)
     */
    $token = $tokenAuth->findToken($request);

    $auth = new \App\Authorization\Auth();

    $auth->getUserByToken($token);
};
$app->add(new TokenAuthentication(['path' => '/api', 'authenticator' => $authenticator, 'secure' => false]));

$app->add(function ($request, $response, $next)
{
    //$response->getBody()->write('BEFORE');
    //$ipAddress = $request->getAttribute('ip_address');
    //$this->logger->addInfo('Route Accessed ' . $ipAddress);
    $response = $next($request, $response);
    return $response;
});

