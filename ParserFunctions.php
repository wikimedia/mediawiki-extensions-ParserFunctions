<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfSetupParserFunctions';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'ParserFunctions',
	'url' => 'http://meta.wikimedia.org/wiki/ParserFunctions',
	'author' => 'Tim Starling',
	'description' => 'Enhance parser with logical functions',
);

$wgHooks['LanguageGetMagic'][]       = 'wfParserFunctionsLanguageGetMagic';

class ExtParserFunctions {
	var $mExprParser;
	var $mTimeCache = array();
	var $mTimeChars = 0;
	var $mMaxTimeChars = 6000; # ~10 seconds

	function clearState() {
		$this->mTimeChars = 0;
		return true;
	}

	function &getExprParser() {
		if ( !isset( $this->mExpr ) ) {
			if ( !class_exists( 'ExprParser' ) ) {
				require( dirname( __FILE__ ) . '/Expr.php' );
			}
			$this->mExprParser = new ExprParser;
		}
		return $this->mExprParser;
	}

	function expr( &$parser, $expr = '' ) {
		try {
			return $this->getExprParser()->doExpression( $expr );
		} catch(ExprError $e) {
			return $e->getMessage();
		}
	}

	function ifexpr( &$parser, $expr = '', $then = '', $else = '' ) {
		try{
			if($this->getExprParser()->doExpression( $expr )) {
				return $then;
			} else {
				return $else;
			}
		} catch (ExprError $e){
			return $e->getMessage();
		}
	}

	function ifHook( &$parser, $test = '', $then = '', $else = '' ) {
		if ( $test !== '' ) {
			return $then;
		} else {
			return $else;
		}
	}

	function ifObj( &$parser, $frame, $args ) {
		$test = isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		if ( $test !== '' ) {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		} else {
			return isset( $args[2] ) ? trim( $frame->expand( $args[2] ) ) : '';
		}
	}

	function ifeq( &$parser, $left = '', $right = '', $then = '', $else = '' ) {
		if ( $left == $right ) {
			return $then;
		} else {
			return $else;
		}
	}

	function ifeqObj( &$parser, $frame, $args ) {
		$left = isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		$right = isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		if ( $left == $right ) {
			return isset( $args[2] ) ? trim( $frame->expand( $args[2] ) ) : '';
		} else {
			return isset( $args[3] ) ? trim( $frame->expand( $args[3] ) ) : '';
		}
	}

