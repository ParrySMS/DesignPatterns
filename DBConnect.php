<?php
interface IConnectInfo
{
    const HOST = 'localhost';
    const UNAME = 'root';
    const PASSWORD = '123456';
    const DBNAME = 'testdb';
}

class MysqliConnect implements IConnectInfo
{
    //todo
}
