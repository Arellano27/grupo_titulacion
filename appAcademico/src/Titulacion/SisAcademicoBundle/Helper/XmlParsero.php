<?php
namespace Titulacion\SisAcademicoBundle\helper;

class XmlParsero{
   var $arrOutput = array( );
   var $resParser;
   var $strXmlData;
  
	function parse( $strInputXML )	{
		$this->resParser = xml_parser_create ( "ISO-8859-1" );
		xml_set_object( $this->resParser, $this );
		xml_set_element_handler( $this->resParser, "tagOpen", "tagClosed" );
		
		xml_set_character_data_handler( $this->resParser, "tagData" );
		
		$strInputXML = utf8_encode( $strInputXML );
		
		$this->strXmlData = xml_parse( $this->resParser, $strInputXML );
		if( !$this->strXmlData )	{
			$lines = preg_split( "/\n/", $strInputXML );
			$text = "";
			for ( $c = 0; $c < count( $lines ); $c ++ )
				$text .= "\n" . ( $c + 1 ) . ".\t" . $lines[$c];
			
			die( sprintf( 
				"XML error: %s at line %d: %s", 
				xml_error_string( xml_get_error_code( $this->resParser ) ),
				xml_get_current_line_number( $this->resParser ),
				"<pre>" . htmlspecialchars( $text ) . "</pre>"
				)
			);
		}
					  
		xml_parser_free( $this->resParser );
		return $this->arrOutput;
	}
	
	function tagOpen( $parser, $name, $attrs )	{
		$tag = array( "name" => $name, "attrs" => $attrs );
		array_push( $this->arrOutput, $tag );
	}
	
	function tagData( $parser, $tagData )	{
	   if( trim( $tagData ) )	{
		   if( isset( $this->arrOutput[count( $this->arrOutput ) - 1]['tagData'] ) )
			   $this->arrOutput[count( $this->arrOutput ) - 1]['tagData'] .= $tagData;
		   else
			   $this->arrOutput[count( $this->arrOutput ) - 1]['tagData'] = $tagData;
	   }
	}
	
	function tagClosed( $parser, $name )	{
		$this->arrOutput[count( $this->arrOutput ) - 2]['children'][] = $this->arrOutput[count( $this->arrOutput ) - 1];
		array_pop( $this->arrOutput );
	}
}


?>
