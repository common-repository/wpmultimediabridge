<?php
/*
 * json_decode definition if it doesn't exist
 * 
 * (PHP 5 >= 5.2.0, PECL json >= 1.2.0)
 * json_decode - Decodes a JSON string
 */
if (!function_exists('json_decode')) :
	function json_decode($json, $assoc = true)
	{
		// Author: walidator.info 2009
		$comment = false;
		$out = '$x=';
		
		for ($i=0; $i<strlen($json); $i++) {
			if (!$comment) {
				if ($json[$i] == '{') $out .= ' array(';
				else if ($json[$i] == '}') $out .= ')';
				else if ($json[$i] == '[') $out .= ' array(';
				else if ($json[$i] == ']') $out .= ')';
				else if ($json[$i] == ':') $out .= '=>';
				else $out .= $json[$i];           
	        } else {
	        	$out .= $json[$i];
	        }
			if ($json[$i] == '"') $comment = !$comment;
		}
		eval($out . ';');
		return $x;
	}
endif;

/*
 * json_encode definition if it doesn't exist
 * 
 * (PHP 5 >= 5.2.0, PECL json >= 1.2.0)
 * json_encode - Returns the JSON representation of a value
 */
if (!function_exists('json_encode')) :
	function json_encode($value = false)
	{
		if (is_null($value)) return 'null';
		if ($value === false) return 'false';
		if ($value === true) return 'true';
		if (is_scalar($value)) {
			if (is_float($value)) {
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($value)));
			}
			if (is_string($value)) {
				static $json_replaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($json_replaces[0], $json_replaces[1], $value) . '"';
			} else {
				return $value;
			}
		}
		$isList = true;
		for ($i = 0, reset($value); $i < count($value); $i++, next($value)) {
			if (key($value) !== $i) {
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList) {
			foreach ($value as $v) $result[] = self::json_encode($v);
			return '[' . join(',', $result) . ']';
		} else {
			foreach ($value as $k => $v) $result[] = self::json_encode($k).':'.self::json_encode($v);
			return '{' . join(',', $result) . '}';
		}
	}
endif;

// Thanks to http://www.bin-co.com/php/scripts/xml2array/
function xml2array($contents, $get_attributes=1, $priority = 'tag') {
	if (!$contents)
		return array();
	
	if (!function_exists('xml_parser_create'))
		return array();
	
	//Get the XML parser of PHP - PHP must have this module for the parser to work
	$parser = xml_parser_create('');
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	
	if (!$xml_values)
		return array();
	
	//Initializations
	$xml_array = array();
	$parents = array();
	$opened_tags = array();
	$arr = array();
	
	$current = &$xml_array; //Refference
	
	//Go through the tags.
	$repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	foreach($xml_values as $data) {
		unset($attributes,$value);//Remove existing values, or there will be trouble
		
		//This command will extract these variables into the foreach scope
		// tag(string), type(string), level(int), attributes(array).
		extract($data);//We could use the array by itself, but this cooler.
		
		$result = array();
		$attributes_data = array();
		        
		if (isset($value)) {
			if ($priority == 'tag')
				$result = $value;
			else
				$result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
		}
		
		//Set the attributes too.
		if (isset($attributes) and $get_attributes) {
			foreach ($attributes as $attr => $val) {
				if ($priority == 'tag')
					$attributes_data[$attr] = $val;
				else
					$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
			}
		}
		
		//See tag status and do the needed.
		if ($type == "open") {//The starting of the tag '<tag>'
			$parent[$level-1] = &$current;
			
			if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
				$current[$tag] = $result;
				
				if ($attributes_data)
					$current[$tag. '_attr'] = $attributes_data;
				
				$repeated_tag_index[$tag.'_'.$level] = 1;
		
				$current = &$current[$tag];
		
			} else { //There was another element with the same tag name
				if (isset($current[$tag][0])) {//If there is a 0th element it is already an array
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
					$repeated_tag_index[$tag.'_'.$level]++;
				} else {//This section will make the value an array if multiple tags with the same name appear together
					$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
					$repeated_tag_index[$tag.'_'.$level] = 2;
		
					if (isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
						$current[$tag]['0_attr'] = $current[$tag.'_attr'];
						unset($current[$tag.'_attr']);
					}
				}
				$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
				$current = &$current[$tag][$last_item_index];
			}
		} elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
			//See if the key is already taken.
			if (!isset($current[$tag])) { //New Key
				$current[$tag] = $result;
				$repeated_tag_index[$tag.'_'.$level] = 1;
				
				if ($priority == 'tag' and $attributes_data)
					$current[$tag. '_attr'] = $attributes_data;
		
			} else { //If taken, put all things inside a list(array)
				if (isset($current[$tag][0]) && is_array($current[$tag])) {//If it is already an array...
					// ...push the new element into that array.
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
			
					if ($priority == 'tag' and $get_attributes and $attributes_data) {
						$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
					}
					$repeated_tag_index[$tag.'_'.$level]++;
				} else { //If it is not an array...
					$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
					$repeated_tag_index[$tag.'_'.$level] = 1;
					
					if ($priority == 'tag' and $get_attributes) {
						if (isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
		
							$current[$tag]['0_attr'] = $current[$tag.'_attr'];
							unset($current[$tag.'_attr']);
						}
		
						if ($attributes_data) {
							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
						}
					}
					$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
				}
			}
		} elseif ($type == 'close') { //End of tag '</tag>'
			$current = &$parent[$level-1];
		}
	}
	
	return($xml_array);
}  
?>