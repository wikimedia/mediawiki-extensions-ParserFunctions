<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

class ConvertError extends MWException {
	public function __construct( $msg /*...*/ ) {
		$args = func_get_args();
		array_shift( $args );
		array_map( 'htmlspecialchars', $args );
		$this->message = '<strong class="error">' . wfMsgForContent( "pfunc-convert-$msg", $args ) . '</strong>';
	}
}

class ConvertParser {

	# A regex which matches the body of the string and the source unit separately
	const UNITS_REGEX = '/^(.+?)\s*([a-z]+\^?\d?(?:\/\w+\^?\d?)*)$/i';

	# A regex which matches a number
	const NUM_REGEX = '/\b((?:\+|\-|&minus;|\x2212)?(\d+(?:\.\d+)?)(?:E(?:\+|\-|&minus;|\x2212)?\d+)?)\b/i';

	# ConvertUnit objects
	protected $sourceUnit;
	protected $targetUnit;

	# Whether to abbreviate the output unit
	protected $abbreviate;

	# Whether to link the output unit, if possible
	protected $link;

	# If set, don't output the unit or format the number
	protected $raw;

	# What precision to round to.
	protected $decimalPlaces;
	protected $significantFigures;

	# The last value converted, which will be used for PLURAL evaluation
	protected $lastValue;

	public function clearState(){
		# Make sure we break any references set up in the parameter passing below
		unset( $this->sourceUnit );
		unset( $this->targetUnit );
		$this->sourceUnit = null;
		$this->targetUnit = null;
		
		$this->lastValue
			= $this->link
			= $this->precision
			= $this->abbreviate
			= $this->raw
			= $this->significantFigures
			= $this->decimalPlaces
			= null;
	}

	/**
	 * Evaluate a convert expression
	 * @param $args Array of the parameters passed to the original tag function
	 * @return String
	 * @throws ConvertError
	 */
	public function execute( $args ) {
		$this->clearState();
		array_shift( $args ); # Dump Parser object

		if( count( $args ) == 0 ){
			# that was easy
			return '';
		}
		$string = trim( array_shift( $args ) );

		# Process the rest of the args
		static $magicWords = array(
			'sourceunit' => null,
			'targetunit' => null,
			'linkunit' => null,
			'decimalplaces' => null,
			'significantfigures' => null,
			'abbreviate' => null,
			'rawsuffix' => null,
		);
		if( !is_object( $magicWords ) ){
			foreach( $magicWords as $key => &$val ){
				$magicWords[$key] =& MagicWord::get( $key );
			}
			# The $magicWords[key]->function() syntax doesn't work, so cast to
			# object so we can use $magicWords->key->function() instead
			$magicWords = (object)$magicWords;
		}

		$n = 0; # Count of unnamed parameters
		foreach ( $args as $arg ) {
			$parts = array_map( 'trim', explode( '=', $arg, 2 ) );
			if ( count( $parts ) == 2 ) {
				# Found "="
				if ( $magicWords->sourceunit->matchStartAndRemove( $parts[0] ) ) {
					if( $magicWords->targetunit->matchStartAndRemove( $parts[1] ) ){
						$this->targetUnit =& $this->sourceUnit;
					} else {
						$this->sourceUnit = new ConvertUnit( $parts[1] );
					}

				} elseif ( $magicWords->targetunit->matchStartAndRemove( $parts[0] ) ) {
					if( $magicWords->sourceunit->matchStartAndRemove( $parts[1] ) ){
						$this->targetUnit =& $this->sourceUnit;
					} else {
						$this->targetUnit = new ConvertUnit( $parts[1] );
					}

				} elseif( $magicWords->decimalplaces->matchStartAndRemove( $parts[0] ) ) {
					$this->decimalPlaces = intval( $parts[1] );

				} elseif( $magicWords->significantfigures->matchStartAndRemove( $parts[0] ) ) {
					# It doesn't make any sense to have negative sig-figs
					if( intval( $parts[1] ) > 0 ){
						$this->significantFigures = intval( $parts[1] );
					}
				}

			} elseif( $magicWords->linkunit->matchStartAndRemove( $parts[0] ) ) {
				$this->link = true;

			} elseif( $magicWords->abbreviate->matchStartAndRemove( $parts[0] ) ) {
				$this->abbreviate = true;

			} elseif( $magicWords->rawsuffix->matchStartAndRemove( $parts[0] ) ) {
				$this->raw = true;

			} elseif( $parts[0] != '' && !$n++ && !$this->targetUnit instanceof ConvertUnit ){
				# First unnamed parameter = output unit
				$this->targetUnit = new ConvertUnit( $parts[0] );
			}
		}

		# Get the source unit, if not already set.  This throws ConvertError on failure
		if ( !$this->sourceUnit instanceof ConvertUnit ){
			$this->deduceSourceUnit( $string );
		}

		# Use the default unit (SI usually)
		if( !$this->targetUnit instanceof ConvertUnit ){
			$this->targetUnit = $this->sourceUnit->getDefaultUnit();
		}

		if( $this->targetUnit->dimension->value != $this->sourceUnit->dimension->value ){
			throw new ConvertError(
				'dimensionmismatch',
				$this->sourceUnit->dimension->getLocalisedName(true),
				$this->targetUnit->dimension->getLocalisedName(true)
			);
		}

		return $this->processString( $string );
	}

