<?php

namespace ATD\CruiseFactory\Services\WordPress\Plugins\ContactForm7;

use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;

class EnquiryHandler {
	public function hydrateFormData( array $posted_data ): array {
		if ( ! isset( $posted_data['atd-cfi-enquiry-departure-id'] ) || ! isset( $posted_data['atd-cfi-enquiry-departure-type'] ) ) {
			return $posted_data;
		}

		/** @var Entity\SpecialDeparture $specialDeparture */
		if ( $posted_data['atd-cfi-enquiry-departure-type'] === 'special' ) {
			$feed             = new Feed\SpecialDeparture();
			$specialDeparture = $feed->getEntityManager()->getMapper( $feed->getEntity() )->find( $posted_data['atd-cfi-enquiry-departure-id'] );
			$departure        = $specialDeparture->getSailingDate();

			$posted_data['atd-cfi-enquiry-departure-name'] = $specialDeparture->getSpecial()->getName();
			$posted_data['atd-cfi-enquiry-external-id']    = $specialDeparture->getSpecial()->getId();
			$posted_data['atd-cfi-enquiry-pricing']        = 'Enquiry only';

			$posted_data = match ( true ) {
				isset( $posted_data['atd-cfi-enquiry-lead-price'] ) => $this->getSpecialLeadPrice( $specialDeparture, $posted_data ),
				isset( $posted_data['atd-cfi-enquiry-cabin-price'] ) => $this->getSpecialCabinPrice( $specialDeparture, $posted_data ),
				default => $posted_data
			};
		} else {
			$feed      = new Feed\Departure();
			$departure = $feed->getEntityManager()->getMapper( $feed->getEntity() )->find( $posted_data['atd-cfi-enquiry-departure-id'] );

			$posted_data['atd-cfi-enquiry-departure-name'] = $departure->getCruise()->getName();
			$posted_data['atd-cfi-enquiry-external-id']    = $departure->getCruise()->getId();
			$posted_data['atd-cfi-enquiry-pricing']        = 'Enquiry only';

			$posted_data = match ( true ) {
				isset( $posted_data['atd-cfi-enquiry-cabin-price'] ) => $this->getCabinPrice( $departure, $posted_data ),
				isset( $posted_data['atd-cfi-enquiry-request-cabin'] ) => $this->getRequestPrice( $departure, $posted_data ),
				default => $posted_data
			};
		}

		$posted_data['atd-cfi-enquiry-destination']    = $departure->getCruise()->getDestination()->getName();
		$posted_data['atd-cfi-enquiry-duration']       = $departure->getCruise()->getDuration();
		$posted_data['atd-cfi-enquiry-cruise-line']    = $departure->getCruise()->getCruiseLine()->getName();
		$posted_data['atd-cfi-enquiry-ship']           = $departure->getCruise()->getShip()->getName();
		$posted_data['atd-cfi-enquiry-departure-date'] = $departure->getSailingDate()->format( get_option( 'date_format' ) );

		return $posted_data;
	}

	private function getRequestPrice( Entity\Departure $departure, array $posted_data ): array {
		$feed = new Feed\Cabin();
		if ( $cabin = $feed->getEntityManager()->getMapper( $feed->getEntity() )->find( (int) $posted_data['atd-cfi-enquiry-request-cabin'] ) ) {
			$posted_data['atd-cfi-enquiry-cabin-type'] = $cabin->getName();
			unset( $posted_data['atd-cfi-enquiry-request-cabin'] );
		}

		return $posted_data;
	}

	private function getSpecialLeadPrice( Entity\SpecialDeparture $specialDeparture, array $posted_data ): array {
		$posted_data['atd-cfi-enquiry-cabin-type'] = $posted_data['atd-cfi-enquiry-lead-price'];

		$method = 'get' . ucfirst( $posted_data['atd-cfi-enquiry-lead-price'] );
		if ( method_exists( $specialDeparture->getSpecial()->getSpecialLeadPrice(), $method ) ) {
			$posted_data['atd-cfi-enquiry-pricing'] = $specialDeparture->getSpecial()->getCurrency()->getSign() . number_format( $specialDeparture->getSpecial()->getSpecialLeadPrice()->{$method}(), 2 );
		}

		unset( $posted_data['atd-cfi-enquiry-lead-price'] );

		return $posted_data;
	}

	private function getSpecialCabinPrice( Entity\SpecialDeparture $specialDeparture, array $posted_data ): array {
		/** @var Entity\SpecialPrice $specialPrice */
		if ( ! $specialPrice = $specialDeparture->getSpecial()->getSpecialPrices()->filter(
			fn( $p ) => $p->getId() === (int) $posted_data['atd-cfi-enquiry-cabin-price']
		)->first() ) {
			return $posted_data;
		}

		$posted_data['atd-cfi-enquiry-cabin-type'] = $specialPrice->getCabin()->getName();
		$posted_data['atd-cfi-enquiry-pricing']    = $specialPrice->getCurrency()->getSign() . number_format( $specialPrice->getPrice(), 2 );

		unset( $posted_data['atd-cfi-enquiry-cabin-price'] );

		return $posted_data;
	}

	private function getCabinPrice( Entity\Departure $departure, array $posted_data ): array {
		/** @var Entity\CruisePrice $price */
		if ( ! $price = $departure->getCruisePrices()->filter(
			fn( $p ) => $p->getId() === (int) $posted_data['atd-cfi-enquiry-cabin-price']
		)->first() ) {
			return $posted_data;
		}

		$posted_data['atd-cfi-enquiry-cabin-type'] = $price->getCabin()->getName();
		$posted_data['atd-cfi-enquiry-pricing']    = $price->getCurrency() . number_format( $price->getPrice(), 2 );

		unset( $posted_data['atd-cfi-enquiry-cabin-price'] );

		return $posted_data;
	}

	public function insertHiddenFields(): string {
		$departure_type = get_query_var( 'departure_type' );
		$departure_id   = get_query_var( 'departure_id', 0 );
		$pax            = get_query_var( 'pax' );
		$cabin          = '';
		if ( $var = get_query_var( 'request_cabin', false ) ) {
			$cabin = '<input type="hidden" name="atd-cfi-enquiry-request-cabin" value="' . $var . '">';
		} else if ( $var = get_query_var( 'lead_price', false ) ) {
			$cabin = '<input type="hidden" name="atd-cfi-enquiry-lead-price" value="' . $var . '">';
		} else if ( $var = get_query_var( 'cabin_price', false ) ) {
			$cabin = '<input type="hidden" name="atd-cfi-enquiry-cabin-price" value="' . $var . '">';
		}

		return <<<HTML
<input type="hidden" name="atd-cfi-enquiry-departure-type" value="$departure_type">
<input type="hidden" name="atd-cfi-enquiry-departure-id" value="$departure_id">
<input type="hidden" name="atd-cfi-enquiry-pax" value="$pax">
$cabin
HTML;
	}
}