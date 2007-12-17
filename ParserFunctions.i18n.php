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
	'pfunc_time_error'                      => 'Error: invalid time',
	'pfunc_time_too_long'                   => 'Error: too many #time calls',
	'pfunc_rel2abs_invalid_depth'           => 'Error: Invalid depth in path: \"$1\" (tried to access a node above the root node)',
	'pfunc_expr_stack_exhausted'            => 'Expression error: Stack exhausted',
	'pfunc_expr_unexpected_number'          => 'Expression error: Unexpected number',
	'pfunc_expr_preg_match_failure'         => 'Expression error: Unexpected preg_match failure',
	'pfunc_expr_unrecognised_word'          => 'Expression error: Unrecognised word "$1"',
	'pfunc_expr_unexpected_operator'        => 'Expression error: Unexpected $1 operator',
	'pfunc_expr_missing_operand'            => 'Expression error: Missing operand for $1',
	'pfunc_expr_unexpected_closing_bracket' => 'Expression error: Unexpected closing bracket',
	'pfunc_expr_unrecognised_punctuation'   => 'Expression error: Unrecognised punctuation character "$1"',
	'pfunc_expr_unclosed_bracket'           => 'Expression error: Unclosed bracket',
	'pfunc_expr_division_by_zero'           => 'Division by zero',
	'pfunc_expr_unknown_error'              => 'Expression error: Unknown error ($1)',
	'pfunc_expr_not_a_number'               => 'In $1: result is not a number',
	'pfunc_ifexist_warning'                 => 'Warning: This page contains too many #ifexist calls. It should have less than $2, there are now $1.',
	'pfunc_max_ifexist_category'            => 'Pages with too many ifexist calls',
),

'ar' => array(
	'pfunc_time_error' => 'خطأ: زمن غير صحيح',
	'pfunc_time_too_long' => 'خطأ: too many #time calls',
	'pfunc_rel2abs_invalid_depth' => 'خطأ: عمق غير صحيح في المسار: \"$1\" (حاول دخول عقدة فوق العقدة الجذرية)',
	'pfunc_expr_stack_exhausted' => 'خطأ في التعبير: ستاك مجهد',
	'pfunc_expr_unexpected_number' => 'خطأ في التعبير: رقم غير متوقع',
	'pfunc_expr_preg_match_failure' => 'خطأ في التعبير: فشل preg_match غير متوقع',
	'pfunc_expr_unrecognised_word' => 'خطأ في التعبير: كلمة غير متعرف عليها "$1"',
	'pfunc_expr_unexpected_operator' => 'خطأ في التعبير: عامل $1 غير متوقع',
	'pfunc_expr_missing_operand' => 'خطأ في التعبير: operand مفقود ل$1',
	'pfunc_expr_unexpected_closing_bracket' => 'خطأ في التعبير: قوس إغلاق غير متوقع',
	'pfunc_expr_unrecognised_punctuation' => 'خطأ في التعبير: علامة ترقيم غير متعرف عليها "$1"',
	'pfunc_expr_unclosed_bracket' => 'خطأ في التعبير: قوس غير مغلق',
	'pfunc_expr_division_by_zero' => 'القسمة على صفر',
	'pfunc_expr_unknown_error' => 'خطأ في التعبير: خطأ غير معروف ($1)',
	'pfunc_expr_not_a_number' => 'في $1: النتيجة ليست رقما',
	'pfunc_ifexist_warning' => 'تحذير: هذه المقالة تحتوي على #ifexist calls كثيرة جدا. ينبغي أن تحتوي على أقل من $2، يوجد الآن $1.',
	'pfunc_max_ifexist_category' => 'الصفحات التي تحتوي على ifexist calls كثيرة جدا',
),

'bg' => array(
	'pfunc_expr_stack_exhausted' => 'Грешка в записа: Стекът е изчерпан',
	'pfunc_expr_unexpected_number' => 'Грешка в записа: Неочаквано число',
	'pfunc_expr_unrecognised_word' => 'Грешка в записа: Неразпозната дума "$1"',
	'pfunc_expr_unexpected_operator' => 'Грешка в записа: Неочакван оператор $1',
	'pfunc_expr_missing_operand' => 'Грешка в записа: Липсващ операнд в $1',
	'pfunc_expr_unexpected_closing_bracket' => 'Грешка в записа: Една затваряща скоба в повече',
	'pfunc_expr_unrecognised_punctuation' => 'Грешка в записа: Неразпознат пунктуационен знак "$1"',
	'pfunc_expr_unclosed_bracket' => 'Грешка в записа: Незатворена скоба',
	'pfunc_expr_division_by_zero' => 'Деление на нула',
	'pfunc_expr_unknown_error' => 'Грешка в записа: Неразпозната грешка ($1)',
	'pfunc_expr_not_a_number' => 'В $1: резултатът не е число',
),

