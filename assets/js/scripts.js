/* global document, gpConvertPTAO90 */

jQuery( document ).ready( function( $ ) {
	// Set array of Translation Sets.
	var translationSets = [];

	// Add attribute 'data-locale' to each row.
	$( 'table.gp-table.translation-sets tr td:first-child a' ).each( function() {
		/**
		 * Check for Locales in the links.
		 * Example: ../project-path/pt/default/
		 */
		var match = $( this ).attr( 'href' ).match( /^.*\/(.+)\/(.+)\/$/ );
		var locale = match[1]; // 'pt'.

		// Get edit status of the variant.
		var editable = gpConvertPTAO90.edit;

		// Add Locale to the array.
		translationSets.push( locale );

		$( this ).closest( 'tr' ).attr( 'data-locale', locale );

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
	}

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
} );
