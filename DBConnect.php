<?php

interface IConnectInfo
{
    const DBTYPE = 'mysql';
    const HOST = 'localhost';
    const UNAME = 'root';
    const PASSWORD = 'password';
    const DBNAME = 'testdb';
    static public function connect();
}

//// Two diff Connection
class PDOConnect implements IConnectInfo
{
    private static $server = IConnectInfo::HOST;
    private static $DBType = IConnectInfo::DBTYPE;
    private static $DBName = IConnectInfo::DBNAME;
    private static $username = IConnectInfo::UNAME;
    private static $password = IConnectInfo::PASSWORD;
    private static $hookup;

    static public function connect()
    {
        $dsn = self::$DBType . ':host=' . self::$server . 'dbname=' . self::$DBName;
        self::$hookup = new PDO($dsn, self::$username, self::$password, [PDO::ATTR_PERSISTENT => true]);
        // throw PDOExcetion if error
        return self::$hookup;
    }
}

class MysqliConnect implements IConnectInfo
{
    private static $server = IConnectInfo::HOST;
    private static $DBName = IConnectInfo::DBNAME;
    private static $username = IConnectInfo::UNAME;
    private static $password = IConnectInfo::PASSWORD;
    private static $hookup;

    /**
     * use static method to avoid create instance of  MysqliConnect over and over again
     * @return false|mysqli
     * @throws Exception
     */
    static public function connect()
    {
        self::$hookup = mysqli_connect(self::$server, self::$username, self::$password, self::$DBName);
        if (!self::$hookup) {
            throw new Exception('connect failed:' . mysqli_connect_error());
        }
        return self::$hookup;
    }
}

//// Two diff Clients
class PDOClient
{
    private $hookup;
    public function __construct()
    {
        // just use it without details about connecting
        $this->hookup = PDOConnect::connect();
    }

    public function __destruct()
    {
        unset($this->hookup);
    }
    public function getData($limit = 3)
    {
        if (!is_numeric($limit) || $limit == 0) {
            throw new Exception('limit param error');
        }
        $sql = "SELECT 'id','name', 'address',  FROM 'table' LIMIT $limit";
        $stmt = $this->hookup->query($sql);
        foreach ($stmt as $row){
            echo "id:{$row['id']}, name:{$row['name']}, address:{$row['address']});" . PHP_EOL;
        }
    }
}

class MysqliClient
{
    private $hookup;

    public function __construct()
    {
        $this->hookup = MysqliConnect::connect();
    }

    public function __destruct()
    {
        $this->hookup->close();
    }

    public function getData($limit = 3)
    {
        if (!is_numeric($limit) || $limit == 0) {
            throw new Exception('limit param error');
        }
        $sql = "SELECT 'id','name', 'address',  FROM 'table' LIMIT $limit";
        $stmt = $this->hookup->prepare($sql);
        if (!$stmt) {
            throw new Exception('stmt prepare error');
        }
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $name, $address);
        while ($stmt->fetch()) {
            echo "id:$id, name:$name, address:$address);" . PHP_EOL;
        }
    }
}

//// worker
try {
    $worker1 = new MysqliClient();
    $worker1->getData();

    $worker2 = new PDOClient();
    $worker2->getData();

}catch (Exception $e){
    echo $e->getMessage();
}