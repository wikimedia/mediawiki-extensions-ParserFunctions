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
		$test = trim( $test );
		if ( preg_match( '/
				(.*?)      # Before the operator (non-greedy, get the first operator not the last)
				(?<!\n)    # Not at the start of the line, because it looks like a heading
				(!=|==)    # The operator
				(?!\n|$)   # Not at the end of the line either, or the end of the string
				(.*)$      # Everything else
				/x', $test, $matches ) ) 
		{
			$left = $matches[1];
			$operator = $matches[2];
			$right = $matches[3];
			if ( $operator == '==' ) {
				$test = $left == $right;
			} else {
				$test = $left != $right;
			}
		}
		if ( $test ) {
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
	
	$wgMessageCache->addMessage( 'expr_parse_error', 'Invalid expression' );
}

?>
