<?php

namespace Config\DB2FA;

class DB2FA
{
    private static $table = 'Anchor_2fa';
    private static $wpdb;
    private static $charset_collate;
    public static function init()
    {
        global $wpdb;
        self::$wpdb = $wpdb;
        self::$charset_collate = $wpdb->get_charset_collate();
    }

    public static function dbSetup()
    {
        self::dbCreation();
        self::dbInsert();
        self::insertRecoveryCodes();
    }


    public  static function dbCreation()
    {
        if (self::$wpdb->get_var("SHOW TABLES LIKE '" . self::$table . "'") != self::$table) {
            $sql = "CREATE TABLE IF NOT EXISTS " . self::$table . " (
           `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `otp_status` ENUM('activated','deactivated') DEFAULT 'deactivated',
            `role` ENUM('editor','subscriber','author','contibutor','administrator') DEFAULT 'subscriber',
            PRIMARY KEY (`id`)
        ) " . self::$charset_collate . ";";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    //Insert into database
    public static function dbInsert()
    {
        $current_user = wp_get_current_user();
        // current user role
        $current_role = implode(', ', $current_user->roles);
        $data_to_insert = array(
            'id' => $current_user->ID,
            'username' => $current_user->user_login,
            'email' => $current_user->user_email,
            'role' => $current_role,
        );

        // Format specifiers for each data value
        $data_formats = array('%d', '%s', '%s', '%s');

        // Insert data into the table
        self::$wpdb->insert(self::$table, $data_to_insert, $data_formats);

        if (self::$wpdb->last_error) {
            echo 'Error inserting data: ' . self::$wpdb->last_error;
        } else {
            echo 'Data inserted successfully!';
        }
    }

    public static function check_user_role()
    {
        $current_user = wp_get_current_user();
        return $current_user->roles;
    }

    // Remove the database
    public static function dbDestroy()
    {
        $sql = 'DROP TABLE IF EXISTS ' . self::$table;
        self::$wpdb->query($sql);
    }

    //handle POST request of Button Click
    public static function insertRecoveryCodes()
    {
        if (isset($_POST['recoveryCodes'])) {
            $recCodes = isset($_POST['recoveryCodes']);
            //create recovery_codes column
            self::$wpdb->query("ALTER TABLE ".self::$table." ADD `recovery_codes` varchar(255) NULL");

            $code_value = self::$wpdb->prepare('%s', $recCodes);
            self::$wpdb->prepare(
                "INSERT INTO ".self::$table." (recovery_codes) VALUES (%s)",
                $code_value
            );
            // $this->wpdb->query(
            //     $this->wpdb->prepare(
            //         "INSERT INTO {$this->table} (recovery_codes) VALUES (%s)",
            //         $recCodes
            //     )
            // );
        }
    }
}
