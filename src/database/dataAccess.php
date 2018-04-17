<?php
namespace App\Database;

use Psr\Log\LoggerInterface;
use PDO;

/**
 * Class DataAccess.
 */
class DataAccess
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \App\DataAccess
     */
    private $maintable;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \PDO                     $pdo
     */

    public function __construct(LoggerInterface $logger, PDO $pdo, $table)
    {
        $this->logger = $logger;
        $this->pdo = $pdo;
        $this->maintable = $table;
    }

    public function checkRateLimitQuery($path, $arrparams, $ip)
    {
        $query = "select count(*) as `Total` from xrequests where originip='$ip'";

        try
        {

            $stmt = $this
                ->pdo
                ->prepare($query);
            $stmt->execute();
            if ($stmt)
            {
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = null;
            }

            return array(
                "status" => "success",
                "max" => getenv('RATE_LIMIT') ,
                "data" => $result
            );

        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function searchCategoriesQuery($path, $arrparams)
    {
        $query = "select distinct book_category from library order by book_category";

        try
        {

            $stmt = $this
                ->pdo
                ->prepare($query);
            $stmt->execute();

            if ($stmt)
            {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = null;
            }

            return array(
                "status" => "success",
                "list" => $result
            );
        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function getBooksQuery($path, $arrparams, $offset)
    {
        $query = "SELECT * FROM library ORDER BY book_id LIMIT :limit OFFSET :offset";
        $countQuery = "SELECT * FROM library";

        try
        {
            $cnt = $this
                ->pdo
                ->prepare($countQuery);
            $stmt = $this
                ->pdo
                ->prepare($query);
            $stmt->bindValue(':limit', $arrparams['limit'], \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $cnt->execute();
            $count = $cnt->rowCount();

            if ($stmt)
            {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = null;
            }
            return array(
                "status" => "success",
                "count" => $count,
                "page" => $arrparams['page'],
                "list" => $result
            );
        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function getBookQuery($path, $arrparams, $id)
    {
        $query = "select * from library where book_id=$id";

        try
        {
            $stmt = $this
                ->pdo
                ->prepare($query);
            $stmt->execute();

            if ($stmt)
            {
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = null;
            }
            return array(
                "status" => "success",
                "item" => $result
            );
        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function addBookQuery($path, $request_data)
    {
        $query = "INSERT INTO library (book_name,book_isbn,book_category) VALUES (:book_name,:book_isbn,:book_category)";

        try
        {

            $stmt = $this
                ->pdo
                ->prepare($query);

            foreach ($request_data as $key => $value)
            {
                $stmt->bindValue(':' . $key, $request_data[$key]);
            }

            $stmt->execute();

            if ($stmt)
            {
                return array(
                    "status" => "success",
                    "message" => "Added Book"
                );
            }
            else
            {
                return array(
                    "status" => "error",
                    "message" => 'Exception: ' . $e->getMessage()
                );
            }

        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function updateBookQuery($path, $request_data, $id)
    {
        $query = "UPDATE library SET book_name = :book_name, book_isbn = :book_isbn, book_category = :book_category WHERE book_id = $id";

        try
        {

            $stmt = $this
                ->pdo
                ->prepare($query);

            foreach ($request_data as $key => $value)
            {
                $stmt->bindValue(':' . $key, $request_data[$key]);
            }

            $stmt->execute();

            if ($stmt)
            {
                return array(
                    "status" => "success",
                    "message" => "Updated Book"
                );
            }
            else
            {
                return array(
                    "status" => "error",
                    "message" => 'Exception: ' . $e->getMessage()
                );
            }

        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function deleteBookQuery($path, $arrparams, $id)
    {
        $query = "DELETE FROM library WHERE book_id=$id";

        try
        {
            $stmt = $this
                ->pdo
                ->prepare($query);
            $stmt->execute();

            if ($stmt)
            {
                return array(
                    "status" => "success",
                    "message" => "Successfully deleted book"
                );
            }
            else
            {
                return array(
                    "status" => "error",
                    "message" => 'Exception: ' . $e->getMessage()
                );
            }

        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function searchByNameQuery($path, $arrparams, $item, $offset)
    {
        $query = "SELECT * FROM library WHERE book_name LIKE '%$item%' LIMIT :limit OFFSET :offset";
        $countQuery = "SELECT * FROM library WHERE book_name LIKE '%$item%'";

        try
        {
            $stmt = $this
                ->pdo
                ->prepare($query);
            $cnt = $this
                ->pdo
                ->prepare($countQuery);
            $stmt->bindValue(':limit', $arrparams['limit'], \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $cnt->execute();
            $count = $cnt->rowCount();

            if ($stmt)
            {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = null;
            }

            return array(
                "status" => "success",
                "count" => $count,
                "page" => $arrparams['page'],
                "list" => $result
            );
        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function searchByISBNQuery($path, $arrparams, $item, $offset)
    {
        $query = "SELECT * FROM library WHERE book_isbn LIKE '%$item%' LIMIT :limit OFFSET :offset";
        $countQuery = "SELECT * FROM library WHERE book_isbn LIKE '%$item%'";

        try
        {
            $stmt = $this
                ->pdo
                ->prepare($query);
            $cnt = $this
                ->pdo
                ->prepare($countQuery);
            $stmt->bindValue(':limit', $arrparams['limit'], \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $cnt->execute();
            $count = $cnt->rowCount();

            if ($stmt)
            {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = null;
            }

            return array(
                "status" => "success",
                "count" => $count,
                "page" => $arrparams['page'],
                "list" => $result
            );
        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

    public function searchByCategoryQuery($path, $arrparams, $item, $offset)
    {
        $query = "select * from library where book_category like '%$item%' LIMIT :limit OFFSET :offset";
        $countQuery = "select * from library where book_category like '%$item%'";

        try
        {
            $stmt = $this
                ->pdo
                ->prepare($query);
            $cnt = $this
                ->pdo
                ->prepare($countQuery);
            $stmt->bindValue(':limit', $arrparams['limit'], \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $cnt->execute();
            $count = $cnt->rowCount();

            if ($stmt)
            {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = null;
            }

            return array(
                "status" => "success",
                "count" => $count,
                "page" => $arrparams['page'],
                "list" => $result
            );
        }
        catch(PDOException $e)
        {
            return array(
                "status" => "error",
                "message" => 'Exception: ' . $e->getMessage()
            );
        }
    }

}

