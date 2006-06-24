<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfSetupParserFunctions';
$wgExtensionCredits['parserhook'][] = array( 'name' => 'ParserFunctions', 'url' => 'http://meta.wikimedia.org/wiki/ParserFunctions', 'author' => 'Tim Starling' );

$wgHooks['MagicWordMagicWords'][]    = 'wfParserFunctionsMagicWordsArray';
$wgHooks['MagicWordwgVariableIDs'][] = 'wfParserFunctionsMagicWordsIDs';
$wgHooks['LanguageGetMagic'][]       = 'wfParserFunctionsLanguageGetMagic';

class ExtParserFunctions {
	var $mExprParser;

	function &getExprParser() {
		if ( !isset( $this->mExpr ) ) {
			if ( !class_exists( 'ExprParser' ) ) {
				require( dirname( __FILE__ ) . '/Expr.php' );
				ExprParser::addMessages();
			}
			$this->mExprParser = new ExprParser;
		}
		return $this->mExprParser;
	}

	function expr( &$parser, $expr = '' ) {
		$exprParser =& $this->getExprParser();
		$result = $exprParser->doExpression( $expr );
		if ( $result === false ) {
			return $exprParser->lastErrorMessage;
		} else {
			return $result;
		}
	}

	function ifexpr( &$parser, $expr = '', $then = '', $else = '' ) {
		$exprParser =& $this->getExprParser();	
		$result = $exprParser->doExpression( $expr );
		if ( $result === false ) {
			return $exprParser->lastErrorMessage;
		} elseif ( $result ) {
			return $then;
		} else {
			return $else;
		}
	}

	function ifHook( &$parser, $test = '', $then = '', $else = '' ) {
		if ( $test !== '' ) {
			return $then;
		} else {
			return $else;
		}
	}

	function ifeq( &$parser, $left = '', $right = '', $then = '', $else = '' ) {
		if ( $left == $right ) {
			return $then;
		} else {
			return $else;
		}
	}
	
	function switchHook( &$parser /*,...*/ ) {
		$args = func_get_args();
		array_shift( $args );
		$value = trim(array_shift($args));
		$found = false;
		$parts = null;
		$default = null;
		foreach( $args as $arg ) {
			$parts = array_map( 'trim', explode( '=', $arg, 2 ) );
			if ( count( $parts ) == 2 ) {
				if ( $found || $parts[0] == $value ) {
					return $parts[1];
				} elseif ( $parts[0] == '#default' ) {
					$default = $parts[1];
				} # else wrong case, continue
			} elseif ( count( $parts ) == 1 ) {
				# Multiple input, single output
				# If the value matches, set a flag and continue
				if ( $parts[0] == $value ) {
					$found = true;
				}
			} # else RAM corruption due to cosmic ray?
		}
		# Default case
		# Check if the last item had no = sign, thus specifying the default case
		if ( count( $parts ) == 1) {
			return $parts[0];
		} elseif ( !is_null( $default ) ) {
			return $default;
		} else {
			return '';
		}
	}
	
	function ifexist( &$parser, $title = '', $then = '', $else = '' ) {
		$title = Title::newFromText( $title );
		return is_object( $title ) && $title->exists() ? $then : $else;
	}
}

function wfSetupParserFunctions() {
	global $wgParser, $wgMessageCache, $wgExtParserFunctions;

	$wgExtParserFunctions = new ExtParserFunctions;

	$wgParser->setFunctionHook( MAG_EXPR, array( &$wgExtParserFunctions, 'expr' ) );
	$wgParser->setFunctionHook( MAG_IF, array( &$wgExtParserFunctions, 'ifHook' ) );
	$wgParser->setFunctionHook( MAG_IFEQ, array( &$wgExtParserFunctions, 'ifeq' ) );
	$wgParser->setFunctionHook( MAG_IFEXPR, array( &$wgExtParserFunctions, 'ifexpr' ) );
	$wgParser->setFunctionHook( MAG_SWITCH, array( &$wgExtParserFunctions, 'switchHook' ) );
	$wgParser->setFunctionHook( MAG_IFEXIST, array( &$wgExtParserFunctions, 'ifexist' ) );	
}

function wfParserFunctionsMagicWordsArray( &$magicWords ) {
	$magicWords[] = 'MAG_EXPR';
	$magicWords[] = 'MAG_IF';
	$magicWords[] = 'MAG_IFEQ';
	$magicWords[] = 'MAG_IFEXPR';
	$magicWords[] = 'MAG_SWITCH';
	$magicWords[] = 'MAG_IFEXIST';
	return true;
}

function wfParserFunctionsMagicWordsIDs( &$magicWords ) {
	$magicWords[] = MAG_EXPR;
	$magicWords[] = MAG_IF;
	$magicWords[] = MAG_IFEQ;
	$magicWords[] = MAG_IFEXPR;
	$magicWords[] = MAG_SWITCH;
	$magicWords[] = MAG_IFEXIST;
	return true;
}

function wfParserFunctionsLanguageGetMagic( &$magicWords ) {
	$magicWords[MAG_EXPR]    = array( 0, 'expr' /* en */, 'ביטוי' /* he */);
	$magicWords[MAG_IF]      = array( 0, 'if' /* en */, 'תנאי' /* he */);
	$magicWords[MAG_IFEQ]    = array( 0, 'ifeq' /* en */, 'שיוויון' /* he */);
	$magicWords[MAG_IFEXPR]  = array( 0, 'ifexpr' /* en */, 'תנאי ביטוי' /* he */);
	$magicWords[MAG_SWITCH]  = array( 0, 'switch' /* en */, 'בחר' /* he */);
	$magicWords[MAG_IFEXIST] = array( 0, 'ifexist' /* en */, 'קיים' /* he */);
	return true;
}

?>