	protected function deduceSourceUnit( $string ){
		# Get the unit from the end of the string
		$matches = array();
		preg_match( self::UNITS_REGEX, $string, $matches );

		if( count( $matches ) == 3 ){
			$this->sourceUnit = new ConvertUnit( $matches[2] );
		} else {
			throw new ConvertError( 'nounit' );
		}
	}

	/**
	 * Identify the values to be converted, and convert them
	 * @param  $string String
	 */
	protected function processString( $string ){
		# Replace values
		$string = preg_replace_callback(
			self::NUM_REGEX,
			array( $this, 'convert' ),
			trim( preg_replace( self::UNITS_REGEX, '$1', $string ) )
		);
		if( $this->raw ){
			return $string;
		} else {
			$unit = $this->targetUnit->getText(
				$this->lastValue,
				$this->link,
				$this->abbreviate
			);
			return  "$string $unit";
		}
	}

	/**
	 * Express a value in the $sourceUnit in terms of the $targetUnit, preserving
	 * an appropriate degree of accuracy.
	 * @param  $value String
	 * @return void
	 */
	public function convert( $value ){
		global $wgContLang;
		$valueFloat = floatval( $value[1] );
		$newValue = $valueFloat
			* $this->sourceUnit->getConversion()
			/ $this->targetUnit->getConversion();
		if( $this->decimalPlaces !== null && $this->significantFigures !== null ){
			# round to the required number of decimal places, or the required number
			# of significant figures, whichever is the least precise
			$dp = floor( $this->significantFigures - log10( abs( $newValue ) ) ); # Convert SF to DP
			$newValue = round( $newValue, max( $dp, $this->decimalPlaces ) );

		} elseif( $this->decimalPlaces !== null ){
			$newValue = round( $newValue, $this->decimalPlaces );

		} elseif( $this->significantFigures !== null ){
			$dp = floor( $this->significantFigures - log10( abs( $newValue ) ) ); # Convert SF to DP
			$newValue = round( $newValue, $dp );

		} else {
			# Need to round to a similar accuracy as the original value.  To do that we
			# select the accuracy which will as closely as possible preserve the maximum
			# percentage error in the value.  So 36ft = 36 ± 0.5 ft, so the uncertainty
			# is ±0.5/36 = ±1.4%.  In metres this is 10.9728 ± 1.4%, or 10.9728 ± 0.154
			# we take the stance of choosing the limit which is *more* precise than the
			# original value.

			# Strip sign and exponent
			$num = preg_replace( self::NUM_REGEX, '$2', $value[1] );

			if( strpos( $num, '.' ) !== false ){
				# If there is a decimal point, this is the number of digits after it.
				$dpAfter = strlen( $num ) - strpos( $num, '.' ) - 1;
				$error = pow( 10, -$dpAfter - 1 ) * 5;

			} elseif( $num == 0 ) {
				# The logarithms below will be unhappy, and it doesn't actually matter
				# what error we come up with, zero is still zero
				$error = 1;

			} else {
				# Number of digits before the point
				$dpBefore = floor( log10( abs( $num ) ) );

				# Number of digits if we reverse the string = number
				# of digits excluding trailing zeros
				$dpAfter = floor( log10( abs( strrev( $num ) ) ) );

				# How many significant figures to consider numbers like "35000" to have
				# is a tricky question.  We say 2 here because if people want to ensure
				# that the zeros are included, they could write it as 3.500E4
				$error = pow( 10, $dpBefore - $dpAfter - 1 ) * 5;
			}

			$errorFraction = $error / $num;

			$i = 10;
			while( $i > -10 && ( round( $newValue, $i - 1 ) != 0 ) &&
				# Rounding to 10dp avoids floating point errors in exact conversions,
				# which are on the order of 1E-16
				( round( 5 * pow( 10, -$i ) / round( $newValue, $i - 1 ), 10 ) <= round( $errorFraction, 10 ) ) )
			{
				$i--;
			}

			$newValue = round( $newValue, $i );
			# We may need to stick significant zeros back onto the number
			if( $i > 0 ){
				if( strpos( $newValue, '.' ) !== false ){
					$newValue = str_pad( $newValue, $i + strpos( $newValue, '.' ) + 1, '0' );
				} else {
					$newValue .= '.' . str_repeat( '0', $i );
				}
			}
		}

		# Store the last value for use in PLURAL later
		$this->lastValue = $newValue;

		return $this->raw
			? $newValue
			: $wgContLang->formatNum( $newValue );
	}

}

