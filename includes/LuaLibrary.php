<?php

namespace MediaWiki\Extension\ParserFunctions;

use MediaWiki\Extension\Scribunto\Engines\LuaCommon\LibraryBase;
use MediaWiki\Extension\Scribunto\Engines\LuaCommon\LuaError;

class LuaLibrary extends LibraryBase {
	/** @inheritDoc */
	public function register() {
		$lib = [
			'expr' => [ $this, 'expr' ],
		];

		return $this->getEngine()->registerInterface(
			__DIR__ . '/mw.ext.ParserFunctions.lua', $lib, []
		);
	}

	/**
	 * Forward the expression to the php expr parser
	 *
	 * @param string|null $expression
	 * @return string[]
	 * @throws LuaError
	 */
	public function expr( $expression = null ) {
		$this->checkType( 'mw.ext.ParserFunctions.expr', 1, $expression, 'string' );
		try {
			$exprParser = new ExprParser();
			return [ $exprParser->doExpression( $expression ) ];
		} catch ( ExprError $e ) {
			throw new LuaError( $e->getMessage() );
		}
	}

}
