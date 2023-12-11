/* global document, gpConvertPTAO90, wp */

jQuery( document ).ready( function( $ ) {
	// Set array of Translation Sets.
	var translationSets = [];

	// Check if user is has GlotPress Admin previleges.
	var glotpressAdmin = gpConvertPTAO90.admin;

	// Check if user is has GlotPress Admin previleges.
	var gpUrlProject = gpConvertPTAO90.gp_url_project;

	var projectPath = null;

	console.log( gpUrlProject );

	// Add attribute 'data-locale' to each row.
	$( 'table.gp-table.translation-sets tr td:first-child a' ).each( function() {
		// Create a regular expression pattern with the variable
		var regexPattern = new RegExp( '^' + gpUrlProject + '(.*).*\/(.+)\/(.+)\/$' );

		/**
		 * Check for project path and Locale in the link.
		 * Example: ../glotpress/projects/plugins/hello-dolly/pt/default/
		 */
		var match = $( this ).attr( 'href' ).match( regexPattern );
		var locale = match[2]; // 'pt'.
		var slug = match[3]; // 'default'.

		// Get edit status of the variant.
		var editable = gpConvertPTAO90.edit;

		projectPath = match[1]; // 'plugins/hello-dolly'.

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

		if ( glotpressAdmin ) {
			$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td:first-child' ).children().last().after( '<span class="gp-convert-pt-ao90-update wp-core-ui"><span class="translation-set-icon edit-status dashicons dashicons-lock"></span><button class="translation-set-icon handlediv button-link gp-convert-pt-ao90-update-button" type="button" aria-expanded="true"><span class="dashicons dashicons-update"></span><span class="screen-reader-text">' + wp.i18n.__( 'Convert', 'gp-convert-pt-ao90' ) + '</span></span>' );
		}
	}

	// Action on click Convert button.
	$( 'table.gp-table.translation-sets tr[data-locale="pt-ao90"][data-slug="default"] td:first-child span.gp-convert-pt-ao90-update button.gp-convert-pt-ao90-update-button' ).on( 'click', function() {
		var locale = $( this ).closest( 'tr' ).attr( 'data-locale' );
		var slug = $( this ).closest( 'tr' ).attr( 'data-slug' );
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
			},
			/*success: function ( response, textStatus, jqXHR ) {
				var response_data = response.data.data;
				var response_data = response;
				if ( response_data != "" || response_data.length != 0) {
				console.log( response_data );
				// write your code here
				} else {
				// write your code here
				}
			},*/

		} ).done( function( html, textStatus, jqXHR ) {
			console.log( 'Ajax request has been completed (' + textStatus + '). Status: ' + jqXHR.status + ' ' + jqXHR.statusText );
			console.log( html );
			console.log( textStatus );
			console.log( jqXHR );
		} ).fail( function( jqXHR, textStatus ) {
			// Show the Error notice.
			console.log( 'Ajax request has failed (' + textStatus + '). Status: ' + jqXHR.status + ' ' + jqXHR.statusText );
		} ).always( function() {
			console.log( 'Ajax end.' );
		} );
	}
} );
