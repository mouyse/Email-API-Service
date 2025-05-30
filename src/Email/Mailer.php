<?php
namespace Src;
use Src\Database\DBConnector;

class Email{
    private $db;
    private $table = 'emails';

    public $subject;
    public $body;
    public $to;
    public $from;
    public $status;

    // Applying Dependency Injection for the database connection
    // This allows for easier testing and flexibility in changing the database connection
    public function __construct(DBConnector $db){
        $this->db = $db;
    }

    public function send(){
        //Data to be inserted into the database
    }
}