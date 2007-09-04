<?php

/**
 * Get translated magic words, if available
 *
 * @param string $lang Language code
 * @return array
 */
function efParserFunctionsWords( $lang ) {
	$words = array();

	/**
	 * English
	 */
	$words['en'] = array(
		'expr' 		=> array( 0, 'expr' ),
		'if' 		=> array( 0, 'if' ),
		'ifeq' 		=> array( 0, 'ifeq' ),
		'ifexpr' 	=> array( 0, 'ifexpr' ),
		'switch' 	=> array( 0, 'switch' ),
		'default' 	=> array( 0, '#default' ),
		'ifexist' 	=> array( 0, 'ifexist' ),
		'time' 		=> array( 0, 'time' ),
		'timel' 	=> array( 0, 'timel' ),
		'rel2abs' 	=> array( 0, 'rel2abs' ),
		'titleparts' => array( 0, 'titleparts' ),
	);

	/**
	 * Farsi-Persian
	 */
	$words['fa'] = array(
		'expr' 		=> array( 0, 'حساب',         'expr' ),
		'if' 		=> array( 0, 'اگر',          'if' ),
		'ifeq' 		=> array( 0, 'اگرمساوی',     'ifeq' ),
		'ifexpr' 	=> array( 0, 'اگرحساب',      'ifexpr' ),
		'switch' 	=> array( 0, 'گزینه',        'switch' ),
		'default' 	=> array( 0, '#پیش‌فرض',      '#default' ),
		'ifexist' 	=> array( 0, 'اگرموجود',     'ifexist' ),
		'time' 		=> array( 0, 'زمان',         'time' ),
		'rel2abs' 	=> array( 0, 'نسبی‌به‌مطلق',   'rel2abs' ),
	);

	/**
	 * Hebrew
	 */
	$words['he'] = array(
		'expr'       => array( 0, 'חשב',         'expr' ),
		'if'         => array( 0, 'תנאי',        'if' ),
		'ifeq'       => array( 0, 'שווה',        'ifeq' ),
		'ifexpr'     => array( 0, 'חשב תנאי',    'ifexpr' ),
		'switch'     => array( 0, 'בחר',         'switch' ),
		'default'    => array( 0, '#ברירת מחדל', '#default' ),
		'ifexist'    => array( 0, 'קיים',        'ifexist' ),
		'time'       => array( 0, 'זמן',         'time' ),
		'timel'      => array( 0, 'זמןמ',        'timel' ),
		'rel2abs'    => array( 0, 'יחסי למוחלט', 'rel2abs' ),
		'titleparts' => array( 0, 'חלק בכותרת',  'titleparts' ),
	);

	/**
	 * Indonesian
	 */
	$words['id'] = array(
		'expr'       => array( 0, 'hitung',       'expr' ),
		'if'         => array( 0, 'jika',         'if' ),
		'ifeq'       => array( 0, 'jikasama',     'ifeq' ),
		'ifexpr'     => array( 0, 'jikahitung',   'ifexpr' ),
		'switch'     => array( 0, 'pilih',        'switch' ),
		'default'    => array( 0, '#baku',        '#default' ),
		'ifexist'    => array( 0, 'jikaada',      'ifexist' ),
		'time'       => array( 0, 'waktu',        'time' ),
		'rel2abs'    => array( 0, 'rel2abs' ),
		'titleparts' => array( 0, 'bagianjudul',  'titleparts' ),
	);

	# English is used as a fallback, and the English synonyms are
	# used if a translation has not been provided for a given word
	return ( $lang == 'en' || !isset( $words[$lang] ) )
		? $words['en']
		: array_merge( $words['en'], $words[$lang] );
}

/**
 * Get extension messages
 *
 * @return array
 */
