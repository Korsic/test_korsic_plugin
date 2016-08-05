<?php
        # Создание таблицы mysql 
    function install_korsic_ip_base () {
        global $wpdb;
        $table_name = $wpdb->prefix . "korsic_ip_base";
        delete_korsic_ip_base();
        
        if( $wpdb->get_var("show tables like '$table_name'" ) != $table_name ) {
      
            $sql = "CREATE TABLE " . $table_name . " (
	           ip int UNSIGNED default '0',
               count SMALLINT UNSIGNED default '0',
               discount BOOL default '0',
               UNIQUE KEY id (ip)
            );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
         } 
    } 


    #=============================================    


?>