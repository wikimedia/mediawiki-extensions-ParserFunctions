<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfSetupParserFunctions';

class ExtParserFunctions {
	var $mExprParser;

	function expr( &$parser, $expr = '' ) {
		if ( !isset( $this->mExpr ) ) {
			if ( !class_exists( 'ExprParser' ) ) {
				require( dirname( __FILE__ ) . '/Expr.php' );
				ExprParser::addMessages();
			}
			$this->mExprParser = new ExprParser;
		}
		$result = $this->mExprParser->doExpression( $expr );
		if ( $result === false ) {
			return $this->mExprParser->lastErrorMessage;
		} else {
			return $result;
		}
	}

	function ifHook( &$parser, $test = '', $then = '', $else = '' ) {
		if ( trim( $test ) ) {
			return trim( $then );
		} else {
			return trim( $else );
		}
	}

	function ifeq( &$parser, $left = '', $right = '', $then = '', $else = '' ) {
		if ( trim( $left ) == trim( $right ) ) {
			return trim( $then );
		} else {
			return trim( $else );
		}
	}

	function rand( &$parser, $min = 1, $max = 100 ) {
		return mt_rand( intval( $min ), intval( $max ) );
	}
}

function wfSetupParserFunctions() {
	global $wgParser, $wgMessageCache, $wgExtParserFunctions;

	$wgExtParserFunctions = new ExtParserFunctions;

	$wgParser->setFunctionHook( 'expr', array( &$wgExtParserFunctions, 'expr' ) );
	$wgParser->setFunctionHook( 'if', array( &$wgExtParserFunctions, 'ifHook' ) );
	$wgParser->setFunctionHook( 'ifeq', array( &$wgExtParserFunctions, 'ifeq' ) );
	$wgParser->setFunctionHook( 'rand', array( &$wgExtParserFunctions, 'rand' ) );
}

?>
