<?php
class Connection
{
    private $dbhost = "localhost";
    private $dbname = "";
    private $username = "";
    private $password = "";
    public function getHost()
    {
        return $this->dbhost;
    }

    public function getDBName()
    {
        return $this->dbname;
    }

    public function getUsername()
    {
        return $this->username;
    }
    
    public function getPWD()
    {
        return $this->password;
    }
}
function getKey()
{
     return "JUlzGXNaj7BJe";
}
?>