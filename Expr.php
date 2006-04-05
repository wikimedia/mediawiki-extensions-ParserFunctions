<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

// Character classes
define( 'EXPR_WHITE_CLASS', " \t\r\n" );
define( 'EXPR_NUMBER_CLASS', '0123456789.' );

// Token types
define( 'EXPR_WHITE', 'WHITE' );
define( 'EXPR_NUMBER', 'NUMBER' );
define( 'EXPR_NEGATIVE', 'NEGATIVE' );
define( 'EXPR_POSITIVE', 'POSITIVE' );
define( 'EXPR_PLUS', 'PLUS' );
define( 'EXPR_MINUS', 'MINUS' );
define( 'EXPR_TIMES', 'TIMES' );
define( 'EXPR_DIVIDE', 'DIVIDE' );
define( 'EXPR_MOD', 'MOD' );
define( 'EXPR_OPEN', 'OPEN' );
define( 'EXPR_CLOSE', 'CLOSE' );
define( 'EXPR_AND', 'AND' );
define( 'EXPR_OR', 'OR' );
define( 'EXPR_NOT', 'NOT' );
define( 'EXPR_EQUALITY', 'EQUALITY' );
define( 'EXPR_ROUND', 'ROUND' );

class ExprParser {
	var $expr, $end;

	var $precedence = array( 
			EXPR_NEGATIVE => 9,
			EXPR_POSITIVE => 9,
			EXPR_TIMES => 8,
			EXPR_DIVIDE => 8,
			EXPR_MOD => 8,
			EXPR_PLUS => 6,
			EXPR_MINUS => 6,
			EXPR_ROUND => 5,
			EXPR_EQUALITY => 4,
			EXPR_AND => 3,
			EXPR_OR => 2,
			EXPR_OPEN => -1,
			EXPR_CLOSE => -1
		);

	var $words = array(
		'mod' => EXPR_MOD,
		'and' => EXPR_AND,
		'or' => EXPR_OR,
		'not' => EXPR_NOT,
		'round' => EXPR_ROUND,
	);

	function error( $msg ) {
		$this->lastError = $msg;
	}

	/**
	 * Evaluate a mathematical expression
	 *
	 * The algorithm here is based on the infix to RPN algorithm given in
	 * http://montcs.bloomu.edu/~bobmon/Information/RPN/infix2rpn.shtml
	 * It's essentially the same as Dijkstra's shunting yard algorithm.
	 */
	function doExpression( $expr ) {
		$operands = array();
		$operators = array();
		$p = 0;
		$end = strlen( $expr );
		$expecting = 'expression';

		while ( $p < $end ) {
			$char = $expr[$p];

			// Mega if-elseif-else construct
			// Only binary operators fall through for processing at the bottom, the rest 
			// finish their processing and continue
			
			if ( false !== strpos( EXPR_WHITE_CLASS, $char ) ) {
				// Whitespace
				$p += strspn( $expr, EXPR_WHITE_CLASS, $p );
				continue;
			} elseif ( false !== strpos( EXPR_NUMBER_CLASS, $char ) ) {
				// Number
				if ( $expecting != 'expression' ) {
					$this->error( 'Unexpected number' );
					return false;
				}

				// Find the rest of it
				$length = strspn( $expr, EXPR_NUMBER_CLASS, $p );
				// Convert it to float, silently removing double decimal points
				$operands[] = floatval( substr( $expr, $p, $length ) );
				$p += $length;
				$expecting = 'operator';
				continue;
			} elseif ( ctype_alpha( $char ) ) {
				// Word
				// Find the rest of it
				$remaining = substr( $expr, $p );
				if ( !preg_match( '/^[A-Za-z]*/', $remaining, $matches ) ) {
					// This should be unreachable
					$this->error( 'Unexpected preg_match failure' );
					return false;
				}
				$word = strtolower( $matches[0] );
				$p += strlen( $word );

				// Interpret the word
				if ( !isset( $this->words[$word] ) ){
					$this->error( 'Unrecognised word' );
					return false;
				}
				$op = $this->words[$word];
				if ( $op == EXPR_NOT ) {
					// Unary operator
					if ( $expecting != 'expression' ) { 
						$this->error( "Unexpected $op operator" );
						return false;
					}
					$operators[] = $op;
					continue;
				} else {
					// Binary operator
					if ( $expecting == 'expression' ) {
						$this->error( "Unexpected $op operator" );
						return false;
					}
				}
			} elseif ( $char == '+' ) {
				++$p;
				if ( $expecting == 'expression' ) {
					// Unary plus
					$operators[] = EXPR_POSITIVE;
					continue;
				} else {
					// Binary plus
					$op = EXPR_PLUS;
				}
			} elseif ( $char == '-' ) {
				++$p;
				if ( $expecting == 'expression' ) {
					// Unary minus
					$operators[] = EXPR_NEGATIVE;
					continue;
				} else {
					// Binary minus
					$op = EXPR_MINUS;
				}
			} elseif ( $char == '*' ) {
				if ( $expecting == 'expression' ) {
					$this->error( 'Unexpected * operator' );
					return false;
				}
				$op = EXPR_TIMES;
				++$p;
			} elseif ( $char == '/' ) {
				if ( $expecting == 'expression' ) {
					$this->error( 'Unexpected / operator' );
					return false;
				}
				$op = EXPR_DIVIDE;
				++$p;
			} elseif ( $char == '(' )  {
				if ( $expecting == 'operator' ) {
					$this->error( 'Unexpected opening bracket' );
					return false;
				} 
				$operators[] = EXPR_OPEN;
				++$p;
				continue;
			} elseif ( $char == ')' ) {
				$lastOp = end( $operators );
				while ( $lastOp && $lastOp != EXPR_OPEN ) {
					if ( !$this->doOperation( $lastOp, $operands ) ) {
						$this->error( "Missing operand for $lastOp" );
						return false;
					}
					array_pop( $operators );
					$lastOp = end( $operators );
				}
				if ( $lastOp ) {
					array_pop( $operators );
				} else {
					$this->error( "Unexpected closing bracket" );
					return false;
				}
				$expecting = 'operator';
				++$p;
				continue;
			} elseif ( $char = '=' ) {
				if ( $expecting == 'expression' ) {
					$this->error( 'Unexpected = operator' );
					return false;
				}
				$op = EXPR_EQUALITY;
				++$p;
			} else {
				$this->error( "Unrecognised punctuation character" );
				return false;
			}

			// Shunting yard magic for binary operators
			$lastOp = end( $operators );
			while ( $lastOp && $this->precedence[$op] <= $this->precedence[$lastOp] ) {
				if ( !$this->doOperation( $lastOp, $operands ) ) {
					$this->error( "Missing operand for $lastOp" );
					return false;
				}
				array_pop( $operators );
				$lastOp = end( $operators );
			}
			$operators[] = $op;
			$expecting = 'expression';
		}

		// Finish off the operator array
		while ( $op = array_pop( $operators ) ) {
			if ( $op == EXPR_OPEN ) {
				$this->error( "Unclosed bracket" );
				return false;
			}
			if ( !$this->doOperation( $op, $operands ) ) {
				$this->error( "Missing operand for $lastOp" );
				return false;
			}
		}
		
		return implode( "<br/>\n", $operands );
	}

