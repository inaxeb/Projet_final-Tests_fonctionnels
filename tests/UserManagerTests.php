<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../UserManager.php';

class UserManagerTests extends TestCase
{
    private UserManager $userManager;
    private PDO $db;

    protected function setUp(): void
    {
        require __DIR__ . '/../config.php';
        $this->db = $pdo;
        $this->userManager = new UserManager($this->db);

        $this->db->exec("DELETE FROM users");
    }

    public function testAddUser(): void
    {
        $name = 'kparry';
        $email = 'e2e_' . time() . '@test.com';

        $this->userManager->addUser($name, $email);

        $users = $this->userManager->getUsers();
        $this->assertCount(1, $users);
        $this->assertEquals($name, $users[0]['name']);
        $this->assertEquals($email, $users[0]['email']);
    }

    public function testRemoveUser(): void
    {
        
        $name = 'kparry';
        $email = 'test@test.fr';
        $this->userManager->addUser($name, $email);

        $users = $this->userManager->getUsers();
        $userId = $users[0]['id'];

        $this->userManager->removeUser($userId);

        $users = $this->userManager->getUsers();
        $this->assertCount(0, $users);
    }

    public function testGetUsers(): void
    {
        
        $this->userManager->addUser('User 1', 'user1@test.com');
        $this->userManager->addUser('User 2', 'user2@test.com');

        
        $users = $this->userManager->getUsers();
        
        
        $this->assertCount(2, $users);
    }

    public function testUpdateUser(): void
    {
        $this->userManager->addUser('Old Name', 'oldemail@test.com');

        $users = $this->userManager->getUsers();
        $userId = $users[0]['id'];

        $this->userManager->updateUser($userId, 'New Name', 'newemail@test.com');

        $updatedUser = $this->userManager->getUser($userId);
        $this->assertEquals('New Name', $updatedUser['name']);
        $this->assertEquals('newemail@test.com', $updatedUser['email']);
    }
}
