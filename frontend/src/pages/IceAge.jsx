import { useState } from 'react';
import { iceAgeAnimals } from '../data/iceAgeAnimals';
import AnimalCard from '../components/AnimalCard';
import Modal from '../components/Modal';
import './Room.css';

const IceAge = () => {
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
      <div className="room-hero room-hero-ice">
        <div className="room-hero-bg">
          <span className="room-bg-emoji">❄️</span>
        </div>
        <div className="container">
          <div className="room-hero-content">
            <span className="room-badge">Salle Ère Glaciaire</span>
            <h1 className="room-title">Le temps des glaciers</h1>
            <p className="room-description">
              Il y a 20 000 ans, des créatures colossales parcouraient les plaines glacées.
              Mammouths, tigres à dents de sabre et paresseux géants vous attendent.
            </p>
          </div>
        </div>
      </div>

      <div className="container">
        <div className="room-content">
          <div className="animals-grid">
            {iceAgeAnimals.map((animal, index) => (
              <div 
                key={animal.id} 
                className="animal-card-wrapper"
                style={{ animationDelay: `${index * 0.15}s` }}
              >
                <AnimalCard 
                  animal={animal} 
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

export default IceAge;