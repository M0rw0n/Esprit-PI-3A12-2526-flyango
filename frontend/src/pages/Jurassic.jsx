import { useState } from 'react';
import { dinosaurs } from '../data/dinosaurs';
import AnimalCard from '../components/AnimalCard';
import Modal from '../components/Modal';
import './Room.css';

const Jurassic = () => {
  const [selectedAnimal, setSelectedAnimal] = useState(null);
  const [isModalOpen, setIsModalOpen] = useState(false);

  const handleExplore = (animal) => {
    setSelectedAnimal(animal);
    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
  };

  return (
    <div className="room-page">
      <div className="room-hero">
        <div className="room-hero-bg">
          <span className="room-bg-emoji">🦖</span>
        </div>
        <div className="container">
          <div className="room-hero-content">
            <span className="room-badge">Salle Jurassique</span>
            <h1 className="room-title">L'ère des géants</h1>
            <p className="room-description">
              Il y a 200 millions d'années, les dinosaures régnaient sur Terre.
              Découvrez les créatures les plus impressionnantes de l'histoire de notre planète.
            </p>
          </div>
        </div>
      </div>

      <div className="container">
        <div className="room-content">
          <div className="animals-grid">
            {dinosaurs.map((dinosaur, index) => (
              <div 
                key={dinosaur.id} 
                className="animal-card-wrapper"
                style={{ animationDelay: `${index * 0.15}s` }}
              >
                <AnimalCard 
                  animal={dinosaur} 
                  onExplore={handleExplore} 
                />
              </div>
            ))}
          </div>
        </div>
      </div>

      <Modal 
        animal={selectedAnimal}
        isOpen={isModalOpen}
        onClose={handleCloseModal}
      />
    </div>
  );
};

export default Jurassic;