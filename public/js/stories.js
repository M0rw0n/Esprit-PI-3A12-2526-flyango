/**
 * Fly&Go Stories System
 * Full Instagram-like experience
 */

let storyFeed = [];
let currentGroupIndex = 0;
let currentStoryIndex = 0;
let storyTimer = null;
const STORY_DURATION = 5000; // 5 seconds per story

document.addEventListener('DOMContentLoaded', () => {
    loadStories();
    setupMercureForStories();
});

// --- API LOADING ---

async function loadStories() {
    try {
        const response = await fetch('/api/story/feed');
        const data = await response.json();
        if (data.success) {
            storyFeed = data.feed;
            renderStoryCircles();
        }
    } catch (err) {
        console.error('Error loading stories:', err);
    }
}

function renderStoryCircles() {
    const container = document.getElementById('storiesContainer');
    if (!container) return;

    let html = '';
    
    // Add Story button
    if (currentUserId) {
        html += `
            <div class="story-circle" onclick="openStoryUploader()" 
                 style="flex-shrink: 0; cursor: pointer; text-align: center; width: 80px;">
                <div style="width: 66px; height: 66px; border-radius: 50%; padding: 3px; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-plus" style="font-size: 1.8rem; color: #fff;"></i>
                </div>
                <span style="font-size: 0.75rem; font-weight: 600; color: var(--navy); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Ajouter</span>
            </div>
        `;
    }
    
    html += storyFeed.map((group, groupIdx) => {
        const hasUnviewed = group.stories.some(s => !s.viewed);
        const avatar = group.userAvatar ? group.userAvatar : `https://ui-avatars.com/api/?name=${encodeURIComponent(group.userName)}`;
        
        return `
            <div class="story-circle ${hasUnviewed ? 'unviewed' : 'viewed'}" 
                 onclick="openStoryViewer(${groupIdx})"
                 style="flex-shrink: 0; cursor: pointer; text-align: center; width: 80px;">
                <div class="circle-border" style="width: 66px; height: 66px; border-radius: 50%; padding: 3px; background: ${hasUnviewed ? 'linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)' : '#dbdbdb'}; margin: 0 auto 0.5rem;">
                    <img src="${avatar}" style="width: 100%; height: 100%; border-radius: 50%; border: 2px solid #fff; object-fit: cover;">
                </div>
                <span style="font-size: 0.75rem; font-weight: 600; color: var(--navy); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    ${group.userId === currentUserId ? 'Ma Story' : group.userName}
                </span>
            </div>
        `;
    }).join('');
    
    container.innerHTML = html;
}

// --- VIEWER LOGIC ---

function openStoryViewer(groupIdx) {
    currentGroupIndex = groupIdx;
    currentStoryIndex = 0;
    
    // Find first unviewed story in this group if any
    const firstUnviewed = storyFeed[groupIdx].stories.findIndex(s => !s.viewed);
    if (firstUnviewed !== -1) currentStoryIndex = firstUnviewed;

    document.getElementById('storyViewer').style.display = 'flex';
    showStory();
}

function closeStoryViewer() {
    document.getElementById('storyViewer').style.display = 'none';
    clearTimeout(storyTimer);
    const video = document.getElementById('viewerVid');
    video.pause();
    video.src = "";
}

