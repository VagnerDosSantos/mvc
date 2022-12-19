<?php

namespace Infra\Database;

abstract class PDODatabase implements Database
{
    protected static $instance;

    public function beginTransaction()
    {
        self::$instance->beginTransaction();
    }

    public function commit()
    {
        self::$instance->commit();
    }

    public function rollBack()
    {
        self::$instance->rollBack();
    }
}
