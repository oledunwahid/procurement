<!-- admin_logs.php -->
<?php
function admin_log($action_type, $table_name, $record_id, $old_data = null, $new_data = null)
{
    global $koneksi;

    try {
        // Get session NIK and user info
        session_start();
        $idnik = isset($_SESSION['niklogin']) ? $_SESSION['niklogin'] : 'system';

        // Get user name for better logging
        $user_name = 'System';
        if ($idnik !== 'system') {
            $stmt = $koneksi->prepare("SELECT nama FROM user WHERE idnik = ?");
            $stmt->bind_param("s", $idnik);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                $user_name = $user['nama'];
            }
            $stmt->close();
        }

        // Format values
        $old_value = null;
        $new_value = null;

        // Handle old data
        if ($old_data !== null) {
            if (is_array($old_data)) {
                $old_data['logged_by'] = $user_name;
                $old_data['logged_at'] = date('Y-m-d H:i:s');
                $old_value = json_encode($old_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } else {
                $old_value = json_encode([
                    'value' => $old_data,
                    'logged_by' => $user_name,
                    'logged_at' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        }

        // Handle new data
        if ($new_data !== null) {
            if (is_array($new_data)) {
                $new_data['logged_by'] = $user_name;
                $new_data['logged_at'] = date('Y-m-d H:i:s');
                $new_value = json_encode($new_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } else {
                $new_value = json_encode([
                    'value' => $new_data,
                    'logged_by' => $user_name,
                    'logged_at' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        }

        // Validate JSON before inserting
        if ($old_value !== null && json_decode($old_value) === null) {
            throw new Exception("Invalid JSON in old_value");
        }
        if ($new_value !== null && json_decode($new_value) === null) {
            throw new Exception("Invalid JSON in new_value");
        }

        // Insert log
        $stmt = $koneksi->prepare("
            INSERT INTO proc_admin_log 
            (idnik, action_type, table_name, record_id, old_value, new_value) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $koneksi->error);
        }

        $stmt->bind_param("ssssss", $idnik, $action_type, $table_name, $record_id, $old_value, $new_value);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert log: " . $stmt->error);
        }

        $stmt->close();
        return true;
    } catch (Exception $e) {
        error_log(sprintf(
            "[%s] Admin Log Error - Action: %s, Table: %s, Record: %s, Error: %s",
            date('Y-m-d H:i:s'),
            $action_type,
            $table_name,
            $record_id,
            $e->getMessage()
        ));
        return false;
    }
}

/**
 * Helper function to ensure JSON consistency
 */
function sanitizeJsonData($data)
{
    if (empty($data)) return null;

    // If data is already a JSON string, decode it first
    if (is_string($data) && isJson($data)) {
        $data = json_decode($data, true);
    }

    // Remove any null or empty values
    if (is_array($data)) {
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    }

    return $data;
}

/**
 * Helper function to check if string is valid JSON
 */
function isJson($string)
{
    if (!is_string($string)) return false;
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}
