<?php
class Logger {
    private $logFile;

    public function __construct($logFile = 'db.log') {
        $this->logFile = $logFile;
    }

    public function log($message, $level = 'INFO') {
        $timestamp = date('H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

class Product {
    private $pdo;
    private $logger;

    public function __construct(PDO $pdo, Logger $logger) {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function getAllProducts() {
        try {
            $this->logger->log('Запрос на получение всех продуктов');

            $query = $this->pdo->query("SELECT * FROM product");
            $products = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product) {
                echo $product['NAME'] . '<br>';
            }

            $this->logger->log('Успешно отображено ' . count($products) . ' продуктов');

        } catch (Exception $e) {
            $this->logger->log('Ошибка при получении всех продуктов: ' . $e->getMessage(), 'ERROR');
            echo 'Произошла ошибка при загрузке продуктов. Пожалуйста, попробуйте позже.' . PHP_EOL;
        }
    }
}

try {
    $logger = new Logger();

    $logger->log('Попытка подключения к базе данных');
    /* < MySQL
    $pdo = new PDO('mysql:host=localhost;dbname=db;charset=utf8mb4', 'user', 'pass', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    */// > MySQL or SQLite <
    $pdo = new PDO('sqlite:../task2/db.sqlite');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    # > SQLite
    $logger->log('Успешное подключение к базе данных');

    $Product = new Product($pdo, $logger);

    $Product->getAllProducts();
} catch (PDOException $e) {
    $logger->log('Ошибка подключения к базе данных: ' . $e->getMessage(), 'ERROR');
    die('Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
} catch (Exception $e) {
    $logger->log('Неизвестная ошибка: ' . $e->getMessage(), 'ERROR');
    die('Произошла непредвиденная ошибка.');
}