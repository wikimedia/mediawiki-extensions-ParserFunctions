<?php

$magicWords = array();

/**
 * English
 */
$magicWords['en'] = array(
	'expr'       => array( 0, 'expr' ),
	'if'         => array( 0, 'if' ),
	'ifeq'       => array( 0, 'ifeq' ),
	'ifexpr'     => array( 0, 'ifexpr' ),
	'iferror'    => array( 0, 'iferror' ),
	'switch'     => array( 0, 'switch' ),
	'default'    => array( 0, '#default' ),
	'ifexist'    => array( 0, 'ifexist' ),
	'time'       => array( 0, 'time' ),
	'timel'      => array( 0, 'timel' ),
	'rel2abs'    => array( 0, 'rel2abs' ),
	'titleparts' => array( 0, 'titleparts' ),
	'len'        => array( 0, 'len' ),
	'pos'        => array( 0, 'pos' ),
	'rpos'       => array( 0, 'rpos' ),
	'sub'        => array( 0, 'sub' ),
	'count'      => array( 0, 'count' ),
	'replace'    => array( 0, 'replace' ),
	'explode'    => array( 0, 'explode' ),
);

$magicWords['ar'] = array(
	'expr'         => array( '0', 'تعبير', 'expr' ),
	'if'           => array( '0', 'لو', 'if' ),
	'iferror'      => array( '0', 'لوخطأ', 'iferror' ),
	'default'      => array( '0', '#افتراضي', '#default' ),
	'ifexist'      => array( '0', 'لوموجود', 'ifexist' ),
	'time'         => array( '0', 'وقت', 'time' ),
	'count'        => array( '0', 'عدد', 'count' ),
	'replace'      => array( '0', 'استبدال', 'replace' ),
	'explode'      => array( '0', 'انفجار', 'explode' ),
);

$magicWords['cs'] = array(
	'expr'         => array( '0', 'výraz', 'expr' ),
	'if'           => array( '0', 'když', 'if' ),
	'ifexist'      => array( '0', 'kdyžexist', 'ifexist' ),
	'time'         => array( '0', 'čas', 'time' ),
	'len'          => array( '0', 'délka', 'len' ),
	'count'        => array( '0', 'počet', 'count' ),
	'replace'      => array( '0', 'nahradit', 'replace' ),
);

$magicWords['fa'] = array(
	'expr'         => array( '0', 'حساب', 'expr' ),
	'if'           => array( '0', 'اگر', 'if' ),
	'ifeq'         => array( '0', 'اگرمساوی', 'ifeq' ),
	'ifexpr'       => array( '0', 'اگرحساب', 'ifexpr' ),
	'iferror'      => array( '0', 'اگرخطا', 'iferror' ),
	'switch'       => array( '0', 'گزینه', 'switch' ),
	'default'      => array( '0', '#پیش‌فرض', '#default' ),
	'ifexist'      => array( '0', 'اگرموجود', 'ifexist' ),
	'time'         => array( '0', 'زمان', 'time' ),
	'timel'        => array( '0', 'زمان‌بلند', 'timel' ),
	'rel2abs'      => array( '0', 'نسبی‌به‌مطلق', 'rel2abs' ),
	'titleparts'   => array( '0', 'پاره‌عنوان', 'titleparts' ),
);

$magicWords['he'] = array(
	'expr'         => array( '0', 'חשב', 'expr' ),
	'if'           => array( '0', 'תנאי', 'if' ),
	'ifeq'         => array( '0', 'שווה', 'ifeq' ),
	'ifexpr'       => array( '0', 'חשב תנאי', 'ifexpr' ),
	'iferror'      => array( '0', 'תנאי שגיאה', 'iferror' ),
	'switch'       => array( '0', 'בחר', 'switch' ),
	'default'      => array( '0', '#ברירת מחדל', '#default' ),
	'ifexist'      => array( '0', 'קיים', 'ifexist' ),
	'time'         => array( '0', 'זמן', 'time' ),
	'timel'        => array( '0', 'זמןמ', 'timel' ),
	'rel2abs'      => array( '0', 'יחסי למוחלט', 'rel2abs' ),
	'titleparts'   => array( '0', 'חלק בכותרת', 'titleparts' ),
);

