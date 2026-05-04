import { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import './Navbar.css';

const Navbar = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const location = useLocation();

  const isActive = (path) => location.pathname === path;

  return (
    <nav className="navbar">
      <div className="navbar-container">
        <Link to="/" className="navbar-logo">
          <span className="logo-icon">🦖</span>
          <span className="logo-text">Musée Virtuel</span>
        </Link>

        <button 
          className={`hamburger ${isMenuOpen ? 'active' : ''}`}
          onClick={() => setIsMenuOpen(!isMenuOpen)}
          aria-label="Menu"
        >
          <span></span>
          <span></span>
          <span></span>
        </button>

        <ul className={`navbar-links ${isMenuOpen ? 'open' : ''}`}>
          <li>
            <Link 
              to="/" 
              className={`nav-link ${isActive('/') ? 'active' : ''}`}
              onClick={() => setIsMenuOpen(false)}
            >
              Accueil
            </Link>
          </li>
          <li>
            <Link 
              to="/jurassic" 
              className={`nav-link ${isActive('/jurassic') ? 'active' : ''}`}
              onClick={() => setIsMenuOpen(false)}
            >
              Salle Jurassique
            </Link>
          </li>
          <li>
            <Link 
              to="/ice-age" 
              className={`nav-link ${isActive('/ice-age') ? 'active' : ''}`}
              onClick={() => setIsMenuOpen(false)}
            >
              Salle Ère Glaciaire
            </Link>
          </li>
          <li>
            <Link 
              to="/about" 
              className={`nav-link ${isActive('/about') ? 'active' : ''}`}
              onClick={() => setIsMenuOpen(false)}
            >
              À propos
            </Link>
          </li>
        </ul>
      </div>
    </nav>
  );
};

export default Navbar;