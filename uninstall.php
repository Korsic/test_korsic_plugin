<?php
        # Удаление таблицы mysql
    function delete_korsic_ip_base () {
        global $wpdb;        
        $table_name = $wpdb->prefix . "korsic_ip_base";
        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
    } 
?>