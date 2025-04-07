<?php
class UserManager {
    private PDO $db;

    public function __construct(PDO $pdo = null) {
        if ($pdo !== null) {
            $this->db = $pdo;
        } else {
            require_once 'config.php';
            $this->db = $pdo;
        }
    }

    public function addUser(string $name, string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email invalide.");
        }
    
        $stmt = $this->db->prepare("INSERT IGNORE INTO users (name, email) VALUES (:name, :email)");
        $stmt->execute(['name' => $name, 'email' => $email]);
    
        if ($stmt->rowCount() == 0) {
            echo json_encode(["message" => "L'utilisateur existe déjà."]);
        }
    }
    

    public function addRandomUsers(int $count): void {
        for ($i = 0; $i < $count; $i++) {
            $name = 'User ' . ($i + 1);
            $email = 'user' . uniqid() . '@test.com';
            $this->addUser($name, $email);
        }
    }

    public function removeUser(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function getUsers(): array {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function getUser(int $id): array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user) throw new Exception("Utilisateur introuvable.");
        return $user;
    }

    public function updateUser(int $id, string $name, string $email): void {
        $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name, 'email' => $email]);
    }
}
?>
