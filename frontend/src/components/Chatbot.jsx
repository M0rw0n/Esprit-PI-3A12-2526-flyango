import { useState, useEffect, useRef } from 'react';
import { useLocation } from 'react-router-dom';
import './Chatbot.css';

const Chatbot = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [messages, setMessages] = useState([
    { id: 1, text: "Bonjour ! Je suis DinoGuide 🦖", sender: 'bot' },
    { id: 2, text: "Ton guide pour explorer le musée virtuel ! Pose-moi des questions sur les animaux préhistoriques.", sender: 'bot' }
  ]);
  const [input, setInput] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const location = useLocation();
  const messagesEndRef = useRef(null);

  const getRoom = () => {
    if (location.pathname === '/jurassic') return 'jurassic';
    if (location.pathname === '/ice-age') return 'iceAge';
    return 'jurassic';
  };

  const getIntroMessage = () => {
    if (location.pathname === '/jurassic') {
      return "Bienvenue dans la Salle Jurassique ! 🦖 Ici, nous voyageons jusqu'au Jurassique, il y a 200 millions d'années... Les rois des dinosaures t'attendent !";
    }
    if (location.pathname === '/ice-age') {
      return "Bienvenue dans la Salle de l'Ère Glaciaire ! ❄️ Nous voilà partis dans le froid du Pléistocène, il y a seulement 20 000 ans... Les géants de la glace t'attendent !";
    }
    return "Bienvenue au Musée Virtuel ! 🦖 Choisis une salle pour commencer ton voyage dans le temps !";
  };

  useEffect(() => {
    if (!messages.find(m => m.text.includes('Bienvenue'))) {
      const introMessage = {
        id: Date.now(),
        text: getIntroMessage(),
        sender: 'bot'
      };
      setMessages(prev => [...prev, introMessage]);
    }
  }, [location.pathname]);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  const sendMessage = async () => {
    if (!input.trim() || isLoading) return;

    const userMessage = {
      id: Date.now(),
      text: input.trim(),
      sender: 'user'
    };

    setMessages(prev => [...prev, userMessage]);
    setInput('');
    setIsLoading(true);

    try {
      const room = getRoom();
      const response = await fetch('/api/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          message: userMessage.text,
          room: room
        })
      });

      const data = await response.json();
      
      const botMessage = {
        id: Date.now() + 1,
        text: data.response || "Désolé, je n'ai pas pu générer une réponse.",
        sender: 'bot'
      };

      setMessages(prev => [...prev, botMessage]);
    } catch (error) {
      console.error('Error sending message:', error);
      const errorMessage = {
        id: Date.now() + 1,
        text: "Oups ! Quelque chose s'est mal passées. Réessayez !",
        sender: 'bot'
      };
      setMessages(prev => [...prev, errorMessage]);
    } finally {
      setIsLoading(false);
    }
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  };

  return (
    <div className="chatbot-container">
      <button 
        className={`chatbot-toggle ${isOpen ? 'open' : ''}`}
        onClick={() => setIsOpen(!isOpen)}
        aria-label={isOpen ? 'Fermer le chat' : 'Ouvrir le chat'}
      >
        {isOpen ? '✕' : '🦖'}
      </button>

      <div className={`chatbot-window ${isOpen ? 'open' : ''}`}>
        <div className="chatbot-header">
          <div className="chatbot-avatar">🦖</div>
          <div className="chatbot-title">
            <h3>DinoGuide</h3>
            <span>Votre guide de musée</span>
          </div>
        </div>

        <div className="chatbot-messages">
          {messages.map((msg) => (
            <div 
              key={msg.id} 
              className={`message ${msg.sender === 'user' ? 'user' : 'bot'}`}
            >
              <div className="message-content">
                {msg.text}
              </div>
            </div>
          ))}
          {isLoading && (
            <div className="message bot">
              <div className="message-content typing">
                <span className="typing-dot"></span>
                <span className="typing-dot"></span>
                <span className="typing-dot"></span>
              </div>
            </div>
          )}
          <div ref={messagesEndRef} />
        </div>

        <div className="chatbot-input">
          <input
            type="text"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            onKeyPress={handleKeyPress}
            placeholder="Pose une question..."
            disabled={isLoading}
          />
          <button 
            onClick={sendMessage}
            disabled={isLoading || !input.trim()}
            aria-label="Envoyer"
          >
            ➤
          </button>
        </div>
      </div>
    </div>
  );
};

export default Chatbot;