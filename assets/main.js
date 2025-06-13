// Skills Data
const skillsData = [
    { icon: "fas fa-code", skill: "HTML/CSS", level: 10, title: "<div>", name: "HTML/CSS" },
    { icon: "fab fa-js", skill: "JavaScript", level: 5, title: "const[]", name: "JavaScript" },
    { icon: "fab fa-node-js", skill: "Node.js", level: 8, title: "npm install", name: "Node.js" },
    { icon: "fab fa-python", skill: "Python", level: 5, title: "print hello world", name: "Python" }
];

function renderSkills(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Container ${containerId} not found`);
        return;
    }
    container.innerHTML = '';
    skillsData.forEach(skill => {
        const statItem = document.createElement("div");
        statItem.classList.add("stat-item");
        statItem.innerHTML = `
            <div class="stat-name"><i class="${skill.icon}"></i> ${skill.skill}</div>
            <div class="stat-bar"><div class="stat-fill" style="width: ${skill.level}%"></div></div>
            <div class="stat-value">Lv. ${skill.level} - ${skill.title}</div>
        `;
        container.appendChild(statItem);
    });
}

// Initialize App
document.addEventListener("DOMContentLoaded", () => {
    console.log('DOM loaded, initializing app');
    renderSkills("skills-container");
    renderSkills("stats-container");

    const loadingScreen = document.getElementById('loading-screen');
    const loadingProgress = document.querySelector('.loading-progress');

    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress > 100) progress = 100;
        loadingProgress.textContent = `${Math.floor(progress)}%`;
        if (progress >= 100) {
            clearInterval(progressInterval);
            loadingScreen.style.opacity = '0';
            setTimeout(() => {
                loadingScreen.style.display = 'none';
                initializeApp();
            }, 500);
        }
    }, 100);

    preloadAssets();
});

function preloadAssets() {
    const assets = [
        'https://i.imgur.com/BW7ZUkq.webp',
        'https://i.imgur.com/v6jgeDg.png',
        'https://minecraft.wiki/images/Gravel_%28texture%29_JE5_BE4.png',
        'https://i.imgur.com/QFIgSz8.webp',
        'https://minecraft.wiki/images/Steve_%28texture%29_JE6.png'
    ];
    assets.forEach(src => {
        const img = new Image();
        img.src = src;
        img.onerror = () => console.warn(`Error loading asset: ${src}`);
        img.onload = () => console.log(`Loaded asset: ${src}`);
    });
}

function initializeApp() {
    console.log('Initializing app');
    const lockScreenWindows = document.getElementById('lock-screen-windows');
    const lockScreenMobile = document.getElementById('lock-screen-mobile');
    const lockContentWindows = document.getElementById('lock-content-windows');
    const lockContentMobile = document.getElementById('lock-content-mobile');
    const unlockBtnWindows = document.getElementById('unlock-btn-windows');
    const unlockBtnMobile = document.getElementById('unlock-btn-mobile');
    const passwordInputWindows = document.getElementById('password-input-windows');
    const passwordInputMobile = document.getElementById('password-input-mobile');
    const lockTimeWindows = document.getElementById('lock-time-windows');
    const lockDateWindows = document.getElementById('lock-date-windows');
    const lockTimeMobile = document.getElementById('lock-time-mobile');
    const lockDateMobile = document.getElementById('lock-date-mobile');

    document.body.classList.add('locked');

    function updateLockTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US');
        const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '/');
        lockTimeWindows.textContent = timeStr;
        lockDateWindows.textContent = dateStr;
        lockTimeMobile.textContent = timeStr;
        lockDateMobile.textContent = `Eddy's World - ${dateStr}`;
    }

    setInterval(updateLockTime, 1000);
    updateLockTime();

    function unlockScreen() {
        lockScreenWindows.classList.add('hidden');
        lockScreenMobile.classList.add('hidden');
        document.body.classList.remove('locked');
        setTimeout(() => {
            lockScreenWindows.style.display = 'none';
            lockScreenMobile.style.display = 'none';
            initializeMusic();
            updateTheme(currentTheme);
        }, 500);
    }

    function setupLockScreen(screen, input, btn, content) {
        function checkPassword() {
            if (input.value.toLowerCase() === 'eddy') {
                unlockScreen();
            } else {
                input.value = '';
                input.placeholder = 'Wrong! Try again!';
            }
        }
        btn.addEventListener('click', checkPassword);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') checkPassword();
        });
        screen.addEventListener('click', () => {
            screen.classList.add('active');
            content.style.display = 'block';
        });
    }

    setupLockScreen(lockScreenWindows, passwordInputWindows, unlockBtnWindows, lockContentWindows);
    setupLockScreen(lockScreenMobile, passwordInputMobile, unlockBtnMobile, lockContentMobile);

    const toggleThemeBtn = document.getElementById('toggle-theme');
    let currentTheme = 'default';
    let isDarkMode = true;

    toggleThemeBtn.addEventListener('click', () => {
        currentTheme = currentTheme === 'default' ? 'minecraft' : 'default';
        isDarkMode = !isDarkMode;
        document.body.classList.toggle('minecraft-theme', currentTheme === 'minecraft');
        document.body.classList.toggle('light-mode', !isDarkMode);
        toggleThemeBtn.textContent = currentTheme === 'minecraft' ? 'Switch Theme ⛏️' : 'Switch Theme 🌸';
        localStorage.setItem("eddy-theme", isDarkMode ? "dark" : "light");
        console.log(`Switched to theme: ${currentTheme}, mode: ${isDarkMode ? 'dark' : 'light'}`);
        const mood = document.getElementById("dev-mood");
        mood.textContent = isDarkMode ? "Switched to night owl mode 🌙" : "Morning vibes, let's go! ☀️";
        mood.style.color = isDarkMode ? "#ff69b4" : "#89d4d4";
        setTimeout(() => updateMood(), 2000);
        updateTheme(currentTheme);
    });

    if (localStorage.getItem("eddy-theme") === "light") {
        isDarkMode = false;
        document.body.classList.add("light-mode");
        toggleThemeBtn.textContent = "☀️ Light Mode";
    }

    function updateTheme(theme) {
        const sakuraRain = document.getElementById('sakura-rain');
        if (!sakuraRain) {
            console.error('Sakura rain container not found');
            return;
        }
        sakuraRain.innerHTML = '';
        const maxSakura = window.innerWidth < 768 ? 10 : 20;
        let sakuraCount = 0;

        function createSakura() {
            if (sakuraCount >= maxSakura) return;
            const sakura = document.createElement('div');
            sakura.className = 'sakura';
            sakura.style.left = Math.random() * 100 + 'vw';
            sakura.style.animationDuration = Math.random() * 3 + 5 + 's';
            const sakuraImg = theme === 'minecraft' 
                ? 'https://minecraft.wiki/images/Gravel_%28texture%29_JE5_BE4.png'
                : 'https://i.imgur.com/QFIgSz8.webp';
            sakura.style.background = `url('${sakuraImg}') center/cover`;
            sakura.style.width = theme === 'minecraft' ? '16px' : '20px';
            sakura.style.height = theme === 'minecraft' ? '16px' : '20px';
            sakura.style.animationName = theme === 'minecraft' ? 'blockFall' : 'sakuraFall';
            sakuraRain.appendChild(sakura);
            sakuraCount++;
            console.log(`Created sakura ${sakuraCount}/${maxSakura}, theme: ${theme}`);
            setTimeout(() => {
                sakura.remove();
                sakuraCount--;
            }, 7000);
        }

        function animate() {
            if (sakuraCount < maxSakura && window.requestAnimationFrame) {
                createSakura();
                window.requestAnimationFrame(animate);
            }
        }
        if (window.requestAnimationFrame) {
            animate();
        } else {
            console.warn('requestAnimationFrame not supported, sakura rain disabled');
        }
    }

    function initializeMusic() {
        console.log('Initializing music player');
        const musicToggle = document.getElementById('music-toggle');
        const nextSong = document.getElementById('next-song');
        const playlistToggle = document.getElementById('playlist-toggle');
        const musicPlayer = document.getElementById('music-player');
        const devSong = document.getElementById('dev-song');
        const playlistPanel = document.getElementById('playlist-panel');
        const closePlaylist = document.getElementById('close-playlist');
        const playlistGrid = document.getElementById('playlist-grid');
        const currentTimeEl = document.getElementById('current-time');
        const durationEl = document.getElementById('duration');
        const progressFill = document.getElementById('progress-fill');
        let isMusicPlaying = false;
        let currentSong = null;

        const musicList = [
            {
                title: 'Từng ngày như mãi mãi',
                artist: 'BuiTruongLinh',
                genre: 'Chill',
                url: 'https://files.catbox.moe/674lwc.mp3'
            },
            {
                title: 'Từng ngày yêu em',
                artist: 'BuiTruongLinh',
                genre: 'Chill',
                url: 'https://files.catbox.moe/inf5xn.mp3'
            },
            {
                title: 'Mất Kết Nối',
                artist: 'Dương Domic',
                genre: 'Dance',
                url: 'https://files.catbox.moe/pk2ku0.mp3'
            },
             {
                title: 'Nắng có mang em về',
                artist: 'Orinn Lofi Ver',
                genre: 'Chill',
                url: 'https://files.catbox.moe/5h0vo9.mp3'
            },
            {
                title: 'Say Đôi Mắt Em ',
                artist: 'BigP x Winno x KProx',
                genre: 'Lofi',
                url: 'https://files.catbox.moe/pjk58m.mp3'
            },
            {
                title: 'Phố Đã Lên Đèn',
                artist: 'Huyền Tâm Môn',
                genre: 'Lofi',
                url: 'https://files.catbox.moe/dldfn1.mp3'
            },
            {
                title: 'Không Yêu Xin Đừng Nói',
                artist: 'Umie x Droppy x Hổ x Orinn',
                genre: 'Lofi Ver',
                url: 'https://files.catbox.moe/x8brfr.mp3'
            },
            {
                title: 'Hai Mươi Hai (22)',
                artist: 'AMEE ft. Hứa Kim Tuyền x Quanvrox',
                genre: 'Lo - Fi Ver',
                url: 'https://files.catbox.moe/krhiud.mp3'
            },
            {
                title: 'Sai Người Sai Thời Điểm ',
                artist: 'Thanh Hưng x MewMew',
                genre: 'Lofi Ver.',
                url: 'https://files.catbox.moe/y158u3.mp3'
            },
            {
                title: 'Ngày Em Đẹp Nhất',
                artist: 'Tama x Bell',
                genre: 'Lofi Ver',
                url: 'https://files.catbox.moe/gyevcz.mp3'
            },
            {
                title: 'Suýt Nữa Thì',
                artist: 'Andiez x Freak',
                genre: 'Lofi Ver',
                url: 'https://files.catbox.moe/j1590y.mp3'
            },
            {
                title: 'Anh Muốn Nghe Giọng Em',
                artist: 'Nguyên x Seth x Freak D',
                genre: 'Lofi Ver',
                url: 'https://files.catbox.moe/ecwr9k.mp3'
            },
            {
                title: 'Tình Cờ Yêu Em ',
                artist: 'Kuun Đức Nam x Linh Thộn x Freak D',
                genre: 'Lofi Ver',
                url: 'https://files.catbox.moe/afb8ya.mp3'
            },
            {
                title: 'EM CHẲNG CẦN MAKEUP (Bé ơi em cứ tự tin)',
                artist: 'LẬP NGUYÊN',
                genre: 'OFFICIAL MUSIC VIDEO',
                url: 'https://files.catbox.moe/yzqtd5.mp3'
            },
            {
                title: 'Th́ì Thôi  - ',
                artist: 'Reddy x Freak D',
                genre: 'Lofi Ver',
                url: 'https://files.catbox.moe/xw5f6q.mp3'
            },
            {
                title: 'TÌNH KA NGỌT NGÀO',
                artist: 'LẬP NGUYÊN x YẾN NỒI CƠM ĐIỆN (Prod. Hoàng Green)',
                genre: 'OFFICIAL MUSIC VIDEO',
                url: 'https://files.catbox.moe/f0tvr0.mp3'
            },
            {
                title: 'NGƯỜI YÊU TÔI ĐỈNH NHẤT',
                artist: 'LẬP NGUYÊN',
                genre: 'HÌNH TRỰC TIẾP ',
                url: 'https://files.catbox.moe/ygynlt.mp3'
            },
            {
                title: ' 3107 ',
                artist: 'W/n',
                genre: 'Album',
                url: 'https://files.catbox.moe/2ql72k.mp3'
            },
            
        ];

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
        }

        musicPlayer.addEventListener('timeupdate', () => {
            const currentTime = musicPlayer.currentTime;
            const duration = musicPlayer.duration || 0;
            currentTimeEl.textContent = formatTime(currentTime);
            durationEl.textContent = formatTime(duration);
            const progressPercent = duration ? (currentTime / duration) * 100 : 0;
            progressFill.style.width = `${progressPercent}%`;
        });

        musicPlayer.addEventListener('loadedmetadata', () => {
            durationEl.textContent = formatTime(musicPlayer.duration);
            console.log(`Loaded metadata for ${musicPlayer.src}`);
        });

        function updateMusicTrack() {
            try {
                if (!musicList.length) throw new Error('Empty music list');
                const randomIndex = Math.floor(Math.random() * musicList.length);
                currentSong = musicList[randomIndex];
                if (!currentSong || !currentSong.url) throw new Error('Invalid song URL');
                musicPlayer.src = currentSong.url;
                devSong.textContent = `🎵 ${currentSong.title} - ${currentSong.artist}`;
                updatePlaylistActive();
                console.log(`Playing: ${currentSong.title}, URL: ${currentSong.url}`);
                if (isMusicPlaying) {
                    musicPlayer.play().catch(error => {
                        console.error('Error playing music:', error);
                        devSong.textContent = '🎵 Music playback error';
                    });
                }
            } catch (error) {
                console.error('Error loading music:', error);
                devSong.textContent = '🎵 Unable to load music';
            }
        }

        function loadPlaylist() {
            try {
                playlistGrid.innerHTML = '';
                if (!musicList.length) throw new Error('Empty music list');
                musicList.forEach(song => {
                    if (!song.title || !song.artist || !song.url) throw new Error('Invalid song data');
                    const item = document.createElement('div');
                    item.classList.add('playlist-item');
                    item.innerHTML = `<p>${song.title} - ${song.artist} (${song.genre})</p>`;
                    item.addEventListener('click', () => {
                        currentSong = song;
                        musicPlayer.src = song.url;
                        devSong.textContent = `🎵 ${song.title} - ${song.artist}`;
                        updatePlaylistActive();
                        console.log(`Selected: ${song.title}`);
                        if (isMusicPlaying) {
                            musicPlayer.play().catch(error => {
                                console.error('Error playing music:', error);
                                devSong.textContent = '🎵 Music playback error';
                            });
                        }
                    });
                    playlistGrid.appendChild(item);
                });
                updatePlaylistActive();
            } catch (error) {
                console.error('Error loading playlist:', error);
                playlistGrid.innerHTML = '<p style="color: #ddd; text-align: center;">Unable to load playlist</p>';
            }
        }

        function updatePlaylistActive() {
            const items = playlistGrid.querySelectorAll('.playlist-item');
            items.forEach(item => {
                const title = item.querySelector('p').textContent.split(' - ')[0];
                item.classList.toggle('active', currentSong && title === currentSong.title);
            });
        }

        musicToggle.addEventListener('click', () => {
            isMusicPlaying = !isMusicPlaying;
            musicToggle.textContent = isMusicPlaying ? '🔊' : '🔇';
            musicToggle.classList.toggle('paused', !isMusicPlaying);
            console.log(`Music ${isMusicPlaying ? 'playing' : 'paused'}`);
            if (isMusicPlaying) {
                if (!musicPlayer.src) updateMusicTrack();
                musicPlayer.play().catch(error => {
                    console.error('Error playing music:', error);
                    devSong.textContent = '🎵 Music playback error';
                });
            } else {
                musicPlayer.pause();
            }
        });

        nextSong.addEventListener('click', () => {
            updateMusicTrack();
        });

        playlistToggle.addEventListener('click', () => {
            playlistPanel.classList.toggle('active');
            document.body.style.overflow = playlistPanel.classList.contains('active') ? 'hidden' : 'auto';
            if (playlistPanel.classList.contains('active') && !playlistGrid.children.length) {
                loadPlaylist();
            }
        });

        closePlaylist.addEventListener('click', () => {
            playlistPanel.classList.remove('active');
            document.body.style.overflow = 'auto';
        });

        musicPlayer.addEventListener('ended', () => {
            if (isMusicPlaying) updateMusicTrack();
        });

        updateMusicTrack();
    }

    const taglines = ['Eddy is me 🌸', 'Super Nova 😈', 'Full-Stack is the dream 💪', 'Encoders and Decoders 🚀'];
    let tagIndex = 0;
    let charIndex = 0;
    const taglineElement = document.querySelector('.dialogue-text');

    function typeTagline() {
        if (charIndex < taglines[tagIndex].length) {
            taglineElement.textContent += taglines[tagIndex].charAt(charIndex);
            charIndex++;
            setTimeout(typeTagline, 100);
        } else {
            setTimeout(eraseTagline, 2000);
        }
    }

    function eraseTagline() {
        if (charIndex > 0) {
            taglineElement.textContent = taglines[tagIndex].substring(0, charIndex - 1);
            charIndex--;
            setTimeout(eraseTagline, 50);
        } else {
            tagIndex = (tagIndex + 1) % taglines.length;
            setTimeout(typeTagline, 500);
        }
    }
    typeTagline();

    document.getElementById('character-portrait').addEventListener('click', () => {
        const avatar = document.getElementById('character-portrait');
        avatar.style.transform = 'scale(1.1)';
        setTimeout(() => avatar.style.transform = 'scale(1)', 200);
    });

    const toggleStats = document.getElementById('toggle-stats');
    const statsPanel = document.getElementById('stats-panel');
    const closeStats = document.getElementById('close-stats');

    toggleStats.addEventListener('click', () => {
        statsPanel.classList.toggle('active');
        document.body.style.overflow = statsPanel.classList.contains('active') ? 'hidden' : 'auto';
    });

    closeStats.addEventListener('click', () => {
        statsPanel.classList.remove('active');
        document.body.style.overflow = 'auto';
    });

    statsPanel.addEventListener('transitionend', () => {
        if (statsPanel.classList.contains('active')) {
            document.querySelectorAll('.stat-fill').forEach(bar => {
                bar.style.width = bar.style.width;
            });
        }
    });

    document.querySelectorAll('.btn, .social-links a').forEach(btn => {
        btn.addEventListener('touchstart', () => {
            btn.style.transform = 'scale(0.95)';
        });
        btn.addEventListener('touchend', () => {
            btn.style.transform = '';
        });
    });

    const liveTimeElement = document.getElementById('live-time');
    const liveDescElement = document.getElementById('live-desc');
    const liveDateElement = document.getElementById('live-date');

    function updateTimeline() {
        const now = new Date();
        liveTimeElement.textContent = now.toLocaleTimeString('en-US');
        liveDateElement.textContent = now.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '/');
    }
    setInterval(updateTimeline, 1000);
    updateTimeline();

    const descList = [
        'Yo! 🌸',
        'Prime time to code and snack 😎',
        'While you sleep, devs fix bugs!',
        'Scroll down, more cool stuff awaits!',
        'Debugging or chilling?',
        'Learning while coding'
    ];

    function changeDesc() {
        const random = Math.floor(Math.random() * descList.length);
        liveDescElement.textContent = descList[random];
    }
    setInterval(changeDesc, 10000);
    changeDesc();

    // Custom DevTools Warning Notification
    function showDevToolsWarning() {
        const existingModal = document.getElementById('devtools-warning');
        if (existingModal) return; // Prevent multiple modals

        const modal = document.createElement('div');
        modal.id = 'devtools-warning';
        modal.className = 'devtools-warning';
        modal.innerHTML = `
            <div class="devtools-warning-content">
                <h2>🚫 Access Denied!</h2>
                <p>Sorry, DevTools are disabled on Eddy's page! 🌸</p>
                <p>Let's keep the magic in the code, not the console!</p>
                <button class="btn close-devtools-warning">Got it!</button>
            </div>
        `;
        document.body.appendChild(modal);

        const closeBtn = modal.querySelector('.close-devtools-warning');
        closeBtn.addEventListener('click', () => {
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 300);
        });

        // Auto-close after 5 seconds
        setTimeout(() => {
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 300);
        }, 5000);
    }

    document.addEventListener('contextmenu', e => {
        e.preventDefault();
        showDevToolsWarning();
    });

    document.addEventListener('keydown', e => {
        if (
            e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && e.key === 'I') ||
            (e.ctrlKey && e.shiftKey && e.key === 'C') ||
            (e.ctrlKey && e.key === 'U')
        ) {
            e.preventDefault();
            showDevToolsWarning();
        }
    });

    document.addEventListener('showDevToolsWarning', showDevToolsWarning);

    const moods = [
        { text: "Coding JavaScript 🚀", color: "#f0db4f", time: [9, 12] },
        { text: "Stuck on a bug 🐛", color: "#ff5555", time: [14, 16] },
        { text: "Listening to Lo-Fi chill 🎧", color: "#0fc", time: [20, 23] },
        { text: "Sipping bubble tea 🥤", color: "#ff9ff3", time: [15, 15] }
    ];

    function updateMood() {
        const now = new Date();
        const hour = now.getHours();
        const currentMood = moods.find(m => hour >= m.time[0] && hour <= m.time[1]) || 
                           { text: "Learning and snacking 😎", color: "#ff69b4" };
        const moodElement = document.getElementById("dev-mood");
        moodElement.textContent = currentMood.text;
        moodElement.style.color = currentMood.color;
    }

    updateMood();
    setInterval(updateMood, 30 * 60 * 1000);

    function updateNetworkStatus() {
        const online = navigator.onLine;
        const netStatusEl = document.getElementById("dev-net");
        netStatusEl.textContent = online ? "📡 Online" : "🔴 Offline";
    }
    window.addEventListener("online", updateNetworkStatus);
    window.addEventListener("offline", updateNetworkStatus);
    updateNetworkStatus();

    updateTheme(currentTheme);
}
// Thêm vào cuối file main.js
// ========== COMMENT SYSTEM ==========
async function getCaptchaToken() {
    return new Promise(resolve => {
        grecaptcha.ready(() => {
            grecaptcha.execute('6LfwWDUrAAAAALb3oXWy1oDOGfxGnYWoGwL7EDGC', { action: 'submit' })
                .then(token => resolve(token));
        });
    });
}

