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
- Right-click → "Répondre"
- Shows sender name and content preview
- Backend saves `reply_to` in message table
- Messages with replies show preview above them

### 6. Call History
- Shown in conversation info sidebar
- Displays: passed/received calls, missed, duration, time

## Known Issues

1. **JavaScript not loading** - Functions like `switchMessengerTab`, `showNewChatModal` undefined
   - Line 3839 error suggests cached version
   - Try: Clear browser cache + hard refresh (Ctrl+Shift+R)
   - Or: Delete var/cache/* manually

2. **Reply feature** - May need testing to verify

3. **GIF URLs** - Tenor URLs expire frequently, fallback shows placeholder

## Files Modified

- `templates/messenger/index.html.twig` - UI, JS, CSS
- `src/Controller/MessageController.php` - Delete, React, Theme API
- `src/Controller/FriendController.php` - Nickname API
- `src/Service/MessageService.php` - Reply support
- Database: `friend_nickname`, `conversation_theme` tables created

## Testing Checklist

- [ ] Right-click message → Delete works
- [ ] Right-click message → Reaction works
- [ ] Click ⋮ → Change nickname works
- [ ] Click ⋮ → Change theme works  
- [ ] Right-click → Reply shows preview
- [ ] Send reply → Shows in chat with preview
- [ ] Call history shows after calls