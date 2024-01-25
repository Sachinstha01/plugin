<?php

defined('ABSPATH') or die('Error Occurred');
class Security
{
    private $table = 'Anchor_2fa';
    private $wpdb;
    private $charset_collate;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();

        add_action('admin_menu', array($this, 'admin_pages'));
        //
        register_activation_hook(__FILE__, array($this, 'add_2fa_login_field'));
        register_deactivation_hook(__FILE__, array($this, 'remove_2fa_field'));

        // Database   
        register_activation_hook(__FILE__, array($this, 'dbSetup'));
        // register_activation_hook(__FILE__, array($this, 'dbInsert'));

        register_deactivation_hook(__FILE__, array($this, 'dbDestroy'));
        add_action('admin_notices', array($this, 'check_user_role'));

        //recovery Code Insertion
        // add_action('wp_ajax_recovery_code_insertion', array($this,'insertRecoveryCodes'));
       
    }

    public function dbSetup(){
        $this->dbCreation();
        $this->dbInsert();
        $this->insertRecoveryCodes();
    }

    public function admin_pages()
    {
        // add_menu_page(string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null);
        add_menu_page(
            'Security',
            'AnchorPoint',
            'manage_options',
            'security_menu_slug', //main menu sliug
            array($this, 'menu_redirect_page'),
            'dashicons-privacy', //icon url
            null
        );

        //submenus Arrays
        $submenus = array(
            array(
                'page_title' => 'Submenu Page 1',
                'menu_title' => 'Submenu 1',
                'capability' => 'manage_options',
                'menu_slug'  => 'submenu_slug_1',
                'callback'   => array($this, 'submenu_page_content_1')
            ),
            array(
                'page_title' => 'Submenu Page 2',
                'menu_title' => 'Submenu 2',
                'capability' => 'manage_options',
                'menu_slug'  => 'submenu_slug_2',
                'callback'   => array($this, 'submenu_page_content_2')

            ),
            array(
                'page_title' => 'Submenu Page 3',
                'menu_title' => 'Submenu 3',
                'capability' => 'manage_options',
                'menu_slug'  => 'submenu_slug_3',
                'callback'   => array($this, 'submenu_page_content_3')
            ),
            array(
                'page_title' => 'Submenu Page 4',
                'menu_title' => 'Submenu 4',
                'capability' => 'manage_options',
                'menu_slug'  => 'submenu_slug_4',
                'callback'   => array($this, 'submenu_page_content_4')

            ),
            array(
                'page_title' => 'AnchorPoint 2FA',
                'menu_title' => '2FA',
                'capability' => 'manage_options', // accesibility option
                'menu_slug'  => '2FA', //unique identifier
                'callback'   => array($this, 'security_2fa')
            ),
        );

        foreach ($submenus as $submenu) {
            add_submenu_page(
                'security_menu_slug', // Parent slug
                $submenu['page_title'],
                $submenu['menu_title'],
                $submenu['capability'],
                $submenu['menu_slug'],
                $submenu['callback']
            );
        }
    }

    // All the main menu redirection files
    public function menu_redirect_page()
    {
        include(plugin_dir_path(__FILE__) . 'security-index.php');
    }
    // All the submenu redirection file 
    public function submenu_page_content_1()
    {
        include(plugin_dir_path(__FILE__) . 'submenu/submenu-index-1.php');
    }
    public function submenu_page_content_2()
    {
        include(plugin_dir_path(__FILE__) . 'submenu/submenu-index-2.php');
    }
    public function submenu_page_content_3()
    {
        include(plugin_dir_path(__FILE__) . 'submenu/submenu-index-3.php');
    }
    public function submenu_page_content_4()
    {
        include(plugin_dir_path(__FILE__) . 'submenu/submenu-index-4.php');
    }

    public function security_2fa()
    {
        include(plugin_dir_path(__FILE__) . 'submenu/security_2fa.php');
    }

    //OTP input field method
    // public function add_2fa_field()
    // {
    //     if (is_plugin_active(plugin_dir_path(__FILE__) . '/wordpress-security.php')) {

    //         $otp_filed ='';
    //         $otp_filed .= '<form id="otpForm">';
    //         $otp_filed.='<label for="otpInput">Enter OTP:</label>';
    //         $otp_filed.='<input type="text" id="otpInput" name="otp" pattern="\d{6}" minlength="6" maxlength="6" required>';
    //         $otp_filed.='<button type="submit">Submit</button>';
    //         $otp_filed.='</form>';

    //         return $otp_filed;
    //     }
    // }

    // remove otp field on deactivation
    public function remove_2fa_field()
    {
        //
    }

    // add otp input on wp-login 
    public function add_2fa_login_field()
    {
        // add_action('login_head',array($this,'add_2fa_field'));
        // add_action('login_enqueue_scripts',array($this,'add_2fa_field'));
    }

    //Database setup
    public function dbCreation()
    {
        if ($this->wpdb->get_var("SHOW TABLES LIKE '{$this->table}'") != $this->table) {
            $sql = "CREATE TABLE IF NOT EXISTS {$this->table}  (
               `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `otp_status` ENUM('activated','deactivated') DEFAULT 'deactivated',
                `role` ENUM('editor','subscriber','author','contibutor','administrator') DEFAULT 'subscriber',
                PRIMARY KEY (`id`)
            ) {$this->charset_collate};";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    //Insert into database
    public function dbInsert()
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
        $this->wpdb->insert($this->table, $data_to_insert, $data_formats);

        if ($this->wpdb->last_error) {
            echo 'Error inserting data: ' . $this->wpdb->last_error;
        } else {
            echo 'Data inserted successfully!';
        }
    }

    public function check_user_role()
    {
        $current_user = wp_get_current_user();
        return $current_user->roles;
    }

    // Remove the database
    function dbDestroy()
    {
        $sql = 'DROP TABLE IF EXISTS ' . $this->table;
        $this->wpdb->query($sql);
    }

    //handle POST request of Button Click
    public function insertRecoveryCodes()
    {
        if (isset($_POST['recoveryCodes'])) {
            $recCodes = isset($_POST['recoveryCodes']);
            //create recovery_codes column
            $this->wpdb->query("ALTER TABLE {$this->table} ADD `recovery_codes` varchar(255) NULL");

            $code_value = $this->wpdb->prepare('%s', $recCodes);
            $this->wpdb->prepare(
                "INSERT INTO {$this->table} (recovery_codes) VALUES (%s)",
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

$newInstance = new Security();
