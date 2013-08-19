<?php

/**
 * GUMP - A fast, extensible PHP input validation class
 *
 * @author		Sean Nieuwoudt (http://twitter.com/SeanNieuwoudt)
 * @copyright	Copyright (c) 2011 Wixel.net
 * @link		http://github.com/Wixel/GUMP
 * @version     1.0
 */

class GUMP
{
	// Validation rules for execution
	protected $validation_rules = array();

	// Filter rules for execution
	protected $filter_rules = array();

	// Instance attribute containing errors from last run
	protected $errors = array();

	protected $display_names = array();

	// ** ------------------------- Validation Data ------------------------------- ** //

	public static $basic_tags	  = "<br><p><a><strong><b><i><em><img><blockquote><code><dd><dl><hr><h1><h2><h3><h4><h5><h6><label><ul><li><span><sub><sup>";

	public static $en_noise_words = "about,after,all,also,an,and,another,any,are,as,at,be,because,been,before,
				  				  	 being,between,both,but,by,came,can,come,could,did,do,each,for,from,get,
				  				  	 got,has,had,he,have,her,here,him,himself,his,how,if,in,into,is,it,its,it's,like,
			      				  	 make,many,me,might,more,most,much,must,my,never,now,of,on,only,or,other,
				  				  	 our,out,over,said,same,see,should,since,some,still,such,take,than,that,
				  				  	 the,their,them,then,there,these,they,this,those,through,to,too,under,up,
				  				  	 very,was,way,we,well,were,what,where,which,while,who,with,would,you,your,a,
				  				  	 b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,$,1,2,3,4,5,6,7,8,9,0,_";

	// ** ------------------------- Validation Helpers ---------------------------- ** //

	/**
	 * Magic method to generate the validation error messages
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->get_readable_errors(true);
	}

	/**
	 * Perform XSS clean to prevent cross site scripting
	 *
	 * @static
	 * @access public
	 * @param  array $data
	 * @return array
	 */
	public static function xss_clean(array $data)
	{
		foreach($data as $k => $v)
		{
			$data[$k] = filter_var($v, FILTER_SANITIZE_STRING);
		}

		return $data;
	}

	/**
	 * Combines the three setting-functions into one
	 *
	 * Usage: "<index>" =>
	 * "[String validation_rules, String filter_rules, String display_name]"
	 * Example: array(blubb => array('numeric', 'trim', 'Blubb'))
	 *
	 * @param  [type] $rules [description]
	 * @return [type]        [description]
	 */
	public function rules($rules)
	{
		$validation_rules = array();
		$filter_rules = array();
		$display_names = array();

		foreach($rules as $name => $rule)
		{
			if(count($rule) == 3)
			{
				if(!empty($rule[0]))
				{
					$validation_rules[$name] = $rule[0];
				}
				if(!empty($rule[1]))
				{
					$filter_rules[$name] = $rule[1];
				}
				if(!empty($rule[2]))
				{
					$display_names[$name] = $rule[2];
				}
			}
			else
			{
				throw new Exception('Wrong Ruleset');
			}
		}

		$this->validation_rules($validation_rules);
		$this->filter_rules($filter_rules);
		$this->display_names($display_names);
	}

	/**
	 * Getter/Setter for the validation rules
	 *
	 * @param array $rules
	 * @return array
	 */
	public function validation_rules(array $rules = array())
	{
		if(!empty($rules)) {
			$this->validation_rules = $rules;
		} else {
			return $this->validation_rules;
		}
	}

	/**
	 * Getter/Setter for the filter rules
	 *
	 * @param array $rules
	 * @return array
	 */
	public function filter_rules(array $rules = array())
	{
		if(!empty($rules)) {
			$this->filter_rules = $rules;
		} else {
			return $this->filter_rules;
		}
	}

	/**
	 * Adds Display-Names for the Post-Elements, so that, on error, not the
	 * Element-name is shown, but his displayName, which might be more readable
	 * for the User
	 * @param  array  $names An Array of Names for each Element
	 *                       Example: array('element' => 'displayName')
	 */
	public function display_names($names = array())
	{
		foreach($names as $name => $display_name)
		{
			$this->display_names[$name] = $display_name;
		}
	}