function showStory() {
    const group = storyFeed[currentGroupIndex];
    const story = group.stories[currentStoryIndex];
    
    // Update UI
    document.getElementById('viewerName').textContent = group.userName;
    document.getElementById('viewerAvatar').src = group.userAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(group.userName)}`;
    document.getElementById('viewerMeta').textContent = formatStoryTime(story.createdAt);
    document.getElementById('viewerCaption').textContent = story.caption || '';
    
    const img = document.getElementById('viewerImg');
    const vid = document.getElementById('viewerVid');
    
    if (story.type === 'video') {
        img.style.display = 'none';
        vid.style.display = 'block';
        vid.src = story.media;
        vid.play();
    } else {
        vid.style.display = 'none';
        img.style.display = 'block';
        img.src = story.media;
    }

    renderProgressBar();
    markStoryAsViewed(story.id);

    // Auto-advance
    clearTimeout(storyTimer);
    const duration = story.type === 'video' ? 10000 : STORY_DURATION;
    storyTimer = setTimeout(nextStory, duration);
}

function nextStory() {
    currentStoryIndex++;
    if (currentStoryIndex >= storyFeed[currentGroupIndex].stories.length) {
        // Go to next user
        currentGroupIndex++;
        currentStoryIndex = 0;
        if (currentGroupIndex >= storyFeed.length) {
            closeStoryViewer();
            return;
        }
    }
    showStory();
}

function prevStory() {
    currentStoryIndex--;
    if (currentStoryIndex < 0) {
        // Go to previous user
        currentGroupIndex--;
        if (currentGroupIndex < 0) {
            currentGroupIndex = 0;
            currentStoryIndex = 0;
        } else {
            currentStoryIndex = storyFeed[currentGroupIndex].stories.length - 1;
        }
    }
    showStory();
}

function renderProgressBar() {
    const container = document.getElementById('storyProgressBar');
    const stories = storyFeed[currentGroupIndex].stories;
    
    container.innerHTML = stories.map((s, idx) => {
        let width = '0%';
        if (idx < currentStoryIndex) width = '100%';
        if (idx === currentStoryIndex) width = '0%'; // Animating part
        
        return `
            <div style="flex:1; height:2px; background:rgba(255,255,255,0.3); border-radius:2px; overflow:hidden;">
                <div id="bar-${idx}" style="width:${width}; height:100%; background:#fff; transition: ${idx === currentStoryIndex ? 'width ' + (s.type === 'video' ? '10s' : '5s') + ' linear' : 'none'};"></div>
            </div>
        `;
    }).join('');

    // Trigger animation for current bar
    setTimeout(() => {
        const bar = document.getElementById(`bar-${currentStoryIndex}`);
        if (bar) bar.style.width = '100%';
    }, 50);
}

// --- INTERACTIONS ---

async function markStoryAsViewed(id) {
    try {
        await fetch(`/api/story/${id}/view`, { method: 'POST' });
        // Update local state to avoid re-marking
        const story = storyFeed[currentGroupIndex].stories[currentStoryIndex];
        if (story) story.viewed = true;
    } catch (err) {}
}

async function reactToStory(emoji) {
    const story = storyFeed[currentGroupIndex].stories[currentStoryIndex];
    try {
        const response = await fetch(`/api/story/${story.id}/react`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ emoji })
        });
        if (response.ok) {
            showToast(`Vous avez réagi avec ${emoji}`, 'success');
        }
    } catch (err) {}
}

async function sendStoryReply() {
    const input = document.getElementById('storyReplyInput');
    const text = input.value.trim();
    if (!text) return;

    const story = storyFeed[currentGroupIndex].stories[currentStoryIndex];
    try {
        const response = await fetch(`/api/story/${story.id}/reply`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text })
        });
        if (response.ok) {
            showToast('Message envoyé !', 'success');
            input.value = '';
        }
    } catch (err) {}
}

function shareCurrentStory() {
    const story = storyFeed[currentGroupIndex].stories[currentStoryIndex];
    const shareUrl = window.location.origin + '/story/' + story.id;
    
    if (navigator.share) {
        navigator.share({
            title: 'Story de ' + storyFeed[currentGroupIndex].userName,
            text: story.caption || 'Regarde cette story !',
            url: shareUrl
        }).catch(() => {});
    } else {
        navigator.clipboard.writeText(shareUrl).then(() => {
            showToast('Lien copié !', 'success');
        });
    }
}

// --- UPLOADER ---

function openStoryUploader() {
    document.getElementById('storyUploadModal').style.display = 'flex';
}

function closeStoryUploader() {
    document.getElementById('storyUploadModal').style.display = 'none';
    resetUploader();
}

function handleStoryFileSelect(input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    const isVideo = file.type.includes('video');
    
    reader.onload = (e) => {
        document.getElementById('uploadPlaceholder').style.display = 'none';
        const img = document.getElementById('uploadPreviewImg');
        const vid = document.getElementById('uploadPreviewVid');
        
        if (isVideo) {
            img.style.display = 'none';
            vid.style.display = 'block';
            vid.src = e.target.result;
        } else {
            vid.style.display = 'none';
            img.style.display = 'block';
            img.src = e.target.result;
        }
        document.getElementById('publishStoryBtn').disabled = false;
    };
    reader.readAsDataURL(file);
}

async function publishStory() {
    const fileInput = document.getElementById('storyFileInput');
    const captionInput = document.getElementById('storyCaptionInput');
    const btn = document.getElementById('publishStoryBtn');
    
    if (!fileInput.files[0]) return;

    btn.disabled = true;
    btn.textContent = 'Publication...';

    const formData = new FormData();
    formData.append('media', fileInput.files[0]);
    formData.append('caption', captionInput.value);

    try {
        const response = await fetch('/api/story/create', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            showToast('Story publiée !', 'success');
            closeStoryUploader();
            loadStories(); // Reload feed
        } else {
            showToast(data.error || 'Erreur lors de la publication', 'error');
            btn.disabled = false;
            btn.textContent = 'Publier maintenant';
        }
    } catch (err) {
        showToast('Erreur réseau', 'error');
        btn.disabled = false;
        btn.textContent = 'Publier maintenant';
    }
}

function resetUploader() {
    document.getElementById('storyFileInput').value = '';
    document.getElementById('storyCaptionInput').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('uploadPreviewImg').style.display = 'none';
    document.getElementById('uploadPreviewVid').style.display = 'none';
    document.getElementById('publishStoryBtn').disabled = true;
}

// --- UTILS ---

function formatStoryTime(dateStr) {
    const date = new Date(dateStr);
    const diff = (new Date() - date) / 1000;
    if (diff < 3600) return Math.floor(diff / 60) + 'm';
    return Math.floor(diff / 3600) + 'h';
}

function setupMercureForStories() {
    // Integration logic if Mercure is enabled
}