$magicWords['hu'] = array(
	'expr'         => array( '0', 'kif', 'expr' ),
	'if'           => array( '0', 'ha', 'if' ),
	'ifeq'         => array( '0', 'haegyenlő', 'ifeq' ),
	'ifexpr'       => array( '0', 'hakif', 'ifexpr' ),
	'iferror'      => array( '0', 'hahibás', 'iferror' ),
	'default'      => array( '0', '#alapértelmezett', '#default' ),
	'ifexist'      => array( '0', 'halétezik', 'ifexist' ),
	'time'         => array( '0', 'idő', 'time' ),
	'len'          => array( '0', 'hossz', 'len' ),
	'pos'          => array( '0', 'pozíció', 'pos' ),
	'rpos'         => array( '0', 'jpozíció', 'rpos' ),
);

$magicWords['id'] = array(
	'expr'         => array( '0', 'hitung', 'expr' ),
	'if'           => array( '0', 'jika', 'if' ),
	'ifeq'         => array( '0', 'jikasama', 'ifeq' ),
	'ifexpr'       => array( '0', 'jikahitung', 'ifexpr' ),
	'iferror'      => array( '0', 'jikasalah', 'iferror' ),
	'switch'       => array( '0', 'pilih', 'switch' ),
	'default'      => array( '0', '#baku', '#default' ),
	'ifexist'      => array( '0', 'jikaada', 'ifexist' ),
	'time'         => array( '0', 'waktu', 'time' ),
	'timel'        => array( '0', 'waktu1', 'timel' ),
	'titleparts'   => array( '0', 'bagianjudul', 'titleparts' ),
);

$magicWords['nl'] = array(
	'expr'         => array( '0', 'expressie', 'expr' ),
	'if'           => array( '0', 'als', 'if' ),
	'ifeq'         => array( '0', 'alsgelijk', 'ifeq' ),
	'ifexpr'       => array( '0', 'alsexpressie', 'ifexpr' ),
	'iferror'      => array( '0', 'alsfout', 'iferror' ),
	'switch'       => array( '0', 'schakelen', 'switch' ),
	'default'      => array( '0', '#standaard', '#default' ),
	'ifexist'      => array( '0', 'alsbestaat', 'ifexist' ),
	'time'         => array( '0', 'tijd', 'time' ),
	'timel'        => array( '0', 'tijdl', 'timel' ),
	'rel2abs'      => array( '0', 'relatiefnaarabsoluut', 'rel2abs' ),
	'titleparts'   => array( '0', 'paginanaamdelen', 'titleparts' ),
	'count'        => array( '0', 'telling', 'count' ),
	'replace'      => array( '0', 'vervangen', 'replace' ),
	'explode'      => array( '0', 'exploderen', 'explode' ),
);

$magicWords['ru'] = array(
	'replace'      => array( '0', 'замена', 'replace' ),
);

$magicWords['yi'] = array(
	'expr'         => array( '0', 'רעכן', 'חשב', 'expr' ),
	'if'           => array( '0', 'תנאי', 'if' ),
	'ifeq'         => array( '0', 'גלייך', 'שווה', 'ifeq' ),
	'ifexpr'       => array( '0', 'אויברעכן', 'חשב תנאי', 'ifexpr' ),
	'switch'       => array( '0', 'קלייב', 'בחר', 'switch' ),
	'default'      => array( '0', '#גרונט', '#ברירת מחדל', '#default' ),
	'ifexist'      => array( '0', 'עקזיסט', 'קיים', 'ifexist' ),
	'time'         => array( '0', 'צייט', 'זמן', 'time' ),
	'timel'        => array( '0', 'צייטל', 'זמןמ', 'timel' ),
);