	/**
	 * Run the filtering and validation after each other
	 *
	 * @param array $data
	 * @return array
	 * @return boolean
	 */
	public function run(array $data)
	{
		$data = $this->filter($data, $this->filter_rules());

		$validated = $this->validate(
			$data, $this->validation_rules()
		);

		if($validated !== true) {
			return false;
		} else {
			return $data;
		}
	}

	/**
	 * Sanitize the input data
	 *
	 * @access public
	 * @param  array $data
	 * @return array
	 */
	public function sanitize(array $input, $fields = NULL)
	{
		trigger_error(
			'GUMP-Method sanitize should not be used; Use the filter instead');

		$magic_quotes = (bool)get_magic_quotes_gpc();

		if(is_null($fields))
		{
			$fields = array_keys($input);
		}

		foreach($fields as $field)
		{
			if(!isset($input[$field]))
			{
				continue;
			}
			else
			{
				$value = $input[$field];

				if(is_string($value))
				{
					if($magic_quotes === TRUE)
					{
						$value = stripslashes($value);
					}

					if(strpos($value, "\r") !== FALSE)
					{
						$value = trim($value);
					}

					// if(function_exists('iconv'))
					// {
					// 	$value = iconv('ISO-8859-1', 'UTF-8', $value);
					// }

					$value = filter_var($value, FILTER_SANITIZE_STRING);
				}

				$input[$field] = $value;
			}
		}

		return $input;
	}

	/**
	 * Return the error array from the last validation run
	 *
	 * @return array
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * Perform data validation against the provided ruleset
	 *
	 * @access public
	 * @param  mixed $input
	 * @param  array $ruleset
	 * @return mixed
	 */
	public function validate(array $input, array $ruleset)
	{
		$this->errors = array();

		foreach($ruleset as $field => $rules)
		{
			#if(!array_key_exists($field, $input))
			#{
			#	continue;
			#}

			$rules = explode('|', $rules);

			if(!$this->field_void_handle($input, $field, $rules))
			{
				continue;
			}

			foreach($rules as $rule)
			{
				$method = NULL;
				$param  = NULL;

				if(strstr($rule, ',') !== FALSE) // has params
				{
					$rule   = explode(',', $rule);
					$method = 'validate_'.$rule[0];
					$param  = $rule[1];
				}
				else
				{
					$method = 'validate_'.$rule;
				}

				if(is_callable(array($this, $method)))
				{
					$result = $this->$method($field, $input, $param);
					if(is_array($result)) // Validation Failed
					{
						$this->errors[] = $result;
					}
				}
				else
				{
					throw new Exception("Validator method '$method' does not exist.");
				}
			}
		}

		return (count($this->errors) > 0)? $this->errors : TRUE;
	}

	/**
	 * Checks if the field is void and what action should be done
	 *
	 * When the field is void, but should not be, an error will be added
	 *
	 * @param  Array $input
	 * @param  String $field
	 * @return boolean true if the field should be processed further, false
	 * if not
	 */
	protected function field_void_handle($input, $field, $rules)
	{
		if(!isset($input[$field]) || trim($input[$field]) == '') {
			if($this->isFieldAllowedVoid($field, $rules))
			{
				return false;
			}
			else
			{
				$this->errors[] = array(
					'field' => $field,
					'value' => NULL,
					'rule'	=> 'validate_required',
					'param' => NULL
					);
				return false;
			}
		}
		else
		{
			return true;
		}
	}

