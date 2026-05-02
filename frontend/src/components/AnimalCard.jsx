import './AnimalCard.css';

const AnimalCard = ({ animal, onExplore }) => {
  return (
    <div className="animal-card">
      <div className="animal-card-image">
        <span className="animal-emoji">{animal.image}</span>
      </div>
      <div className="animal-card-content">
        <div className="animal-card-header">
          <h3 className="animal-name">{animal.name}</h3>
          <span className="animal-order">{animal.order}</span>
        </div>
        <p className="animal-description">{animal.shortDescription}</p>
        <button className="btn btn-primary explore-btn" onClick={() => onExplore(animal)}>
          Explorer
          <span className="btn-arrow">→</span>
        </button>
      </div>
    </div>
  );
};

export default AnimalCard;