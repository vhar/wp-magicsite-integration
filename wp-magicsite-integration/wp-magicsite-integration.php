<?php
/*
 * Plugin name: E-Publish MagicSite Integration
 * Plugin URI: https://github.com/e-publish/wp-magicsite-integration
 * Text domain: wp-magicsite-intergation
 * Description: Плагин интеграции сведений об образовательной организации из среды MagicSite АО "Е-Паблиш"
 * Version: 1.1.3
 * License: MIT
 * Requires at least: 4.0
 * Requires PHP: 7.2
 * Tested up to: 7.4
 * Author: Vladimir Kharinenkov
 * Author URI: https://github.com/vhar
 * Text Domain: wp-magicsite-integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class MagicSiteIntegration
{
	public static function plugin_activation() {
		$navigation_menu = self::get_magicsite_nav_menu();
		$menu_id = wp_create_nav_menu( 'MagicSiteMenu' );
		foreach ( $navigation_menu as $section => $item ) {
			if ( $item['type'] == 'post_type_archive' ) {
				$section_id = wp_update_nav_menu_item( $menu_id, 0, [
					'menu-item-title'    => $item['title'],
					'menu-item-object'   => $section,
					'menu-item-status'   => 'publish',
					'menu-item-type'     => 'post_type_archive',
					'menu-item-position' => $item['weight'],
				] );
				if ( isset( $item['below'] ) ) {
					foreach ( $item['below'] as $link => $link_item ) {
						$post = [
							'post_title'     => $link_item['title'],
							'post_content'   => $link_item['content'],
							'post_type'      => $link_item['type'],
							'post_status'    => 'publish',
							'post_author'    => 1,
							'comment_status' => 'closed',
							'post_name'      => $link,
						];
						$post_id = wp_insert_post( $post );
						wp_update_nav_menu_item( $menu_id, 0, [
							'menu-item-title'     => $link_item['title'],
							'menu-item-object-id' => $post_id,
							'menu-item-object'    => $link_item['type'],
							'menu-item-status'    => 'publish',
							'menu-item-type'      => 'post_type',
							'menu-item-parent-id' => $section_id,
							'menu-item-position'  => $link_item['weight'],
						] );
					}
				}
			} else {
				$post = [
					'post_title'     => $item['title'],
					'post_content'   => $item['content'],
					'post_type'      => $item['type'],
					'post_status'    => 'publish',
					'post_author'    => 1,
					'comment_status' => 'closed',
					'post_name'      => $section,
				];
				$post_id = wp_insert_post( $post );
				$section_id = wp_update_nav_menu_item( $menu_id, 0, [
					'menu-item-title'     => $item['title'],
					'menu-item-object-id' => $post_id,
					'menu-item-object'    => $item['type'],
					'menu-item-status'    => 'publish',
					'menu-item-type'      => 'post_type',
					'menu-item-position'  => $item['weight'],
				] );
				if ( isset( $item['below'] ) ) {
					foreach ( $item['below'] as $link => $link_item ) {
						$post = [
							'post_title'     => $link_item['title'],
							'post_content'   => $link_item['content'],
							'post_type'      => $link_item['type'],
							'post_status'    => 'publish',
							'post_author'    => 1,
							'comment_status' => 'closed',
							'post_name'      => $link,
						];
						$post_id = wp_insert_post( $post );
						wp_update_nav_menu_item( $menu_id, 0, [
							'menu-item-title'     => $link_item['title'],
							'menu-item-object-id' => $post_id,
							'menu-item-object'    => $link_item['type'],
							'menu-item-status'    => 'publish',
							'menu-item-type'      => 'post_type',
							'menu-item-parent-id' => $section_id,
							'menu-item-position'  => $link_item['weight'],
						] );
					}
				}
			}
		}
		update_option( 'rewrite_rules', '' );
	}

	public static function plugin_deactivation() {
		wp_delete_nav_menu( 'MagicSiteMenu' );
		$post_types = self::get_magicsite_pages();
		foreach ( $post_types as $type => $items ) {
			$posts = get_posts( ['post_type' => $type, 'numberposts' => -1] );
			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true );
			}
		}
		$post = get_page_by_path( 'meals', OBJECT, 'post' );
		wp_delete_post( $post->ID, true );

		delete_option( 'magicsite_intergration_settings_options' );
		update_option( 'rewrite_rules', '' );
	}

	private static function get_magicsite_pages() {
		return [
			'anticorruption' => [
				'normativnieacti',
				'expertise',
				'iniemetodmaterialy',
				'forms',
				'svedenodohodah',
				'commission',
				'feedback',
				'responsibility',
				'infomaterial'
			],
			'educative' => [
				'edwpartdo',
				'edwpartnoo',
				'edwpartooo',
				'edwpartsoo',
				'edwanaliz',
				'edwinfo',
				'edwevents'
			],
			'food' => [
				'index'
			],
			'infosec' => [
				'common',
				'normreg',
				'educator',
				'students',
				'parents',
				'sites'
			],
			'qualityassessment' => [
				'qualityassessment'
			],
			'shedule' => [
				'distance_education'
			],
			'sveden' => [
				'common',
				'struct',
				'document',
				'education',
				'edustandarts',
				'employees',
				'objects',
				'grants',
				'paid_edu',
				'budget',
				'vacant',
				'ovz',
				'inter',
				'gia',
				'meals',
				'accounting_policy',
				'labor_protection'
			],
		];
	}

	private function magicsite_includes() {
		require_once plugin_dir_path(__FILE__) . 'includes/navigation-menu-walker.php';
		require_once plugin_dir_path(__FILE__) . 'includes/navigation-menu-widget.php';
	}

	private function get_magicsite_page( $uri, $header = FALSE ) {
		$response = [];
		$url = parse_url( $uri );
		$curlInit = curl_init( $uri );
		curl_setopt( $curlInit, CURLOPT_CONNECTTIMEOUT, 20 );
		if ( $header ) {
			curl_setopt( $curlInit, CURLOPT_HEADER, true );
			curl_setopt( $curlInit, CURLOPT_NOBODY, true );
		}
		if ( $url['scheme'] == 'https' ) {
			curl_setopt( $curlInit, CURLOPT_SSL_VERIFYHOST	, 2 );
		}
		curl_setopt( $curlInit, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curlInit, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curlInit, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curlInit, CURLOPT_COOKIEJAR, '-' );
		curl_setopt( $curlInit, CURLOPT_REFERER, $_SERVER['SERVER_NAME'] );
		curl_setopt( $curlInit, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)" );
		$response['response']      = curl_exec( $curlInit );
		$response['effective_url'] = curl_getinfo( $curlInit, CURLINFO_EFFECTIVE_URL );
		$response['response_code'] = intval(curl_getinfo( $curlInit, CURLINFO_HTTP_CODE ) );
		curl_close( $curlInit );
		return $response;
	}

	private function magicsite_post_content ( $magicsite_url, $post_type, $post_name ) {
		$magicsite_content = '';
		$response = $this->get_magicsite_page( $magicsite_url . $post_type . '/' . $post_name . '.html' );
		if ( $response['response_code'] == 200 ) {
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = false;
			libxml_use_internal_errors( true );
			$dom->loadHTML( $response['response'] );
			$xpath = new DOMXPath( $dom );
			$ls_ads = $xpath->query( '//a' );
			foreach ( $ls_ads as $ad ) {
				if ( $ad->hasAttribute( 'href' ) ) {
					$ad_url = $ad->getAttribute( 'href' );
					$f = parse_url( $ad_url );
					if ( ! isset( $f['scheme'] ) && ! isset( $f['host'] ) && isset( $f['path'] ) ) {
						$ad->setAttribute( 'href', $magicsite_url . $f['path'] );
					}
				}
			}
			$images = $dom->getElementsByTagName( 'img' );
			foreach ( $images as $image ) {
				$src = $image->getAttribute( 'src' );
				$f = parse_url( $src );
				if ( ! isset( $f['scheme'] ) && ! isset( $f['host'] ) && isset( $f['path'] ) ) {
					$image->setAttribute( 'src', $magicsite_url . $f['path'] );
				}
			}
			$sections = $xpath->query( "//*[contains(@class, 'inner-page-block')]" );
			foreach ( $sections as $section ) {
				$magicsite_content .= $dom->saveHTML( $section );
			}
			libxml_clear_errors();
			$magicsite_content = str_replace( "\n", "", $magicsite_content );
			$magicsite_content .= '<div></div>';
			$sections = $xpath->query( "//span[contains(@id, 'site-modified-gmt')]" );

			$post = get_page_by_path( $post_name, OBJECT, $post_type );

			foreach ( $sections as $section ) {
				$modified = $section->nodeValue;

				if ($modified > $post->post_modified_gmt) {
					$new_post = array(
						'ID'           => $post->ID,
						'post_content' => $magicsite_content,
					);
					
					wp_update_post( $new_post );
				}
			}
		} else {
			$magicsite_content = '<div>' . 'Неудалось получить данные. Удаленный сервер вернул код' . ' ' . $response['response_code'] ?? '0' . '</div>';
		}

		return $magicsite_content;
	}

	private function get_magicsite_url( $uri ) {
		$uri = trim( $uri );
		preg_match( '/^(https?:\/\/)?/', $uri, $proto );
		if ( ! isset( $proto[1] ) ) {
			$uri = "http://" . $uri;
		}
		$url = parse_url( $uri );
		if ( ! isset( $url['host'] ) ) {
			return FALSE;
		}
		$edusite  = array_column(dns_get_record( 'edusite.ru', DNS_A ), 'ip');
		$userHost = array_column(dns_get_record( $url['host'], DNS_A ), 'ip');

		if ( count ( array_intersect ( $edusite, $userHost ) ) ) {
			$output = $url['scheme'] . '://';
			$output .= $url['host'];
			if ( isset( $url['port'] ) ) {
				$output .= ':' . $url['port'];
			}
			if ( isset( $url['path'] ) ) {
				$output .= $url['path'];
			}
			$res = $this->get_magicsite_page( $output, 1 );

			if ( $res['response_code'] == 200 ) {
				return $res['effective_url'];
			}
		}
		return FALSE;
	}

	public static function get_magicsite_nav_menu() {
		return [
			'sveden' => [
				'type'    => 'post_type_archive',
				'title'   => 'Сведения об образовательной организации',
				'content' => '',
				'weight'  => 10,
				'below'   => [
					'common' => [
						'title'   => 'Основные сведения',
						'weight'  => 10,
						'content' => '',
						'type'    => 'sveden'
					],
					'struct' => [
						'title'   => 'Структура и органы управления образовательной организацией',
						'weight'  => 20,
						'content' => '',
						'type'    => 'sveden'
					],
					'document' => [
						'title'   => 'Документы',
						'weight'  => 30,
						'content' => '',
						'type'    => 'sveden'
					],
					'education' => [
						'title'   => 'Образование',
						'weight'  => 40,
						'content' => '',
						'type'    => 'sveden'
					],
					'edustandarts' => [
						'title'   => 'Образовательные стандарты',
						'weight'  => 50,
						'content' => '',
						'type'    => 'sveden'
					],
					'employees' => [
						'title'   => 'Руководство. Педагогический (научно-педагогический) состав',
						'weight'  => 60,
						'content' => '',
						'type'    => 'sveden'
					],
					'objects' => [
						'title' => 'Материально-техническое обеспечение и оснащенность образовательного процесса',
						'weight' => 70,
						'content' => '',
						'type' => 'sveden'
					],
					'grants' => [
						'title'   => 'Стипендии и меры поддержки обучающихся',
						'weight'  => 80,
						'content' => '',
						'type'    => 'sveden'
					],
					'paid_edu' => [
						'title'   => 'Платные образовательные услуги',
						'weight'  => 90,
						'content' => '',
						'type'    => 'sveden'
					],
					'budget' => [
						'title'   => 'Финансово-хозяйственная деятельность',
						'weight'  => 100,
						'content' => '',
						'type'    => 'sveden'
					],
					'vacant' => [
						'title'   => 'Вакантные места для приема (перевода) обучающихся',
						'weight'  => 110,
						'content' => '',
						'type'    => 'sveden'
					],
					'ovz' => [
						'title'   => 'Доступная среда',
						'weight'  => 120,
						'content' => '',
						'type'    => 'sveden'
					],
					'inter' => [
						'title'   => 'Международное сотрудничество',
						'weight'  => 130,
						'content' => '',
						'type'    => 'sveden'
					],
				],
			],
			'infosec' => [
				'type'    => 'post_type_archive',
				'title'   => 'Информационная безопасность',
				'content' => '',
				'weight'  => 20,
				'below'   => [
					'common'   => [
						'title'   => 'Локальные нормативные акты в сфере обеспечения информационной безопасности обучающихся',
						'content' => '',
						'weight'  => 10,
						'type'    => 'infosec'
					],
					'normreg'  => [
						'title'   => 'Нормативное регулирование',
						'content' => '',
						'weight'  => 20,
						'type'    => 'infosec'
					],
					'educator' => [
						'title'   => 'Педагогическим работникам',
						'content' => '',
						'weight'  => 30,
						'type'    => 'infosec'
					],
					'students' => [
						'title'   => 'Обучающимся',
						'content' => '',
						'weight'  => 40,
						'type'    => 'infosec'
					],
					'parents'  => [
						'title'   => 'Родителям',
						'content' => '',
						'weight'  => 50,
						'type'    => 'infosec'
					],
					'sites' => [
						'title'    => 'Детские безопасные сайты',
						'content'  => '',
						'weight'   => 60,
						'type'     => 'infosec'
					],
				],
			],
			'anticorruption' => [
				'type'    => 'post_type_archive',
				'title'   => 'Противодействие коррупции',
				'content' => '',
				'weight'  => 30,
				'below'   => [
					'normativnieacti' => [
						'title'   => 'Нормативные правовые и иные акты в сфере противодействия коррупции',
						'content' => '',
						'weight'  => 10,
						'type'    => 'anticorruption'
					],
					'expertise' => [
						'title'   => 'Антикоррупционная экспертиза',
						'content' => '',
						'weight'  => 20,
						'type'    => 'anticorruption'
					],
					'iniemetodmaterialy' => [
						'title'   => 'Методические материалы',
						'content' => '',
						'weight'  => 30,
						'type'    => 'anticorruption'
					],
					'forms' => [
						'title'   => 'Формы документов, связанных с противодействием коррупции, для заполнения',
						'content' => '',
						'weight'  => 40,
						'type'    => 'anticorruption'
					],
					'svedenodohodah' => [
						'title'   => 'Сведения о доходах, расходах, об имуществе и обязательствах имущественного характера',
						'content' => '',
						'weight'  => 50,
						'type'    => 'anticorruption'
					],
					'commission' => [
						'title'   => 'Комиссия по соблюдению требований к служебному поведению и урегулированию конфликта интересов (аттестационная комиссия)',
						'content' => '',
						'weight'  => 60,
						'type'    => 'anticorruption'
					],
					'feedback' => [
						'title'   => 'Обратная связь для сообщений о фактах коррупции',
						'content' => '',
						'weight'  => 70,
						'type'    => 'anticorruption'
					],
					'responsibility' => [
						'title'   => 'Меры юридической ответственности',
						'content' => '',
						'weight'  => 80,
						'type'    => 'anticorruption'
					],
					'infomaterial' => [
						'title'   => 'Информационные материалы',
						'content' => '',
						'weight'  => 90,
						'type'    => 'anticorruption'
					],
				],
			],
			'qualityassessment' => [
				'type'    => 'qualityassessment',
				'title'   => 'Независимая оценка качества',
				'content' => '',
				'weight'  => 40,
			],
			'distance_education' => [
				'type'    => 'shedule',
				'title'   => 'Дистанционное обучение',
				'content' => '',
				'weight'  => 50,
			],
			'educative' => [
				'type'    => 'post_type_archive',
				'title'   => 'Воспитательная работа',
				'content' => '',
				'weight'  => 60,
				'below'   => [
					'edwpartdo' => [
						'title'   => 'Часть основной образовательной программы дошкольного образования',
						'content' => '',
						'weight'  => 10,
						'type'    => 'educative'
					],
					'edwpartnoo' => [
						'title'   => 'Часть основной образовательной программы начального общего образования',
						'content' => '',
						'weight'  => 20,
						'type'    => 'educative'
					],
					'edwpartooo' => [
						'title'   => 'Часть основной образовательной программы основного общего образования',
						'content' => '',
						'weight'  => 30,
						'type'    => 'educative'
					],
					'edwpartsoo' => [
						'title'   => 'Часть основной образовательной программы среднего общего образования',
						'content' => '',
						'weight'  => 40,
						'type'    => 'educative'
					],
					'edwanaliz' => [
						'title'   => 'Анализ достижений',
						'content' => '',
						'weight'  => 50,
						'type'    => 'educative'
					],
					'edwinfo' => [
						'title'   => 'Информация о психолого-педагогической и социальной помощи',
						'content' => '',
						'weight'  => 60,
						'type'    => 'educative'
					],
					'edwevents' => [
						'title'   => 'Общешкольные события',
						'content' => '',
						'weight'  => 70,
						'type'    => 'educative'
					],
				],
			],
			'gia' => [
				'type'    => 'sveden',
				'title'   => 'Государственная итоговая аттестация',
				'content' => '',
				'weight'  => 70,
			],
			'meals' => [
				'type'    => 'post',
				'title'   => 'Организация питания',
				'content' => '<ul><li><a href="sveden/meals">' . 'Организация питания' . '</a></li><li><a href="food/index">'.'Ежедневное меню горячего питания' . '</a></li></ul>',
				'weight'  => 80,
				'below'   => [
					'meals' => [
						'title'   => 'Организация питания',
						'content' => '',
						'weight'  => 10,
						'type' => 'sveden'
					],
					'index' => [
						'title'   => 'Ежедневное меню горячего питания',
						'content' => '',
						'weight'  => 20,
						'type'    => 'food'
					],
				],
			],
			'labor_protection' => [
				'type'    => 'sveden',
				'title'   => 'Охрана труда',
				'content' => '',
				'weight'  => 90,
			],
			'accounting_policy' => [
				'type'    => 'sveden',
				'title'   => 'Основные положения учетной политики',
				'content' => '',
				'weight'  => 100,
			],
		];
	}

	public function register(){
		add_action( 'init', [ $this, 'magicsite_integration_post_type'] );

		add_filter( 'template_include', [ $this, 'magicsite_integration_template'] );
		add_action( 'wp_enqueue_scripts', [ $this, 'magicsite_enqueue'] );
		add_filter( 'the_content', [ $this, 'magicsite_page_content'] );
		add_action( 'wp_footer', [ $this, 'magicsite_page_footer'] );

		add_action( 'admin_init', [ $this, 'magicsite_integration_settings_init'] );
		add_action( 'admin_menu', [ $this, 'add_admin_menu'] );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'magicsite_integration_settings_link'] );
		$this->magicsite_includes();
	}

	public function magicsite_integration_post_type() {
		register_post_type( 'anticorruption', [
			'label'       => 'Противодействие коррупции',
			'public'      => true,
			'has_archive' => true,
			'rewrite'     => ['slug' => 'anticorruption'],
			'supports'    => false,
			'show_ui'     => false,
		]);
		register_post_type( 'educative', [
			'label' => 'Воспитательная работа',
			'public'      => true,
			'has_archive' => true,
			'rewrite'     => ['slug' => 'educative'],
			'supports'    => false,
			'show_ui'     => false,
		]);
		register_post_type( 'food', [
			'label'       => 'Ежедневное меню горячего питания',
			'public'      => true,
			'has_archive' => false,
			'rewrite'     => ['slug' => 'food'],
			'supports'    => false,
			'show_ui'     => false,
		]);
		register_post_type( 'infosec', [
			'label'       => 'Информационная безопасность',
			'public'      => true,
			'has_archive' => true,
			'rewrite'     => ['slug' => 'infosec'],
			'supports'    => false,
			'show_ui'     => false,
		]);
		register_post_type( 'qualityassessment', [
			'label'       => 'Независимая оценка качества',
			'public'      => true,
			'has_archive' => false,
			'rewrite'     => ['slug' => 'qualityassessment'],
			'supports'    => false,
			'show_ui'     => false,
		]);
		register_post_type( 'shedule', [
			'label'       => 'Дистанционное обучение',
			'public'      => true,
			'has_archive' => false,
			'rewrite'     => ['slug' => 'shedule'],
			'supports'    => false,
			'show_ui'     => false,
		]);
		register_post_type( 'sveden', [
			'label'       => 'Сведения об образовательной организации',
			'public'      => true,
			'has_archive' => true,
			'rewrite'     => ['slug' => 'sveden'],
			'supports'    => false,
			'show_ui'     => false,
		]);
	}

	public function magicsite_integration_template( $template ) {
		if ( is_post_type_archive( 'anticorruption' ) ) {
			$theme_files = ['archive-anticorruption.php','magicsite-integration/archive-anticorruption.php'];
			$theme_exist = locate_template( $theme_files, false );
			if ( $theme_exist != '' ) {
				return $theme_exist;
			} else {
				return plugin_dir_path(__FILE__) . 'templates/archive-anticorruption.php';
			}
		} elseif ( is_post_type_archive( 'educative' ) ) {
			$theme_files = ['archive-educative.php','magicsite-integration/archive-educative.php'];
			$theme_exist = locate_template( $theme_files, false );
			if ( $theme_exist != '' ) {
				return $theme_exist;
			} else {
				return plugin_dir_path(__FILE__) . 'templates/archive-educative.php';
			}
		} elseif ( is_post_type_archive( 'infosec' ) ) {
			$theme_files = ['archive-infosec.php','magicsite-integration/archive-infosec.php'];
			$theme_exist = locate_template( $theme_files, false );
			if ( $theme_exist != '' ) {
				return $theme_exist;
			} else {
				return plugin_dir_path(__FILE__) . 'templates/archive-infosec.php';
			}
		} elseif ( is_post_type_archive( 'sveden' ) ) {
			$theme_files = ['archive-sveden.php','magicsite-integration/archive-sveden.php'];
			$theme_exist = locate_template( $theme_files, false );
			if ( $theme_exist != '' ) {
				return $theme_exist;
			} else {
				return plugin_dir_path(__FILE__) . 'templates/archive-sveden.php';
			}
		} elseif (
			is_singular( 'anticorruption' ) ||
			is_singular( 'educative' ) ||
			is_singular( 'food' ) ||
			is_singular( 'infosec' ) ||
			is_singular( 'qualityassessment' ) ||
			is_singular( 'shedule' ) ||
			is_singular( 'sveden' )
		) {
			$theme_files = ['single-sveden.php','magicsite-integration/single-sveden.php'];
			$theme_exist = locate_template( $theme_files, false );
			if ( $theme_exist != '' ) {
				return $theme_exist;
			} else {
				return plugin_dir_path(__FILE__) . 'templates/single-sveden.php';
			}
		}
		return $template;
	}

	public function add_admin_menu() {
		add_options_page(
			'Настрока интеграции с MagicSite',
			'MagicSite Integration',
			'manage_options',
			'magicsite-integration-settings',
			[ $this, 'magicsite_integration_settings'],
			100
		);
	}

	public function magicsite_integration_settings() {
		require_once plugin_dir_path(__FILE__) . 'admin/settings.php';
	}

	public function magicsite_integration_settings_link( $links ) {
		$link = '<a href="admin.php?page=magicsite-integration-settings">' . 'Настройки' . '</a>';
		array_push( $links, $link );
		return $links;
	}

	public function magicsite_integration_settings_init() {
		register_setting(
			'magicsite_intergration_settings',
			'magicsite_intergration_settings_options',
			[
				'type' => 'string',
				'group' => 'magicsite_intergration_settings',
				'description'       => '',
				'sanitize_callback' => [ $this, 'magicsite_intergration_settings_validate'],
				'show_in_rest'      => false
			]
		);
		add_settings_section(
			'magicsite_intergration_settings_section',
			'',
			[ $this, 'magicsite_integration_settings_section_html'],
			'magicsite-integration-settings'
		);
		add_settings_field(
			'magicsite_url',
			'URL сайта в ИС MagicSite',
			[ $this, 'magicsite_intergration_url_html'],
			'magicsite-integration-settings',
			'magicsite_intergration_settings_section'
		);
	}

	public function magicsite_integration_settings_section_html() {
	}

	public function magicsite_intergration_url_html() {
		$options = get_option('magicsite_intergration_settings_options');
		?>
		<input type="text" name="magicsite_intergration_settings_options[magicsite_url]" value="<?php echo $options['magicsite_url'] ?? ''; ?>" />
		<?php
	}

	public function magicsite_intergration_settings_validate( $input ) {
		if ( isset( $input['magicsite_url'] ) && $magicsite_url = $this->get_magicsite_url( $input['magicsite_url'] ) ) {
			$input['magicsite_url'] = $magicsite_url;

			$post_types = self::get_magicsite_pages();
			foreach ( $post_types as $type => $items ) {
				foreach ( $items as $item ) {
					$content = $this->magicsite_post_content( $magicsite_url, $type, $item );
					$post = get_page_by_path( $item, OBJECT, $type );
					$new_post = array(
						'ID'           => $post->ID,
						'post_content' => $content,
					);

					wp_update_post( $new_post );
				}
			}
		} else {
			$input['magicsite_url'] = '';
			add_settings_error( 'magicsite_url', 'magicsite-url', 'Неверное значение поля "URL сайта в среде MagicSite"' );
		}
		return $input;
	}

	public function magicsite_enqueue() {
		wp_enqueue_script( 'jquery' );
		$options = get_option( 'magicsite_intergration_settings_options' );
		if ( isset( $options['magicsite_url'] ) && $magicsite_url = $this->get_magicsite_url( $options['magicsite_url'] ) ) {
			?>
			<script type="text/javascript">var magicsite_url = '<?php echo $magicsite_url ?>';</script>
			<?php
			wp_enqueue_style( 'magicsite-page', 'https://js.edusite.ru/mmagicutf.css' );
			wp_enqueue_style( 'magicsite-fancybox', 'https://js.edusite.ru/jquery.fancybox.min.css' );

			wp_enqueue_script( 'magicsite-integration', plugin_dir_url(__FILE__) . 'assets/js/integration.js', ['jquery'] );
			wp_enqueue_script( 'magicsite-fancybox', 'https://js.edusite.ru/jquery.fancybox.min.js', ['jquery'] );
			wp_enqueue_script( 'magicsite-yandex-map', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU' );
		}
		wp_enqueue_style( 'magicsite-navigation-menu', plugin_dir_url(__FILE__) . 'assets/css/navigation-menu.css' );
		wp_enqueue_script( 'magicsite-navigation-menu', plugin_dir_url(__FILE__) . 'assets/js/navigation-menu.js', ['jquery'] );
	}

	public function magicsite_page_footer() {
		$options = get_option( 'magicsite_intergration_settings_options' );
		if ( isset( $options['magicsite_url'] ) && $magicsite_url = $this->get_magicsite_url($options['magicsite_url'] ) ) {
			if (
				is_singular( 'anticorruption' ) ||
				is_singular( 'educative' ) ||
				is_singular( 'food' ) ||
				is_singular( 'infosec' ) ||
				is_singular( 'qualityassessment' ) ||
				is_singular( 'shedule' ) ||
				is_singular( 'sveden' )
			) {
				$post = get_post();
				$magicpages = $this->get_magicsite_pages();
				if ( ! in_array( $post->post_name, $magicpages[ $post->post_type ] ) ) {
					$post->post_type = 'sveden';
					$post->post_name = 'common';
				}
				$response = $this->get_magicsite_page( $options['magicsite_url'] . $post->post_type . '/' . $post->post_name . '.html' );
				if ( $response['response_code'] == 200 ) {
					$dom = new DOMDocument();
					$dom->preserveWhiteSpace = false;
					libxml_use_internal_errors( true );
					$dom->loadHTML( $response['response'] );
					$xpath = new DOMXPath( $dom );
					$links = $xpath->query( "//link[contains(@id, 'magic-skin')]" );
					$sections = $xpath->query( "//*[contains(@class, '" . $post->post_name . "-page-script')]" );
					$magic_script ='';
					foreach ( $sections as $section ) {
						$magic_script .= $dom->saveHTML( $section );
					}
					libxml_clear_errors();
					echo $magic_script;
				}
			}
		}
	}

	public function magicsite_page_content( $content ) {
		if (
			is_singular( 'anticorruption' ) ||
			is_singular( 'educative' ) ||
			is_singular( 'food' ) ||
			is_singular( 'infosec' ) ||
			is_singular( 'qualityassessment' ) ||
			is_singular( 'shedule' ) ||
			is_singular( 'sveden' )
		) {
			$options = get_option( 'magicsite_intergration_settings_options' );
			if ( isset( $options['magicsite_url'] ) && $magicsite_url = $this->get_magicsite_url( $options['magicsite_url'] ) ) {
				$post = get_post();

				$magicpages = $this->get_magicsite_pages();
				if ( ! in_array( $post->post_name, $magicpages[ $post->post_type ] ) ) {
					$post->post_type = 'sveden';
					$post->post_name = 'common';
				}

				$magicsite_content = $this->magicsite_post_content ( $magicsite_url, $post->post_type, $post->post_name );
			} else {
				$magicsite_content = '<div>' . 'Ошибка настройки плагина интеграции с MagicSite' . '</div>';
			}	
		}
		return $magicsite_content ?? $content;
	}
}

if ( class_exists( 'MagicSiteIntegration' ) ) {
	$magicSite = new MagicSiteIntegration();
	$magicSite->register();
}

register_activation_hook( __FILE__, [ $magicSite, 'plugin_activation'] );
register_deactivation_hook( __FILE__, [ $magicSite, 'plugin_deactivation'] );
