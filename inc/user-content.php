<?php
/**
 * Code Reference user submitted content (comments, notes, etc).
 *
 * @package wporg-developer
 */

/**
 * Class to handle user submitted content.
 */
class DevHub_User_Submitted_Content {

	/**
	 * Initializer
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Handles adding/removing hooks to enable comments as user contributed notes.
	 */
	public static function do_init() {

		// Disable pings.
		add_filter( 'pings_open',                       '__return_false' );

		// Sets whether submitting notes is open for the user
		add_filter( 'comments_open',                    '\DevHub\\can_user_post_note', 10, 2 );

		// Enqueue scripts and styles
		add_action( 'wp_enqueue_scripts',               array( __CLASS__, 'scripts_and_styles' ), 11 );

		// Force comment registration to be true
		add_filter( 'pre_option_comment_registration', '__return_true' );

		// Force comment moderation to be true
		add_filter( 'pre_option_comment_moderation',   '__return_true' );

		// Remove reply to link
		add_filter( 'comment_reply_link',              '__return_empty_string' );

		// Remove cancel reply link
		add_filter( 'cancel_comment_reply_link',       '__return_empty_string' );

		// Disable smilie conversion
		remove_filter( 'comment_text',                 'convert_smilies',    20 );

		// Disable capital_P_dangit
		remove_filter( 'comment_text',                 'capital_P_dangit',   31 );

		// Enable shortcodes for comments
		add_filter( 'comment_text',                    'do_shortcode' );

		// Customize allowed tags
		add_filter( 'wp_kses_allowed_html',            array( __CLASS__, 'wp_kses_allowed_html' ), 10, 2 );

		// Allowed HTML for a new child comment
		add_filter( 'preprocess_comment',              array( __CLASS__, 'comment_new_allowed_html' ) );

		// Allowed HTML for an edited child comment (There is no decent hook to filter child comments only)
		add_action( 'edit_comment',                    array( __CLASS__, 'comment_edit_allowed_html' ) );

		// Adds hidden fields to a comment form for editing
		add_filter( 'comment_form_submit_field',       array( __CLASS__, 'add_hidden_fields' ), 10, 2 );

		// Disable the search query in the insert link modal window
		add_filter( 'wp_link_query_args',              array( __CLASS__, 'disable_link_query' ) );

		// Disable moderation emails to post author.
		add_filter( 'comment_notification_recipients', array( __CLASS__, 'disable_comment_notifications' ), 10, 2 );
	}

	/**
	 * Customizes the allowed HTML tags for comments.
	 *
	 * @param array  $allowed List of allowed tags and their allowed attributes.
	 * @param string $context The context for which to retrieve tags.
	 * @return array
	 */
	public static function wp_kses_allowed_html( $allowed, $context ) {
		// Unfortunately comments don't have a specific context, so apply to any context not explicitly known.
		if ( ! in_array( $context, array( 'post', 'user_description', 'pre_user_description', 'strip', 'entities', 'explicit' ) ) ) {
			foreach ( array( 'ol', 'ul', 'li' ) as $tag ) {
				$allowed[ $tag ] = array();
			}
		}

		return $allowed;
	}

	/**
	 * Updates edited child comments with allowed HTML.
	 *
	 * Allowed html is <strong>, <em>, <code> and <a>.
	 *
	 * @param int $comment_ID Comment ID.
	 */
	public static function comment_edit_allowed_html( $comment_ID ) {

		// Get the edited comment.
		$comment = get_comment( $comment_ID, ARRAY_A );
		if ( ! $comment ) {
			return;
		}

		if ( ( 0 === (int) $comment['comment_parent'] ) || empty( $comment['comment_content'] ) ) {
			return;
		}

		$content = $comment['comment_content'];
		$data    = self::comment_new_allowed_html( $comment );

		if ( $data['comment_content'] !== $content ) {
			$commentarr = array(
				'comment_ID' => $data['comment_ID'],
				'comment_content' => $data['comment_content']
			);

			// Update comment content.
			wp_update_comment( $commentarr );
		}
	}

	/**
	 * Filter new child comments content with allowed HTML.
	 *
	 * Allowed html is <strong>, <em>, <code> and <a>.
	 *
	 * @param array $commentdata Array with comment data.
	 * @return array Array with filtered comment data.
	 */
	public static function comment_new_allowed_html( $commentdata ) {
		$comment_parent  = isset( $commentdata['comment_parent'] ) ? absint( $commentdata['comment_parent'] ) : 0;
		$comment_content = isset( $commentdata['comment_content'] ) ? trim( $commentdata['comment_content'] ) : '';

		if ( ( $comment_parent === 0 ) || ! $comment_content ) {
			return $commentdata;
		}

		$allowed_html = array(
			'a'      => array(
				'href'   => true,
				'rel'    => true,
				'target' => true,
			),
			'em'     => array(),
			'strong' => array(),
			'code'   => array(),
		);

		$allowed_protocols = array( 'http', 'https' );
		$comment_content   = wp_kses( $comment_content, $allowed_html, $allowed_protocols );

		// Replace newlines with a space.
		$commentdata['comment_content'] = preg_replace( '/\r?\n|\r/', ' ', $comment_content );

		return $commentdata;
	}