async function submitComment() {
    try {
        const token = await getCaptchaToken();
        const content = document.getElementById('comment-content').value.trim();
        
        if (!content) {
            alert('Vui lòng nhập nội dung bình luận!');
            return;
        }

        const response = await fetch('api.php?action=submit', {
            method: 'POST',
            body: new URLSearchParams({
                content: content,
                captcha_token: token
            })
        });

        if (response.ok) {
            loadComments();
            document.getElementById('comment-content').value = '';
            showNotification('Bình luận đã được gửi thành công! 🌸');
        } else {
            alert('Gửi bình luận thất bại');
        }
    } catch (error) {
        console.error('Lỗi:', error);
    }
}

function loadComments(filter = 'latest') {
    fetch(`api.php?action=load&filter=${filter}`)
        .then(res => res.json())
        .then(comments => {
            const html = comments.map(c => `
                <div class="comment">
                    <div class="meta">
                        <span class="comment-name">${c.name}</span>
                        <span class="comment-time">${c.time}</span>
                    </div>
                    <div class="comment-content">${c.content}</div>
                    <div class="actions">
                        <button class="like-btn" onclick="likeComment(${c.id})">
                            <i class="fas fa-heart"></i> ${c.likes}
                        </button>
                    </div>
                </div>
            `).join('');
            document.getElementById('comment-list').innerHTML = html;
        });
}

async function likeComment(id) {
    try {
        const response = await fetch('api.php?action=like', {
            method: 'POST',
            body: new URLSearchParams({ id })
        });
        
        const data = await response.json();
        if (data.error) {
            showNotification(data.error, 'error');
        } else {
            loadComments(document.querySelector('#comment-filter select').value);
            showNotification('Đã thêm like! ❤️');
        }
    } catch (error) {
        console.error('Lỗi:', error);
    }
}

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', () => {
    loadComments();
    
    // Thêm sự kiện Enter để gửi comment
    document.getElementById('comment-content').addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            submitComment();
        }
    });
});

// Hàm hiển thị thông báo
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}