'cs' => array(
	'pfunc_time_error' => 'Chyba: neplatný čas',
	'pfunc_time_too_long' => 'Chyba: příliš mnoho volání #time',
	'pfunc_rel2abs_invalid_depth' => 'Chyba: Neplatná hloubka v cestě: \"$1\" (pokus o přístup do uzlu vyššího než kořen)',
	'pfunc_expr_stack_exhausted' => 'Chyba ve výrazu: Zásobník plně obsazen',
	'pfunc_expr_unexpected_number' => 'Chyba ve výrazu: Očekáváno číslo',
	'pfunc_expr_unrecognised_word' => 'Chyba ve výrazu: Nerozpoznané slovo „$1“',
	'pfunc_expr_unexpected_operator' => 'Chyba ve výrazu: Neočekávaný operátor $1',
	'pfunc_expr_missing_operand' => 'Chyba ve výrazu: Chybí operand pro $1',
	'pfunc_expr_unexpected_closing_bracket' => 'Chyba ve výrazu: Neočekávaná uzavírací závorka',
	'pfunc_expr_unrecognised_punctuation' => 'Chyba ve výrazu: Nerozpoznaný interpunkční znak „$1“',
	'pfunc_expr_unclosed_bracket' => 'Chyba ve výrazu: Neuzavřené závorky',
	'pfunc_expr_division_by_zero' => 'Dělení nulou',
	'pfunc_expr_unknown_error' => 'Chyba ve výrazu: Neznámá chyba ($1)',
	'pfunc_expr_not_a_number' => 'V $1: výsledkem není číslo',
	'pfunc_ifexist_warning' => 'Varování: Tato stránka obsahuje příliš mnoho volání funkce #ifexist. Mělo by jich být méně než $2, momentálně jich je $1.',
	'pfunc_max_ifexist_category' => 'Stránky s příliš mnoha voláními funkce ifexist',
),

/* German */
'de' => array(
	'pfunc_time_error'                      => 'Fehler: ungültige Zeitangabe',
	'pfunc_time_too_long'                   => 'Fehler: zu viele #time-Aufrufe',
	'pfunc_rel2abs_invalid_depth'           => 'Fehler: ungültige Tiefe in Pfad: „$1“ (Versuch, auf einen Knotenpunkt oberhalb des Hauptknotenpunktes zuzugreifen)',
	'pfunc_expr_stack_exhausted'            => 'Expression-Fehler: Stacküberlauf',
	'pfunc_expr_unexpected_number'          => 'Expression-Fehler: Unerwartete Zahl',
	'pfunc_expr_preg_match_failure'         => 'Expression-Fehler: Unerwartete „preg_match“-Fehlfunktion',
	'pfunc_expr_unrecognised_word'          => 'Expression-Fehler: Unerkanntes Wort „$1“',
	'pfunc_expr_unexpected_operator'        => 'Expression-Fehler: Unerwarteter Operator: <strong><tt>$1</tt></strong>',
	'pfunc_expr_missing_operand'            => 'Expression-Fehler: Fehlender Operand für <strong><tt>$1</tt></strong>',
	'pfunc_expr_unexpected_closing_bracket' => 'Expression-Fehler: Unerwartete schließende eckige Klammer',
	'pfunc_expr_unrecognised_punctuation'   => 'Expression-Fehler: Unerkanntes Satzzeichen „$1“',
	'pfunc_expr_unclosed_bracket'           => 'Expression-Fehler: Nicht geschlossene eckige Klammer',
	'pfunc_expr_division_by_zero'           => 'Expression-Fehler: Division durch Null',
	'pfunc_expr_unknown_error'              => 'Expression-Fehler: Unbekannter Fehler ($1)',
	'pfunc_expr_not_a_number'               => 'Expression-Fehler: In $1: Ergebnis ist keine Zahl',
	'pfunc_ifexist_warning'                 => 'Warnung: Diese Seite enthält zuviele #ifexist-Aufrufe. Es dürfen maximal $2 Aufrufe sein, es sind aber $1 Aufrufe.',
	'pfunc_max_ifexist_category'            => 'Seiten mit zuvielen ifexist-Aufrufen',
),

