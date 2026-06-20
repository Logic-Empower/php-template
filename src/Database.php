<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? self::connect();
    }

    public static function connect(): PDO
    {
        $host = (string) env_value('DB_HOST', '127.0.0.1');
        $port = (string) env_value('DB_PORT', '3306');
        $database = (string) env_value('DB_DATABASE', '');
        $username = (string) env_value('DB_USERNAME', '');
        $password = (string) env_value('DB_PASSWORD', '');
        $charset = (string) env_value('DB_CHARSET', 'utf8mb4');

        if ($database === '' || $username === '') {
            throw new RuntimeException('Database credentials are not configured.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $port,
            $database,
            $charset
        );

        try {
            return new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());

            throw new RuntimeException('Unable to connect to the database.');
        }
    }

    public function insertContactSubmission(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO contact_submissions
                (name, email, phone, message, ip_address, user_agent, created_at)
             VALUES
                (:name, :email, :phone, :message, :ip_address, :user_agent, NOW())'
        );

        $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
