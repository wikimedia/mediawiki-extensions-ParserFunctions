<?php

namespace MediaWiki\Extension\ParserFunctions;

use MediaWiki\Cache\LinkCache;
use MediaWiki\Config\Config;
use MediaWiki\FileRepo\RepoGroup;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Languages\LanguageConverterFactory;
use MediaWiki\Languages\LanguageFactory;
use MediaWiki\Languages\LanguageNameUtils;
use MediaWiki\Parser\Parser;
use MediaWiki\SpecialPage\SpecialPageFactory;

class Hooks implements
	\MediaWiki\Hook\ParserFirstCallInitHook,
	\MediaWiki\Hook\ParserTestGlobalsHook
{
	private Config $config;
	private ParserFunctions $parserFunctions;

	public function __construct(
		Config $config,
		HookContainer $hookContainer,
		LanguageConverterFactory $languageConverterFactory,
		LanguageFactory $languageFactory,
		LanguageNameUtils $languageNameUtils,
		LinkCache $linkCache,
		RepoGroup $repoGroup,
		SpecialPageFactory $specialPageFactory
	) {
		$this->config = $config;
		$this->parserFunctions = new ParserFunctions(
			$config,
			$hookContainer,
			$languageConverterFactory,
			$languageFactory,
			$languageNameUtils,
			$linkCache,
			$repoGroup,
			$specialPageFactory
		);
	}

	/**
	 * Enables string functions during parser tests.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserTestGlobals
	 *
	 * @param array &$globals
	 */
	public function onParserTestGlobals( &$globals ) {
		$globals['wgPFEnableStringFunctions'] = true;
	}

	/**
	 * Registers our parser functions with a fresh parser.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 *
	 * @param Parser $parser
	 */
	public function onParserFirstCallInit( $parser ) {
		// These functions accept DOM-style arguments
		$parser->setFunctionHook( 'if', [ $this->parserFunctions, 'if' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'ifeq', [ $this->parserFunctions, 'ifeq' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'switch', [ $this->parserFunctions, 'switch' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'ifexist', [ $this->parserFunctions, 'ifexist' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'ifexpr', [ $this->parserFunctions, 'ifexpr' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'iferror', [ $this->parserFunctions, 'iferror' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'time', [ $this->parserFunctions, 'time' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'timel', [ $this->parserFunctions, 'localTime' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'timef', [ $this->parserFunctions, 'timef' ], Parser::SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'timefl', [ $this->parserFunctions, 'timefl' ], Parser::SFH_OBJECT_ARGS );

		$parser->setFunctionHook( 'expr', [ $this->parserFunctions, 'expr' ] );
		$parser->setFunctionHook( 'rel2abs', [ $this->parserFunctions, 'rel2abs' ] );
		$parser->setFunctionHook( 'titleparts', [ $this->parserFunctions, 'titleparts' ] );

		// String Functions: enable if configured
		if ( $this->config->get( 'PFEnableStringFunctions' ) ) {
			$parser->setFunctionHook( 'len', [ $this->parserFunctions, 'runLen' ] );
			$parser->setFunctionHook( 'pos', [ $this->parserFunctions, 'runPos' ] );
			$parser->setFunctionHook( 'rpos', [ $this->parserFunctions, 'runRPos' ] );
			$parser->setFunctionHook( 'sub', [ $this->parserFunctions, 'runSub' ] );
			$parser->setFunctionHook( 'count', [ $this->parserFunctions, 'runCount' ] );
			$parser->setFunctionHook( 'replace', [ $this->parserFunctions, 'runReplace' ] );
			$parser->setFunctionHook( 'explode', [ $this->parserFunctions, 'runExplode' ] );
			$parser->setFunctionHook( 'urldecode', [ $this->parserFunctions, 'runUrlDecode' ] );
		}
	}
}
