/**
 * Awesomplete module.
 *
 * Adapted from (MIT license): https://leaverou.github.io/awesomplete/
 */

const slice = Array.prototype.slice;

export class Awesomplete {
	static all = [];

	static FILTER_CONTAINS( text, input ) {
		return RegExp( Awesomplete.$.regExpEscape( input.trim() ), 'i' ).test( text );
	}

	static FILTER_STARTSWITH( text, input ) {
		return RegExp( '^' + Awesomplete.$.regExpEscape( input.trim() ), 'i' ).test( text );
	}

	static SORT_BYLENGTH( a, b ) {
		if ( a.length !== b.length ) {
			return a.length - b.length;
		}

		return a < b ? -1 : 1;
	}

	// eslint-disable-next-line id-length
	static $( expr, con ) {
		return typeof expr === 'string' ? ( con || document ).querySelector( expr ) : expr || null;
	}

	// eslint-disable-next-line id-length
	static $$( expr, con ) {
		return slice.call( ( con || document ).querySelectorAll( expr ) );
	}

	constructor( input, opts = {} ) {
		// eslint-disable-next-line id-length
		const me = this;

		// Setup
		this.input = Awesomplete.$( input );
		this.input.setAttribute( 'autocomplete', 'off' );
		this.input.setAttribute( 'aria-autocomplete', 'list' );

		this.configure(
			{
				minChars: 2,
				maxItems: 10,
				autoFirst: false,
				filter: Awesomplete.FILTER_CONTAINS,
				sort: Awesomplete.SORT_BYLENGTH,
				item: ( text, itemInput ) => {
					const html =
						itemInput === ''
							? text
							: text.replace(
									RegExp( Awesomplete.$.regExpEscape( itemInput.trim() ), 'gi' ),
									'<mark>$&</mark>'
							  );
					return Awesomplete.$.create( 'li', {
						innerHTML: html,
						'aria-selected': 'false',
					} );
				},
				replace: ( text ) => {
					this.input.value = text;
				},
			},
			opts
		);

		this.index = -1;

		// Create necessary elements

		this.container = Awesomplete.$.create( 'div', {
			className: 'awesomplete',
			around: input,
		} );

		// eslint-disable-next-line id-length
		this.ul = Awesomplete.$.create( 'ul', {
			hidden: 'hidden',
			inside: this.container,
		} );

		this.status = Awesomplete.$.create( 'span', {
			className: 'visually-hidden',
			role: 'status',
			'aria-live': 'assertive',
			'aria-relevant': 'additions',
			inside: this.container,
		} );

		// Bind events

		Awesomplete.$.bind( this.input, {
			input: this.evaluate.bind( this ),
			blur: this.close.bind( this ),
			keydown: ( evt ) => {
				const keyCode = evt.keyCode;

				// If the dropdown `ul` is in view, then act on keydown for the following keys:
				// Enter / Esc / Up / Down
				if ( this.opened ) {
					if ( keyCode === 13 && this.selected ) {
						// Enter (13)
						evt.preventDefault();
						this.select();
					} else if ( keyCode === 27 ) {
						// Escape (27)
						this.close();
					} else if ( keyCode === 38 || keyCode === 40 ) {
						// Up (38) / Down (40)
						evt.preventDefault();
						this[ keyCode === 38 ? 'previous' : 'next' ]();
					}
				}
			},
		} );

		Awesomplete.$.bind( this.input.form, { submit: this.close.bind( this ) } );

		Awesomplete.$.bind( this.ul, {
			mousedown( evt ) {
				// eslint-disable-next-line id-length
				let li = evt.target;

				if ( li !== this ) {
					while ( li && ! /li/i.test( li.nodeName ) ) {
						li = li.parentNode;
					}

					if ( li && evt.button === 0 ) {
						// Only select on left click
						evt.preventDefault();
						me.select( li, evt );
					}
				}
			},
		} );

		if ( this.input.hasAttribute( 'list' ) ) {
			this.list = '#' + this.input.getAttribute( 'list' );
			this.input.removeAttribute( 'list' );
		} else {
			this.list = this.input.getAttribute( 'data-list' ) || opts.list || [];
		}

		Awesomplete.all.push( this );
	}

	configure( properties, o ) {
		for ( var i in properties ) {
			var initial = properties[ i ],
				attrValue = this.input.getAttribute( 'data-' + i.toLowerCase() );

			if ( typeof initial === 'number' ) {
				this[ i ] = parseInt( attrValue );
			} else if ( initial === false ) {
				// Boolean options must be false by default anyway
				this[ i ] = attrValue !== null;
			} else if ( initial instanceof Function ) {
				this[ i ] = null;
			} else {
				this[ i ] = attrValue;
			}

			if ( ! this[ i ] && this[ i ] !== 0 ) {
				this[ i ] = i in o ? o[ i ] : initial;
			}
		}
	}

