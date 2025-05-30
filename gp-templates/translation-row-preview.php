<?php
/**
 * Template for the preview part of a single translation row in a translation set display.
 *
 * @package    GlotPress
 * @subpackage Templates
 *
 * @var GP_Locale                    $locale                    GlotPress Locale object.
 * @var Translation_Entry            $translation               GlotPress Translation object.
 * @var bool                         $has_root                  Wether the Locale has a root Locale.
 * @var bool                         $is_ptao90                 Wether the Locale is pt_PT_ao90.
 * @var bool                         $supports_variants         Wether the Locale supports variants.
 * @var array<int,Translation_Entry> $root_translations         Array of the translations from the root Locale.
 * @var bool                         $can_approve               Wether the user can approve.
 * @var bool                         $can_approve_translation   Wether the user can approve the translation.
 * @var bool                         $can_edit                  Wether the user can edit.
 * @var string                       $translation_singular      The translation singular.
 */

$priority_char = array(
	'-2' => array( '&times;', 'transparent', '#ccc' ),
	'-1' => array( '&darr;', 'transparent', 'blue' ),
	'0'  => array( '', 'transparent', 'white' ),
	'1'  => array( '&uarr;', 'transparent', 'green' ),
);

$root_translation = null;

if ( $is_ptao90 && ! $supports_variants && defined( 'GP_CONVERT_PT_AO90_SHOWDIFF' ) && GP_CONVERT_PT_AO90_SHOWDIFF && $has_root ) {

	foreach ( $root_translations as $r_translation ) {
		if ( ! property_exists( $translation, 'original_id' ) ) {
			continue;
		}
		if ( ! property_exists( $r_translation, 'original_id' ) ) {
			continue;
		}
		if ( $translation->original_id === $r_translation->original_id ) {
			$root_translation = $r_translation;
			break;
		}
	}
}

?>