'el' => array(
	'pfunc_time_error' => 'Σφάλμα: άκυρος χρόνος',
),

/* French */
'fr' => array(
	'pfunc_time_error' => 'Erreur : durée invalide',
	'pfunc_time_too_long' => 'Erreur : parser #time appelé trop de fois',
	'pfunc_rel2abs_invalid_depth' => 'Erreur: niveau de répertoire invalide dans le chemin : \"$1\" (a essayé d’accéder à un niveau au-dessus du répertoire racine)',
	'pfunc_expr_stack_exhausted' => 'Expression erronée : pile épuisée',
	'pfunc_expr_unexpected_number' => 'Expression erronée : nombre non attendu',
	'pfunc_expr_preg_match_failure' => 'Expression erronée : echec d\'une expression non comprise',
	'pfunc_expr_unrecognised_word' => 'Erreur d\'expression : le mot \'\'\'$1\'\'\' n\'est pas reconnu',
	'pfunc_expr_unexpected_operator' => 'Erreur d\'expression : l\'opérateur \'\'\'$1\'\'\' n\'est pas reconnu',
	'pfunc_expr_missing_operand' => 'Erreur d\'expression : l\'opérande \'\'\'$1\'\'\' n\'est pas reconnue',
	'pfunc_expr_unexpected_closing_bracket' => 'Erreur d\'expression : parenthèse fermante non prévue',
	'pfunc_expr_unrecognised_punctuation' => 'Erreur d\'expresion : caractère de ponctuation « $1 » non reconnu',
	'pfunc_expr_unclosed_bracket' => 'Erreur d’expression : parenthèse non fermée',
	'pfunc_expr_division_by_zero' => 'Division par zéro',
	'pfunc_expr_unknown_error' => 'Erreur d\'expression : erreur inconnue ($1)',
	'pfunc_expr_not_a_number' => 'Dans $1 : le résultat n\'est pas un nombre',
	'pfunc_ifexist_warning' => 'Attention : Cette page contient trop d\'appels à <code>#ifexist</code>. Elle devrait en avoir moins que $2, alors qu\'elle en a $1.',
	'pfunc_max_ifexist_category' => 'Pages avec trop d\'appels à <code>#ifexist</code>',
),

/** Galician (Galego)
 * @author Xosé
 * @author Alma
 */
'gl' => array(
	'pfunc_time_error'                      => 'Erro: hora non válida',
	'pfunc_time_too_long'                   => 'Erro: demasiadas chamadas a #time',
	'pfunc_rel2abs_invalid_depth'           => 'Erro: Profundidade da ruta non válida: \"$1\" (tentouse acceder a un nodo por riba do nodo raíz)',
	'pfunc_expr_stack_exhausted'            => 'Erro de expresión: Pila esgotada',
	'pfunc_expr_unexpected_number'          => 'Erro de expresión: Número inesperado',
	'pfunc_expr_preg_match_failure'         => 'Erro de expresión: Fallo de preg_match inesperado',
	'pfunc_expr_unrecognised_word'          => 'Erro de expresión: Palabra descoñecida "$1"',
	'pfunc_expr_unexpected_operator'        => 'Erro de expresión: Operador $1 inesperado',
	'pfunc_expr_missing_operand'            => 'Erro de expresión: Falta un operador para $1',
	'pfunc_expr_unexpected_closing_bracket' => 'Expresión de erro: Inesperado corchete',
	'pfunc_expr_unrecognised_punctuation'   => 'Erro de expresión: Signo de puntuación descoñecido "$1"',
	'pfunc_expr_unclosed_bracket'           => 'Erro de expresión: Paréntese sen pechar',
	'pfunc_expr_division_by_zero'           => 'División por cero',
	'pfunc_expr_unknown_error'              => 'Erro de expresión: Erro descoñecido ($1)',
	'pfunc_expr_not_a_number'               => 'En $1: o resultado non é un número',
	'pfunc_ifexist_warning'                 => 'Advertencia: Este artigo inclúe demasiadas chamadas a #ifexists. Debería ter menos de $2 e agora hai $1.',
	'pfunc_max_ifexist_category'            => 'Páxinas con demasiadas chamadas a ifexists',

),

