/* global document, gpConvertPTAO90, setTimeout, wp */

jQuery( document ).ready( function( $ ) {
	// Set array of Translation Sets.
	var translationSets = [];

	// Check if user is has GlotPress Admin previleges.
	var glotpressAdmin = gpConvertPTAO90.admin;

	// Check if user is has GlotPress Admin previleges.
	var gpUrlProject = gpConvertPTAO90.gp_url_project;

	console.log( gpUrlProject );

	// Add attribute 'data-locale' to each row.
	$( 'table.gp-table.translation-sets tr td:first-child a' ).each( function() {
		// Create a regular expression pattern with the variable
		var regexPattern = new RegExp( '^' + gpUrlProject + '(.*).*\/(.+)\/(.+)\/$' );

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

		$.ajax( {

			url: gpConvertPTAO90.ajaxurl,
			type: 'POST',
			data: {
				action: 'convert_project',
				projectPath: projectPath,
				locale: locale,
				slug: slug,
				nonce: gpConvertPTAO90.nonce,
			},
			beforeSend: function() {
				console.log( 'Ajax request is starting...' );
				button.attr( 'disabled', true ).removeClass( 'success fail' ).addClass( 'updating' ).children( 'span.label' ).text( wp.i18n.__( 'Syncing...', 'gp-convert-pt-ao90' ) );
			},

		} ).done( function( response, textStatus, jqXHR ) {
			// Set translation set data.
			var percent = response.data.percent + '%';
			var current = response.data.current;
			var fuzzy = response.data.fuzzy;
			var untranslated = response.data.untranslated;
			var waiting = response.data.waiting;

			// Set translation set row data.
			$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.percent' ).text( percent );
			$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.translated a' ).text( current );
			$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.fuzzy a' ).text( fuzzy );
			$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.untranslated a' ).text( untranslated );
			$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td.stats.waiting a' ).text( waiting );

			// Change button status to 'Synced'.
			button.children( 'span.icon.dashicons' ).hide().removeClass( 'dashicons-update' ).addClass( 'dashicons-yes' ).show();
			button.removeClass( 'updating' ).addClass( 'success' ).children( 'span.label' ).text( wp.i18n.__( 'Synced!', 'gp-convert-pt-ao90' ) );

			console.log( 'Ajax request has been completed (' + textStatus + '). Status: ' + jqXHR.status + ' ' + jqXHR.statusText );
			console.log( response );
			console.log( textStatus );
			console.log( jqXHR );
		} ).fail( function( jqXHR, textStatus ) {
			// Change button status to 'Failed'.
			button.children( 'span.icon.dashicons' ).hide().removeClass( 'dashicons-update' ).addClass( 'dashicons-warning' ).show();
			button.removeClass( 'updating' ).addClass( 'fail' ).children( 'span.label' ).text( wp.i18n.__( 'Failed!', 'gp-convert-pt-ao90' ) );

			// Show the Error notice.
			console.log( 'Ajax request has failed (' + textStatus + '). Status: ' + jqXHR.status + ' ' + jqXHR.statusText );
		} ).always( function() {
			// Change button status back to default.
			setTimeout(
				function() {
					button.children( 'span.icon.dashicons' ).hide().removeClass( 'dashicons-yes dashicons-warning' ).addClass( 'dashicons-update' ).show();
					button.attr( 'disabled', false ).removeClass( 'success fail' ).children( 'span.label' ).text( wp.i18n.__( 'Sync', 'gp-convert-pt-ao90' ) );
				},
				3000 // Wait 3 Seconds.
			);

			console.log( 'Ajax end.' );
		} );
	}
} );
