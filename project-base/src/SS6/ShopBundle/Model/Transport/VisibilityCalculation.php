<?php

namespace SS6\ShopBundle\Model\Transport;

class VisibilityCalculation {

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $allPayments
	 * @return boolean
	 */
	public function isVisible(Transport $transport, array $allPayments) {
		if (!$transport->isHidden()) {
			return $this->existsVisiblePaymentWithTransport($allPayments, $transport);
		} else {
			return false;
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $allPayments
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return boolean
	 */
	private function existsVisiblePaymentWithTransport(array $allPayments, Transport $transport) {
		foreach ($allPayments as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			if ($payment->isVisible() && $payment->getTransports()->contains($transport)) {
				return true;
			}
		}
		return false;
	}

}
