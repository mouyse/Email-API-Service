<?php
use PHPUnit\Framework\TestCase;
use Src\Database\PdoFactory;

class PdoFactoryTest extends TestCase
{
    // public function testCreateReturnsPDO()
    // {
    //     $config = [
    //         'host' => 'localhost',
    //         'name' => 'test_db',
    //         'user' => 'root',
    //         'password' => ''
    //     ];
    //     $pdo = PdoFactory::create($config);
    //     $this->assertInstanceOf(\PDO::class, $pdo);
    // }

    public function testCreateThrowsExceptionOnInvalidConfig()
    {
        $this->expectException(\PDOException::class);
        PdoFactory::create([
            'host' => 'invalid',
            'name' => 'invalid',
            'user' => 'invalid',
            'password' => 'invalid'
        ]);
    }
}