/**
 * A dimension
 */
class ConvertDimension {

	const MASS = 1;          # KILOGRAM
	const LENGTH = 10;       # METRE
	const TIME = 100;        # SECOND
	const TEMPERATURE = 1E3; # KELVIN
	const QUANTITY = 1E4;    # MOLE
	const CURRENT = 1E5;     # AMPERE
	const INTENSITY = 1E6;   # CANDELA

	# fuel efficiencies are ugly and horrible and dimensionally confused, and have the
	# same dimensions as LENGTH or 1/LENGTH.  But someone wanted to include them... so
	# we have up to ten dimensions which can be identified by values of this.
	# 0 = sane unit
	# 1 = some sort of fuel efficiency
	const UGLY_HACK_VALUE = 1E7;

	/**
	 * Dimension constants.  These are the values you'd get if you added the SI
	 * base units together with the weighting given above, also the output from
	 * getDimensionHash().  Cool thing is, you can add these together to get new
	 * compound dimensions.
	 */
	const DIM_DIMENSIONLESS = 0; # Numbers etc
	const DIM_LENGTH        = 10;
	const DIM_AREA          = 20;
	const DIM_VOLUME        = 30;
	const DIM_TIME          = 100;
	const DIM_TIME_SQ       = 200;
	const DIM_MASS          = 1;
	const DIM_TEMPERATURE   = 1000;
	const DIM_SPEED         = -90;  # LENGTH / TIME
	const DIM_ACCELERATION  = -190; # LENGTH / TIME_SQ
	const DIM_FORCE         = -189; # MASS * LENGTH / TIME_SQ
	const DIM_TORQUE        = -179; # also MASS * AREA / TIME_SQ, but all units are single
	const DIM_ENERGY        = -179; # MASS * AREA / TIME_SQ, all units are compound
	const DIM_PRESSURE      = -209; # MASS / ( LENGTH * TIME_SQ )
	const DIM_POWER         = -79;  # MASS * AREA / TIME
	const DIM_DENSITY       = -29;  # MASS / VOLUME
	const DIM_FUELEFFICIENCY_PVE = 10000020; # fuel efficiency in VOLUME / LENGTH
	const DIM_FUELEFFICIENCY_NVE = 99999990; # fuel efficiency in LENGTH / VOLUME

	# Map of dimension names to message keys. This also serves as a list of what
	# dimensions will not throw an error when encountered.
	public static $legalDimensions = array(
		self::DIM_LENGTH => 'length',
		self::DIM_AREA => 'area',
		self::DIM_VOLUME => 'volume',
		self::DIM_TIME => 'time',
		self::DIM_TIME_SQ => 'timesquared',
		self::DIM_MASS => 'mass',
		self::DIM_TEMPERATURE => 'temperature',
		self::DIM_SPEED => 'speed',
		self::DIM_ACCELERATION => 'acceleration',
		self::DIM_FORCE => 'force',
		self::DIM_TORQUE => 'torque',
		self::DIM_ENERGY => 'energy',
		self::DIM_PRESSURE => 'pressure',
		self::DIM_POWER => 'power',
		self::DIM_DENSITY => 'density',
		self::DIM_FUELEFFICIENCY_PVE => 'fuelefficiencypositive',
		self::DIM_FUELEFFICIENCY_NVE => 'fuelefficiencynegative',
	);

