/**
 * External dependencies
 */
import { render, unmountComponentAtNode } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { recordEvent } from '@woocommerce/tracks';

/**
 * Internal dependencies
 */
import PageTabs from './page-tabs';
import PresetsTab from './presets';
import { TRACKS_PREFIX } from './settings';

const loadTabHandler = ( defaultTabName, tabs ) => {
	/**
	 * Insert root DOM element for tabs root.
	 *
	 * @return {HTMLDivElement} The root element for workflow tabs.
	 */
	const insertTabsRootDomElement = () => {
		const rootElement = document.createElement( 'div' );
		rootElement.setAttribute( 'id', 'automatewoo-workflow-tabs-root' );
		const headerEnd = document.querySelector(
			'#wpbody-content .wrap > h2.screen-reader-text'
		);
		headerEnd.parentNode.insertBefore( rootElement, headerEnd );
		return rootElement;
	};

	const hackyUpdateAllWorkflowsTabVisibility = ( tabName ) => {
		const elements = [
			document.querySelector( '#wpbody-content .subsubsub' ),
			document.querySelector( '#wpbody-content #posts-filter' ),
		];
		const display = tabName === defaultTabName ? 'block' : 'none';

		elements.forEach( ( item ) => {
			item.style.display = display;
		} );
	};

	const onTabSelect = ( tabName ) => {
		currentTab = tabName;
		// Set the hash to remember the open tab
		window.location.hash = tabName;
		hackyUpdateAllWorkflowsTabVisibility( tabName );
		recordTracksTabViewEvent( tabName );
	};

	const getTabContent = ( tab ) => {
		maybeHideWelcomeNotice();
		if ( tab.name === 'presets' ) {
			return <PresetsTab />;
		}
	};

	const getHash = () => {
		return window.location.hash.substr( 1 );
	};

	const renderPageTabs = ( tabName ) => {
		// If the tab doesn't exist use current or default
		if ( ! tabs.find( ( tab ) => tab.name === tabName ) ) {
			tabName = currentTab || defaultTabName;
		}

		currentTab = tabName;

		render(
			<PageTabs
				tabs={ tabs }
				onSelect={ onTabSelect }
				initialTabName={ tabName }
			>
				{ getTabContent }
			</PageTabs>,
			tabsRootEl
		);

		recordTracksTabViewEvent( tabName );
		hackyUpdateAllWorkflowsTabVisibility( tabName );
	};

	const recordTracksTabViewEvent = ( tab ) => {
		recordEvent( TRACKS_PREFIX + 'workflow_tab_view', { tab } );
	};

	const handleHashChange = () => {
		const tabName = getHash();

		if ( tabName === currentTab ) {
			// Tab name has not changed
			return;
		}

		// Hack: Unmount and rerender tabs because we can't programmatically change the current tab
		unmountComponentAtNode( tabsRootEl );
		renderPageTabs( tabName );
	};

	/**
	 * Hide WelcomeNotice when we are in Presets tab.
	 */
	const maybeHideWelcomeNotice = () => {
		const welcomeNotice = document.querySelector(
			'.automatewoo-welcome-notice'
		);

		if ( ! welcomeNotice ) {
			return;
		}

		if ( getHash() === 'presets' ) {
			welcomeNotice.style.display = 'none';
		} else {
			welcomeNotice.style.display = 'block';
		}
	};

	// Init
	let currentTab;
	const tabsRootEl = insertTabsRootDomElement();
	window.addEventListener( 'hashchange', handleHashChange, false );
	renderPageTabs( getHash() );
};

if ( /edit.php.+post_type=aw_workflow/.test( window.location.href ) ) {
	document.addEventListener(
		'DOMContentLoaded',
		() => {
			loadTabHandler( 'workflows', [
				{
					name: 'workflows',
					title: __( 'My workflows', 'automatewoo' ),
				},
				{
					name: 'presets',
					title: __( 'Browse presets', 'automatewoo' ),
				},
			] );
		},
		false
	);
}
