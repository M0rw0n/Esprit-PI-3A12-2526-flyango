import { Link } from 'react-router-dom';
import './Home.css';

const Home = () => {
  return (
    <div className="home-page">
      <div className="hero-background">
        <div className="particles">
          <span className="particle">🦖</span>
          <span className="particle">🦕</span>
          <span className="particle">🐘</span>
          <span className="particle">🦴</span>
          <span className="particle">🌋</span>
          <span className="particle">🌿</span>
          <span className="particle">❄️</span>
          <span className="particle">🦖</span>
        </div>
        <div className="hero-gradient"></div>
      </div>

      <div className="hero-content">
        <div className="hero-badge">
          <span>🎭 Musée Virtuel</span>
        </div>

        <h1 className="hero-title">
          <span className="title-line animate-fade-in-up">Voyage dans</span>
          <span className="title-line animate-fade-in-up delay-1">le temps</span>
        </h1>

        <p className="hero-subtitle animate-fade-in-up delay-2">
          Les animaux préhistoriques
        </p>

        <div className="hero-description animate-fade-in-up delay-3">
          <p>
            Explorez les creatures géantes qui ont marqué l'histoire de notre planète.
            Des dinosaures du Jurassique aux mammouths de l'ère glaciaire.
          </p>
        </div>

        <div className="hero-actions animate-fade-in-up delay-4">
          <Link to="/jurassic" className="btn btn-primary btn-large">
            <span>Entrer dans le musée</span>
            <span className="btn-icon">→</span>
          </Link>
        </div>

        <div className="hero-features animate-fade-in-up delay-4">
          <div className="feature">
            <span className="feature-icon">🦖</span>
            <span className="feature-text">7 Dinosaures</span>
          </div>
          <div className="feature">
            <span className="feature-icon">🏛️</span>
            <span className="feature-text">2 Salles</span>
          </div>
          <div className="feature">
            <span className="feature-icon">🤖</span>
            <span className="feature-text">Guide IA</span>
          </div>
        </div>
      </div>

      <div className="scroll-indicator">
        <span>Scroll vers le bas</span>
        <div className="scroll-arrow">↓</div>
      </div>
    </div>
  );
};

export default Home;