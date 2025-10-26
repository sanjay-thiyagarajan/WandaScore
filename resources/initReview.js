const Vue = require( 'vue' );
const ReviewPage = require( './components/ReviewPage.vue' );

// Mount the review page component
const mount = document.getElementById( 'wandascore-review-container' );
if ( mount ) {
	Vue.createMwApp( ReviewPage ).mount( mount );
}
