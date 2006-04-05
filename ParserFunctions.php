<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfSetupParserFunctions';

class ExtParserFunctions {
	var $mExprParser;

	function exprHook( &$parser, $expr = '' ) {
		if ( !isset( $this->mExpr ) ) {
			if ( !class_exists( 'ExprParser' ) ) {
				require_once( dirname( __FILE__ ) . '/Expr.php' );
			}
			$this->mExprParser = new ExprParser;
		}
		$result = $this->mExprParser->doExpression( $expr );
		if ( $result === false ) {
			return wfMsg( 'expr_parse_error' );
		} else {
			return $result;
		}
	}

	function ifHook( &$parser, $test = '', $then = '', $else = '' ) {
		if ( trim( $test ) ) {
			return $then;
		} else {
			return $else;
		}
	}

	function ifeqHook( &$parser, $left = '', $right = '', $then = '', $else = '' ) {
		if ( trim( $left ) == trim( $right ) ) {
			return $then;
		} else {
			return $else;
		}
	}
}

function wfSetupParserFunctions() {
	global $wgParser, $wgMessageCache, $wgExtParserFunctions;

	$wgExtParserFunctions = new ExtParserFunctions;

	$wgParser->setFunctionHook( 'expr', array( &$wgExtParserFunctions, 'exprHook' ) );
	$wgParser->setFunctionHook( 'if', array( &$wgExtParserFunctions, 'ifHook' ) ) ;
	$wgParser->setFunctionHook( 'ifeq', array( &$wgExtParserFunctions, 'ifeqHook' ) ) ;
	
	$wgMessageCache->addMessage( 'expr_parse_error', 'Invalid expression' );
}

?>
