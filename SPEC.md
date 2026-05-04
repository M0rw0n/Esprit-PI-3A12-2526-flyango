# Voyage dans le temps – Les animaux préhistoriques

## Project Overview

- **Project Name**: Musée Virtuel - Voyage dans le temps
- **Type**: Full-stack web application (React + Node.js)
- **Core Functionality**: Interactive virtual museum featuring prehistoric animals with AI-powered chatbot guide
- **Target Users**: History enthusiasts, students, families interested in paleontology

---

## Architecture

### Frontend (React + Vite)
- Modern SPA with React Router
- Responsive design (mobile-first)
- Smooth animations with CSS/Framer Motion

### Backend (Node.js + Express)
- REST API for chatbot
- OpenAI integration for AI responses
- CORS enabled for local development

---

## UI/UX Specification

### Color Palette
| Name | Hex Code | Usage |
|------|---------|-------|
| Primary Blue | #0D5BD7 | Buttons, active states, accents |
| Orange | #FF6B2C | Hover states, highlights |
| White | #FFFFFF | Cards, modals, content backgrounds |
| Background | #F4F7FB | Page backgrounds |
| Dark Text | #1A1A2E | Primary text |
| Light Text | #6B7280 | Secondary text |

### Typography
- **Headings**: "Playfair Display", serif (Google Fonts)
- **Body**: "Inter", sans-serif (Google Fonts)
- **Sizes**:
  - H1: 48px (mobile: 32px)
  - H2: 36px (mobile: 24px)
  - H3: 24px (mobile: 18px)
  - Body: 16px
  - Small: 14px

### Spacing System
- Base unit: 8px
- Sections: 80px vertical padding
- Cards: 24px padding
- Elements: 16px gap

### Visual Effects
- Card shadows: `0 4px 20px rgba(13, 91, 215, 0.1)`
- Hover shadow: `0 8px 30px rgba(13, 91, 215, 0.2)`
- Border radius: 16px (cards), 12px (buttons), 50% (avatars)
- Transitions: 300ms ease-out

---

## Page Structure

### 1. Home Page (/)
**Layout**:
- Full viewport height
- Centered content with animated title
- Background: Immersive gradient with floating particles or dinosaur silhouette

**Components**:
- Animated title: "Voyage dans le temps" (fade-in slide-up)
- Subtitle: "Les animaux préhistoriques"
- CTA Button: "Entrer dans le musée" (Primary Blue, hover Orange)
- Decorative background elements

**Animations**:
- Title: Slide-up with fade (1s delay 0.3s)
- Subtitle: Fade-in (1s delay 0.8s)
- Button: Scale on hover + shadow expansion

### 2. Navigation Bar
**Structure**:
- Fixed top position
- Logo on left: "🦖 Musée Virtuel"
- Nav links on right: Accueil, Salle Jurassique, Salle Ère Glaciaire, À propos
- Mobile: Hamburger menu

**Styling**:
- Background: White with subtle shadow
- Links: Dark text, blue on hover
- Active state: Blue underline

### 3. Jurassic Room (/jurassic)

**Data**:
| Animal | Order | Description |
|--------|-------|-------------|
| Tyrannosaurus rex | Saurischia | King of dinosaurs, apex predator |
| Velociraptor | Saurischia | Swift hunter, intelligent pack hunter |
| Triceratops | Ornithischia | Three-horned herbivore |
| Brachiosaurus | Saurischia | Giant long-necked sauropod |

**Layout**:
- Hero section with room title
- Grid of 4 cards (2x2 desktop, 1 column mobile)

**Card Design**:
- Image placeholder (dinosaur illustration)
- Animal name (H3)
- Order badge
- Short description (2 lines)
- "Explorer" button

**Interactions**:
- Card hover: Lift + shadow expansion
- "Explorer" click: Opens modal

### 4. Modal Component
**Structure**:
- Overlay: Semi-transparent dark
- Modal: White card, centered, max-width 600px
- Close button (X) top-right
- Large image
- Full description
- "Fermer" button

