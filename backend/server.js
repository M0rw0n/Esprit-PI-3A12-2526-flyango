require('dotenv').config();
const express = require('express');
const cors = require('cors');
const OpenAI = require('openai');

const app = express();
const PORT = process.env.PORT || 5000;

app.use(cors());
app.use(express.json());

const openai = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY || 'sk демо-ключ'
});

const roomContext = {
  jurassic: `Tu es DinoGuide, un guide expert en paléontologie spécialisé dans l'ère jurassique. Tu connais tous les détails sur les dinosaures qui vivaient pendant cette période :
- Tyrannosaurus rex : Le roi des prédateurs, apex predator du crétacé supérieur
- Velociraptor : Petit mais mortel chasseur intelligent qui chassait en meute
- Triceratops : Herbivore paisible avec trois cornes, proche parent des oiseaux modernes
- Brachiosaurus : Géant sauropode au cou immensément long qui broutait la cime des arbres

Réponds de manière éducative et divertissante, comme un guide de musée enthousiaste. Utilise des emojis quand pertinent. Sois concis mais informatif.`,
  iceAge: `Tu es DinoGuide, un guide expert en paléontologie spécialisé dans l'ère glaciaire. Tu connais tous les détails sur les animaux préhistoriques de cette période :
- Mammouth laineux : Géant laineux aux défenses impressionnantes qui errait dans les plaines glacées
- Smilodon : Prédateur redoutable aux longues canines en forme de sabre, connu aussi comme tigre à dents de sabre
- Megatherium : Paresseux géante au sol, herbivore massif qui pouvait se dresser sur ses pattes arrière

Réponds de manière éducative et divertissante, comme un guide de musée enthousiaste. Utilise des emojis quand pertinent. Sois concis mais informatif.`
};

const roomIntro = {
  jurassic: "Bienvenue dans la Salle Jurassique ! 🦖 Ici, nous voyageons jusqu'au Jurassique, il y a 200 millions d'années...",
  iceAge: "Bienvenue dans la Salle de l'Ère Glaciaire ! ❄️ Nous voilà partis dans le froid du Pléistocène, il y a seulement 20 000 ans..."
};

app.post('/api/chat', async (req, res) => {
  try {
    const { message, room = 'jurassic' } = req.body;

    if (!message) {
      return res.status(400).json({ error: 'Message is required' });
    }

    const context = roomContext[room] || roomContext.jurassic;

    const completion = await openai.chat.completions.create({
      model: 'gpt-3.5-turbo',
      messages: [
        { role: 'system', content: context },
        { role: 'user', content: message }
      ],
      max_tokens: 500,
      temperature: 0.7
    });

    const response = completion.choices[0]?.message?.content || 'Désolé, je n\'ai pas pu générer une réponse.';
    res.json({ response });

  } catch (error) {
    console.error('Error calling OpenAI:', error.message);

    const fallbackResponses = {
      jurassic: [
        "Fascinant ! Le Jurassique était une période incroyable pour les dinosaures ! 🦖",
        "Ah, tu poses une question intéressante sur nos amis dinosaures ! Chaque découverte nous en apprend plus.",
        "Les dinosaures ont régné sur Terre pendant 165 millions d'années ! Bien plus que nous, les humains !",
        "Imagine-toi traverser une forêt jurassique... Les scents des fougères géantes t'entourent, et au loin, le rugissement d'un T-Rex ! 🦖"
      ],
      iceAge: [
        "L'ère glaciaire était une période rude mais fascinante ! ❄️",
        "Ces animaux étaient parfaitement adaptés au froid extreme !",
        "Le mammouth pouvait pesar jusqu'à 6 tonnes ! Imagine un elephant deux fois plus grand !",
        "La chasse glaciaire exigeait des compétences extraordinaires de nos ancêtres !"
      ]
    };

    const room = req.body.room || 'jurassic';
    const responses = fallbackResponses[room] || fallbackResponses.jurassic;
    const randomResponse = responses[Math.floor(Math.random() * responses.length)];

    res.json({ response: randomResponse });
  }
});

app.get('/api/intro/:room', (req, res) => {
  const { room } = req.params;
  res.json({ intro: roomIntro[room] || roomIntro.jurassic });
});

app.listen(PORT, () => {
  console.log(`🎭 Server running on http://localhost:${PORT}`);
});