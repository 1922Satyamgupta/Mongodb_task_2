
<?php
use Phalcon\Mvc\MongoCollection;

class UserCollection extends MongoCollection
{
    public $name;
    public $email;
    public $password;

    public function getSource()
    {
        return 'users';
    }
}