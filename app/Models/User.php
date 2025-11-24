<?php

namespace App\Models;

class User
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $query = "SELECT u.*, (SELECT MAX(datum) FROM clanky WHERE user_id = u.id AND viditelnost = 1 AND datum <= NOW()) as last_article FROM users u WHERE u.public_visible = 1 ORDER BY last_article DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT u.*, (SELECT COUNT(*) FROM clanky WHERE user_id = u.id AND viditelnost = 1 AND datum <= NOW()) AS views FROM users u WHERE u.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getByName($name, $surname)
    {
        $query = "SELECT u.*, (SELECT COUNT(*) FROM clanky WHERE user_id = u.id AND viditelnost = 1 AND datum <= NOW()) AS views FROM users u WHERE u.name = :name AND u.surname = :surname";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':surname', $surname, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO users (email, heslo, name, surname, role, profil_foto, popis)
                VALUES (:email, :heslo, :name, :surname, :role, :profil_foto, :popis)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $data['email'], \PDO::PARAM_STR);
        $stmt->bindValue(':heslo', $data['heslo'], \PDO::PARAM_STR);
        $stmt->bindValue(':name', $data['name'], \PDO::PARAM_STR);
        $stmt->bindValue(':surname', $data['surname'], \PDO::PARAM_STR);
        $stmt->bindValue(':role', $data['role'], \PDO::PARAM_INT);
        $stmt->bindValue(':profil_foto', $data['profil_foto'] ?? '', \PDO::PARAM_STR);
        $stmt->bindValue(':popis', $data['popis'] ?? '', \PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function update($data)
    {
        $query = "UPDATE users SET email = :email, name = :name, surname = :surname, role = :role,
                public_visible = :public_visible, profil_foto = :profil_foto, popis = :popis WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $data['id'], \PDO::PARAM_INT);
        $stmt->bindValue(':email', $data['email'], \PDO::PARAM_STR);
        $stmt->bindValue(':name', $data['name'], \PDO::PARAM_STR);
        $stmt->bindValue(':surname', $data['surname'], \PDO::PARAM_STR);
        $stmt->bindValue(':role', $data['role'], \PDO::PARAM_INT);
        $stmt->bindValue(':public_visible', $data['public_visible'] ?? 1, \PDO::PARAM_INT);
        $stmt->bindValue(':profil_foto', $data['profil_foto'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':popis', $data['popis'] ?? '', \PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getByEmail($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC); // Vrátí uživatele jako pole nebo false
        } catch (\PDOException $e) {
            error_log("Chyba při načítání uživatele podle e-mailu: " . $e->getMessage());
            return false;
        }
    }

    public function getAllWithSortingAndFiltering($sortBy = 'id', $order = 'ASC', $filter = '')
    {
        $allowedColumns = ['id', 'name', 'surname', 'email', 'role'];
        $allowedOrder = ['ASC', 'DESC'];

        // Ověření sloupce a směru řazení
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'id';
        }
        if (!in_array($order, $allowedOrder)) {
            $order = 'ASC';
        }

        // SQL dotaz pro filtrování a řazení
        $query = "
            SELECT * FROM users
            WHERE name LIKE :filter OR surname LIKE :filter OR email LIKE :filter
            ORDER BY $sortBy $order
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['filter' => '%' . $filter . '%']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createUser($data)
    {
        try {
            $hashedPassword = password_hash($data['heslo'], PASSWORD_DEFAULT); // Hash hesla pro bezpečnost
            $stmt = $this->db->prepare("
                INSERT INTO users (email, heslo, role, name, surname, profil_foto, popis)
                VALUES (:email, :heslo, :role, :name, :surname, :profil_foto, :popis)
            ");
            $stmt->bindParam(':email', $data['email'], \PDO::PARAM_STR);
            $stmt->bindParam(':heslo', $hashedPassword, \PDO::PARAM_STR);
            $stmt->bindParam(':role', $data['role'], \PDO::PARAM_INT); // Výchozí role = 0
            $stmt->bindParam(':name', $data['name'], \PDO::PARAM_STR);
            $stmt->bindParam(':surname', $data['surname'], \PDO::PARAM_STR);
            $profil_foto = '';
            $popis = '';
            $stmt->bindParam(':profil_foto', $profil_foto, \PDO::PARAM_STR);
            $stmt->bindParam(':popis', $popis, \PDO::PARAM_STR);
            return $stmt->execute(); // Vrátí true, pokud je vložení úspěšné
        } catch (\PDOException $e) {
            error_log("Chyba při vytváření uživatele: " . $e->getMessage());
            return false; // Pokud dojde k chybě, vrátí false
        }
    }

    public function checkEmailExists($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log("Chyba při ověřování e-mailu: " . $e->getMessage());
            return false;
        }
    }

    public function resetUserPassword($email, $newPassword)
    {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET heslo = :heslo WHERE email = :email");
            $stmt->bindParam(':heslo', $hashedPassword, \PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Chyba při resetu hesla: " . $e->getMessage());
            return false;
        }
    }

    // Uloží reset token do DB
    public function storeResetToken($userId, $email, $token, $expiresAt)
    {
        // Nejdříve smažeme všechny staré tokeny pro daného uživatele
        $deleteStmt = $this->db->prepare("
            DELETE FROM password_resets WHERE user_id = :user_id
        ");
        $deleteStmt->execute([':user_id' => $userId]);
        
        // Poté vložíme nový token
        $stmt = $this->db->prepare("
            INSERT INTO password_resets (user_id, email, token, expires_at)
            VALUES (:user_id, :email, :token, :expires_at)
        ");
        return $stmt->execute([
            ':user_id' => $userId,
            ':email' => $email,
            ':token' => $token,
            ':expires_at' => $expiresAt
        ]);
    }

    // Ověří platnost tokenu
    public function getValidResetToken($token)
    {
        try {
            error_log("DEBUG: Hledám token v databázi: " . $token);
            
            // Změna SQL dotazu - odstraníme podmínku expires_at >= NOW(), která může způsobovat problémy
            $stmt = $this->db->prepare("
                SELECT pr.*, u.email 
                FROM password_resets pr
                JOIN users u ON pr.user_id = u.id
                WHERE pr.token = :token
            ");
            $stmt->execute([':token' => $token]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("DEBUG: Token nalezen: " . print_r($result, true));
                
                // Kontrola expirace mimo SQL pro lepší diagnostiku
                $expiresAt = strtotime($result['expires_at']);
                $now = time();
                
                error_log("DEBUG: Expirace token: " . date('Y-m-d H:i:s', $expiresAt) . ", Nyní: " . date('Y-m-d H:i:s', $now));
                
                if ($expiresAt < $now) {
                    error_log("DEBUG: Token expiroval, expirace byla: " . $result['expires_at']);
                    return false;
                }
                
                return $result;
            } else {
                error_log("DEBUG: Token nenalezen v databázi: " . $token);
                return false;
            }
        } catch (\PDOException $e) {
            error_log("ERROR v getValidResetToken: " . $e->getMessage());
            return false;
        }
    }

    // Aktualizuje heslo uživatele
    public function updatePassword($userId, $hashedPassword)
    {
        $stmt = $this->db->prepare("
            UPDATE users SET heslo = :heslo WHERE id = :id
        ");
        return $stmt->execute([':heslo' => $hashedPassword, ':id' => $userId]);
    }

    // Smaže použitý token
    public function deleteResetToken($token)
    {
        $stmt = $this->db->prepare("
            DELETE FROM password_resets WHERE token = :token
        ");
        return $stmt->execute([':token' => $token]);
    }

    public function updateUser($id, $name, $surname, $email, $description, $profile_photo)
    {
        $query = "UPDATE users SET 
                    name = :name, 
                    surname = :surname, 
                    email = :email, 
                    popis = :description";
        
        $params = [
            ':id' => $id,
            ':name' => $name,
            ':surname' => $surname,
            ':email' => $email,
            ':description' => $description
        ];
        
        // Přidáme profil_foto pouze pokud je nastaveno
        if ($profile_photo !== null) {
            $query .= ", profil_foto = :profile_photo";
            $params[':profile_photo'] = $profile_photo;
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function getSocials($userId)
    {
        $query = "SELECT s.id as social_id, s.fa_class, s.nazev, us.link 
                  FROM user_social us 
                  JOIN socials s ON us.social_id = s.id 
                  WHERE us.user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteUserSocialLinks($userId)
    {
        $query = "DELETE FROM user_social WHERE user_id = :userId";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':userId' => $userId]);
    }

    public function saveUserSocialLink($userId, $socialId, $link)
    {
        $query = "INSERT INTO user_social (user_id, social_id, link) 
                  VALUES (:userId, :socialId, :link)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':userId' => $userId,
            ':socialId' => $socialId,
            ':link' => $link
        ]);
    }

    public function getAvailableSocialSites()
    {
        $query = "SELECT * FROM socials ORDER BY nazev ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getVisiblePublishedArticlesCount($userId)
    {
        $query = "SELECT COUNT(*) FROM clanky WHERE user_id = :userId AND viditelnost = 1 AND datum <= NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getTableInfo($table)
    {
        try {
            // Získání informací o sloupcích tabulky
            $stmt = $this->db->prepare("DESCRIBE $table");
            $stmt->execute();
            $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Získání počtu řádků v tabulce
            $countStmt = $this->db->prepare("SELECT COUNT(*) FROM $table");
            $countStmt->execute();
            $rowCount = $countStmt->fetchColumn();
            
            return [
                'columns' => $columns,
                'rowCount' => $rowCount
            ];
        } catch (\PDOException $e) {
            error_log("Chyba při získávání informací o tabulce $table: " . $e->getMessage());
            throw $e;
        }
    }
}
