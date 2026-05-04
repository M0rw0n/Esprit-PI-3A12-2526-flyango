import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Chatbot from './components/Chatbot';
import Home from './pages/Home';
import Jurassic from './pages/Jurassic';
import IceAge from './pages/IceAge';
import About from './pages/About';

function App() {
  return (
    <Router>
      <div className="app">
        <Navbar />
        <main>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/jurassic" element={<Jurassic />} />
            <Route path="/ice-age" element={<IceAge />} />
            <Route path="/about" element={<About />} />
          </Routes>
        </main>
        <Chatbot />
      </div>
    </Router>
  );
}

export default App;