<tr class="preview <?php gp_translation_row_classes( $translation ); ?>" id="preview-<?php echo esc_attr( $translation->row_id ?? '' ); ?>" row="<?php echo esc_attr( $translation->row_id ?? '' ); ?>">
	<?php
	if ( $can_approve_translation ) {
		?>
		<th scope="row" class="checkbox"><input type="checkbox" name="selected-row[]"/></th>
		<?php
	} elseif ( $can_approve ) {
		?>
		<th scope="row"></th>
		<?php
	}

	$translation_priority_id   = $translation->priority ?? '0';
	$translation_priority_name = is_string( gp_array_get( GP::$original->get_static( 'priorities' ), $translation_priority_id ) ) ? gp_array_get( GP::$original->get_static( 'priorities' ), $translation_priority_id ) : 'normal';

	?>
	<td class="priority" title="<?php echo esc_attr( sprintf( /* translators: %s: Priority of original */ __( 'Priority: %s', 'gp-convert-pt-ao90' ), $translation_priority_name ) ); ?>">
		<?php
		echo esc_html( $priority_char[ $translation_priority_id ][0] );
		?>
	</td>
	<td class="original">
		<?php
		if ( ! $translation->plural ) {
			?>
			<span class="original-text"><?php echo wp_kses_post( prepare_original( $translation_singular ) ); ?></span>
			<?php
		} else {
			$translation_plural = isset( $translation->plural_glossary_markup ) ? $translation->plural_glossary_markup : wp_kses_post( prepare_original( esc_translation( $translation->plural ) ) );
			?>
			<ul>
				<li><small><?php esc_html_e( 'Singular:', 'gp-convert-pt-ao90' ); ?></small><br><span class="original-text"><?php echo wp_kses_post( prepare_original( $translation_singular ) ); ?></span></li>
				<li><small><?php esc_html_e( 'Plural:', 'gp-convert-pt-ao90' ); ?></small><br><span class="original-text"><?php echo wp_kses_post( prepare_original( $translation_plural ) ); ?></span></li>
			</ul>
			<?php
		}

		if ( $translation->context ) {
			?>
			<span class="context bubble" title="<?php echo esc_attr( sprintf( /* translators: %s: Context of original */ __( 'Context: %s', 'gp-convert-pt-ao90' ), $translation->context ) ); ?>"><?php echo esc_html( $translation->context ); ?></span>
			<?php
		}
		?>
	</td>
	<td class="translation foreign-text">
		<?php

		if ( $can_edit ) {
			$edit_text = __( 'Double-click to add', 'gp-convert-pt-ao90' );
		} elseif ( is_user_logged_in() ) {
			$edit_text = __( 'You are not allowed to add a translation.', 'gp-convert-pt-ao90' );
		} else {
			$edit_text = sprintf(
				/* translators: %s: URL. */
				__( 'You <a href="%s">have to log in</a> to add a translation.', 'gp-convert-pt-ao90' ),
				esc_url( wp_login_url( gp_url_current() ) )
			);
		}

		$missing_text = "<span class='missing'>$edit_text</span>";

		if ( array_filter( $translation->translations, 'gp_is_not_null' ) === array() ) {
			// Do if no translations found.

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $missing_text;

		} elseif ( is_null( $translation->plural ) || $locale->nplurals === 1 ) {
			// Do if no plurals or number of plurals is 1.

			$singular_translation = esc_translation( $translation->translations[0] );
			// Check if has root.
			if ( $is_ptao90 && ( isset( $translation->root_id ) || $has_root ) ) {
				if ( defined( 'GP_CONVERT_PT_AO90_SHOWDIFF' ) && GP_CONVERT_PT_AO90_SHOWDIFF === true ) {
					$singular_translation = GP_Convert_PT_AO90\Portuguese_AO90::highlight_diff(
						esc_translation( $supports_variants ? property_exists( $translation, 'root_translation_0' ) && $translation->root_translation_0 : ( ! is_null( $root_translation ) ? $root_translation->translations[0] : '' ) ),
						esc_translation( $translation->translations[0] )
					);
				}
			}
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<span class="translation-text">' . prepare_original( $singular_translation ) . '</span>';

		} elseif ( $translation->plural && $locale->nplurals === 2 && $locale->plural_expression === 'n != 1' ) {
			// Do if plurals exist, number of plurals is 2 and plural expression is 'n != 1'.

			?>
			<ul>
				<li>
					<small><?php esc_html_e( 'Singular:', 'gp-convert-pt-ao90' ); ?></small><br>
					<?php
					if ( ! isset( $translation->translations[0] ) || gp_is_empty_string( $translation->translations[0] ) ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $missing_text;
					} else {
						$singular_translation = esc_translation( $translation->translations[0] );
						// Check if has root.
						if ( $is_ptao90 && ( isset( $translation->root_id ) || $has_root ) ) {
							if ( defined( 'GP_CONVERT_PT_AO90_SHOWDIFF' ) && GP_CONVERT_PT_AO90_SHOWDIFF === true ) {
								$singular_translation = GP_Convert_PT_AO90\Portuguese_AO90::highlight_diff(
									esc_translation( $supports_variants ? property_exists( $translation, 'root_translation_0' ) && $translation->root_translation_0 : ( ! is_null( $root_translation ) ? $root_translation->translations[0] : '' ) ),
									esc_translation( $translation->translations[0] )
								);
							}
						}
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '<span class="translation-text">' . prepare_original( $singular_translation ) . '</span>';
					}
					?>
				</li>
				<li>
					<small><?php esc_html_e( 'Plural:', 'gp-convert-pt-ao90' ); ?></small><br>
					<?php
					if ( ! isset( $translation->translations[1] ) || gp_is_empty_string( $translation->translations[1] ) ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $missing_text;
					} else {
						$plural_translation = esc_translation( $translation->translations[1] );
						// Check if has root.
						if ( $is_ptao90 && ( isset( $translation->root_id ) || $has_root ) ) {
							if ( defined( 'GP_CONVERT_PT_AO90_SHOWDIFF' ) && GP_CONVERT_PT_AO90_SHOWDIFF === true ) {
								$plural_translation = GP_Convert_PT_AO90\Portuguese_AO90::highlight_diff(
									esc_translation( $supports_variants ? property_exists( $translation, 'root_translation_1' ) && $translation->root_translation_1 : ( ! is_null( $root_translation ) ? $root_translation->translations[1] : '' ) ),
									esc_translation( $translation->translations[1] )
								);
							}
						}
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '<span class="translation-text">' . prepare_original( $plural_translation ) . '</span>';
					}
					?>
				</li>
			</ul>
			<?php

		} else {
			// Do elsewhere.

			?>
			<ul>
				<?php
				foreach ( range( 0, $locale->nplurals - 1 ) as $plural_index ) {
					$plural_string = implode( ', ', $locale->numbers_for_index( $plural_index ) );
					?>
					<li>
						<small>
							<?php
							printf(
								/* translators: %s: Plural form. */
								esc_html__( '%s:', 'gp-convert-pt-ao90' ),
								esc_html( $plural_string )
							);
							?>
						</small><br>
						<?php
						if ( ! isset( $translation->translations[ $plural_index ] ) || gp_is_empty_string( $translation->translations[ $plural_index ] ) ) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $missing_text;
						} else {
							$plural_translation = esc_translation( $translation->translations[ $plural_index ] );
							// Check if has root.
							if ( $is_ptao90 && ( isset( $translation->root_id ) || $has_root ) ) {
								if ( defined( 'GP_CONVERT_PT_AO90_SHOWDIFF' ) && GP_CONVERT_PT_AO90_SHOWDIFF === true ) {
									$plural_translation = GP_Convert_PT_AO90\Portuguese_AO90::highlight_diff(
										esc_translation( $supports_variants ? $translation->{ 'root_translation_' . $plural_index } : ( ! is_null( $root_translation ) ? $root_translation->translations[ $plural_index ] : '' ) ),
										esc_translation( $translation->translations[ $plural_index ] )
									);
								}
							}
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '<span class="translation-text">' . prepare_original( $plural_translation ) . '</span>';
						}
						?>
					</li>
					<?php
				}
				?>
			</ul>
			<?php

		}

		?>
	</td>
	<td class="actions">
		<a href="#" class="action edit"><?php esc_html_e( 'Details', 'gp-convert-pt-ao90' ); ?></a>
	</td>
</tr>
