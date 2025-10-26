<template>
  <div class="wandascore-tile" :class="scoreClass" v-if="showTile" @click="goToReview">
    <div class="wandascore-tile-icon">📊</div>
    <div class="wandascore-tile-content">
      <div class="wandascore-tile-label">{{ msg('wandascore-tile-title') }}</div>
      <div class="wandascore-tile-score" v-if="!loading && score !== null">
        {{ score }}
      </div>
      <div class="wandascore-tile-loading" v-if="loading">
        <cdx-progress-bar :value="null" :inline="true" />
      </div>
      <div class="wandascore-tile-error" v-if="error">⚠️</div>
    </div>
    <div class="wandascore-tile-tooltip" v-if="!loading && score !== null">
      {{ msg('wandascore-tile-click-details') }}
    </div>
  </div>
</template>

<script>
const { CdxProgressBar } = require( '../codex.js' );

module.exports = exports = {
  name: 'ScoreTile',
  components: { CdxProgressBar },
  data() {
    return {
      score: null,
      loading: true,
      error: false,
      showTile: true
    };
  },
  computed: {
    scoreClass() {
      if ( this.loading || this.score === null ) {
        return '';
      }
      if ( this.score >= 90 ) {
        return 'wandascore-excellent';
      } else if ( this.score >= 70 ) {
        return 'wandascore-good';
      } else if ( this.score >= 50 ) {
        return 'wandascore-fair';
      }
      return 'wandascore-poor';
    }
  },
  mounted() {
    this.fetchScore();
  },
  methods: {
    msg( key ) {
      return mw.message( key ).text();
    },
    async fetchScore() {
      const pageTitle = mw.config.get( 'wgWandaScorePageTitle' );
      if ( !pageTitle ) {
        this.showTile = false;
        return;
      }

      try {
        const api = new mw.Api();
        const data = await api.get( {
          action: 'wandascore',
          page: pageTitle,
          format: 'json'
        } );

        if ( data && data.wandascore && typeof data.wandascore.overall_score === 'number' ) {
          this.score = data.wandascore.overall_score;
        } else {
          this.error = true;
        }
      } catch ( e ) {
        console.error( 'WandaScore: Error fetching score', e );
        this.error = true;
      } finally {
        this.loading = false;
      }
    },
    goToReview() {
      const pageTitle = mw.config.get( 'wgWandaScorePageTitle' );
      const url = mw.util.getUrl( 'Special:WandaScore', { page: pageTitle } );
      window.location.href = url;
    }
  }
};
</script>