**Animations**:
- Open: Fade-in overlay + scale-up modal
- Close: Reverse animation

### 5. Ice Age Room (/ice-age)

**Data**:
| Animal | Order | Description |
|--------|-------|-------------|
| Mammouth laineux | Proboscidea | Woolly giant of the ice |
| Smilodon | Carnivora | Saber-toothed predator |
| Megatherium | Pilosa | Giant ground sloth |

**Layout**: Same as Jurassic Room

### 6. About Page (/about)
**Content**:
- Museum description
- Educational purpose
- Technology credits

---

## Chatbot "DinoGuide 🦖"

### UI Design
- Floating button: Bottom-right, fixed position
- Button style: Primary Blue circle with dino icon
- Size: 56px diameter
- Shadow: Card shadow

**Chat Window**:
- Position: Above button
- Size: 350px width, 450px height
- Background: White
- Border radius: 16px
- Header: DinoGuide with avatar
- Message area: Scrollable
- Input area: Text input + send button

### Functionality
- Toggle open/close on button click
- Send message on Enter or button click
- Display user messages (right-aligned, blue bubble)
- Display AI responses (left-aligned, gray bubble)
- Auto-scroll to bottom

### Context Behavior
- **Jurassic room**: Only dinosaur-related responses
- **Ice Age room**: Only Ice Age animal responses
- **Default**: General prehistoric facts

### Initial Messages
- "Bonjour ! Je suis DinoGuide, ton guide pour explorer le musée !"
- "Bienvenue dans le Jurassique... Ici vivaient les rois des dinosaurs !"
- "Attention... un T-Rex approche ! 🦖"

---

## Backend API

### Endpoint: POST /api/chat

**Request**:
```json
{
  "message": "string",
  "room": "jurassic" | "iceAge"
}
```

**Response**:
```json
{
  "response": "string"
}
```

**Logic**:
1. Receive message and room context
2. Build context prompt based on room
3. Call OpenAI API (GPT-3.5 Turbo)
4. Return AI response

### Environment Variables
- OPENAI_API_KEY: OpenAI API key

---

## File Structure

```
/prehistoric-museum
├── /backend
│   ├── package.json
│   ├── server.js
│   └── .env
├── /frontend
│   ├── package.json
│   ├── /vite.config.js
│   ├── /index.html
│   ├── /src
│   │   ├── /main.jsx
│   │   ├── /App.jsx
│   │   ├── /index.css
│   │   ├── /components
│   │   │   ├── /Navbar.jsx
│   │   │   ├── /Chatbot.jsx
│   │   │   ├── /AnimalCard.jsx
│   │   │   └── /Modal.jsx
│   │   ├── /pages
│   │   │   ├── /Home.jsx
│   │   │   ├── /Jurassic.jsx
│   │   │   ├── /IceAge.jsx
│   │   │   └── /About.jsx
│   │   └── /data
│   │       ├── /dinosaurs.js
│   │       └── /iceAgeAnimals.js
│   └── /public
└── README.md
```

---

## Acceptance Criteria

### Visual
- [ ] Home page shows animated title and CTA button
- [ ] Navigation is fixed and responsive
- [ ] Cards display with proper shadows and hover effects
- [ ] Modal opens/closes with smooth animations
- [ ] Chatbot toggles and sends/receives messages

### Functionality
- [ ] All routes work (/, /jurassic, /ice-age, /about)
- [ ] Cards open modals with full details
- [ ] Chatbot sends messages to API and displays responses
- [ ] API returns contextual responses based on room

### Technical
- [ ] No console errors
- [ ] Responsive on mobile (320px) to desktop (1920px)
- [ ] API handles errors gracefully

---

## Dependencies

### Backend
- express
- cors
- dotenv
- openai

### Frontend
- react
- react-dom
- react-router-dom
- framer-motion (optional for advanced animations)