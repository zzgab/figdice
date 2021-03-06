<?php
/**
 * @author Gabriel Zerbib <gabriel@figdice.org>
 * @copyright 2004-2017, Gabriel Zerbib.
 * @version 3
 * @package FigDice
 *
 * This file is part of FigDice.
 */

namespace figdice\classes\functions;

use figdice\classes\Context;
use figdice\exceptions\XMLParsingException;
use figdice\FigFunction;

/**
 * Class Function_xml
 *
 * This class exposes the "xml( )" function to FigDice expressions.
 * This function parses a string as an XML document, and returns a DOMXPath instance
 * bound to this DOMDocument.
 * If the XML parsing fails, a warning is triggered.
 * The returned DOMXPath reference can be used in subsequent `evaluate` calls,
 * through the "xpath( )" Fig function.
 *
 * A second string argument can be passed, to be used if the string cannot evaluate to valid XML due
 * to the lack of a root node, in which case this 2nd arg is used as name of root tag.
 */
class Function_xml implements FigFunction {

    /**
     * @param Context $context
     * @param integer $arity
     * @param array $arguments
     * @return \DOMXPath
     * @throws XMLParsingException
     */
    public function evaluate(Context $context, $arity, $arguments) {
		$xmlString = $arguments[0];
		$xml = new \DOMDocument();


		// We suppose the specified string is valid XML, which means that it is supposed to have a wrapping root tag.
        // Or, the user can specify an artificial one explicitly, as the second argument to the xml() function.
        $explicitRoot = '';
        if ($arity >= 2) {
            // If artificial root, we just wrap our string with the specified <tagname>  </tagname>
            $explicitRoot =  $arguments[1];
            $xmlString = '<'.$explicitRoot.'>' . $arguments[0] . '</'.$explicitRoot.'>';
        }

		$successParse = @ $xml->loadXML($xmlString, LIBXML_NOENT);
		if (! $successParse && ! $explicitRoot) {
            // If a root was not artificially specified, and the parsing fails,
            // let's add our own root, calling it "xml" by default.
            $explicitRoot = 'xml';
            $xmlString    = '<' . $explicitRoot . '>' . $arguments[0] . '</' . $explicitRoot . '>';

            // This time we let a warning be fired in case of invalid xml.
            $successParse = @ $xml->loadXML($xmlString, LIBXML_NOENT);
        }
        // If we failed to parse. whether after adding a default implicit root, or simply because even with an
        // implicit or explicit root the string is no valid XML, raise an error.
        if (! $successParse) {
            $xmlError = libxml_get_last_error();
            throw new XMLParsingException('xml() function: ' . $xmlError->message,
                $context->tag->getLineNumber());
		}
		$xpath = new \DOMXPath($xml);
		return $xpath;
	}
}