	/**
	 * Enqueues scripts and styles.
	 */
	public static function scripts_and_styles() {
		if ( is_singular() ) {
			wp_enqueue_script(
				'wporg-developer-function-reference',
				get_stylesheet_directory_uri() . '/js/function-reference.js',
				array( 'jquery', 'wp-a11y' ),
				filemtime( dirname( __DIR__ ) . '/js/function-reference.js' ),
				true
			);
			wp_localize_script(
				'wporg-developer-function-reference',
				'wporgFunctionReferenceI18n',
				array(
					'copy'   => __( 'Copy', 'wporg' ),
					'copied' => __( 'Code copied', 'wporg' ),
					'expand'   => __( 'Expand code', 'wporg' ),
					'collapse' => __( 'Collapse code', 'wporg' ),
					'sourceFile'   => DevHub\get_source_file(),
				)
			);

			wp_enqueue_script(
				'wporg-developer-user-notes',
				get_stylesheet_directory_uri() . '/js/user-notes.js',
				array( 'jquery', 'quicktags' ),
				filemtime( dirname( __DIR__ ) . '/js/user-notes.js' ),
				true
			);

			wp_enqueue_script(
				'wporg-developer-user-notes-feedback',
				get_stylesheet_directory_uri() . '/js/user-notes-feedback.js',
				array( 'jquery', 'quicktags' ),
				filemtime( dirname( __DIR__ ) . '/js/user-notes-feedback.js' ),
				true
			);
			wp_localize_script(
				'wporg-developer-user-notes-feedback',
				'wporg_note_feedback',
				array(
					'show' => __( 'Show feedback', 'wporg' ),
					'hide' => __( 'Hide feedback', 'wporg' ),
					'hide_feedback' => __( 'Hide feedback form', 'wporg' ),
					'add_feedback' => __( 'Add feedback', 'wporg' ),
				)
			);
		}
	}

	/**
	 * Add hidden fields to the comment form if the context is 'edit'
	 *
	 * @param string $submit_field HTML string with the submit button fields.
	 * @param array  $args         Arguments for the comment form.
	 * @return HTML string with the submit button fields.
	 */
	public static function add_hidden_fields( $submit_field, $args ) {
		$context = isset( $args['context'] ) ? $args['context'] : '';
		$comment = isset( $args['comment_edit'] ) ? $args['comment_edit'] : false;
		if ( ! ( $comment && ( 'edit' === $context ) ) ) {
			return $submit_field;
		}

		$comment_id = isset( $comment->comment_ID ) ? $comment->comment_ID : 0;

		return $submit_field . self::get_edit_fields( $comment_id, $instance = 0 );
	}

	/**
	 * Disable the search query in the insert link modal window.
	 *
	 * The search query field in the link modal is hidden with CSS.
	 *
	 * @param array $query An array of WP_Query arguments.
	 */
	public static function disable_link_query( $query ) {
		$query['post__in'] = array(0);
		return $query;
	}

	/**
	 * Get the comment form arguments by context.
	 *
	 * @param WP_Comment|false $comment Comment object or false. Default false.
	 * @param string           $context Context of arguments. Accepts 'edit' or empty string.
	 * @return array Array with comment form arguments.
	 */
	public static function comment_form_args( $comment = false, $context = '' ) {
		$args = array(
			'logged_in_as'        => '',
			'label_submit'        => __( 'Submit feedback', 'wporg' ),
			'title_reply'         => '', //'Add Example'
			'title_reply_to'      => '',
			'title_reply_before'  => '',
			'title_reply_after'  => '',
		);

		$args['comment_field'] = self::wp_editor_comments( $comment );

		// Args for adding hidden links after the comment form submit field.
		$args['context']      = $context;
		$args['comment_edit'] = $comment;

		if ( $comment && ( 'edit' === $context ) ) {
			$comment_id = isset( $comment->comment_ID ) ? $comment->comment_ID : 0;
			$post_id    = isset( $comment->comment_post_ID ) ? $comment->comment_post_ID : 0;

			$args['action'] = get_permalink( $post_id ) . '#comment-' . $comment_id;
		}

		if ( class_exists( 'DevHub_Note_Preview' ) ) {
			$args['class_form'] = "comment-form tab-container";
		}

		return $args;
	}

