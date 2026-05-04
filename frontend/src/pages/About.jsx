import './About.css';

const About = () => {
  return (
    <div className="about-page">
      <div className="container">
        <div className="about-hero">
          <span className="about-icon">🏛️</span>
          <h1 className="about-title">À propos du musée</h1>
          <p className="about-subtitle">
            Une expérience éducative et immersif pour découvrir les animaux préhistoriques
          </p>
        </div>

        <div className="about-content">
          <div className="about-section">
            <h2>Notre mission</h2>
            <p>
              Le Musée Virtuel "Voyage dans le temps" vous invite à une aventure unique à travers lesères géologiques de notre planète. Grâce à la technologie moderne et l'intelligence artificielle, nous rendons accessible à tous la fascinante histoire des créatures préhistoriques qui ont peuplé la Terre avant nous.
            </p>
          </div>

          <div className="about-features">
            <div className="about-feature">
              <div className="feature-header">
                <span className="feature-icon">🦖</span>
                <h3>Exposition Jurassique</h3>
              </div>
              <p>
                Explorez l'ère des dinosaures, de -200 à -65 millions d'années. 
                Découvrez le Tyrannosaurus rex, le Velociraptor, le Triceratops et le Brachiosaurus.
              </p>
            </div>

            <div className="about-feature">
              <div className="feature-header">
                <span className="feature-icon">❄️</span>
                <h3>Exposition Ère Glaciaire</h3>
              </div>
              <p>
                Plongez dans le Pléistocène, il y a 20 000 ans. 
                Rencontrez le Mammouth laineux, le Smilodon et le Megatherium.
              </p>
            </div>

            <div className="about-feature">
              <div className="feature-header">
                <span className="feature-icon">🤖</span>
                <h3>DinoGuide IA</h3>
              </div>
              <p>
                Notre assistant IA vous guide à travers les salles, répond à vos questions
                et rend votre visite encore plus interactive et éducative.
              </p>
            </div>
          </div>

          <div className="about-section">
            <h2>Crédits</h2>
            <div className="credits">
              <div className="credit-item">
                <span className="credit-label">Développement</span>
                <span className="credit-value">React + Node.js</span>
              </div>
              <div className="credit-item">
                <span className="credit-label"> IA</span>
                <span className="credit-value">OpenAI GPT-3.5 Turbo</span>
              </div>
              <div className="credit-item">
                <span className="credit-label">Design</span>
                <span className="credit-value">Musée Virtuel 2024</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default About;