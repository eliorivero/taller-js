/* global tallerJsData, wp, _, moment */

let TallerJS;

( $ => {

	TallerJS = {
		ready: () => {
			const base = $( '#tallerjs' );
			$.getJSON(
				// http://localhost/wp-json/wp/v2/posts/?per_page=5
				`${ tallerJsData.wpApiRoot }wp/v2/posts/?per_page=${ tallerJsData.number }`
			).then(
				posts => _.each( posts, post => base.append(
					`<li>
						<small>${ moment( post.date ).format( tallerJsData.dateFormat ) }</small>
						<h3><a href="${ post.link }">${ post.title.rendered }</a></h3>
					</li>`
				) )
			)
		}
	};

	$( document ).ready( () => TallerJS.ready() );

} )( jQuery );
