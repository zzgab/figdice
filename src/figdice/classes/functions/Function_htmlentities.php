<?php
/**
 * @author Gabriel Zerbib <gabriel@figdice.org>
 * @copyright 2004-2015, Gabriel Zerbib.
 * @version 2.1.2
 * @package FigDice
 *
 * This file is part of FigDice.
 * http://figdice.org/
 */

namespace figdice\classes\functions;

use figdice\classes\Context;
use figdice\FigFunction;
use figdice\exceptions\FunctionCallException;

class Function_htmlentities implements FigFunction {
    /**
     * Function's arguments:
     *  string to escape
     *
     * @param Context $context
     * @param integer $arity
     * @param array $arguments
     * @return mixed|string
     * @throws FunctionCallException
     */
	public function evaluate(Context $context, $arity, $arguments) {
		if($arity != 1) {
                    // @codeCoverageIgnoreStart
		    throw new FunctionCallException(
			'htmlentities',
			'Expects exactly 1 argument.',
			$context->getFilename(),
			$context->tag->getLineNumber()
		    );
                    // @codeCoverageIgnoreEnd
		}

		$string = $arguments[0];
		return htmlentities($string);
	}
}
