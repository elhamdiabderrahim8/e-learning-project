<?php
// db_prof.php - Emulateur PDO pour la compatibilité avec l'ancien code mysqli de l'espace professeur
// Compatible Railway (variables d'environnement) et XAMPP (localhost)

class MysqliResultEmulator {
    private $stmt;
    public $num_rows;

    public function __construct($stmt) {
        $this->stmt = $stmt;
        $this->num_rows = $stmt->rowCount();
    }

    public function fetch_assoc() {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
}

class MysqliStatementEmulator {
    private $pdo_stmt;
    private $params = [];
    private $types = '';

    public $affected_rows = 0;
    public $error = '';

    public function __construct($pdo_stmt) {
        $this->pdo_stmt = $pdo_stmt;
    }

    public function bind_param($types, &...$vars) {
        $this->types = $types;
        $this->params = [];
        foreach ($vars as &$var) {
            $this->params[] = &$var;
        }
    }

    public function send_long_data($param_num, $data) {
        $this->params[$param_num] = $data;
    }

    public function execute() {
        try {
            $result = $this->pdo_stmt->execute($this->params);
            $this->affected_rows = $this->pdo_stmt->rowCount();
            return $result;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function get_result() {
        return new MysqliResultEmulator($this->pdo_stmt);
    }

    public function close() {
        $this->pdo_stmt = null;
    }
}

class MysqliEmulator {
    private $pdo;
    public $error = '';
    public $connect_error = null;

    public function __construct() {
        $host   = getenv('DB_HOST') ?: 'localhost';
        $port   = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'elearning';
        $user   = getenv('DB_USER') ?: 'root';
        $pass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            $this->connect_error = $e->getMessage();
            die("Erreur de connexion : " . $this->connect_error);
        }
    }

    public function query($sql) {
        try {
            $stmt = $this->pdo->query($sql);
            if ($stmt === false) return false;
            return new MysqliResultEmulator($stmt);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function prepare($sql) {
        try {
            // Convert ? to PDO ? 
            // Luckily PDO also supports ? positional parameters
            $stmt = $this->pdo->prepare($sql);
            return new MysqliStatementEmulator($stmt);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function real_escape_string($string) {
        // Remove quotes added by quote() to emulate real_escape_string
        return substr($this->pdo->quote($string), 1, -1);
    }

    public function close() {
        $this->pdo = null;
    }
}

function db_prof() {
    static $conn = null;
    if ($conn === null) {
        $conn = new MysqliEmulator();
    }
    return $conn;
}

// Support pour mysqli_real_escape_string si utilisé d'une façon procédurale
if (!function_exists('mysqli_real_escape_string')) {
    function mysqli_real_escape_string($conn, $escapestr) {
        if ($conn instanceof MysqliEmulator) {
            return $conn->real_escape_string($escapestr);
        }
        return $escapestr;
    }
}
?>
