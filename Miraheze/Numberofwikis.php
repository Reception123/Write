<?php
/**
 * Number of wikis(SiteMatrix)

class NumberOfWikis {

	public static function assignValue( &$parser, &$cache, &$magicWordId, &$ret ) {
		global $wgMemc;
		if ( $magicWordId == 'NUMBEROFWIKIS' ) {
			$key = wfMemcKey( 'metawiki', 'numberofwikis' );
			$data = $wgMemc->get( $key );
			if ( $data != '' ) {
				// Cache
				wfDebugLog(
					'Number Of Wikis',
					'Got the amount of wikis from memcached'
				);
				// return value
				$ret = $data;
			} else {
				// Not cached → have to fetch it from the database
				$dbr = wfGetDB( DB_SLAVE );
				$res = $dbr->select(
					'cw_wikis',
					'COUNT(*) AS count',
					__METHOD__
				);
				wfDebugLog( 'Number Of Wikis', 'Got the amount of wikis from DB' );
				foreach ( $res as $row ) {
					// Store the count in cache...
					// (86400 = seconds in a day)
					$wgMemc->set( $key, $row->count, 86400 );
					// ...and return the value to the user
					$ret = $row->count;
				}
			}
		}
		return true;
	}
	public static function variableIds( &$variableIds ) {
		$variableIds[] = 'NUMBEROFWIKIS';
		return true;
	}
