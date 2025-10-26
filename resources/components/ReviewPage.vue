<template>
  <div class="wandascore-review">
    <div class="wandascore-review-header">
      <h2>{{ msg('wandascore-review-title') }} <a :href="pageUrl" target="_blank">{{ pageTitle }}</a></h2>
      <cdx-button @click="refreshScore" :disabled="loading" action="progressive">
        {{ msg('wandascore-review-refresh') }}
      </cdx-button>
    </div>

    <div class="wandascore-review-loading" v-if="loading">
      <cdx-progress-bar :value="null" />
      <p>{{ msg('wandascore-review-loading') }}</p>
    </div>

    <div class="wandascore-review-error" v-if="error && !loading">
      <cdx-message type="error">
        {{ msg('wandascore-review-error') }}
      </cdx-message>
    </div>

    <div class="wandascore-review-content" v-if="!loading && reviewData">
      <!-- Overall Score -->
      <div class="wandascore-overall" :class="overallScoreClass">
        <div class="wandascore-overall-circle">
          <div class="wandascore-overall-score">{{ reviewData.overall_score }}</div>
          <div class="wandascore-overall-label">{{ msg('wandascore-review-score-label') }}</div>
        </div>
        <div class="wandascore-overall-description">
          <p v-if="reviewData.overall_score >= 90">✅ Excellent quality content</p>
          <p v-else-if="reviewData.overall_score >= 70">👍 Good quality content</p>
          <p v-else-if="reviewData.overall_score >= 50">⚠️ Fair quality - needs improvement</p>
          <p v-else>❌ Poor quality - significant improvements needed</p>
        </div>
      </div>

      <!-- Detailed Factors -->
      <div class="wandascore-factors">
        <div class="wandascore-factor" v-for="(factor, key) in reviewData.factors" :key="key">
          <div class="wandascore-factor-header">
            <div class="wandascore-factor-name">
              <span class="wandascore-factor-icon">{{ getFactorIcon(key) }}</span>
              {{ getFactorLabel(key) }}
            </div>
            <div class="wandascore-factor-score" :class="getScoreClass(factor.score)">
              {{ factor.score }}
            </div>
          </div>
          <div class="wandascore-factor-details">
            <cdx-message :type="getMessageType(factor.score)">
              <div v-html="factor.details"></div>
            </cdx-message>
          </div>
        </div>
      </div>

      <!-- Timestamp -->
      <div class="wandascore-timestamp">
        <small>{{ msg('wandascore-review-timestamp') }}: {{ formatTimestamp(reviewData.timestamp) }}</small>
      </div>
    </div>
  </div>
</template>

<script>
const { CdxButton, CdxProgressBar, CdxMessage } = require( '../codex.js' );

module.exports = exports = {
  name: 'ReviewPage',
  components: { CdxButton, CdxProgressBar, CdxMessage },
  data() {
    return {
      reviewData: null,
      loading: true,
      error: false,
      pageTitle: mw.config.get( 'wgWandaScorePageTitle' ) || '',
      pageUrl: mw.config.get( 'wgWandaScorePageUrl' ) || ''
    };
  },
  computed: {
    overallScoreClass() {
      if ( !this.reviewData ) {
        return '';
      }
      const score = this.reviewData.overall_score;
      if ( score >= 90 ) {
        return 'wandascore-excellent';
      } else if ( score >= 70 ) {
        return 'wandascore-good';
      } else if ( score >= 50 ) {
        return 'wandascore-fair';
      }
      return 'wandascore-poor';
    }
  },
  mounted() {
    this.fetchReview();
  },
  methods: {
    msg( key ) {
      return mw.message( key ).text();
    },
    async fetchReview( refresh = false ) {
      this.loading = true;
      this.error = false;

      try {
        const api = new mw.Api();
        const data = await api.get( {
          action: 'wandascore',
          page: this.pageTitle,
          refresh: refresh,
          format: 'json'
        } );

        if ( data && data.wandascore ) {
          this.reviewData = data.wandascore;
        } else {
          this.error = true;
        }
      } catch ( e ) {
        console.error( 'WandaScore: Error fetching review', e );
        this.error = true;
      } finally {
        this.loading = false;
      }
    },
    refreshScore() {
      this.fetchReview( true );
    },
    getFactorLabel( key ) {
      const labels = {
        bias: this.msg( 'wandascore-review-bias-label' ),
        llm_generated: this.msg( 'wandascore-review-llm-label' ),
        language: this.msg( 'wandascore-review-language-label' ),
        grammar: this.msg( 'wandascore-review-grammar-label' ),
        conciseness: this.msg( 'wandascore-review-conciseness-label' )
      };
      return labels[ key ] || key;
    },
    getFactorIcon( key ) {
      const icons = {
        bias: '⚖️',
        llm_generated: '🤖',
        language: '🌐',
        grammar: '✍️',
        conciseness: '📝'
      };
      return icons[ key ] || '📊';
    },
    getScoreClass( score ) {
      if ( score >= 90 ) {
        return 'score-excellent';
      } else if ( score >= 70 ) {
        return 'score-good';
      } else if ( score >= 50 ) {
        return 'score-fair';
      }
      return 'score-poor';
    },
    getMessageType( score ) {
      if ( score >= 80 ) {
        return 'success';
      } else if ( score >= 60 ) {
        return 'notice';
      } else if ( score >= 40 ) {
        return 'warning';
      }
      return 'error';
    },
    formatTimestamp( timestamp ) {
      if ( !timestamp ) {
        return '';
      }
      // Convert MediaWiki timestamp (YYYYMMDDHHmmss) to readable format
      const year = timestamp.substr( 0, 4 );
      const month = timestamp.substr( 4, 2 );
      const day = timestamp.substr( 6, 2 );
      const hour = timestamp.substr( 8, 2 );
      const minute = timestamp.substr( 10, 2 );
      
      return `${year}-${month}-${day} ${hour}:${minute}`;
    }
  }
};
</script>