/* Hebrew */
'he' => array(
	'pfunc_time_error'                      => 'שגיאה: זמן שגוי',
	'pfunc_time_too_long'                   => 'שגיאה: שימוש ב"#זמן" פעמים רבות מדי',
	'pfunc_rel2abs_invalid_depth'           => 'שגיאה: עומק שגוי בנתיב: "$1" (ניסיון כניסה לצומת מעל צומת השורש)',
	'pfunc_expr_stack_exhausted'            => 'שגיאה בביטוי: המחסנית מלאה',
	'pfunc_expr_unexpected_number'          => 'שגיאה בביטוי: מספר בלתי צפוי',
	'pfunc_expr_preg_match_failure'         => 'שגיאה בביטוי: כישלון בלתי צפוי של התאמת ביטוי רגולרי',
	'pfunc_expr_unrecognised_word'          => 'שגיאה בביטוי: מילה בלתי מזוהה, "$1"',
	'pfunc_expr_unexpected_operator'        => 'שגיאה בביטוי: אופרנד $1 בלתי צפוי',
	'pfunc_expr_missing_operand'            => 'שגיאה בביטוי: חסר אופרנד ל־$1',
	'pfunc_expr_unexpected_closing_bracket' => 'שגיאה בביטוי: סוגריים סוגרים בלתי צפויים',
	'pfunc_expr_unrecognised_punctuation'   => 'שגיאה בביטוי: תו פיסוק בלתי מזוהה, "$1"',
	'pfunc_expr_unclosed_bracket'           => 'שגיאה בביטוי: סוגריים בלתי סגורים',
	'pfunc_expr_division_by_zero'           => 'חלוקה באפס',
	'pfunc_expr_unknown_error'              => 'שגיאה בביטוי: שגיאה בלתי ידועה ($1)',
	'pfunc_expr_not_a_number'               => 'התוצאה של $1 אינה מספר',
	'pfunc_ifexist_warning'                 => 'אזהרה: דף זה כולל יותר מדי קריאות ל"#קיים". הוא צריך לכלול פחות מ־$2, אך כעת יש $1.',
	'pfunc_max_ifexist_category'            => 'דפים עם הוראות קיום רבות מדי',
),

/** Croatian (Hrvatski)
 * @author SpeedyGonsales
 */
'hr' => array(
	'pfunc_time_error'                      => 'Greška: oblik vremena nije valjan',
	'pfunc_time_too_long'                   => 'Greška: prevelik broj #time (vremenskih) poziva',
	'pfunc_rel2abs_invalid_depth'           => 'Greška: Nevaljana dubina putanje: \"$1\" (pokušaj pristupanja čvoru iznad korijenskog)',
	'pfunc_expr_stack_exhausted'            => 'Greška u predlošku: prepunjen stog',
	'pfunc_expr_unexpected_number'          => 'Greška u predlošku: Neočekivan broj',
	'pfunc_expr_preg_match_failure'         => 'Greška u predlošku: Neočekivana preg_match greška',
	'pfunc_expr_unrecognised_word'          => 'Greška u predlošku: Nepoznata riječ "$1"',
	'pfunc_expr_unexpected_operator'        => 'Greška u predlošku: Neočekivani operator $1',
	'pfunc_expr_missing_operand'            => 'Greška u predlošku: Operator $1 nedostaje',
	'pfunc_expr_unexpected_closing_bracket' => 'Greška u predlošku: Neočekivana zatvorena zagrada',
),

