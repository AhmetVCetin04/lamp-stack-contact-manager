<?php

class AuthService
{
    private static $secretKey = 'contact_manager_secret_key_2024';
    private static $dbHost = "127.0.0.1";
    private static $dbUser = "admin";
    private static $dbPass = "gA5cGi6WndELh5Ky";
    private static $dbName = "contact_manager";

    public static function authenticate($username, $password)
    {
        $conn = self::getConnection();
        if (!$conn) {
            return ['error' => 'Database connection failed'];
        }

        $stmt = $conn->prepare("SELECT id, first_name, last_name, user_name, email, password FROM users_table WHERE user_name=? OR email=?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $userData = [
                    'id' => $row['id'],
                    'firstName' => $row['first_name'],
                    'lastName' => $row['last_name'],
                    'userName' => $row['user_name'],
                    'email' => $row['email']
                ];

                self::createPersistentAuth($userData);

                $stmt->close();
                $conn->close();
                return ['success' => true, 'user' => $userData];
            } else {
                $stmt->close();
                $conn->close();
                return ['error' => 'Invalid Password'];
            }
        } else {
            $stmt->close();
            $conn->close();
            return ['error' => 'No Records Found'];
        }
    }

    public static function checkAuthentication()
    {
        // Check cookie for persistent auth
        if (isset($_COOKIE['stay_signed_in'])) {
            return self::validateCookie();
        }

        return null;
    }

    public static function logout()
    {
        // Clear cookie
        if (isset($_COOKIE['stay_signed_in'])) {
            setcookie('stay_signed_in', '', time() - 3600, '/', '', false, true);
        }

        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    private static function createPersistentAuth($userData)
    {
        $cookieData = json_encode([
            'user_id' => $userData['id'],
            'user_name' => $userData['userName']
        ]);

        $iv = substr(hash('sha256', self::$secretKey), 0, 16);
        $encryptedData = openssl_encrypt($cookieData, 'AES-128-CBC', self::$secretKey, 0, $iv);

        setcookie('stay_signed_in', $encryptedData, 0, '/', '', false, true);
    }

    private static function validateCookie()
    {
        try {
            $iv = substr(hash('sha256', self::$secretKey), 0, 16);
            $decryptedData = openssl_decrypt($_COOKIE['stay_signed_in'], 'AES-128-CBC', self::$secretKey, 0, $iv);

            if ($decryptedData === false) {
                return null;
            }

            $userData = json_decode($decryptedData, true);
            if (!$userData || !isset($userData['user_id'])) {
                return null;
            }

            $conn = self::getConnection();
            if (!$conn) {
                return null;
            }

            $stmt = $conn->prepare("SELECT id, first_name, last_name, user_name, email FROM users_table WHERE id = ?");
            $stmt->bind_param("i", $userData['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $validatedUser = [
                    'id' => $row['id'],
                    'firstName' => $row['first_name'],
                    'lastName' => $row['last_name'],
                    'userName' => $row['user_name'],
                    'email' => $row['email']
                ];

                $stmt->close();
                $conn->close();
                return $validatedUser;
            }

            $stmt->close();
            $conn->close();
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    private static function getConnection()
    {
        $conn = new mysqli(self::$dbHost, self::$dbUser, self::$dbPass, self::$dbName);

        if ($conn->connect_error) {
            return null;
        }

        return $conn;
    }
}