	function doOperation( $op, &$stack ) {
		switch ( $op ) {
			case EXPR_NEGATIVE:
				if ( count( $stack ) < 1 ) return false;
				$arg = array_pop( $stack );
				$stack[] = -$arg;
				break;
			case EXPR_POSITIVE:
				if ( count( $stack ) < 1 ) return false;
				break;
			case EXPR_TIMES:
				if ( count( $stack ) < 2 ) return false;
				$right = array_pop( $stack );
				$left = array_pop( $stack );
				$stack[] = $left * $right;
				break;
			case EXPR_DIVIDE:
				if ( count( $stack ) < 2 ) return false;
				$right = array_pop( $stack );
				$left = array_pop( $stack );
				$stack[] = $left / $right;
				break;
			case EXPR_MOD:
				if ( count( $stack ) < 2 ) return false;
				$right = array_pop( $stack );
				$left = array_pop( $stack );
				$stack[] = $left % $right;
				break;
			case EXPR_PLUS:
				if ( count( $stack ) < 2 ) return false;
				$right = array_pop( $stack );
				$left = array_pop( $stack );
				$stack[] = $left + $right;
				break;
			case EXPR_MINUS:
				if ( count( $stack ) < 2 ) return false;
				$right = array_pop( $stack );
				$left = array_pop( $stack );
				$stack[] = $left - $right;
				break;
			case EXPR_AND:
				if ( count( $stack ) < 2 ) return false;
				$right = array_pop( $stack );
				$left = array_pop( $stack );
				$stack[] = ( $left && $right ) ? 1 : 0;
				break;
			case EXPR_OR:
				if ( count( $stack ) < 2 ) return false;
				$right = array_pop( $stack );
				$left = array_pop( $stack );
				$stack[] = ( $left || $right ) ? 1 : 0;
				break;
			case EXPR_EQUALITY:
				if ( count( $stack ) < 2 ) return false;
				$right = array_pop( $stack );
				$left = array_pop( $stack );
				$stack[] = ( $left == $right ) ? 1 : 0;
				break;
			case EXPR_NOT:
				if ( count( $stack ) < 1 ) return false;
				$arg = array_pop( $stack );
				$stack[] = (!$arg) ? 1 : 0;
				break;
			case EXPR_ROUND:
				if ( count( $stack ) < 2 ) return false;
				$digits = intval( array_pop( $stack ) );
				$value = array_pop( $stack );
				$stack[] = round( $value, $digits );
				break;
				
		}
		return true;
	}
}

?>