	public $value;
	protected $name;

	public function __construct( $var, $var2=null ){
		static $legalDimensionsFlip;

		if( is_string( $var ) ){
			if( $legalDimensionsFlip === null ){
				$legalDimensionsFlip = array_flip( self::$legalDimensions );
			}
			if( isset( $legalDimensionsFlip[$var] ) ){
				$dim = $legalDimensionsFlip[$var];
			} else {
				# Should be unreachable
				throw new ConvertError( 'unknowndimension' );
			}
		} elseif( $var instanceof self ){
			$dim = $var->value;
		} else {
			$dim = intval( $var );
		}

		if( $var2 === null ){
			$this->value = $dim;
			$this->name = $this->compoundName = self::$legalDimensions[$this->value];

		} else {
			if( is_string( $var2 ) ){
				if( $legalDimensionsFlip === null ){
					$legalDimensionsFlip = array_flip( self::$legalDimensions );
				}
				if( isset( $legalDimensionsFlip[$var2] ) ){
					$dim2 = $legalDimensionsFlip[$var2];
				} else {
					# Should be unreachable
					throw new ConvertError( 'unknowndimension' );
				}
			} elseif( $var2 instanceof self ){
				$dim2 = $var2->value;
			} else {
				$dim2 = intval( $var2 );
			}

			$this->value = $dim - $dim2;
			if( in_array( $this->value, array_keys( self::$legalDimensions ) ) ){
				$this->name = self::$legalDimensions[$this->value];
				$this->compoundName = array(
					self::$legalDimensions[$var],
					self::$legalDimensions[$var2],
				);
			} else {
				# Some combinations of units are fine (carats per bushel is a perfectly good,
				# if somewhat bizarre, measure of density, for instance).  But others (like
				# carats per miles-per-gallon) are definitely not.
				# TODO: this allows compound units like <gigawatthours>/<pascal> as a unit
				# of volume; is that a good thing or a bad thing?
				throw new ConvertError( 'invalidcompoundunit', "$var/$var2" );
			}
		}
	}

	/**
	 * Convert to string.  Magic in PHP 5.1 and above.
	 * @return String
	 */
	public function __toString(){
		return strval( $this->name );
	}

	/**
	 * Get the name, or names, of the dimension
	 * @return String|Array of String
	 */
	public function getName( $expandCompound = false ){
		return $expandCompound
			? $this->name
			: $this->compoundName;
	}

	/**
	 * Get the localised name of the dimension.  Output is unescaped
	 * @return String
	 */
	public function getLocalisedName(){
		return wfMsg( "pfunc-convert-dimension-{$this->name}" );
	}

}

class ConvertUnit {

