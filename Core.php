<?php
	
	$cols_width = array();

	//Given an array of arrays, it returns a set of the keys of the subarrays
	function getKeySet($array){
		$keys = array();
		foreach ($array as $subarray){
			$tempkeys =  array_keys($subarray);
			$keys = array_unique(array_merge($keys, $tempkeys));
		}
		return $keys;
	}

	//Returns a random hex color
	function rand_color() {
		return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
	}

	//Returns a set of unique colors. The size of the set is equal to the size of the table
	function generateColors($array){
		$colors = array();
		for ($i = 0 ; $i < sizeof($array) ; $i++){
			$color = rand_color();
			while(in_array($color, $colors)){
				$color = rand_color();
			}
			$colors[] = $color; 
		}
		return $colors;
	}

	//Returns an ascii line separator 
	function getLineSeparator($cols, $array,$keys){
		$line = '';
		for($i = 0 ; $i < $cols ; $i++ ){
			$width = getColWidth($array,$keys[$i]);
			$line .= '+';
			for($j = 0; $j < $width; $j++){
				$line .= '-';
			}
		}
		$line .= '+';
		$line .= getNewLine();

		return $line;
	}
	
	//Returns an empty ascii field
	function getEmptyField($width){
		$field = '';
		for( $i = 0 ; $i < $width ; $i++ ){
			$field .= '&nbsp;';
		}
		$field .= "|";
		return $field;
	}
	
	//Returns the ascii field given a color and a value
	function getField($color,$value,$width){
		$field = "<span style='color: ".$color."'>  ".$value;
		$width = $width - strlen($value) - 1;
		for($i = 0 ; $i < $width ; $i++){
			$field .= '&nbsp;';
		}
		$field .= "</span>|";
		return $field;
	}

	//Returns an ascii table line from an array line
	function getArrayLine($keys, $colors, $array_line,$array){
		$line = '|';
		foreach ($keys as $position => $key){
			$width = getColWidth($array,$key);
			if( array_key_exists($key, $array_line)){
				$line .= getField($colors[$position], $array_line[$key],$width);
			}else{
				$line .= getEmptyField($width);
			}
		}
		$line .= getNewLine();
		return $line;
	}

	//Returns the max with of a given column. the global variable cols_width serves as a cache for with information
	function getColWidth($array,$key){
		
		global $cols_width;
		if(array_key_exists($key, $cols_width)){
			return $cols_width[$key];
		}

		$width = 0;

		foreach($array as $subarray){
			if(array_key_exists($key, $subarray)){
				if ($width < strlen($subarray[$key])){
					$width = strlen($subarray[$key]);
				}
			}
		}
		if ($width < strlen($key)){
			$width = strlen($key);
		}

		$width += 2;

		$cols_width[$key] = $width;

		return $cols_width[$key];
	}

	//Returns the table heading line from a set of keys
	function getTableHeading($colors,$keys,$array){
		
		$line = '|';

		foreach($keys as $position => $key){
			$width = getColWidth($array,$key);
			$line .= getField($colors[$position],$key,$width);
		}

		$line .= getNewLine();

		return $line;
	}

	//Returns an html new line tag
	function getNewLine(){
		return '<br/>';
	}

	//Returns the ascii form of a given array
	function getAsciiArray($array){
		
		$keys   = getKeySet($array);
		$cols   = sizeof($keys);
		$colors = generateColors($keys);
		
		$ascii  = '';
		$ascii .= getLineSeparator($cols,$array,$keys);
		$ascii .= getTableHeading($colors,$keys,$array);
		$ascii .= getLineSeparator($cols,$array,$keys);

		foreach($array as $subarray){
			$ascii .= getArrayLine($keys,$colors,$subarray,$array);
			$ascii .= getLineSeparator($cols,$array,$keys);
		}

		return $ascii;
	}

?>
