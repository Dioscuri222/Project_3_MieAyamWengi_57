<?php
// Custom Database Session Handler for Serverless Environments (Vercel)
// Stores PHP sessions in the MySQL database to prevent session loss across stateless lambdas.

class DatabaseSessionHandler implements SessionHandlerInterface {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function open($savePath, $sessionName): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    #[\ReturnTypeWillChange]
    public function read($id): string {
        try {
            $stmt = $this->pdo->prepare("SELECT data FROM sessions WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetchColumn();
            return $data !== false ? $data : '';
        } catch (PDOException $e) {
            return '';
        }
    }

    #[\ReturnTypeWillChange]
    public function write($id, $data): bool {
        try {
            $stmt = $this->pdo->prepare("REPLACE INTO sessions (id, data, last_access) VALUES (?, ?, ?)");
            return $stmt->execute([$id, $data, time()]);
        } catch (PDOException $e) {
            return false;
        }
    }

    #[\ReturnTypeWillChange]
    public function destroy($id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    #[\ReturnTypeWillChange]
    public function gc($maxlifetime): int|false {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE last_access < ?");
            $stmt->execute([time() - $maxlifetime]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