'hsb' => array(
	'pfunc_time_error' => 'Zmylk: njepłaćiwe časowe podaće',
	'pfunc_time_too_long' => 'Zmylk: přewjele zawołanjow #time',
	'pfunc_rel2abs_invalid_depth' => 'Zmylk: Njepłaćiwa hłubokosć w pućiku: \"$1\" (Pospyt, zo by na suk wyše hłowneho suka dohrabnyło)',
	'pfunc_expr_stack_exhausted' => 'Wurazowy zmylk: Staplowy skład wučerpany',
	'pfunc_expr_unexpected_number' => 'Wurazowy zmylk: Njewočakowana ličba',
	'pfunc_expr_preg_match_failure' => 'Wurazowy zmylk: Njewočakowana zmylna funkcija "preg_match"',
	'pfunc_expr_unrecognised_word' => 'Wurazowy zmylk: Njespóznate słowo "$1"',
	'pfunc_expr_unexpected_operator' => 'Wurazowy zmylk: Njewočakowany operator $1',
	'pfunc_expr_missing_operand' => 'Wurazowy zmylk: Falowacy operand za $1',
	'pfunc_expr_unexpected_closing_bracket' => 'Wurazowy zmylk: Njewočakowana kónčna róžkata spinka',
	'pfunc_expr_unrecognised_punctuation' => 'Wurazowy zmylk: Njespóznate interpunkciske znamješko "$1"',
	'pfunc_expr_unclosed_bracket' => 'Wurazowy zmylk: Njewotzamknjena róžkata spinka',
	'pfunc_expr_division_by_zero' => 'Diwizija přez nulu',
	'pfunc_expr_unknown_error' => 'Wurazowy zmylk: Njeznaty zmylk ($1)',
	'pfunc_expr_not_a_number' => 'W $1: Wuslědk ličba njeje',
	'pfunc_ifexist_warning' => 'Warnowanje: Tutón nastawk wobsahuje přewjele zawołanjow #ifexist. Měło mjenje hač $2 być, nětko je $1.',
	'pfunc_max_ifexist_category' => 'Strony z přewjele zawołanjow ifexist',
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

/** Latin (Latina)
 * @author UV
 */
'la' => array(
	'pfunc_ifexist_warning'      => 'Monitio: Haec pagina nimis #ifexist adhibet. Licet uti $2, haec pagina nunc utitur $1.',
	'pfunc_max_ifexist_category' => 'Paginae quae nimis ifexist adhibent',

),

'nds' => array(
	'pfunc_time_error' => 'Fehler: mit de Tiet stimmt wat nich',
	'pfunc_time_too_long' => 'Fehler: #time warrt to faken opropen',
	'pfunc_rel2abs_invalid_depth' => 'Fehler: Mit den Padd „$1“ stimmt wat nich, liggt nich ünner den Wuddelorner',
),

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
'nl' => array(
	'pfunc_time_error'                      => 'Fout: ongeldige tijd',
	'pfunc_time_too_long'                   => 'Fout: #time te vaak aangeroepen',
	'pfunc_rel2abs_invalid_depth'           => 'Fout: ongeldige diepte in pad: \"$1\" (probeerde een node boven de stamnode aan te roepen)',
	'pfunc_expr_stack_exhausted'            => 'Fout in uitdrukking: stack uitgeput',
	'pfunc_expr_unexpected_number'          => 'Fout in uitdrukking: onverwacht getal',
	'pfunc_expr_preg_match_failure'         => 'Fout in uitdrukking: onverwacht falen van preg_match',
	'pfunc_expr_unrecognised_word'          => 'Fout in uitdrukking: woord "$1" niet herkend',
	'pfunc_expr_unexpected_operator'        => 'Fout in uitdrukking: niet verwachte operator $1',
	'pfunc_expr_missing_operand'            => 'Fout in uitdrukking: operand voor $1 mist',
	'pfunc_expr_unexpected_closing_bracket' => 'Fout in uitdrukking: haakje sluiten op onverwachte plaats',
	'pfunc_expr_unrecognised_punctuation'   => 'Fout in uitdrukking: niet herkend leesteken "$1"',
	'pfunc_expr_unclosed_bracket'           => 'Fout in uitdrukking: niet gesloten haakje openen',
	'pfunc_expr_division_by_zero'           => 'Deling door nul',
	'pfunc_expr_unknown_error'              => 'Fout in uitdrukking: onbekende fout ($1)',
	'pfunc_expr_not_a_number'               => 'In $1: resultaat is geen getal',
	'pfunc_ifexist_warning'                 => 'Waarschuwing: deze pagina gebruik #ifexists te vaak. Dat zou minder dan $2 keer moeten zijn en is nu $1 keer.',
	'pfunc_max_ifexist_category'            => "Pagina's die ifexist te vaak gebruiken",

),

/** Occitan (Occitan)
 * @author Cedric31
 */
'oc' => array(
	'pfunc_time_error'                      => 'Error: durada invalida',
	'pfunc_time_too_long'                   => 'Error: parser #time apelat tròp de còps',
	'pfunc_rel2abs_invalid_depth'           => 'Error: nivèl de repertòri invalid dins lo camin : \"$1\" (a ensajat d’accedir a un nivèl al-dessús del repertòri raiç)',
	'pfunc_expr_unexpected_number'          => 'Expression erronèa : nombre pas esperat',
	'pfunc_expr_preg_match_failure'         => 'Expression erronèa : una expression pas compresa a pas capitat',
	'pfunc_expr_unrecognised_word'          => "Error d'expression : lo mot '''$1''' es pas reconegut",
	'pfunc_expr_unexpected_operator'        => "Error d'expression : l'operator '''$1''' es pas reconegut",
	'pfunc_expr_missing_operand'            => "Error d'expression : l'operanda '''$1''' es pas reconeguda",
	'pfunc_expr_unexpected_closing_bracket' => "Error d'expression : parentèsi tampanta pas prevista",
	'pfunc_expr_unrecognised_punctuation'   => "Error d'expression : caractèr de ponctuacion « $1 » pas reconegut",
	'pfunc_expr_unclosed_bracket'           => 'Error d’expression : parentèsi pas tampada',
	'pfunc_expr_division_by_zero'           => 'Division per zèro',
	'pfunc_expr_unknown_error'              => "Error d'expression : error desconeguda ($1)",
	'pfunc_expr_not_a_number'               => 'Dins $1 : lo resultat es pas un nombre',
	'pfunc_ifexist_warning'                 => "Atencion : Aquesta pagina conten tròp d'apèls a <code>#ifexist</code>. Ne deuriá aver mens que $2, alara que n'a $1.",
	'pfunc_max_ifexist_category'            => "Paginas amb tròp d'apèls a <code>#ifexist</code>",
),

'pl' => array(
	'pfunc_time_error' => 'Błąd: niepoprawny czas',
	'pfunc_time_too_long' => 'Błąd: za dużo wywołań funkcji #time',
	'pfunc_rel2abs_invalid_depth' => 'Błąd: Nieprawidłowa głębokość w ścieżce: \"$1\" (próba dostępu do węzła powyżej korzenia)',
	'pfunc_expr_stack_exhausted' => 'Błąd w wyrażeniu: Stos wyczerpany',
	'pfunc_expr_unexpected_number' => 'Błąd w wyrażeniu: Niespodziewana liczba',
	'pfunc_expr_preg_match_failure' => 'Błąd w wyrażeniu: Niespodziewany błąd w preg_match',
	'pfunc_expr_unrecognised_word' => 'Błąd w wyrażeniu: Nierozpoznane słowo "$1"',
	'pfunc_expr_unexpected_operator' => 'Błąd w wyrażeniu: Nieoczekiwany operator $1',
	'pfunc_expr_missing_operand' => 'Błąd w wyrażeniu: Brakujący operand dla $1',
	'pfunc_expr_unexpected_closing_bracket' => 'Błąd w wyrażeniu: Nieoczekiwany nawias zamykający',
	'pfunc_expr_unrecognised_punctuation' => 'Błąd w wyrażeniu: Nierozpoznany znak interpunkcyjny "$1"',
	'pfunc_expr_unclosed_bracket' => 'Błąd w wyrażeniu: Niedomknięty nawias',
	'pfunc_expr_division_by_zero' => 'Dzielenie przez zero',
	'pfunc_expr_unknown_error' => 'Błąd w wyrażeniu: Nieznany błąd ($1)',
	'pfunc_expr_not_a_number' => 'W $1: wynik nie jest liczbą',
),

'pms' => array(
	'pfunc_time_error' => 'Eror: temp nen bon',
	'pfunc_time_too_long' => 'Eror: #time a ven ciamà tròpe vire',
	'pfunc_rel2abs_invalid_depth' => 'Eror: profondità nen bon-a ant ël përcors: \"$1\" (a l\'é provasse a ciamé un grop dzora a la rèis)',
),

'sk' => array(
	'pfunc_time_error' => 'Chyba: Neplatný čas',
	'pfunc_time_too_long' => 'Chyba: príliš veľa volaní #time',
	'pfunc_rel2abs_invalid_depth' => 'Chyba: Neplatná hĺbka v ceste: „$1“ (pokus o prístup k uzlu nad koreňovým uzlom)',
),

/* Swedish */
'sv' => array(
	 'pfunc_time_error'             => 'Fel: ogiltig tid',
	 'pfunc_time_too_long'          => 'Fel: för många anrop av #time',
	 'pfunc_rel2abs_invalid_depth'  => 'Fel: felaktig djup i sökväg: "$1" (försöker nå en nod ovanför rotnoden)',
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