	set list( list ) {
		if ( Array.isArray( list ) ) {
			this._list = list;
		} else if ( typeof list === 'string' && list.indexOf( ',' ) > -1 ) {
			this._list = list.split( /\s*,\s*/ );
		} else {
			// Element or CSS selector
			list = Awesomplete.$( list );

			if ( list && list.children ) {
				this._list = slice.apply( list.children ).map( ( el ) => el.textContent.trim() );
			}
		}

		if ( document.activeElement === this.input ) {
			this.evaluate();
		}
	}

	get selected() {
		return this.index > -1;
	}

	get opened() {
		return this.ul && this.ul.getAttribute( 'hidden' ) == null;
	}

	close() {
		this.ul.setAttribute( 'hidden', '' );
		this.index = -1;

		Awesomplete.$.fire( this.input, 'awesomplete-close' );
	}

	open() {
		this.ul.removeAttribute( 'hidden' );

		if ( this.autoFirst && this.index === -1 ) {
			this.goto( 0 );
		}

		Awesomplete.$.fire( this.input, 'awesomplete-open' );
	}

	next() {
		var count = this.ul.children.length;

		this.goto( this.index < count - 1 ? this.index + 1 : -1 );
	}

	previous() {
		var count = this.ul.children.length;

		this.goto( this.selected ? this.index - 1 : count - 1 );
	}

	// Should not be used, highlights specific item without any checks!
	goto( i ) {
		const lis = this.ul.children;

		if ( this.selected ) {
			lis[ this.index ].setAttribute( 'aria-selected', 'false' );
		}

		this.index = i;

		if ( i > -1 && lis.length > 0 ) {
			lis[ i ].setAttribute( 'aria-selected', 'true' );
			this.status.textContent = lis[ i ].textContent;
		}

		Awesomplete.$.fire( this.input, 'awesomplete-highlight' );
	}

	select( selected, originalEvent ) {
		selected = selected || this.ul.children[ this.index ];

		if ( selected ) {
			let prevented;

			Awesomplete.$.fire( this.input, 'awesomplete-select', {
				text: selected.textContent,
				preventDefault: function () {
					prevented = true;
				},
				originalEvent: originalEvent,
			} );

			if ( ! prevented ) {
				this.replace( selected.textContent );
				this.close();
				Awesomplete.$.fire( this.input, 'awesomplete-selectcomplete' );
			}
		}
	}

	evaluate() {
		const value = this.input.value;

		if ( value.length >= this.minChars && this._list.length > 0 ) {
			this.index = -1;
			// Populate list with options that match
			this.ul.innerHTML = '';

			this._list
				.filter( ( item ) => {
					return this.filter( item, value );
				} )
				.sort( this.sort )
				.every( ( text, i ) => {
					this.ul.appendChild( this.item( text, value ) );

					return i < this.maxItems - 1;
				} );

			if ( this.ul.children.length === 0 ) {
				this.close();
			} else {
				this.open();
			}
		} else {
			this.close();
		}
	}
}

Awesomplete.$.create = function ( tag, o ) {
	const element = document.createElement( tag );

	for ( const i in o ) {
		const val = o[ i ];

		if ( i === 'inside' ) {
			Awesomplete.$( val ).appendChild( element );
		} else if ( i === 'around' ) {
			const ref = Awesomplete.$( val );
			ref.parentNode.insertBefore( element, ref );
			element.appendChild( ref );
		} else if ( i in element ) {
			element[ i ] = val;
		} else {
			element.setAttribute( i, val );
		}
	}

	return element;
};

Awesomplete.$.bind = function ( element, opts ) {
	if ( element ) {
		for ( const event in opts ) {
			const callback = opts[ event ];

			event.split( /\s+/ ).forEach( function ( eventName ) {
				element.addEventListener( eventName, callback );
			} );
		}
	}
};

Awesomplete.$.fire = function ( target, type, properties ) {
	const evt = document.createEvent( 'HTMLEvents' );

	// @todo Deprecated: replace. @see https://developer.mozilla.org/en-US/docs/Web/API/Event/initEvent
	evt.initEvent( type, true, true );

	for ( const prop in properties ) {
		evt[ prop ] = properties[ prop ];
	}

	target.dispatchEvent( evt );
};

Awesomplete.$.regExpEscape = function ( str ) {
	return str.replace( /[-\\^$*+?.()|[\]{}]/g, '\\$&' );
};
