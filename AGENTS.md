# Fly&Go Messenger - Tasks Summary

## Completed Features

### 1. Delete Message
- Right-click on own message → "Supprimer"
- Backend: `DELETE /api/messages/delete/{id}`
- Users can only delete their own messages

### 2. React to Message  
- Right-click on any message → "Réaction"
- Picker shows: 👍 ❤️ 😂 😮 😢 😡 🙏 👎
- Backend: `POST /api/messages/react/{id}`, `POST /api/messages/unreact/{id}`

### 3. Change Friend's Nickname
- Click ⋮ in chat header → enter nickname
- Backend: `POST /api/friend/nickname`
- Stored in `friend_nickname` table

### 4. Change Conversation Theme
- Click ⋮ in chat header → click color
- 12 colors (solid + gradients)
- Backend: `POST /api/messages/theme`
- Stored in `conversation_theme` table

### 5. Reply to Message
- Right-click → "Répondre" or click ↩️ button
- Shows sender name and content preview in chat bubble
- Active reply bar appears above input with cancel button
- Backend: `POST /api/messages/conversation/{id}/messages` with `replyTo` in body
- Messages with replies show preview above them with styled border
- Mercure real-time updates include replyTo data

### 6. Call History
- Shown in conversation info sidebar
- Displays: passed/received calls, missed, duration, time

## Known Issues

1. **JavaScript not loading** - Functions like `switchMessengerTab`, `showNewChatModal` undefined
   - Line 3839 error suggests cached version
   - Try: Clear browser cache + hard refresh (Ctrl+Shift+R)
   - Or: Delete var/cache/* manually

2. **GIF URLs** - Tenor URLs expire frequently, fallback shows placeholder

## Files Modified

- `templates/messenger/index.html.twig` - UI, JS, CSS, reply active bar
- `src/Controller/MessageController.php` - Delete, React, Theme, Reply API
- `src/Controller/FriendController.php` - Nickname API
- `src/Service/MessageService.php` - Reply support, Mercure replyTo data
- `src/Repository/MessageRepository.php` - SQL joins for reply_to data
- `src/Entity/Message.php` - Fixed reply_to column name mapping
- `public/js/messenger.js` - Reply send, preview, active bar, clear
- Database: `friend_nickname`, `conversation_theme` tables created

## Testing Checklist

- [x] Right-click message → Reply shows preview
- [x] Send reply → Shows in chat with preview
- [x] Reply preview rendered in message bubble
- [x] Active reply bar appears above input
- [x] Cancel reply clears selection
- [ ] Right-click message → Delete works
- [ ] Right-click message → Reaction works
- [ ] Click ⋮ → Change nickname works
- [ ] Click ⋮ → Change theme works  
- [ ] Call history shows after calls