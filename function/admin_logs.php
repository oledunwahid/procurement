<?php
function admin_log($action_type, $table_name, $record_id, $description = null, $new_data = null)
{
    global $koneksi;

    try {
        // Get session NIK
        session_start();
        $idnik = isset($_SESSION['niklogin']) ? $_SESSION['niklogin'] : 'system';

        // Format values
        $old_value = null;
        $new_value = null;

        if ($description !== null) {
            $old_value = '';
            $new_value = $description;
        } else if ($new_data !== null) {
            $new_value = json_encode($new_data, JSON_UNESCAPED_UNICODE);
        }

        // Insert log
        $stmt = $koneksi->prepare("INSERT INTO proc_admin_log (idnik, action_type, table_name, record_id, old_value, new_value) VALUES (?, ?, ?, ?, ?, ?)");

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
