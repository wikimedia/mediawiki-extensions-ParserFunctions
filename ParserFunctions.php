<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfSetupParserFunctions';

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
}

function wfSetupParserFunctions() {
	global $wgParser, $wgMessageCache, $wgExtParserFunctions;

	$wgExtParserFunctions = new ExtParserFunctions;

	$wgParser->setFunctionHook( 'expr', array( &$wgExtParserFunctions, 'expr' ) );
	$wgParser->setFunctionHook( 'if', array( &$wgExtParserFunctions, 'ifHook' ) );
	$wgParser->setFunctionHook( 'ifeq', array( &$wgExtParserFunctions, 'ifeq' ) );
	$wgParser->setFunctionHook( 'ifexpr', array( &$wgExtParserFunctions, 'ifexpr' ) );
	$wgParser->setFunctionHook( 'switch', array( &$wgExtParserFunctions, 'switchHook' ) );
}

?>