	/**
	 * Capture an {@see wp_editor()} instance as the 'User Contributed Notes' comment form.
	 *
	 * Uses output buffering to capture the editor instance for use with the {@see comments_form()}.
	 *
	 * @param WP_Comment|false $comment Comment object or false. Default false.
	 * @return string HTML output for the wp_editor-ized comment form.
	 */
	public static function wp_editor_comments( $comment = false ) {
		$content = isset( $comment->comment_content ) ? trim( $comment->comment_content ) : '';

		// wp_kses() converts htmlspecialchars in source code.
		$content = $content ? htmlspecialchars_decode( $content ) : '';

		ob_start();

		if ( class_exists( 'DevHub_Note_Preview' ) ) {
			echo "<ul class='tablist' style='display: none;'>";
			echo '<li><a href="#comment-form-comment">' . __( 'Write', 'wporg' ) . '</a></li>';
			echo '<li><a href="#comment-preview">' . __( 'Preview', 'wporg' ) . '</a></li></ul>';
			echo '<label class="screen-reader-text" for="comment">' . esc_html__( 'Note', 'wporg' ) . '</label>';
		}

		echo '<div class="comment-form-comment tab-section" id="comment-form-comment">';
		wp_editor( $content, 'comment', array(
				'media_buttons' => false,
				'textarea_name' => 'comment',
				'textarea_rows' => 8,
				'quicktags'     => array(
					'buttons' => 'strong,em,ul,ol,li,link'
				),
				'teeny'         => true,
				'tinymce'       => false,
			) );
		echo '</div>';

		if ( class_exists( 'DevHub_Note_Preview' ) ) {
			echo DevHub_Note_Preview::comment_preview();
		}

		return ob_get_clean();
	}

	/**
	 * Capture an {@see wp_editor()} instance as the 'User Contributed Notes' feedback form.
	 *
	 * Uses output buffering to capture the editor instance.
	 *
	 * @param WP_Comment|false $comment Comment object or false. Default false.
	 * @param string           $display Display the editor. Default 'show'.
	 * @param bool             $edit    True if the editor used for editing a note. Default false.
	 * @return string HTML output for the wp_editor-ized feedback form.
	 */
	public static function wp_editor_feedback( $comment, $display = 'show', $edit = false ) {

		if ( ! ( isset( $comment->comment_ID ) && absint( $comment->comment_ID ) ) ) {
			return '';
		}

		$comment_id = absint( $comment->comment_ID );

		static $instance = 0;
		$instance++;

		$display       = ( 'hide' === $display ) ? ' style="display: none;"' : '';
		$parent        = $comment_id;
		$action        = site_url( '/wp-comments-post.php' );
		$button_text   = __( 'Submit feedback', 'wporg' );
		$post_id       = isset( $comment->comment_post_ID ) ? $comment->comment_post_ID : get_the_ID();
		$content       = '';
		$form_type     = '';
		$note_link     = '';
		$class         = '';

		if ( $edit ) {
			$content       = isset( $comment->comment_content ) ? $comment->comment_content : '';
			$title         = __( 'Edit feedback', 'wporg' );
			$form_type     = '-edit';
			$button_text   = __( 'Edit Note', 'wporg' );
			$post_url      = get_permalink( $post_id );
			$action        = $post_url ? $post_url . '#comment-' . $comment_id : '';
			$parent        = isset( $comment->comment_parent ) ? $comment->comment_parent : 0;
			$parent_author = \DevHub\get_note_author( $parent );
			$class         = ' edit-feedback-editor';

			if ( $parent && $post_url && $parent_author ) {
				$post_url  = $post_url . '#comment-' . $parent;
				$parent_note = sprintf( __( 'note %d', 'wporg' ), $parent );

				/* translators: 1: note, 2: note author name */
				$note_link = sprintf( __( '%1$s by %2$s', 'wporg' ), "<a href='{$post_url}'>{$parent_note}</a>", $parent_author );
			}
		}

		$allowed_tags = '';
		foreach ( array( '<strong>', '<em>', '<code>', '<a>' ) as $tag ) {
			$allowed_tags .= '<code>' . htmlentities( $tag ) . '</code>, ';
		}

		ob_start();
		echo "<div id='feedback-editor-{$comment_id}' class='feedback-editor{$class}'{$display}>\n";
		echo "<form id='feedback-form-{$instance}{$form_type}' class='feedback-form' method='post' action='{$action}' name='feedback-form-{$instance}'>\n";
		printf(
			"<label class='screen-reader-text' for='feedback-comment-%s'>%s</label>",
			esc_attr( $instance ),
			esc_html__( 'Feedback', 'wporg' ),
		);
		wp_editor( $content, 'feedback-comment-' . $instance, array(
				'media_buttons' => false,
				'textarea_name' => 'comment',
				'textarea_rows' => 3,
				'quicktags'     => array(
					'buttons' => 'strong,em,link'
				),
				'teeny'         => true,
				'tinymce'       => false,
			) );
		echo '<p class="wp-block-wporg-code-reference-comments-small"><strong>' . __( 'Note', 'wporg' ) . '</strong>: ' . __( 'No newlines allowed', 'wporg' ) . '. ';
		printf( __( 'Allowed tags: %s', 'wporg' ), trim( $allowed_tags, ', ' ) );
		echo '</p>';
		echo "<div class='wp-block-button'><input id='submit-{$instance}' class='submit wp-block-button__link wp-element-button' type='submit' value='{$button_text}' name='submit-{$instance}'></div>";
		echo self::get_editor_rules( 'feedback', $note_link );
		echo "<input type='hidden' name='comment_post_ID' value='{$post_id}' id='comment_post_ID-{$instance}' />\n";
		echo "<input type='hidden' name='comment_parent' id='comment_parent-{$instance}' value='{$parent}' />\n";

		if ( $edit ) {
			echo self::get_edit_fields( $comment_id, $instance );
		}

		echo "</p>\n</form>\n</div><!-- #feedback-editor-{$comment_id} -->\n";
		return ob_get_clean();
	}