function efParserFunctionsMessages() {
	$messages = array(

/* English */
'en' => array(
	 'pfunc_time_error'             => 'Error: invalid time',
	 'pfunc_time_too_long'          => 'Error: too many #time calls',
	 'pfunc_rel2abs_invalid_depth'  => 'Error: Invalid depth in path: \"$1\" (tried to access a node above the root node)',
),

/* German */
'de' => array(
	 'pfunc_time_error'             => 'Fehler: ungültige Zeitangabe',
	 'pfunc_time_too_long'          => 'Fehler: zu viele #time-Aufrufe',
	 'pfunc_rel2abs_invalid_depth'  => 'Fehler: ungültige Tiefe in Pfad: „$1“ (Versuch, auf einen Knotenpunkt oberhalb des Hauptknotenpunktes zuzugreifen)',
),

/* French */
'fr' => array(
	 'pfunc_time_error'            => 'Erreur: durée invalide',
	 'pfunc_time_too_long'         => 'Erreur: parser #time appelé trop de fois',
	 'pfunc_rel2abs_invalid_depth' => 'Erreur: niveau de répertoire invalide dans le chemin : \"$1\" (a essayé d’accéder à un niveau au-dessus du répertoire racine)',
),

/* Hebrew */
'he' => array(
	 'pfunc_time_error'             => 'שגיאה: זמן שגוי',
	 'pfunc_time_too_long'          => 'שגיאה: שימוש ב"#זמן" פעמים רבות מדי',
	 'pfunc_rel2abs_invalid_depth'  => 'שגיאה: עומק שגוי בנתיב: \"$1\" (ניסיון כניסה לצומת מעל צומת השורש)',
),

/* Kazakh Cyrillic */
'kk-kz' => array(
	 'pfunc_time_error'             => 'Қате: жарамсыз уақыт',
	 'pfunc_time_too_long'          => 'Қате: #time әмірін шақыруы тым көп',
	 'pfunc_rel2abs_invalid_depth'  => 'Қате: Мына жолдың жарамсыз терендігі "$1" (тамыр түйіннің үстіндегі түйінге қатынау талабы)',
),
/* Kazakh Latin */
'kk-tr' => array(
	 'pfunc_time_error'             => 'Qate: jaramsız waqıt',
	 'pfunc_time_too_long'          => 'Qate: #time ämirin şaqırwı tım köp',
	 'pfunc_rel2abs_invalid_depth'  => 'Qate: Mına joldıñ jaramsız terendigi "$1" (tamır tüýinniñ üstindegi tüýinge qatınaw talabı)',
),
/* Kazakh Arabic */
'kk-cn' => array(
	 'pfunc_time_error'             => 'قاتە: جارامسىز ۋاقىت',
	 'pfunc_time_too_long'          => 'قاتە: #time ٵمٸرٸن شاقىرۋى تىم كٶپ',
	 'pfunc_rel2abs_invalid_depth'  => 'قاتە: مىنا جولدىڭ جارامسىز تەرەندٸگٸ "$1" (تامىر تٷيٸننٸڭ ٷستٸندەگٸ تٷيٸنگە قاتىناۋ تالابى)',
),

/* Dutch */
'nl' => array(
	 'pfunc_time_error'             => 'Fout: ongeldige tijd',
	 'pfunc_time_too_long'          => 'Fout: #time te vaak aangeroepen',
	 'pfunc_rel2abs_invalid_depth'  => 'Fout: ongeldige diepte in pad: \"$1\" (probeerde een node boven de stamnode aan te roepen)',
),

/* Cantonese */
'yue' => array(
	 'pfunc_time_error'             => '錯: 唔啱嘅時間',
	 'pfunc_time_too_long'          => '錯: 太多 #time 呼叫',
	 'pfunc_rel2abs_invalid_depth'  => '錯: 唔啱路徑嘅深度: \"$1\" (已經試過由頭點落個點度)',
),

/* Chinese (Simplified) */
'zh-hans' => array(
	 'pfunc_time_error'             => '错误: 不正确的时间',
	 'pfunc_time_too_long'          => '错误: 过多 #time 的呼叫',
	 'pfunc_rel2abs_invalid_depth'  => '错误: 不正确的路径深度: \"$1\" (已经尝试在顶点访问该点)',
),


/* Chinese (Traditional) */
'zh-hant' => array(
	 'pfunc_time_error'             => '錯誤: 不正確的時間',
	 'pfunc_time_too_long'          => '錯誤: 過多 #time 的呼叫',
	 'pfunc_rel2abs_invalid_depth'  => '錯誤: 不正確的路徑深度: \"$1\" (已經嘗試在頂點存取該點)',
),

);

	/* Kazakh default, fallback to kk-kz */
	$messages['kk'] = $messages['kk-kz'];

	/* Chinese defaults, fallback to zh-hans */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-sg'] = $messages['zh-hans'];
	$messages['zh-tw'] = $messages['zh-hant'];

	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $messages ;
}
