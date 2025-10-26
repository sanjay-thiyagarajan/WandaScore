<?php

namespace MediaWiki\Extension\WandaScore\Hooks;

use MediaWiki\MediaWikiServices;
use OutputPage;
use Skin;

class PageDisplayHooks {

	/**
	 * Hook to add WandaScore tile to pages
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ): void {
		$title = $out->getTitle();

		// Don't show on special pages
		if ( !$title || $title->isSpecialPage() ) {
			return;
		}

		$config = MediaWikiServices::getInstance()->getMainConfig();
		$showTile = $config->get( 'WandaScoreShowTile' );
		$enabledNamespaces = $config->get( 'WandaScoreNamespaces' );

		// Check if tile is enabled and namespace is allowed
		if ( !$showTile || !in_array( $title->getNamespace(), $enabledNamespaces ) ) {
			return;
		}

		// Add the score tile module
		$out->addModules( 'ext.wandascore.tile' );

		// Pass page title to JavaScript
		$out->addJsConfigVars(
			[
			'wgWandaScorePageTitle' => $title->getPrefixedText(),
			'wgWandaScorePageId' => $title->getArticleID()
			]
		);
	}
}
