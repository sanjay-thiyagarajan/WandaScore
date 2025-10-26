<?php

namespace MediaWiki\Extension\WandaScore\Hooks;

use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;
use MediaWiki\User\User;
use WikiPage;

class PageSaveHooks {

	/**
	 * Hook to trigger scoring when a page is saved
	 *
	 * @param WikiPage $wikiPage
	 * @param User $user
	 * @param string $summary
	 * @param int $flags
	 * @param RevisionRecord $revisionRecord
	 * @param EditResult $editResult
	 */
	public static function onPageSaveComplete(
		WikiPage $wikiPage,
		$user,
		$summary,
		$flags,
		$revisionRecord,
		$editResult
	): void {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$autoReview = $config->get( 'WandaScoreAutoReview' );
		$enabledNamespaces = $config->get( 'WandaScoreNamespaces' );

		$title = $wikiPage->getTitle();

		// Check if auto-review is enabled and namespace is allowed
		if ( !$autoReview || !in_array( $title->getNamespace(), $enabledNamespaces ) ) {
			return;
		}

		// Defer the scoring to a job queue to avoid slowing down the save
		$jobQueueGroup = MediaWikiServices::getInstance()->getJobQueueGroup();
		$jobQueueGroup->push(
			new \MediaWiki\Extension\WandaScore\Jobs\ScorePageJob(
				$title,
				[
					'pageId' => $title->getArticleID(),
					'pageTitle' => $title->getPrefixedText()
				]
			)
		);
	}
}