	/**
	 * array(
	 *     DIMENSION => array(
	 *         UNIT => array(
	 *             CONVERSION,
	 *             REGEX
	 *         )
	 *     )
	 * )
	 */
	protected static $units = array(
		ConvertDimension::DIM_LENGTH => array(
			'gigametre'        => array( 1000000000, 'Gm' ),
			'megametre'        => array( 1000000, '(?:(?-i)Mm)' ), # Case-sensitivity is forced
			'kilometre'        => array( 1000, 'km' ),
			'hectometre'       => array( 100, 'hm' ),
			'decametre'        => array( 10, 'dam' ),
			'metre'            => array( 1, 'm' ),
			'decimetre'        => array( 0.1, 'dm' ),
			'centimetre'       => array( 0.01, 'cm' ),
			'millimetre'       => array( 0.001, '(?:(?-i)mm)' ), # Case-sensitivity is forced
			'micrometre'       => array( 0.0001, '\x03BCm|\x00B5m|um' ), # There are two similar mu characters
			'nanometre'        => array( 0.0000001, 'nm' ),
			'angstrom'         => array( 0.00000001, '\x00C5' ),

			'mile'             => array( 1609.344, 'mi|miles?' ),
			'furlong'          => array( 201.168, 'furlong' ),
			'chain'            => array( 20.1168 , 'chain' ),
			'rod'              => array( 5.0292, 'rod|pole|perch' ),
			'fathom'           => array( 1.8288, 'fathom' ),
			'yard'             => array( 0.9144, 'yards?|yd' ),
			'foot'             => array( 0.3048, 'foot|feet|ft' ),
			'hand'             => array( 0.1016, 'hands?' ),
			'inch'             => array( 0.0254, 'inch|inches|in' ),

			'nauticalmile'     => array( 1852, 'nauticalmiles?|nmi' ),
			'nauticalmileuk'   => array( 1853.184, 'oldUKnmi|Brnmi|admi' ),
			'nauticalmileus'   => array( 1853.24496, 'oldUSnmi' ),

			'gigaparsec'       => array( 3.0856775813057E25, 'gigaparsecs?|Gpc' ),
			'megaparsec'       => array( 3.0856775813057E22, 'megaparsecs?|Mpc' ),
			'kiloparsec'       => array( 3.0856775813057E19, 'kiloparsecs?|kpc' ),
			'parsec'           => array( 3.0856775813057E16, 'parsecs?|pc' ),
			'gigalightyear'    => array( 9.4607304725808E24, 'gigalightyears?|Gly' ),
			'mrgalightyear'    => array( 9.4607304725808E21, 'megalightyears?|Mly' ),
			'kilolightyear'    => array( 9.4607304725808E18, 'kilolightyears?|kly' ),
			'lightyear'        => array( 9.4607304725808E15, 'lightyears?|ly' ),
			'astronomicalunit' => array( 149597870700, 'astronomicalunits?|AU' ),
		),

		ConvertDimension::DIM_AREA => array(
			'squarekilometre'    => array( 1E6, 'km2|km\^2' ),
			'squaremetre' => array( 1, 'm2|m\^2' ),
			'squarecentimetre'   => array( 1E-4, 'cm2|cm\^2' ),
			'squaremillimetre'   => array( 1E-6, 'mm2|mm\^2' ),
			'hectare'            => array( 1E4, 'hectares?|ha' ),

			'squaremile'         => array( 2589988.110336, 'sqmi|mi2|mi\^2' ),
			'acre'               => array( 4046.856422 , 'acres?' ),
			'squareyard'         => array( 0.83612736, 'sqyd|yd2|yd\^2' ),
			'squarefoot'         => array( 0.09290304, 'sqft|ft2|ft\^2' ),
			'squareinch'         => array( 0.00064516, 'sqin|in2|in\^2' ),

			'squarenauticalmile' => array( 3429904, 'sqnmi|nmi2|nmi\^2' ),
			'dunam'              => array( 1000, 'dunam' ),
			'tsubo'              => array( 3.305785, 'tsubo' ),
		),

		ConvertDimension::DIM_VOLUME => array(
			'cubicmetre'      => array( 1, 'm3|m\^3' ),
			'cubiccentimetre' => array( 1E-6, 'cm3|cm\^3' ),
			'cubicmillimetre' => array( 1E-9, 'mm3|mm\^3' ),
			'kilolitre'       => array( 1, 'kl' ),
			'litre'           => array( 1E-3 , 'l' ),
			'centilitre'      => array( 1E-5, 'cl' ),
			'millilitre'      => array( 1E-6, 'ml' ),

			'cubicyard'       => array( 0.764554857984, 'cuyd|yd3|yd\^3' ),
			'cubicfoot'       => array( 0.028316846592, 'cuft|ft3|ft\^3' ),
			'cubicinch'       => array( 0.000016387064, 'cuin|in3|in\^3' ),
			'barrel'          => array( 0.16365924, 'bbl|barrels?|impbbl' ),
			'bushel'          => array( 0.03636872, 'bsh|bushels?|impbsh' ),
			'gallon'          => array( 0.00454609, 'gal|gallons?|impgal' ),
			'quart'           => array( 0.0011365225, 'qt|quarts?|impqt' ),
			'pint'            => array( 0.00056826125, 'pt|pints?|imppt' ),
			'fluidounce'      => array( 0.0000284130625, 'floz|impfloz' ),

			'barrelus'        => array( 0.119240471196, 'usbbl' ),
			'barreloil'       => array( 0.158987294928, 'oilbbl' ),
			'barrelbeer'      => array( 0.117347765304, 'beerbbl' ),
			'usgallon'        => array( 0.003785411784, 'usgal' ),
			'usquart'         => array( 0.000946352946, 'usqt' ),
			'uspint'          => array( 0.000473176473, 'uspt' ),
			'usfluidounce'    => array( 0.0000295735295625, 'usfloz' ),
			'usdrybarrel'     => array( 0.11562819898508, 'usdrybbl' ),
			'usbushel'        => array( 0.03523907016688, 'usbsh' ),
			'usdrygallon'     => array( 0.00440488377086, 'usdrygal' ),
			'usdryquart'      => array( 0.001101220942715, 'usdryqt' ),
			'usdrypint'       => array( 0.0005506104713575, 'usdrypt' ),
		),

		ConvertDimension::DIM_TIME => array(
			'year'   => array( 31557600, 'yr' ),
			'day'    => array( 86400, 'days?' ),
			'hour'   => array( 3600, 'hours?|hr|h' ),
			'minute' => array( 60, 'minutes?|mins?' ),
			'second' => array( 1, 's' ),
		),

		ConvertDimension::DIM_SPEED => array(
			'knot' => array( 0.514444444, 'knot|kn' ),
			'speedoflight' => array( 2.9979E8, 'c' ),
		),

		ConvertDimension::DIM_PRESSURE => array(
			'gigapascal'        => array( 1000000000, 'GPa' ),
			'megapascal'        => array( 1000000, '(?:(?-i)M[Pp]a)' ), # Case-sensitivity is forced
			'kilopascal'        => array( 1000, 'kPa' ),
			'hectopascal'       => array( 100, 'hPa' ),
			'pascal'            => array( 1, 'Pa' ),
			'millipascal'       => array( 0.001, '(?:(?-i)m[Pp]a)' ), # Case-sensitivity is forced

			'bar'               => array( 100000, 'bar' ),
			'decibar'           => array( 10000, 'dbar' ),
			'milibar'           => array( 100 , 'mbar|mb' ),
			'kilobarye'         => array( 100, 'kba' ),
			'barye'             => array( 0.1, 'ba' ),
			
			'atmosphere'        => array( 101325, 'atm|atmospheres?' ),
			'torr'              => array( 133.32237, 'torr' ),
			'mmhg'              => array( 133.322387415, 'mmHg' ),
			'inhg'              => array( 3386.38864034, 'inHg' ),
			'psi'               => array( 6894.757293, 'psi' ),
		),
		# TODO: other dimensions as needed
	);

