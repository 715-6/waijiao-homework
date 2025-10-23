<?php
// CSRF保护类
class CSRFProtection {
    const TOKEN_NAME = 'csrf_token';
    
    // 生成CSRF令牌
    public static function generateToken() {
        if (!isset($_SESSION[self::TOKEN_NAME])) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_NAME];
    }
    
    // 验证CSRF令牌
    public static function validateToken($token) {
        if (!isset($_SESSION[self::TOKEN_NAME]) || !isset($token)) {
            return false;
        }
        return hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }
    
    // 生成隐藏的CSRF令牌字段
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . $token . '">';
    }
    
    // 清除CSRF令牌
    public static function clearToken() {
        unset($_SESSION[self::TOKEN_NAME]);
    }
}
?>