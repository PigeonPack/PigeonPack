<?php
/**
 * Converts HTML email to Plain Text email
 *
 * @package Pigeon Pack
 * @since 0.0.1
 */
 
/******************************************************************************
 * Copyright (c) 2010 Jevon Wright and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Jevon Wright - initial API and implementation
 ****************************************************************************/

if ( !function_exists( 'convert_html_to_text' ) ) {
		
	/**
	 * Tries to convert the given HTML into a plain text format - best suited for
	 * e-mail display, etc.
	 *
	 * <p>In particular, it tries to maintain the following features:
	 * <ul>
	 *   <li>Links are maintained, with the 'href' copied over
	 *   <li>Information in the &lt;head&gt; is lost
	 * </ul>
	 *
	 * @param string $html the input HTML
	 * @return string the HTML converted, as best as possible, to text
	 */
	function convert_html_to_text( $html ) {
		
		$html = fix_newlines( $html );
		
		$doc = new DOMDocument();
		if ( !@$doc->loadHTML( $html ) )
			throw new Html2TextException( 'Could not load HTML - badly formed?', $html );
		
		$output = iterate_over_node( $doc );
		
		// remove leading and trailing spaces on each line
		$output = preg_replace( '/[ \t]*\n[ \t]*/im', "\n", $output );
		
		// remove leading and trailing whitespace
		$output = trim( $output );
		
		return $output;
		
	}

}

if ( !function_exists( 'fix_newlines' ) ) {
		
	/**
	 * Unify newlines; in particular, \r\n becomes \n, and
	 * then \r becomes \n. This means that all newlines (Unix, Windows, Mac)
	 * all become \ns.
	 *
	 * @param string $text text with any number of \r, \r\n and \n combinations
	 * @return string the fixed text
	 */
	function fix_newlines( $text ) {
	
		// replace \r\n to \n
		$text = str_replace( "\r\n", "\n", $text );
		// remove \rs
		$text = str_replace( "\r", "\n", $text );
	
		return $text;
		
	}
	
}

if ( !function_exists( 'next_child_name' ) ) {
			
	/**
	 * Returns the next child node name
	 *
	 * @param object $node The current node
	 * @return string the next node name
	 */
	function next_child_name( $node ) {
	
		// get the next child
		$nextNode = $node->nextSibling;
		
		while ( NULL != $nextNode ) {
			
			if ( $nextNode instanceof DOMElement )
				break;
				
			$nextNode = $nextNode->nextSibling;
		}
		
		$nextName = NULL;
		
		if ( $nextNode instanceof DOMElement && NULL != $nextNode )
			$nextName = strtolower( $nextNode->nodeName );
	
		return $nextName;
			
	}

}

if ( !function_exists( 'prev_child_name' ) ) {
			
	/**
	 * Returns the previous child node name
	 *
	 * @param object $node The current node
	 * @return string the previous node name
	 */
	function prev_child_name( $node ) {
		
		// get the previous child
		$nextNode = $node->previousSibling;
		
		while ( NULL != $nextNode ) {
			
			if ( $nextNode instanceof DOMElement )
				break;
			
			$nextNode = $nextNode->previousSibling;
			
		}
		
		$nextName = NULL;
		if ( $nextNode instanceof DOMElement && NULL != $nextNode )
			$nextName = strtolower( $nextNode->nodeName );
			
		return $nextName;
		
	}

}

if ( !function_exists( 'iterate_over_node' ) ) {
			
	/**
	 * Iterates over the node
	 *
	 * @param object $node The current node
	 * @return string text replacement of the HTML node
	 */
	function iterate_over_node( $node ) {
		
		if ( $node instanceof DOMText )
			return preg_replace( '/\s+/im', ' ', $node->wholeText );
			
		if ( $node instanceof DOMDocumentType )
			return '';
			
		$nextName = next_child_name( $node );
		$prevName = prev_child_name( $node );
	
		$name = strtolower( $node->nodeName );
	
		// start whitespace
		switch ( $name ) {
			
			case 'hr': //http://tools.ietf.org/html/rfc5322#section-2.1.1
				return "------------------------------------------------------------------------------\n";
	
			case 'style':
			case 'head':
			case 'title':
			case 'meta':
			case 'script':
				// ignore these tags
				return '';
	
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
				// add two newlines
				$output = "\n\n";
				break;
	
			case 'p':
			case 'div':
				// add one line
				$output = "\n";
				break;
	
			default:
				// print out contents of unknown tags
				$output = '';
				break;
				
		}
	
		for ( $i = 0; $i < $node->childNodes->length; $i++ ) {
			
			$n = $node->childNodes->item( $i );
			
			$text = iterate_over_node( $n );
			
			$output .= $text;
				
		}
	
		// end whitespace
		switch ( $name ) {
			
			case 'style':
			case 'head':
			case 'title':
			case 'meta':
			case 'script':
				// ignore these tags
				return '';
	
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
				$output .= "\n\n";
				break;
	
			case 'p':
			case 'br':
				// add one line
				if ( 'div' != $nextName )
					$output .= "\n";
				break;
	
			case 'div':
				// add one line only if the next child isn't a div
				if ( 'div' != $nextName && NULL != $nextName )
					$output .= "\n";
				break;
	
			case 'a':
				// links are returned in [text](link) format
				$href = $node->getAttribute( 'href' );
				
				if ( NULL === $href ) {
					
					// it doesn't link anywhere
					if ( NULL != $node->getAttribute( 'name' ) )
						$output = "[$output]";
						
				} else {
					
					if ( $href === $output ) // link to the same address: just use link
						$output;
					else // replace it
						$output = "[$output]($href)";
					
				}
	
				// does the next node require additional whitespace?
				switch ( $nextName ) {
					
					case 'h1': case 'h2': case 'h3': case 'h4': case 'h5': case 'h6':
						$output .= "\n";
						break;
						
				}
	
			default:
				// do nothing
				
		}
		
		return $output;
		
	}

}

if ( !class_exists( 'Html2TextException' ) ) {
	
	/**
	 * Class extends Exception to output thrown errors
	 */
	class Html2TextException extends Exception {
	
		/**
		 * String of more info
		 * @var string $more_info String of more info
		 */
		var $more_info;
			
		/**
		 * Returns the next child node name
		 *
		 * @param string $message Error message
		 * @param string $more_info String of more infos
		 */
		public function __construct( $message = '', $more_info = '' ) {
			
			parent::__construct( $message );
			$this->more_info = $more_info;
			
		}
			
	}

}