<?php
use App\Controllers\Books;
use App\Controllers\Categories;
use App\Controllers\Rates;

// API group
$app->group('/api', function () use ($app)
{
    // Version group
    $app->group('/v1', function () use ($app)
    {

        $app->get('/ratelimit', Rates::class . ':checkRateLimit')
            ->setName('RateLimit');

        // books routes
        $app->get('/books', Books::class . ':getBooks')
            ->setName('AllBooks');
        $app->get('/books/{id}', Books::class . ':getBook')
            ->setName('GetBookDetail');
        $app->post('/books', Books::class . ':addBooks')
            ->setName('AddBook');
        $app->put('/books/{id}', Books::class . ':updateBook')
            ->setName('UpdateBook');
        $app->delete('/books/{id}', Books::class . ':deleteBook')
            ->setName('DeleteBook');

        // search route group
        $app->group('/search', function () use ($app)
        {
            $app->get('/name/{query}', Books::class . ':searchBooks')
                ->setName('SearchBookName');
            $app->get('/isbn/{query}', Books::class . ':searchISBN')
                ->setName('SearchBookISBN');
            $app->get('/category/{query}', Books::class . ':searchCategory')
                ->setName('SearchBookCategory');
            $app->get('/categories', Categories::class . ':getCategories')
                ->setName('Search Categories');
        });
    });
});

// Default 404 Route
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($req, $res)
{
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

