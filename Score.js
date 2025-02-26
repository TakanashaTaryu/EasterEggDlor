export default class Score {
  score = 0;
  HIGH_SCORE_KEY = "highScore";
  playerName = "";

  constructor(ctx, scaleRatio) {
    this.ctx = ctx;
    this.canvas = ctx.canvas;
    this.scaleRatio = scaleRatio;
    
    // Get player name from global variable set in game.php
    if (typeof playerName !== 'undefined') {
      this.playerName = playerName;
    }
  }

  update(frameTimeDelta) {
    this.score += frameTimeDelta * 0.01;
  }

  reset() {
    this.score = 0;
  }

  setHighScore() {
    const currentScore = Math.floor(this.score);
    const highScore = Number(localStorage.getItem(this.HIGH_SCORE_KEY));
    
    if (currentScore > highScore) {
      localStorage.setItem(this.HIGH_SCORE_KEY, currentScore);
    }
    
    // Send score to server
    this.sendScoreToServer(currentScore);
  }
  
  sendScoreToServer(score) {
    if (!this.playerName) return;
    
    fetch('save_score.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `playerName=${encodeURIComponent(this.playerName)}&score=${encodeURIComponent(score)}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.newHighScore) {
        alert(`Congratulations! New high score: ${score}`);
      }
    })
    .catch(error => {
      console.error('Error saving score:', error);
    });
  }

  draw() {
    const highScore = Number(localStorage.getItem(this.HIGH_SCORE_KEY));
    const y = 20 * this.scaleRatio;

    const fontSize = 20 * this.scaleRatio;
    this.ctx.font = `${fontSize}px serif`;
    this.ctx.fillStyle = "#525250";
    const scoreX = this.canvas.width - 75 * this.scaleRatio;
    const highScoreX = scoreX - 125 * this.scaleRatio;
    const nameX = 20 * this.scaleRatio;

    const scorePadded = Math.floor(this.score).toString().padStart(6, 0);
    const highScorePadded = highScore.toString().padStart(6, 0);

    // Draw player name
    this.ctx.fillText(this.playerName, nameX, y);
    
    // Draw scores
    this.ctx.fillText(scorePadded, scoreX, y);
    this.ctx.fillText(`HI ${highScorePadded}`, highScoreX, y);
  }
}