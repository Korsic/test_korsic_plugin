<?php
# Добавляем хук для меню
add_action('admin_menu', 'mt_add_pages');
function mt_add_pages() {
    # Добавляем новый раздел меню Top уровня
    add_menu_page('Korsic Plugin Menu', 'Korsic Plugin Menu', 8, __FILE__, 'mt_toplevel_page');
}

# Выводим IP посещений и наличие скидки у пользователей в настройках
function mt_toplevel_page() {
    echo "<h2>Статистика посещений сайта по IP</h2>";
    global $wpdb;
    $table_name = $wpdb->prefix . "korsic_ip_base";
    $mytable = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_N);  
    $count_ip  = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name;")); # Определяем количество строк в нашей базе
    echo "IP | количество посещений | Наличие скидки <br />";
    while ( $count_ip > 0 ) {
        $count_ip--;
        echo "<br />" . long2ip($mytable[ $count_ip ][0]) . " | " .$mytable[ $count_ip ][1];
        if ( true == $mytable[ $count_ip ][2] ) {
            echo " | Есть скидка";
        } 
    }
    echo "<br /> <br />";
    
    
    # Выводим тестовое поле для удаления и добавления скидки
    echo "<form name='delete_discount' method=\"post\" action=\"\" >
        Введите IP, у которого необходимо удалить скидку: <br />
        <input name='delete_discount_tex' type='text' size='30' value='Введите IP адрес' />
        <input name='delete' type=\"submit\" value='Удалить скидку' />
        </form>";
    echo "<form name='add_discount' method=\"post\" action=\"\" >
        Введите IP, которому необходимо добавить скидку: <br />
        <input name='add_discount_tex' type='text' size='30' value='Введите IP адрес' />
        <input name='add' type=\"submit\" value='Добавить скидку' />
        </form>";

    
    
}

?>