	/**
	 * Get the rules list for the comment form.
	 *
	 * @param string $context   Accepts 'feedback' or empty sring.
	 * @param string $note_link Link to parent note.
	 * @return string Editor rules.
	 */
	public static function get_editor_rules( $context = '', $note_link = '' ) {
		if ( 'feedback' === $context ) {
			if ( $note_link ) {
				$rules[] = sprintf( __( 'Use this form to report errors or to add additional information to %s.', 'wporg' ), $note_link );
			} else {
				$rules[] = __( 'Feedback is part of documentation. Use this form to report errors or to add additional information to this note', 'wporg' );
			}
		} else {
			$rules[] = __( 'Notes should supplement code reference entries, for example examples, tips, explanations, use-cases, and best practices.', 'wporg' );
			$rules[] = __( 'Feedback can be to report errors or omissions with the documentation on this page. Feedback will not be publicly posted.', 'wporg' );
		}

		$rules[] = __( 'Notes and feedback must be written in English.', 'wporg' );
		$rules[] = __( 'This form is not for support requests, discussions, spam, bug reports, complaints, or self-promotion.', 'wporg' );
		$rules[] = __('Any notes not meeting these requirements will be removed by the moderation team.', 'wporg' );
		$rules[] = __('In the editing area the Tab key indents text. To move below this area by pressing Tab, press the Esc key followed by the Tab key. In some cases the Esc key will need to be pressed twice before the Tab key will allow you to continue.', 'wporg');

		$rules[] = sprintf(
			/* translators: 1: GFDL link */
			__( 'Any notes not meeting these requirements will be removed by the moderation team. All contributions are licensed under %s and are moderated before appearing on the site.', 'wporg' ),
			'<a href="https://gnu.org/licenses/fdl.html">GFDL</a>'
		);

		return sprintf( '<ul class="wp-block-wporg-code-reference-comments-rules">%s</ul>',
			implode( '', array_map( function( $rule ) {
				return "<li>{$rule}</li>";
			}, $rules ) )
		);
	}

	/**
	 * Get the hidden input fields HTML used for editing a note.
	 *
	 * @param int     $comment_id Comment ID.
	 * @param integer $instance   Comment form instance number used in HTML id's.
	 * @return string Hidden input fields HTML.
	 */
	public static function get_edit_fields( $comment_id, $instance = 0 ) {
		$fields = "<input type='hidden' name='comment_ID' id='comment_ID-{$instance}' value='{$comment_id}' />\n";
		$fields .= "<input type='hidden' name='update_user_note' id='update_user_note-{$instance}' value='1' />\n";
		$fields .= wp_nonce_field( 'update_user_note_' . $comment_id, '_wpnonce', true, false );

		return $fields;
	}

	/**
	 * Disables moderation emails to post author for parsed post types.
	 *
	 * Parsed post types aren't legitimately authored by any given user, so whoever
	 * is assigned does not need these notifications. A team of moderators are
	 * responsible for handling submitted comments, most of which start off in
	 * moderation.
	 *
	 * @param string[] $emails     An array of email addresses to receive a comment notification.
	 * @param int      $comment_id The comment ID.
	 */
	public static function disable_comment_notifications( $emails, $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( $comment && DevHub\is_parsed_post_type( get_post_type( $comment->comment_post_ID ) ) ) {
			$emails = [];
		}

		return $emails;
	}
	
} // DevHub_User_Submitted_Content

DevHub_User_Submitted_Content::init();
