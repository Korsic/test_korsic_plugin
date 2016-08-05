<?php
# Получение IP адреса клиента
function korsic_ip_get() {
	return ip2long( $_SERVER['REMOTE_ADDR'] );
}

#+ Удаляем выбранную строку из нашей страницы с заданным значением ID
function delete_ip_from_base( $korsic_ip_long ) {
	global $wpdb;
	$table_name = $wpdb->prefix . "korsic_ip_base";
	$wpdb->delete(
		$table_name,
		array( 'ip' => $korsic_ip_long ),
		array( '%d' )
	);
}

# Проверяем строку на соответствие IP адресу и если все OK - возвращаем IP адрес
function get_ip( $str ) {
	$pattern = '#[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}#';
	$b       = preg_match_all( $pattern, $str, $rows );
	if ( $b ) {
		foreach ( $rows[0] as $row ) {
			if ( ip2long( $row ) === false ) {
				return null;
			} else {
				return $row;
			}
		}
	}
}

#### Перехватываем отправку данных $_POST
# Удаляем скидку (Удаляем строку с этим IP из базы данных)
if ( isset( $_POST['delete_discount_tex'] ) ) {
	$ip_adress_post = get_ip( $_POST['delete_discount_tex'] );
	if ( null == $ip_adress_post ) {
		echo "<script>alert(\"Вы ввели некорректный адрес\");</script>";
	} else { # Ищем введенный IP в базе и удаляем при нахождении
		delete_ip_from_base( ip2long( $ip_adress_post ) );
	}
}
# Добавляем скидку (Булевая величина скидки = true, если IP существует. Если IP нет - добавляем в базу)
if ( isset( $_POST['add_discount_tex'] ) ) {
	$ip_adress_post = get_ip( $_POST['add_discount_tex'] );
	if ( null == $ip_adress_post ) {
		echo "<script>alert(\"Вы ввели некорректный адрес\");</script>";
	} else { # Ищем введенный IP в базе и удаляем при нахождении
		global $wpdb;
		$table_name = $wpdb->prefix . "korsic_ip_base";
		# Ищем совпадение по IP в базе
		$mytable = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name" ), ARRAY_N );
		# Ищем строку с найденным IP, ищем с конца чтобы не рассматривать старые данные
		$bool_new_ip    = 1;
		$ip_adress_post = ip2long( $ip_adress_post );
		$count_ip       = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name;" ) ); # Определяем количество строк в нашей базе
		while ( $count_ip > 0 ) {
			$count_ip --;
			if ( $mytable[ $count_ip ][0] == $ip_adress_post ) {
				$wpdb->update( $table_name,
					array( 'discount' => 1 ),
					array( 'ip' => $ip_adress_post ),
					array( '%d' ),
					array( '%d' )
				);
				$bool_new_ip --;
				break;
			}
		}
		# Если новый IP - вставляем строку в нашу таблицу с заданным IP и скидкой = true
		if ( 1 == $bool_new_ip ) {
			$wpdb->insert(
				$table_name,
				array( 'ip' => $ip_adress_post, 'count' => 0, 'discount' => 1 ),
				array( '%d', '%d', '%d' )
			);
		}
	}
}
# Обрабатываем запрос покупки товара. Проверяем есть ли у IP клиента скидка. Если да - обнуляем счетчик при покупке
if ( isset( $_POST['button_buy_product'] ) ) {
	$ip_adress_post = korsic_ip_get();
	global $wpdb;
	$table_name = $wpdb->prefix . "korsic_ip_base";
	# Ищем совпадение по IP в базе
	$mytable = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name" ), ARRAY_N );
	# Ищем строку с найденным IP и меняем ее счетчик на заданное число, ищем с конца чтобы не рассматривать старые данные
	$count_ip = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name;" ) ); # Определяем количество строк в нашей базе
	$bool_buy = 0;
	while ( $count_ip > 0 ) {
		$count_ip --;
		if ( $mytable[ $count_ip ][0] == $ip_adress_post ) {
			$bool_buy = 1;
			if ( 1 == $mytable[ $count_ip ][2] ) { # Если находим скидку у пользователя - продаем со скидкой и обнуляем счетчик
				echo "<script>alert(\"Поздравляем с покупкой со скидкой!\");</script>";
				$wpdb->update( $table_name,
					array( 'count' => 0, 'discount' => 0 ),
					array( 'ip' => $ip_adress_post ),
					array( '%d', '%d' ),
					array( '%d' )
				);
			} else { # Если у пользователя нет скидки - продаем без скидки счетчик
				echo "<script>alert(\"Поздравляем с покупкой! К сожалению у вас пока нет скидки, заходите к нам почаще :)\");</script>";
			}
			break;
		}
	}
	if ( 0 == $bool_buy ) { # Если по любой причине не находим пользователя - продаем без скидки
		echo "<script>alert(\"Поздравляем с покупкой! К сожалению у вас пока нет скидки, заходите к нам почаще :)\");</script>";
	}

}
# ==================================

# Обработка нового посещения сайта
function work_korsic_ip_base( $korsic_ip ) {
	global $wpdb;
	$table_name = $wpdb->prefix . "korsic_ip_base";
	# Ищем совпадение по IP в базе


	$mytable = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_N );
	# Ищем строку с найденным IP и меняем ее счетчик на заданное число, ищем с конца чтобы не рассматривать старые данные
	$bool_new_ip = 1;
	$count_ip    = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name;" ) ); # Определяем количество строк в нашей базе
	while ( $count_ip > 0 ) {
		$count_ip --;
		if ( $mytable[ $count_ip ][0] == $korsic_ip ) {
			$bool_new_ip --;
			if ( 1 == $mytable[ $count_ip ][2] ) {
				break;
			}
			$temp_var = $mytable[ $count_ip ][1] + 1;
			$wpdb->update( $table_name,
				array( 'count' => $temp_var ),
				array( 'ip' => $korsic_ip ),
				array( '%d' ),
				array( '%d' )
			);
			if ( $temp_var >= 10 ) {
				$wpdb->update( $table_name,
					array( 'discount' => 1 ),
					array( 'ip' => $korsic_ip ),
					array( '%d' ),
					array( '%d' )
				);
			}
			break;
		}
	}
	# Если новый IP - вставляем строку в нашу таблицу с заданным IP и значением счетчика 1
	if ( 1 == $bool_new_ip ) {
		$wpdb->insert(
			$table_name,
			array( 'ip' => $korsic_ip, 'count' => 1 ),
			array( '%d', '%d' )
		);
	}
}

# ============================================
?>