<?php
require_once __DIR__ . '/db.php';

/**
 * 管理者一覧の取得
 *
 * @return array[] 管理者一覧（admin_id, last_name, first_name, login_id）
 */
function get_admins(): array
{
    $db  = db_connect();
    $sql = 'SELECT id AS admin_id, last_name, first_name, login_id FROM m_admins ORDER BY id ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * 管理者詳細の取得
 *
 * @param  int   $admin_id 管理者ID
 * @return array 管理者情報（admin_id, first_name, last_name, login_id）。該当なしは空配列
 */
function get_admin(int $admin_id): array
{
    $db  = db_connect();
    $sql = 'SELECT id AS admin_id, first_name, last_name, login_id FROM m_admins WHERE id = :admin_id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ?: [];
}

/**
 * 管理者の追加
 *
 * パスワードは password_hash() でハッシュ化して保存する。
 *
 * @param  array $data first_name, last_name, login_id, password を含む連想配列
 * @return bool  成功時 true
 */
function add_admin(array $data): bool
{
    $db  = db_connect();
    $sql = 'INSERT INTO m_admins (first_name, last_name, login_id, password) VALUES (:first_name, :last_name, :login_id, :password)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':first_name', $data['first_name'], PDO::PARAM_STR);
    $stmt->bindValue(':last_name',  $data['last_name'],  PDO::PARAM_STR);
    $stmt->bindValue(':login_id',   $data['login_id'],   PDO::PARAM_STR);
    $stmt->bindValue(':password',   password_hash($data['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
    return $stmt->execute();
}

/**
 * 管理者ログイン
 *
 * login_id でDBを検索し password_verify() で検証する。
 * 成功時は $_SESSION['admin_id'] と $_SESSION['admin_name'] をセットする。
 *
 * @param  string $login_id ログインID
 * @param  string $password 平文パスワード
 * @return bool   成功時 true、失敗時 false
 */
function admin_login(string $login_id, string $password): bool
{
    $db   = db_connect();
    $sql  = 'SELECT id AS admin_id, last_name, first_name, password FROM m_admins WHERE login_id = :login_id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
    $stmt->execute();
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        return false;
    }

    $_SESSION['admin_id']   = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['last_name'] . ' ' . $admin['first_name'];
    return true;
}

/**
 * 管理者情報の更新
 *
 * 渡されたキーのみ動的にUPDATEする。
 * password キーが含まれる場合はハッシュ化して更新する。
 * 更新可能なカラム: first_name, last_name, login_id, password
 *
 * @param  int   $admin_id 更新対象の管理者ID
 * @param  array $data     更新するカラムと値の連想配列
 * @return bool  成功時 true、失敗時（対象なし・不正カラム含む）false
 */
function update_admin(int $admin_id, array $data): bool
{
    $allowed_columns = ['first_name', 'last_name', 'login_id', 'password'];

    $set_clauses = [];
    $params      = [':admin_id' => $admin_id];

    foreach ($data as $column => $value) {
        if (!in_array($column, $allowed_columns, true)) {
            continue;
        }
        $set_clauses[]        = "{$column} = :{$column}";
        $params[":{$column}"] = ($column === 'password')
            ? password_hash($value, PASSWORD_DEFAULT)
            : $value;
    }

    if (empty($set_clauses)) {
        return false;
    }

    $db  = db_connect();
    $sql = 'UPDATE m_admins SET ' . implode(', ', $set_clauses) . ' WHERE id = :admin_id';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount() > 0;
}
