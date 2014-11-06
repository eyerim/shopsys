<?php

namespace SS6\ShopBundle\Model\Grid\Ordering\Exception;

use Exception;

class OrderingEntityNotSupportException extends Exception implements OrderingException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