	protected function isFieldAllowedVoid($field, $rules)
	{
		foreach($rules as $rule) {
			if(strstr($rule, 'required')) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Process the validation errors and return human readable error messages
	 *
	 * @param bool $convert_to_string = false
	 * @param string $field_class
	 * @param string $error_class
	 * @return array
	 * @return string
	 */
	public function get_readable_errors($convert_to_string = false, $field_class="field", $error_class="error-message")
	{
		if(empty($this->errors)) {
			return ($convert_to_string)? null : array();
		}

		$resp = array();

		foreach($this->errors as $e) {

			$field = ucwords(str_replace(array('_','-'), chr(32), $e['field']));
			$param = $e['param'];

			if(!isset($this->display_names[$field])) {
				$resp[] = "Feld \"$field\" hat kein displayName";
				$this->display_names[$field] = $field;
			}

			switch($e['rule']) {
				case 'validate_required':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field is required";
					break;
				case 'validate_disallowed':
					$resp[] = "The $displayName field has to be void, because: '$param'";
					break;
				case 'validate_valid_email':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field is required to be a valid email address";
					break;
				case 'validate_max_len':
					if($param == 1) {
						$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field needs to be shorter than $param character";
					} else {
						$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field needs to be shorter than $param characters";
					}
					break;
				case 'validate_min_len':
					if($param == 1) {
						$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field needs to be longer than $param character";
					} else {
						$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field needs to be longer than $param characters";
					}
					break;
				case 'validate_exact_len':
					if($param == 1) {
						$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field needs to be exactly $param character in length";
					} else {
						$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field needs to be exactly $param characters in length";
					}
					break;
				case 'validate_alpha':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field may only contain alpha characters(a-z)";
					break;
				case 'validate_alpha_numeric':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field may only contain alpha-numeric characters";
					break;
				case 'validate_alpha_dash':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field may only contain alpha characters &amp; dashes";
					break;
				case 'validate_numeric':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field may only contain numeric characters";
					break;
				case 'validate_integer':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field may only contain a numeric value";
					break;
				case 'validate_boolean':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field may only contain a true or false value";
					break;
				case 'validate_float':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field may only contain a float value";
					break;
				case 'validate_valid_url':
					$resp[] = "The <span class=\"$field_class\">$this->display_names[$field]</span> field is required to be a valid URL";
					break;
				case 'validate_url_exists':
					$resp[] = "The <span class=\"$field_class\">$field</span> URL does not exist";
					break;
				case 'validate_valid_ip':
					$resp[] = "The <span class=\"$field_class\">$field</span> field needs to contain a valid IP address";
					break;
				case 'validate_valid_cc':
					$resp[] = "The <span class=\"$field_class\">$field</span> field needs to contain a valid credit card number";
					break;
				case 'validate_valid_name':
					$resp[] = "The <span class=\"$field_class\">$field</span> field needs to contain a valid human name";
					break;
				case 'validate_contains':
					$resp[] = "The <span class=\"$field_class\">$field</span> field needs contain one of these values: ".implode(', ', $param);
					break;
				}
			}
		}

		/**
		 * Like get_readable_errors, but without HTML-Markup. Also the texts language is german
		 *
		 * @param bool $convert_to_string = false
		 * @param string $field_class
		 * @param string $error_class
		 * @return array
		 * @return string
		 */
		public function get_readable_string_errors($convert_to_string = false)
		{
			if(empty($this->errors)) {
				return ($convert_to_string)? null : array();
			}

			$resp = array();

			foreach($this->errors as $e) {

				$field = $e['field'];
				$param = $e['param'];
				$value = $e['value'];
				$displayName = $this->display_names[$field];
				// $field = ucwords(str_replace(array('_','-'), chr(32), $e['field']));

				if(!isset($this->display_names[$field])) {
					$resp[] = "Feld \"$field\" hat kein displayName";
					$this->display_names[$field] = $field;
				}

				switch($e['rule']) {
					case 'validate_required':
						$resp[] = "Das $displayName Feld muss ausgefüllt werden";
						break;
					case 'validate_disallowed':
						$resp[] = "Das $displayName Feld muss leer sein, weil: '$param'";
						break;
					case 'validate_valid_email':
						$resp[] = "Das $displayName Feld muss eine korrekte Email-Adresse beinhalten";
						break;
					case 'validate_max_len':
						$resp[] = "Das $displayName Feld muss kürzer oder gleich lang wie $param Zeichen sein";
						break;
					case 'validate_min_len':
						$resp[] = "Das $displayName Feld muss länger oder gleich lang wie $param Zeichen sein";
						break;
					case 'validate_exact_len':
						$resp[] = "Das $displayName Feld muss genau $param Zeichen lang sein";
						break;
					case 'validate_alpha':
						$resp[] = "Das $displayName Feld Kann nur Buchstaben enthalten (A-Z)";
						break;
					case 'validate_alpha_numeric':
						$resp[] = "Das $displayName Feld kann nur Buchstaben und Zahlen beinhalten";
						break;
					case 'validate_alpha_dash':
						$resp[] = "Das $displayName Feld kann nur Buchstaben und &amp; Striche beinhalten";
						break;
					case 'validate_numeric':
						$resp[] = "Das $displayName Feld kann nur Zahlen beinhalten";
						break;
					case 'validate_integer':
						$resp[] = "Das $displayName Feld kann nur Zahlen beinhalten";
						break;
					case 'validate_boolean':
						$resp[] = "Das $displayName Feld kann nur einen true oder false Wert beinhalten";
						break;
					case 'validate_float':
						$resp[] = "Das $displayName Feld kann nur eine Komma-Nummer enthalten";
						break;
					case 'validate_valid_url':
						$resp[] = "Das $displayName Feld muss eine gültige URL sein";
						break;
					case 'validate_url_exists':
						$resp[] = "Die $displayName URL existiert nicht";
						break;
					case 'validate_valid_ip':
						$resp[] = "Das $displayName Feld muss eine korrekte IP-Adresse beinhalten";
						break;
					case 'validate_valid_cc':
						$resp[] = "Das $displayName Feld muss eine korrekte Kreditkartennummer beinhalten";
						break;
					case 'validate_valid_name':
						$resp[] = "Das $displayName Feld muss ein korrekten menschlichen Namen beinhalten";
						break;
					case 'validate_contains':
						$resp[] = "Das $displayName Feld muss eine der folgenden Werte beinhalten: ".implode(', ', $param);
						break;
					case 'validate_isodate':
						$resp[] = "Das $displayName Feld muss ein Datum im Format Jahr-Monat-Tag (Bsp:'2013-05-14') beinhalten.";
						break;
					case 'validate_alpha_dash_space':
						$resp[] = "Das $displayName Feld $value darf nur aus Leerzeichen, Unterstrich, Minus und Buchstaben bestehen";
						break;
					default:
						$resp[] = "Das $displayName Feld wurde falsch eingegeben, genauer Grund ist aber unbekannt";
						break;
				}
			}

		if(!$convert_to_string) {
			return $resp;
		} else {
			$buffer = '';
			foreach($resp as $s) {
				$buffer .= $s . '<br />';
			}
			return $buffer;
		}
	}

	/**
	 * Filter the input data according to the specified filter set
	 *
	 * @access public
	 * @param  mixed $input
	 * @param  array $filterset
	 * @return mixed
	 */
	public function filter(array $input, array $filterset)
	{
		foreach($filterset as $field => $filters)
		{
			if(!array_key_exists($field, $input))
			{
				continue;
			}

			$filters = explode('|', $filters);

			foreach($filters as $filter)
			{
				$params = NULL;

				if(strstr($filter, ',') !== FALSE)
				{
					$filter = explode(',', $filter);

					$params = array_slice($filter, 1, count($filter) - 1);

					$filter = $filter[0];
				}

				if(is_callable(array($this, 'filter_'.$filter)))
				{
					$method = 'filter_'.$filter;
					$input[$field] = $this->$method($input[$field], $params);
				}
				else if(function_exists($filter))
				{
					$input[$field] = $filter($input[$field]);
				}
				else
				{
					throw new Exception("Filter method '$filter' does not exist.");
				}
			}
		}

		return $input;
	}

	// ** ------------------------- Filters --------------------------------------- ** //

	/**
	 * Replace noise words in a string (http://tax.cchgroup.com/help/Avoiding_noise_words_in_your_search.htm)
	 *
	 * Usage: '<index>' => 'noise_words'
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_noise_words($value, $params = NULL)
	{
		$value = preg_replace('/\s\s+/u', chr(32),$value);

		$value = " $value ";

		$words = explode(',', self::$en_noise_words);

		foreach($words as $word)
		{
			$word = trim($word);

			$word = " $word "; // Normalize

			if(stripos($value, $word) !== FALSE)
			{
				$value = str_ireplace($word, chr(32), $value);
			}
		}

		return trim($value);
	}


	/**
	 * Escapes the string for MySQL
	 *
	 * Usage: '<index>' => 'sql_escape'
	 *
	 * @access protected
	 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_sql_escape($value, $params = NULL)
	{
		$locValue = $value;
		if(class_exists('TableMng')) {
			TableMng::sqlEscape($locValue);
		}
		else {
			trigger_error('TableMng not existing in gump!');
		}

		return $locValue;
	}

	/**
	 * Remove all known punctuation from a string
	 *
	 * Usage: '<index>' => 'rmpunctuataion'
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_rmpunctuation($value, $params = NULL)
	{
		return preg_replace("/(?![.=$'€%-])\p{P}/u", '', $value);
	}

	/**
	 * Translate an input string to a desired language [DEPRECIATED]
	 *
	 * Any ISO 639-1 2 character language code may be used
	 *
	 * See: http://www.science.co.il/language/Codes.asp?s=code2
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	/*
	protected function filter_translate($value, $params = NULL)
	{
		$input_lang  = 'en';
		$output_lang = 'en';

		if(is_null($params))
		{
			return $value;
		}

		switch(count($params))
		{
			case 1:
				$input_lang  = $params[0];
				break;
			case 2:
				$input_lang  = $params[0];
				$output_lang = $params[1];
				break;
		}

		$text = urlencode($value);

		$translation = file_get_contents(
			"http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q={$text}&langpair={$input_lang}|{$output_lang}"
		);

		$json = json_decode($translation, true);

		if($json['responseStatus'] != 200)
		{
			return $value;
		}
		else
		{
			return $json['responseData']['translatedText'];
		}
	}
	*/

	/**
	 * Sanitize the string by removing any script tags
	 *
	 * Usage: '<index>' => 'sanitize_string'
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_sanitize_string($value, $params = NULL)
	{
		return filter_var($value, FILTER_SANITIZE_STRING);
	}

	/**
	 * Sanitize the string by urlencoding characters
	 *
	 * Usage: '<index>' => 'urlencode'
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_urlencode($value, $params = NULL)
	{
		return filter_var($value, FILTER_SANITIZE_ENCODED);
	}

	/**
	 * Sanitize the string by converting HTML characters to their HTML entities
	 *
	 * Usage: '<index>' => 'htmlencode'
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_htmlencode($value, $params = NULL)
	{
		return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
	}

	/**
	 * Sanitize the string by removing illegal characters from emails
	 *
	 * Usage: '<index>' => 'sanitize_email'
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_sanitize_email($value, $params = NULL)
	{
		return filter_var($value, FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Sanitize the string by removing illegal characters from numbers
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_sanitize_numbers($value, $params = NULL)
	{
		return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

	/**
	 * Filter out all HTML tags except the defined basic tags
	 *
	 * @access protected
	 * @param  string $value
	 * @param  array $params
	 * @return string
	 */
	protected function filter_basic_tags($value, $params = NULL)
	{
		return strip_tags($value, self::$basic_tags);
	}

	// ** ------------------------- Validators ------------------------------------ ** //

	/**
	 * Verify that a value is contained within the pre-defined value set
	 *
	 * Usage: '<index>' => 'contains,value value value'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_contains($field, $input, $param = NULL)
	{
		$invalid = false;

		$param = explode(chr(32), trim(strtolower($param)));

		$value = trim(strtolower($input[$field]));

		if(!in_array($value, $param)) {
			$invalid = true;
		}

		if(!$invalid) {
			return;
		} else {
			return array(
				'field' => $field,
				'value' => NULL,
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Check if the specified key is present and not empty
	 *
	 * Usage: '<index>' => 'required'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_required($field, $input, $param = NULL)
	{
		if(isset($input[$field]) && trim($input[$field]) != '')
		{
			return;
		}
		else
		{
			return array(
				'field' => $field,
				'value' => NULL,
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Checks if the specified key is not existing or empty
	 *
	 * Usage: '<index>' => 'disallowed'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_disallowed($field, $input, $param = NULL)
	{
		if(!isset($input[$field]) || trim($input[$field]) == '')
		{
			return;
		}
		else
		{
			return array(
				'field' => $field,
				'value' => NULL,
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided email is valid
	 *
	 * Usage: '<index>' => 'valid_email'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_valid_email($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!filter_var($input[$field], FILTER_VALIDATE_EMAIL))
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value length is less or equal to a specific value
	 *
	 * Usage: '<index>' => 'max_len,240'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_max_len($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(function_exists('mb_strlen'))
		{
			if(mb_strlen($input[$field]) <= (int)$param)
			{
				return;
			}
		}
		else
		{
			if(strlen($input[$field]) <= (int)$param)
			{
				return;
			}
		}

		return array(
			'field' => $field,
			'value' => $input[$field],
			'rule'	=> __FUNCTION__,
			'param' => $param
		);
	}

	/**
	 * Determine if the provided value length is more or equal to a specific value
	 *
	 * Usage: '<index>' => 'min_len,4'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_min_len($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(function_exists('mb_strlen'))
		{
			if(mb_strlen($input[$field]) >= (int)$param)
			{
				return;
			}
		}
		else
		{
			if(strlen($input[$field]) >= (int)$param)
			{
				return;
			}
		}

		return array(
			'field' => $field,
			'value' => $input[$field],
			'rule'	=> __FUNCTION__,
			'param' => $param
		);
	}

	/**
	 * Determine if the provided value length matches a specific value
	 *
	 * Usage: '<index>' => 'exact_len,5'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_exact_len($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(function_exists('mb_strlen'))
		{
			if(mb_strlen($input[$field]) == (int)$param)
			{
				return;
			}
		}
		else
		{
			if(strlen($input[$field]) == (int)$param)
			{
				return;
			}
		}

		return array(
			'field' => $field,
			'value' => $input[$field],
			'rule'	=> __FUNCTION__,
			'param' => $param
		);
	}

	/**
	 * Validates for a String in the Birthday-Format YYYY-MM-DD
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_isodate($field, $input, $param = NULL)
	{
		if(isset($input[$field]))
		{
			if(preg_match('/\A\d{4}-\d{1,2}-\d{1,2}\z/', $input[$field]))
			{
				return;
			}
			else
			{
				return array(
					'field' => $field,
					'value' => $input[$field],
					'rule'	=> __FUNCTION__,
					'param' => $param
				);
			}
		}
		else
		{
			return;
		}
	}

	/**
	 * Determine if the provided value contains only alpha characters
	 *
	 * Usage: '<index>' => 'alpha'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_alpha($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!preg_match("/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i", $input[$field]) !== FALSE)
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value contains only alpha-numeric characters
	 *
	 * Usage: '<index>' => 'alpha_numeric'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_alpha_numeric($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i", $input[$field]) !== FALSE)
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value contains only alpha characters with dashed and underscores
	 *
	 * Usage: '<index>' => 'alpha_dash'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_alpha_dash($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿß_-])+$/i", $input[$field]) !== FALSE)
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	protected function validate_alpha_dash_space($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿß_\- ])+$/i", $input[$field]) !== FALSE)
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value is a valid number or numeric string
	 *
	 * Usage: '<index>' => 'numeric'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_numeric($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!is_numeric($input[$field]))
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value is a valid integer
	 *
	 * Usage: '<index>' => 'integer'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_integer($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!filter_var($input[$field], FILTER_VALIDATE_INT))
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value is a PHP accepted boolean
	 *
	 * Usage: '<index>' => 'boolean'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_boolean($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		$bool = filter_var($input[$field], FILTER_VALIDATE_BOOLEAN);

		if(!is_bool($bool))
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value is a valid float
	 *
	 * Usage: '<index>' => 'float'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_float($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!filter_var($input[$field], FILTER_VALIDATE_FLOAT))
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value is a valid URL
	 *
	 * Usage: '<index>' => 'valid_url'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_valid_url($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!filter_var($input[$field], FILTER_VALIDATE_URL))
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if a URL exists & is accessible
	 *
	 * Usage: '<index>' => 'url_exists'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_url_exists($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		$url = str_replace(
			array('http://', 'https://', 'ftp://'), '', strtolower($input[$field])
		);

		if(function_exists('checkdnsrr'))
		{
			if(!checkdnsrr($url))
			{
				return array(
					'field' => $field,
					'value' => $input[$field],
					'rule'	=> __FUNCTION__,
					'param' => $param
				);
			}
		}
		else
		{
			if(gethostbyname($url) == $url)
			{
				return array(
					'field' => $field,
					'value' => $input[$field],
					'rule'	=> __FUNCTION__,
					'param' => $param
				);
			}
		}
	}

	/**
	 * Determine if the provided value is a valid IP address
	 *
	 * Usage: '<index>' => 'valid_ip'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_valid_ip($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!filter_var($input[$field], FILTER_VALIDATE_IP) !== FALSE)
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value is a valid IPv4 address
	 *
	 * Usage: '<index>' => 'valid_ipv4'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_valid_ipv4($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!filter_var($input[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== FALSE)
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the provided value is a valid IPv6 address
	 *
	 * Usage: '<index>' => 'valid_ipv6'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_valid_ipv6($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

		if(!filter_var($input[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE)
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Checks the Value based on the Regex-Parameter given
	 *
	 * Usage: '<index>' => 'regex,/YourRegexHere/'
	 *
	 * @author  Pascal Ernst <pascal.cc.ernst@gmail.com>
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_regex($field, $input, $param = NULL)
	{
		if(isset($param))
		{
			if(substr($param, -1, 1) == '/' || substr($param, 0, 1) == '/')
			{
				if (preg_match($param, $input[$field]))
				{
					return;
				}
			}
		}
		//error occurred
		return array(
			'field' => $field,
			'value' => $input[$field],
			'rule'	=> __FUNCTION__,
			'param' => $param
		);
	}


	/**
	 * Determine if the input is a valid credit card number
	 *
	 * See: http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php
	 * Usage: '<index>' => 'valid_cc'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_valid_cc($field, $input, $param = NULL)
	{
		$number = preg_replace('/\D/', '', $input[$field]);

		if(function_exists('mb_strlen'))
		{
			$number_length = mb_strlen($input[$field]);
		}
		else
		{
			$number_length = strlen($input[$field]);
		}

	  	$parity = $number_length % 2;

	 	$total = 0;

	  	for($i = 0; $i < $number_length; $i++)
		{
	    	$digit = $number[$i];

	    	if ($i % 2 == $parity)
			{
	      		$digit *= 2;

	      		if ($digit > 9)
				{
	        		$digit -= 9;
	      		}
	    	}

	    	$total += $digit;
	  	}

		if($total % 10 == 0)
		{
			return; // Valid
		}
		else
		{
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule'	=> __FUNCTION__,
				'param' => $param
			);
		}
	}

	/**
	 * Determine if the input is a valid human name [Credits to http://github.com/ben-s]
	 *
	 * See: https://github.com/Wixel/GUMP/issues/5
	 * Usage: '<index>' => 'valid_name'
	 *
	 * @access protected
	 * @param  string $field
	 * @param  array $input
	 * @return mixed
	 */
	protected function validate_valid_name($field, $input, $param = NULL)
	{
		if(!isset($input[$field]))
		{
			return;
		}

	    if(!preg_match("/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïñðòóôõöùúûüýÿ '-])+$/i", $input[$field]) !== FALSE)
	    {
	        return array(
	            'field' => $field,
	            'value' => $input[$field],
	            'rule'  => __FUNCTION__,
				'param' => $param
	        );
	    }
	}

	/**
	 * Fills Variables of varContainer with a void String if they do not exist
	 *
	 * For each array-Element in $ruleset: If the varContainer does not have a
	 * Element with a Key the same as the $ruleset's array-Element-Key, it
	 * creates a void String
	 *
	 * @param  Array $varContainer The Array that gets checked for the
	 * variables. (Given by Reference)
	 * @param  Array $ruleset      An Array
	 */
	public function voidVarsToStringByRuleset($varContainer, $ruleset) {

		foreach($ruleset as $field => $rules) {
			if(!isset($varContainer[$field]) || $varContainer[$field] === NULL) {
				$varContainer[$field] = '';
			}
		}

		return $varContainer;
	}

} // EOC
