<?php

namespace ATD\CruiseFactory\Controller\Helper;

use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Post\Port;
use ATD\CruiseFactory\Services\Data\Collections\Collection;
use DateInterval;
use DateTimeInterface;
use WP_Query;

trait ItineraryTrait {
	private function formatItinerary( DateTimeInterface $dateTime, $departure ): string {
		if ( $departure instanceof Entity\SpecialDeparture ) {
			$special   = $departure->getSpecial();
			$departure = $departure->getSailingdate();
		}

		$html = $this->startTable();

		if ( ! empty( $special ) ) {
			$html = $this->formatSpecialPreCruiseRows( $html, $special, $dateTime );
		}
		$html = $this->formatCruiseRows( $html, $departure, $dateTime );
		if ( ! empty( $special ) ) {
			$html = $this->formatSpecialPostCruiseRows( $html, $special, $dateTime );
		}

		$html .= $this->endTable();

		return $html;
	}

	private function formatSpecialPreCruiseRows( string $html, Entity\Special $special, DateTimeInterface $dateTime ): string {
		$activities = $special->getSpecialPreItinerary()->uasort( function ( $a, $b ) {
			return (int) $a->getOrder() > (int) $b->getOrder();
		} );

		if ( $activities->count() === 0 ) {
			return $html;
		}

		$last = $activities->last();
		if ( $last ) {
			$dateTime->sub( new DateInterval( 'P' . ( $last->getDay() ) . 'D' ) );
		}
		$html .= '<tr><th colspan="4">Pre-Cruise Itinerary</th></tr>';

		return $this->formatSpecialRows( $html, $activities, $dateTime );
	}

	private function formatSpecialPostCruiseRows( string $html, Entity\Special $special, DateTimeInterface $dateTime ): string {
		$activities = $special->getSpecialPostItinerary()->uasort( function ( $a, $b ) {
			return (int) $a->getOrder() > (int) $b->getOrder();
		} );

		if ( $activities->count() === 0 ) {
			return $html;
		}

		$dateTime->sub( new DateInterval( 'P1D' ) );
		$html .= '<tr><th colspan="4">Post-Cruise Itinerary</th></tr>';

		return $this->formatSpecialRows( $html, $activities, $dateTime );
	}

	private function formatSpecialRows( string $html, Collection $activities, DateTimeInterface $dateTime ): string {
		foreach ( $activities as $activity ) {
			$html .= $this->formatDayRow( $dateTime->add( new DateInterval( 'P1D' ) ), $activity->getActivity(), '', '' );
		}

		return $html;
	}

	private function formatCruiseRows( string $html, Entity\Departure $departure, DateTimeInterface $dateTime ): string {
		$query = new WP_Query( [
			'post_type'      => Port::$postType,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_query'     => [
				[
					'compare' => 'IN',
					'key'     => Feed\Port::$metaKeyId,
					'value'   => $departure->getCruise()->getItinerary()->map( function ( $a ) {
						return $a->getPort()->getId();
					} )->toArray()
				]
			]
		] );

		$ports = [];
		if ( $query->post_count > 0 ) {
			foreach ( $query->posts as $port ) {
				$id           = get_metadata_raw( 'post', $port->ID, Feed\Port::$metaKeyId, true );
				$ports[ $id ] = $port;
			}
			unset( $query );
		}

		$day = 0;
		$dateTime->sub( new DateInterval( 'P1D' ) );

		$html .= '<tr><th colspan="4">Cruise Itinerary</th></tr>';
		foreach ( $departure->getCruise()->getItinerary() as $activity ) {
			$port = '<a data-action="atd-cfi-popover#json" href="/wp-json/wp/v2/' . Port::$postType . '?include[]=' . $ports[ $activity->getPort()->getId() ]->ID . '">' . $ports[ $activity->getPort()->getId() ]->post_title . '</a>';
			if ( ( $day + 1 ) !== $activity->getDay() ) {
				for ( $i = $day + 1, $c = $activity->getDay(); $i < $c; $i ++ ) {
					$html .= $this->formatDayRow( $dateTime->add( new DateInterval( 'P1D' ) ), 'At Sea', '', '' );
				}
			}

			$html .= $this->formatDayRow( $dateTime->add( new DateInterval( 'P1D' ) ), $port, $activity->getArrive(), $activity->getDepart() );
			$day  = $activity->getDay();
		}

		return $html;
	}

	private function startTable(): string {
		return <<<HTML
<table class="atd-cfi__itinerary-table wp-block-table is-style-stripes" data-controller="atd-cfi-popover">
	<thead>
	<tr>
		<th>Date</th>
		<th style="text-align: left;">Port</th>
		<th>Arrive</th>
		<th>Depart</th>
	</tr>
	</thead>
	<tbody>
HTML;
	}

	private function endTable(): string {
		return <<<HTML
	</tbody>
</table>
HTML;
	}

	private function formatDayRow( DateTimeInterface $dateTime, string $activity, string $arrive, string $depart ): string {
		return <<<HTML
	<tr>
		<td>{$dateTime->format( 'd M y' )}</td>
		<td>$activity</td>
		<td>$arrive</td>
		<td>$depart</td>
	</tr>
HTML;

	}
}