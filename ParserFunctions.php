<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/**
 * CONFIGURATION
 * These variables may be overridden in LocalSettings.php after you include the
 * extension file.
 */

/**
 * Defines the maximum length of a string that string functions are allowed to operate on
 * Prevention against denial of service by string function abuses.
 */
$wgPFStringLengthLimit = 1000;

/**
 * Enable string functions.
 *
 * Set this to true if you want your users to be able to implement their own
 * parsers in the ugliest, most inefficient programming language known to man:
 * MediaWiki wikitext with ParserFunctions.
 *
 * WARNING: enabling this may have an adverse impact on the sanity of your users.
 * An alternative, saner solution for embedding complex text processing in
 * MediaWiki templates can be found at: http://www.mediawiki.org/wiki/Extension:Scribunto
 */
$wgPFEnableStringFunctions = false;

/**
  * Enable string functions, when running Wikimedia Jenkins unit tests.
  *
  * Running Jenkins unit tests without setting $wgPFEnableStringFunctions = true;
  * will cause all the parser tests for string functions to be skipped.
  */
if ( isset( $wgWikimediaJenkinsCI ) && $wgWikimediaJenkinsCI === true ) {
	$wgPFEnableStringFunctions = true;
}

/** REGISTRATION */
$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'ParserFunctions',
	'version' => '1.6.0',
	'url' => 'https://www.mediawiki.org/wiki/Extension:ParserFunctions',
	'author' => array( 'Tim Starling', 'Robert Rohde', 'Ross McClure', 'Juraj Simlovic' ),
	'descriptionmsg' => 'pfunc_desc',
);

$wgAutoloadClasses['ExtParserFunctions'] = __DIR__ . '/ParserFunctions_body.php';
$wgAutoloadClasses['ExprParser'] = __DIR__ . '/Expr.php';
$wgAutoloadClasses['ExprError'] = __DIR__ . '/Expr.php';
$wgAutoloadClasses['Scribunto_LuaParserFunctionsLibrary'] = __DIR__ . '/ParserFunctions.library.php';
$wgAutoloadClasses['ParserFunctionsHooks'] = __DIR__ . '/ParserFunctions.hooks.php';

$wgMessagesDirs['ParserFunctions'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['ParserFunctionsMagic'] = __DIR__ . '/ParserFunctions.i18n.magic.php';

$wgParserTestFiles[] = __DIR__ . "/funcsParserTests.txt";
$wgParserTestFiles[] = __DIR__ . "/stringFunctionTests.txt";

$wgHooks['ParserFirstCallInit'][] = 'ParserFunctionsHooks::onParserFirstCallInit';
$wgHooks['UnitTestsList'][] = 'ParserFunctionsHooks::onUnitTestsList';
$wgHooks['ScribuntoExternalLibraries'][] = 'ParserFunctionsHooks::onScribuntoExternalLibraries';
