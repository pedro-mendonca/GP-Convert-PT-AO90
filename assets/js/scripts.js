/* global document, gpConvertPTAO90, setTimeout, wp, wpApiSettings */

jQuery( document ).ready( function( $ ) {
	// Set array of Translation Sets.
	var translationSets = [];

	// Check if user is has GlotPress Admin previleges.
	var glotpressAdmin = gpConvertPTAO90.admin;

	// Check if user is has GlotPress Admin previleges.
	var gpUrlProject = gpConvertPTAO90.gp_url_project;

	// Add attribute 'data-locale' to each row.
	$( 'table.gp-table.translation-sets tr td:first-child a' ).each( function() {
		// Create a regular expression pattern with the variable
		var regexPattern = new RegExp( '^' + gpUrlProject + '(.*).*/(.+)/(.+)/$' );

		/**
		 * Check for Locale and Slug in the link.
		 * Example: ../glotpress/projects/plugins/hello-dolly/pt/default/
		 */
		var match = $( this ).attr( 'href' ).match( regexPattern );
		var locale = match[2]; // 'pt'.
		var slug = match[3]; // 'default'.

		// Get edit status of the variant.
		var editable = gpConvertPTAO90.edit;

		// Add Locale to the array.
		translationSets.push( locale );

		$( this ).closest( 'tr' ).attr( 'data-locale', locale );
		$( this ).closest( 'tr' ).attr( 'data-slug', slug );

		// Add class 'variant' to 'pt-ao90' and 'data-editable' status.
		if ( locale === 'pt-ao90' ) {
			// Add editable status.
			$( this ).closest( 'tr' ).attr( 'data-editable', editable );
		}
	} );

	// If both root and variant exist, add 'variant' class for tablesorter cssChildRow.
	if ( translationSets.includes( 'pt' ) && translationSets.includes( 'pt-ao90' ) ) {
		// Add class for tablesorter cssChildRow.
		$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"] td:first-child a' ).closest( 'tr' ).addClass( 'variant' );

		// Add span for the conversion meta.
		$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td:first-child' ).children().last().after( '<span class="gp-convert-pt-ao90-update"></span>' );

		// Add editable icon.
		$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td:first-child span.gp-convert-pt-ao90-update' ).html( '<span class="translation-set-icon edit-status dashicons dashicons-lock"></span>' );

		if ( glotpressAdmin ) {
			$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td:first-child span.gp-convert-pt-ao90-update' ).children().last().after( '<button class="button is-small gp-convert-pt-ao90-update-button"><span class="dashicons dashicons-update icon"></span><span class="label">' + wp.i18n.__( 'Sync', 'gp-convert-pt-ao90' ) + '</span></button>' );
		}
	}

	// Action on click Convert button.
	$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td:first-child span.gp-convert-pt-ao90-update button.gp-convert-pt-ao90-update-button' ).on( 'click', function() {
		var locale = $( this ).closest( 'tr' ).attr( 'data-locale' );
		var slug = $( this ).closest( 'tr' ).attr( 'data-slug' );

		// Create a regular expression pattern with the variable
		var regexPattern = new RegExp( '^' + gpUrlProject + '(.*)' + '/' + locale + '/' + slug + '/$' );

		/**
		 * Check for project path the link.
		 * Example: ../glotpress/projects/plugins/hello-dolly/pt/default/
		 */
		var match = $( this ).closest( 'td' ).find( 'a' ).attr( 'href' ).match( regexPattern );
		var projectPath = match[1];

		convertProject( projectPath, locale, slug );
	} );

	// Override tablesorter settings to allow adding cssChildRow.
	$( '.translation-sets' ).tablesorter( {
		theme: 'glotpress',
		sortList: [ [ 2, 1 ] ],
		cssChildRow: 'variant', // Sets 'variant' row as child of the previous.
		headers: {
			0: {
				sorter: 'text',
			},
		},
	} );

	/**
	 * Convert project Root translation set to Variant translation set.
	 *
	 * @param {string} projectPath : Path ot the GP_Project.
	 * @param {string} locale      : Locale of the GP_Translation_Set.
	 * @param {string} slug        : Slug of the GP_Translation_Set.
	 */
	function convertProject( projectPath, locale, slug ) {
		var button = $( 'table.gp-table.translation-sets tr[data-locale="' + locale + '"][data-slug="' + slug + '"] td:first-child button.gp-convert-pt-ao90-update-button' );
		console.log( 'Clicked to convert project "' + projectPath + '" locale "' + locale + '/' + slug + '"' );

		button.attr( 'disabled', true ).removeClass( 'success fail' ).addClass( 'updating' ).children( 'span.label' ).text( wp.i18n.__( 'Syncing...', 'gp-convert-pt-ao90' ) );

		$.ajax( {

			url: wpApiSettings.root + 'gp-convert-pt-ao90/v1/translation-set/' + projectPath + '/' + locale + '/' + slug + '/-convert',
			type: 'POST',
			data: {
				_wpnonce: gpConvertPTAO90.nonce,
			},

			success: function( response ) {
				// Set translation set data.
				var percent = response.percent;
				var current = response.current;
				var fuzzy = response.fuzzy;
				var untranslated = response.untranslated;
				var waiting = response.waiting;

				// For compatibility with GP Toolbox where these columns might be shown.
				var old = response.old;
				var rejected = response.rejected;
				var warnings = response.warnings;

				// Check if GP Toolbox is available.
				var gpToolboxUpdateHighlight = wp.hooks.hasAction( 'gpToolboxUpdateHighlight', 'update_highlight_action' );

				// Check if bubble of more than 90% exist.
				var bubbleMoreThan90 = button.closest( 'td' ).children( 'span.bubble.morethan90' ).length;

				// Set translation set row data.
				$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.percent' ).text( percent + '%' );
				$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.translated a' ).text( current );
				$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.fuzzy a' ).text( fuzzy );
				$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.untranslated a' ).text( untranslated );
				$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.waiting a' ).text( waiting );

				// Do if GP Toolbox update highlight hook is available.
				if ( gpToolboxUpdateHighlight ) {
					console.log( 'GP Toolbox gpToolboxUpdateHighlight action is available:', gpToolboxUpdateHighlight );
					// Set translation set row data.
					$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.old a' ).text( old );
					$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.rejected a' ).text( rejected );
					$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.warnings a' ).text( warnings );

					// Trigger the Update Highlight function from GP Toolbox.
					wp.hooks.doAction( 'gpToolboxUpdateHighlight' );
				}

				// Add Bubble of more than 90% if currently doesn't exist.
				if ( percent >= 90 ) {
					console.log( 'Setting percentage in the morethan90 Bubble: ' + percent + '%' );

					// Update current Bubble.
					if ( bubbleMoreThan90 ) {
						console.log( 'Change Bubble from ' + button.closest( 'td' ).children( 'span.bubble.morethan90' ).text() + ' to ' + percent + '%' );
						$( button ).closest( 'td' ).children( 'span.bubble.morethan90' ).text( percent + '%' );

					// Add new Bubble.
					} else {
						console.log( 'Add Bubble ' + percent + '%' );

						$( '<span class="bubble morethan90" style="margin-left: 0.25em;">' + percent + '%' + '</span>' ).insertAfter( button.closest( 'td' ).find( 'strong' ) );
					}

				// Check if there is a bubble to remove.
				} else if ( bubbleMoreThan90 ) {
					// Remove Bubble.
					$( button ).closest( 'td' ).children( 'span.bubble.morethan90' ).remove();
				}

				// Change button status to 'Synced'.
				button.removeClass( 'updating' ).addClass( 'success' );
				button.children( 'span.icon.dashicons' ).hide().removeClass( 'dashicons-update' ).addClass( 'dashicons-yes' ).show();
				button.children( 'span.label' ).text( wp.i18n.__( 'Synced!', 'gp-convert-pt-ao90' ) );

				console.log( response );
			},

			error: function( response ) {
				// Change button status to 'Failed'.
				button.removeClass( 'updating' ).addClass( 'fail' );
				button.children( 'span.icon.dashicons' ).hide().removeClass( 'dashicons-update' ).addClass( 'dashicons-warning' ).show();
				button.children( 'span.label' ).text( wp.i18n.__( 'Failed!', 'gp-convert-pt-ao90' ) );

				// Show the Error notice.
				console.log( 'Failed to convert Translation Set.' );
				console.log( 'Error message:', response.responseJSON.message );
			},

			complete: function() {
				// Change button status back to default.
				setTimeout(
					function() {
						button.attr( 'disabled', false ).removeClass( 'success fail' );
						button.children( 'span.icon.dashicons' ).hide().removeClass( 'dashicons-yes dashicons-warning' ).addClass( 'dashicons-update' ).show();
						button.children( 'span.label' ).text( wp.i18n.__( 'Sync', 'gp-convert-pt-ao90' ) );
					},
					3000 // Wait 3 Seconds.
				);

				console.log( 'Request ended.' );
			},

		} );
	}
} );
