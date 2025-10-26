const Vue = require( 'vue' );
const ScoreTile = require( './components/ScoreTile.vue' );

// Create a container for the score tile and mount it
const container = document.body.appendChild( document.createElement( 'div' ) );
container.id = 'wandascore-tile-container';
Vue.createMwApp( ScoreTile ).mount( container );
