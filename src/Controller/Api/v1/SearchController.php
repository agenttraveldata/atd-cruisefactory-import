<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Post\Departure;
use ATD\CruiseFactory\Taxonomy\CruiseLine;
use ATD\CruiseFactory\Taxonomy\DepartureType;
use ATD\CruiseFactory\Taxonomy\Destination;
use ATD\CruiseFactory\Taxonomy\DisembarkPort;
use ATD\CruiseFactory\Taxonomy\Duration;
use ATD\CruiseFactory\Taxonomy\EmbarkPort;
use ATD\CruiseFactory\Taxonomy\Month;
use ATD\CruiseFactory\Taxonomy\PromoCode;
use ATD\CruiseFactory\Taxonomy\Ship;
use ATD\CruiseFactory\Taxonomy\SpecialType;
use DateTime;
use WP_Query;
use WP_REST_Request;
use WP_Term_Query;

class SearchController extends AbstractController {
	public function __construct() {
		$this->addRoute( '/search/options', [ $this, 'options' ] );
	}

	public function options( WP_REST_Request $request ): array {
		$taxQuery = [];
        $metaQuery[ Month::$name . '_baseline' ] = [
            'key'     => 'atd_cfi_sailing_date',
            'value'   => (new DateTime())->getTimestamp(),
            'type'    => 'NUMERIC',
            'compare' => '>='
        ];

		foreach ( $request->get_query_params() as $param => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			switch ( $param ) {
				case Month::$name . '_from':
				case Month::$name . '_to':
					if ( $dateValue = DateTime::createFromFormat( 'Ymd', $value . '01' ) ) {
						$dateCompare = '>=';
						if ( substr( $param, - 3 ) === '_to' ) {
							$dateCompare = '<=';
							$dateValue   = DateTime::createFromFormat( 'Ymd', $dateValue->format( 'Ymt' ) );
						}

						$dateValue->setTime( 0, 0 );

						if ( isset( $metaQuery[ Month::$name ] ) ) {
							$metaQuery[ Month::$name ]['compare'] = 'BETWEEN';

							if ( substr( $param, - 3 ) === '_to' ) {
								$metaQuery[ Month::$name ]['value'] = [
									$metaQuery[ Month::$name ]['value'],
									$dateValue->getTimestamp()
								];
							} else {
								$metaQuery[ Month::$name ]['value'] = [
									$dateValue->getTimestamp(),
									$metaQuery[ Month::$name ]['value']
								];
							}
						} else {
							$metaQuery[ Month::$name ] = [
								'key'     => 'atd_cfi_sailing_date',
								'value'   => $dateValue->getTimestamp(),
								'type'    => 'NUMERIC',
								'compare' => $dateCompare
							];
						}
					}
					break;
				case DepartureType::$name:
				case SpecialType::$name:
				case PromoCode::$name:
				case Destination::$name:
				case CruiseLine::$name:
				case Ship::$name:
				case EmbarkPort::$name:
				case DisembarkPort::$name:
				case Duration::$name:
					$taxQuery[ $param ] = [
						'taxonomy' => $param,
						'terms'    => $value,
						'field'    => 'slug',
					];
			}
		}

		if ( ! empty( $taxQuery ) || ! empty( $metaQuery ) ) {
			$wpQuery = [
				'post_type'      => Departure::$postType,
				'posts_per_page' => - 1,
				'nopaging'       => true,
				'no_found_rows'  => true,
				'post_status'    => 'publish',
				'fields'         => 'ids'
			];

			if ( ! empty( $taxQuery ) ) {
				$wpQuery['tax_query'] = [
					$taxQuery
				];
			}
			if ( ! empty( $metaQuery ) ) {
				$wpQuery['meta_query'] = $metaQuery;
			}

			$postQuery = new WP_Query( $wpQuery );
		}

		$terms = [];
		foreach ( get_object_taxonomies( Departure::$postType ) as $taxonomy ) {
			$atd = substr( $taxonomy, 0, 3 );

			if ( $atd === 'atd' ) {
				$termQueryParams = [
					'taxonomy'   => $taxonomy,
					'object_ids' => ! empty( $postQuery ) && $postQuery->post_count > 0 ? $postQuery->posts : null,
					'childless'  => true,
					'orderby'    => 'name',
					'order'      => 'ASC'
				];

				if ( $taxonomy === Month::$name ) {
					$termQueryParams = array_merge( $termQueryParams, [
						'orderby' => 'slug'
					] );
				}

				$termQuery          = new WP_Term_Query( $termQueryParams );
				$terms[ $taxonomy ] = [];
				if ( $termQuery->terms ) {
					foreach ( $termQuery->terms as $term ) {
						$terms[ $taxonomy ][ $term->slug ] = htmlspecialchars_decode( $term->name );
					}
				}
			}
		}

		return $terms;
	}
}