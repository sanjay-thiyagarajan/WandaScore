<?php

namespace MediaWiki\Extension\WandaScore\Hooks;

use DatabaseUpdater;

class SchemaHooks {

	/**
	 * Hook to add database tables
	 *
	 * @param DatabaseUpdater $updater
	 */
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ): void {
		$base = dirname( __DIR__, 2 );
		$dbType = $updater->getDB()->getType();

		// Determine the correct SQL file based on database type
		if ( $dbType === 'postgres' ) {
			$sqlFile = "{$base}/sql/postgres/wandascore.sql";
		} elseif ( $dbType === 'sqlite' ) {
			$sqlFile = "{$base}/sql/sqlite/wandascore.sql";
		} else {
			// MySQL/MariaDB (default)
			$sqlFile = "{$base}/sql/wandascore.sql";
		}

		// Add the wandascore table
		$updater->addExtensionTable( 'wandascore', $sqlFile );
	}
}
