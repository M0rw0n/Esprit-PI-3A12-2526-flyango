import { useEffect } from 'react';
import './Modal.css';

const Modal = ({ animal, isOpen, onClose }) => {
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'unset';
    }
    return () => {
      document.body.style.overflow = 'unset';
    };
  }, [isOpen]);

  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === 'Escape') {
        onClose();
      }
    };
    if (isOpen) {
      window.addEventListener('keydown', handleEscape);
    }
    return () => {
      window.removeEventListener('keydown', handleEscape);
    };
  }, [isOpen, onClose]);

  if (!isOpen || !animal) return null;

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content" onClick={(e) => e.stopPropagation()}>
        <button className="modal-close" onClick={onClose} aria-label="Fermer">
          ×
        </button>
        
        <div className="modal-image">
          <span className="modal-emoji">{animal.image}</span>
        </div>
        
        <div className="modal-body">
          <div className="modal-header">
            <h2 className="modal-title">{animal.name}</h2>
            <span className="modal-order">{animal.order}</span>
          </div>
          
          <div className="modal-stats">
            <div className="stat-item">
              <span className="stat-label">Période</span>
              <span className="stat-value">{animal.period}</span>
            </div>
            <div className="stat-item">
              <span className="stat-label">Longueur</span>
              <span className="stat-value">{animal.length}</span>
            </div>
            <div className="stat-item">
              <span className="stat-label">Poids</span>
              <span className="stat-value">{animal.weight}</span>
            </div>
            <div className="stat-item">
              <span className="stat-label">Régime</span>
              <span className="stat-value">{animal.diet}</span>
            </div>
          </div>
          
          <div className="modal-description">
            <h3>Description</h3>
            <p>{animal.fullDescription}</p>
          </div>
          
          <button className="btn btn-primary modal-btn" onClick={onClose}>
            Fermer
          </button>
        </div>
      </div>
    </div>
  );
};

export default Modal;