	# Default units for each dimension
	# TODO: this should ideally be localisable
	protected static $defaultUnit = array(
		ConvertDimension::DIM_LENGTH => 'metre',
		ConvertDimension::DIM_AREA => 'squaremetre',
		ConvertDimension::DIM_VOLUME => 'cubicmetre',
		ConvertDimension::DIM_TIME => 'second',
		ConvertDimension::DIM_SPEED => 'metre/second',
		ConvertDimension::DIM_PRESSURE => 'pascal',
	);

	# An array of preprocessing conversions to apply to units
	protected static $unitConversions = array(
		'/^mph$/ui' => 'mi/h',
	);

	# Map of UNIT => DIMENSION, created on construct
	protected static $dimensionMap = false;

	/***************** MEMBER VARIABLES *****************/

	# @var ConvertDimension
	public $dimension;

	# What number you need to multiply this unit by to get the equivalent
	# value in SI base units
	protected $conversion = 1;

	# A regex which matches the unit
	protected $regex;

	# The name of the unit (key into $units[$dimension] above
	protected $unitName;

	/***************** MEMBER FUNCTIONS *****************/

	/**
	 * Constructor
	 * @param  $rawUnit String
	 */
	public function __construct( $rawUnit ){
		if( self::$dimensionMap === false ){
			self::$dimensionMap = array();
			foreach( self::$units as $dimension => $arr ){
				foreach( $arr as $unit => $val ){
					self::$dimensionMap[$unit] = $dimension;
				}
			}
		}

		$this->parseUnit( $rawUnit );
	}