	function switchHook( &$parser /*,...*/ ) {
		$args = func_get_args();
		array_shift( $args );
		$value = trim(array_shift($args));
		$found = false;
		$parts = null;
		$default = null;
		$mwDefault =& MagicWord::get( 'default' );
		foreach( $args as $arg ) {
			$parts = array_map( 'trim', explode( '=', $arg, 2 ) );
			if ( count( $parts ) == 2 ) {
				if ( $found || $parts[0] == $value ) {
					return $parts[1];
				} else {
					if ( $mwDefault->matchStartAndRemove( $parts[0] ) ) {
						$default = $parts[1];
					} # else wrong case, continue
				}
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

	/**
	 * Returns the absolute path to a subpage, relative to the current article
	 * title. Treats titles as slash-separated paths.
	 *
	 * Following subpage link syntax instead of standard path syntax, an 
	 * initial slash is treated as a relative path, and vice versa.
	 */
	public function rel2abs( &$parser , $to = '' , $from = '' ) {

		$from = trim($from);
		if( $from == '' ) {
			$from = $parser->getTitle()->getPrefixedText();
		}

		$to = rtrim( $to , ' /' );

		// if we have an empty path, or just one containing a dot
		if( $to == '' || $to == '.' ) {
			return $from;
		}

		// if the path isn't relative
		if ( substr( $to , 0 , 1) != '/' &&
		 substr( $to , 0 , 2) != './' &&
		 substr( $to , 0 , 3) != '../' &&
		 $to != '..' )
		{
			$from = '';
		}
		// Make a long path, containing both, enclose it in /.../
		$fullPath = '/' . $from . '/' .  $to . '/';

		// remove redundant current path dots
		$fullPath = preg_replace( '!/(\./)+!', '/', $fullPath );

		// remove double slashes
		$fullPath = preg_replace( '!/{2,}!', '/', $fullPath );

		// remove the enclosing slashes now
		$fullPath = trim( $fullPath , '/' );
		$exploded = explode ( '/' , $fullPath );
		$newExploded = array();

		foreach ( $exploded as $current ) {
			if( $current == '..' ) { // removing one level
				if( !count( $newExploded ) ){
					// attempted to access a node above root node
					return '<strong class="error">' . wfMsgForContent( 'pfunc_rel2abs_invalid_depth', $fullPath ) . '</strong>';
				}
				// remove last level from the stack
				array_pop( $newExploded );
			} else {
				// add the current level to the stack
				$newExploded[] = $current;
			}
		}

		// we can now join it again
		return implode( '/' , $newExploded );
	}

	function ifexist( &$parser, $title = '', $then = '', $else = '' ) {
		$title = Title::newFromText( $title );
		if ( $title ) {
			/* If namespace is specified as NS_MEDIA, then we want to check the physical file,
			 * not the "description" page.
			 */
			if( $title->getNamespace() == NS_MEDIA ) {
				$file = wfFindFile($title);
				if(!$file)
						return $else;
				$parser->mOutput->addImage($file->getName());
				return $file->exists() ? $then : $else;

			}
			$id = $title->getArticleID();
			$parser->mOutput->addLink( $title, $id );
			if ( $id ) {
				return $then;
			}
		}
		return $else;
	}

	function time( &$parser, $format = '', $date = '', $local = false ) {
		global $wgContLang, $wgLocaltimezone;
		if ( isset( $this->mTimeCache[$format][$date][$local] ) ) {
			return $this->mTimeCache[$format][$date][$local];
		}

		if ( $date !== '' ) {
			$unix = @strtotime( $date );
		} else {
			$unix = time();
		}

		if ( $unix == -1 || $unix == false ) {
			$result = '<strong class="error">' . wfMsgForContent( 'pfunc_time_error' ) . '</strong>';
		} else {
			$this->mTimeChars += strlen( $format );
			if ( $this->mTimeChars > $this->mMaxTimeChars ) {
				return '<strong class="error">' . wfMsgForContent( 'pfunc_time_too_long' ) . '</strong>';
			} else {
				if ( $local ) {
					# Use the time zone
					if ( isset( $wgLocaltimezone ) ) {
						$oldtz = getenv( 'TZ' );
						putenv( 'TZ='.$wgLocaltimezone );
					}
					wfSuppressWarnings(); // E_STRICT system time bitching
					$ts = date( 'YmdHis', $unix );
					wfRestoreWarnings();
					if ( isset( $wgLocaltimezone ) ) {
						putenv( 'TZ='.$oldtz );
					}
				} else {
					$ts = wfTimestamp( TS_MW, $unix );
				}
				if ( method_exists( $wgContLang, 'sprintfDate' ) ) {
					$result = $wgContLang->sprintfDate( $format, $ts );
				} else {
					if ( !class_exists( 'SprintfDateCompat' ) ) {
						require( dirname( __FILE__ ) . '/SprintfDateCompat.php' );
					}

					$result = SprintfDateCompat::sprintfDate( $format, $ts );
				}
			}
		}
		$this->mTimeCache[$format][$date][$local] = $result;
		return $result;
	}
	
	function localTime( &$parser, $format = '', $date = '' ) {
		return $this->time( $parser, $format, $date, true );
	}
	
	/**
	 * Obtain a specified number of slash-separated parts of a title,
	 * e.g. {{#titleparts:Hello/World|1}} => "Hello"
	 *
	 * @param Parser $parser Parent parser
	 * @param string $title Title to split
	 * @param int $parts Number of parts to keep
	 * @param int $offset Offset starting at 1
	 * @return string
	 */
	public function titleparts( $parser, $title = '', $parts = -1, $offset = 1 ) {
		$parts = intval( $parts );
		$offset = intval( $offset ) - 1;
		$ntitle = Title::newFromText( $title );
		if( $ntitle instanceof Title ) {
			$bits = explode( '/', $ntitle->getPrefixedText() );
			if( $parts <= 0 || $parts > count( $bits ) ) {
				return $ntitle->getPrefixedText();
			} elseif( $offset < 0 || $offset > count( $bits ) ) {
				return $ntitle->getPrefixedText();
			} else {
				$keep = array();
				for( $i = 0; $i < $offset; $i++ )
					array_shift( $bits );
				for( $i = 0; $i < $parts; $i++ )
					$keep[] = array_shift( $bits );
				return implode( '/', $keep );			
			}
		} else {
			return $title;
		}
	} 
	
}

function wfSetupParserFunctions() {
	global $wgParser, $wgMessageCache, $wgExtParserFunctions, $wgMessageCache, $wgHooks;

	$wgExtParserFunctions = new ExtParserFunctions;

	// Check for SFH_OBJECT_ARGS capability
	if ( !StubObject::isRealObject( $wgParser ) ) {
		$wgParser->_unstub();
	}
	if ( defined( get_class( $wgParser ) . '::SFH_OBJECT_ARGS' ) ) {
		// These functions accept DOM-style arguments
		$wgParser->setFunctionHook( 'if', array( &$wgExtParserFunctions, 'ifObj' ), SFH_OBJECT_ARGS );
		$wgParser->setFunctionHook( 'ifeq', array( &$wgExtParserFunctions, 'ifeqObj' ), SFH_OBJECT_ARGS );
	} else {
		$wgParser->setFunctionHook( 'if', array( &$wgExtParserFunctions, 'ifHook' ) );
		$wgParser->setFunctionHook( 'ifeq', array( &$wgExtParserFunctions, 'ifeq' ) );
	}

	$wgParser->setFunctionHook( 'switch', array( &$wgExtParserFunctions, 'switchHook' ) );
	$wgParser->setFunctionHook( 'expr', array( &$wgExtParserFunctions, 'expr' ) );
	$wgParser->setFunctionHook( 'ifexpr', array( &$wgExtParserFunctions, 'ifexpr' ) );
	$wgParser->setFunctionHook( 'switch', array( &$wgExtParserFunctions, 'switchHook' ) );
	$wgParser->setFunctionHook( 'ifexist', array( &$wgExtParserFunctions, 'ifexist' ) );
	$wgParser->setFunctionHook( 'time', array( &$wgExtParserFunctions, 'time' ) );
	$wgParser->setFunctionHook( 'timel', array( &$wgExtParserFunctions, 'localTime' ) );
	$wgParser->setFunctionHook( 'rel2abs', array( &$wgExtParserFunctions, 'rel2abs' ) );
	$wgParser->setFunctionHook( 'titleparts', array( &$wgExtParserFunctions, 'titleparts' ) );
	
	require_once( dirname( __FILE__ ) . '/ParserFunctions.i18n.php' );
	foreach( efParserFunctionsMessages() as $lang => $messages )
		$wgMessageCache->addMessages( $messages, $lang );

	$wgHooks['ParserClearState'][] = array( &$wgExtParserFunctions, 'clearState' );
}

function wfParserFunctionsLanguageGetMagic( &$magicWords, $langCode ) {
	require_once( dirname( __FILE__ ) . '/ParserFunctions.i18n.php' );
	foreach( efParserFunctionsWords( $langCode ) as $word => $trans )
		$magicWords[$word] = $trans;
	return true;
}
