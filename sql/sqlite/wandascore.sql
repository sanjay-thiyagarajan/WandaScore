-- SQLite schema for WandaScore
-- Stores page quality scores and review data

CREATE TABLE IF NOT EXISTS /*_*/wandascore (
  -- Page ID from the page table
  ws_page_id INTEGER NOT NULL PRIMARY KEY,
  
  -- Overall WandaScore (0-100)
  ws_overall_score INTEGER NOT NULL DEFAULT 0,
  
  -- JSON data containing detailed scores for each factor
  ws_score_data BLOB NOT NULL,
  
  -- Timestamp when the score was generated
  ws_timestamp BLOB NOT NULL
);

-- Index for timestamp-based queries
CREATE INDEX /*i*/ws_timestamp ON /*_*/wandascore (ws_timestamp);

-- Index for score-based queries
CREATE INDEX /*i*/ws_overall_score ON /*_*/wandascore (ws_overall_score);