	protected function parseUnit( $rawUnit ){

		# Do mappings like 'mph' --> 'mi/h'
		$rawUnit = preg_replace(
			array_keys( self::$unitConversions ),
			array_values( self::$unitConversions ),
			$rawUnit
		);

		$parts = explode( '/', $rawUnit );
		array_map( 'trim', $parts );
		if( count( $parts ) == 1 ){
			# Single unit
			foreach( self::$units as $dimension => $units ){
				foreach( $units as $unit => $data ){
					if( $rawUnit == $unit || preg_match( "/^({$data[1]})$/ui", $parts[0] ) ){
						$this->dimension = new ConvertDimension( self::$dimensionMap[$unit] );
						$this->conversion = self::$units[$this->dimension->value][$unit][0];
						$this->regex = $data[1];
						$this->unitName = $unit;
						return;
					}
				}
			}

			# Unknown unit
			throw new ConvertError( 'unknownunit', $rawUnit );

		} elseif( count( $parts ) == 2 ){
			# Compound unit.
			$top = new self( $parts[0] );
			$bottom = new self( $parts[1] );
			$this->dimension = new ConvertDimension( $top->dimension, $bottom->dimension );
			$this->conversion = $top->conversion / $bottom->conversion;
			$this->regex = "(?:{$top->regex})/(?:{$bottom->regex})";
			$this->unitName = array( $top->unitName, $bottom->unitName );
			return;

		} else {
			# Whaaat?  Too many parts
			throw new ConvertError( 'doublecompoundunit', $rawUnit );
		}
	}

	public function getConversion(){
		return $this->conversion;
	}

	public function getRegex(){
		return $this->regex;
	}

	/**
	 * Get the text of the unit
	 * @param $value String number for PLURAL support
	 * @param $link Bool
	 * @return String
	 */
	public function getText( $value, $link=false, $abbreviate=false ){
		global $wgContLang;
		$value = $wgContLang->formatNum( $value );
		$abbr = $abbreviate ? '-abbr' : '';

		if( !is_array( $this->unitName ) ){
			$msg = "pfunc-convert-unit-{$this->dimension->getName()}-{$this->unitName}";
			$msgText = wfMsgExt( "$msg$abbr", array( 'parsemag', 'content' ), $value );
			if( $link && !wfEmptyMsg( "$msg-link" ) ){
				$title = Title::newFromText( wfMsgForContentNoTrans( "$msg-link" ) );
				$msgText = "[[{$title->getFullText()}|$msgText]]";
			}

		} elseif( !wfEmptyMsg( "pfunc-convert-unit-{$this->dimension->getName(true)}-{$this->unitName[0]}-{$this->unitName[1]}" ) ){
			# A wiki has created, say, [[MediaWiki:pfunc-convert-unit-speed-metres-second]]
			# so they can have it display "<metres per second>" rather than
			# "<metres>/<second>"
			$msg = "pfunc-convert-unit-{$this->dimension->getName(true)}-{$this->unitName[0]}-{$this->unitName[1]}";
			$msgText = wfMsgExt( "$msg$abbr", array( 'parsemag', 'content' ), $value );
			if( $link && !wfEmptyMsg( "$msg-link" ) ){
				$title = Title::newFromText( wfMsgForContentNoTrans( "$msg-link" ) );
				if( $title instanceof Title ){
					$msgText = "[[{$title->getFullText()}|$msgText]]";
				}
			}

		} else {
			$dimensionNames = $this->dimension->getName();
			$msg = "pfunc-convert-unit-{$dimensionNames[0]}-{$this->unitName[0]}";
			$msgText = wfMsgExt( "$msg$abbr", array( 'parsemag', 'content' ), $value );
			if( $link && !wfEmptyMsg( "$msg-link" ) ){
				$title = Title::newFromText( wfMsgForContentNoTrans( "$msg-link" ) );
				$msgText = "[[{$title->getFullText()}|$msgText]]";
			}

			$msg2 = "pfunc-convert-unit-{$dimensionNames[1]}-{$this->unitName[1]}";
			$msg2Text = wfMsgExt( "$msg2$abbr", array( 'parsemag', 'content' ), 1 ); # Singular for denominator
			if( $link && !wfEmptyMsg( "$msg2-link" ) ){
				$title = Title::newFromText( wfMsgForContentNoTrans( "$msg2-link" ) );
				if( $title instanceof Title ){
					$msg2Text = "[[{$title->getFullText()}|$msg2Text]]";
				}
			}
			$msgText = "$msgText/$msg2Text";
		}

		return $msgText;
	}

	/**
	 * Get the default (usually SI) unit associated with this particular dimension
	 * @return ConvertUnit
	 */
	public function getDefaultUnit(){
		return new ConvertUnit( self::$defaultUnit[$this->dimension->value] );
	}
}
