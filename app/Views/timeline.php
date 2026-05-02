<!DOCTYPE html>
<html lang="it" class="h-full bg-slate-900 border-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline - Olmo's Got Talent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-bg { background: radial-gradient(circle at top right, #3b82f6, transparent), radial-gradient(circle at bottom left, #8b5cf6, transparent); }
        .timeline-track { background: linear-gradient(90deg, #1e293b 0%, #334155 100%); }
        .timeline-row {
            background-image: repeating-linear-gradient(
                to right,
                rgba(148, 163, 184, 0.2) 0,
                rgba(148, 163, 184, 0.2) 1px,
                transparent 1px,
                transparent var(--snap-width, 100px)
            );
        }
        .timeline-row.snap-target { outline: 1px solid rgba(59, 130, 246, 0.8); }
        .media-slot { transition: border-color 0.15s ease, box-shadow 0.15s ease; cursor: grab; }
        .media-slot:active { cursor: grabbing; }
        .media-slot.selected { border-color: #60a5fa; box-shadow: 0 0 0 1px rgba(96, 165, 250, 0.75), 0 8px 18px rgba(15, 23, 42, 0.35); }
        .media-slot.dragging { z-index: 20; box-shadow: 0 10px 22px rgba(15, 23, 42, 0.45); }
        .resize-handle { position: absolute; top: 0; width: 8px; height: 100%; cursor: ew-resize; opacity: 0; transition: opacity 0.15s ease, background 0.15s ease; }
        .resize-handle.left { left: 0; border-radius: 0.375rem 0 0 0.375rem; }
        .resize-handle.right { right: 0; border-radius: 0 0.375rem 0.375rem 0; }
        .media-slot:hover .resize-handle, .resize-handle:hover { opacity: 1; background: rgba(96, 165, 250, 0.95); }
        .inspector-panel { transform: translateX(105%); transition: transform 0.18s ease; }
        .inspector-panel.open { transform: translateX(0); }
        .preview-frame video, .preview-frame img { width: 100%; height: 100%; object-fit: contain; }
    </style>
</head>
<body class="h-full text-slate-200 overflow-hidden">
    <div class="flex h-full">
        <!-- Main Content - Timeline -->
        <main class="flex-1 flex flex-col relative overflow-hidden">
            <div class="absolute inset-0 gradient-bg opacity-10 pointer-events-none"></div>
            
            <!-- Header -->
            <header class="p-4 flex justify-between items-center glass border-b border-slate-700/50">
                <div>
                    <h1 class="text-2xl font-bold">Timeline</h1>
                    <p id="slot-name" class="text-slate-400 text-xs">Slot: Caricamento...</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="saveTimeline()" class="px-3 py-1.5 bg-green-600 hover:bg-green-500 rounded-lg font-semibold transition-all text-sm">Salva</button>
                    <button onclick="window.close()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg font-semibold transition-all text-sm">Chiudi</button>
                </div>
            </header>

            <!-- Timeline Controls -->
            <div class="p-4">
                <div class="glass rounded-xl p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-bold text-blue-400">Timeline Editor</h3>
                        <div class="flex gap-2">
                            <button onclick="zoomIn()" class="text-xs bg-slate-700 hover:bg-slate-600 px-2 py-1 rounded">Zoom In</button>
                            <button onclick="zoomOut()" class="text-xs bg-slate-700 hover:bg-slate-600 px-2 py-1 rounded">Zoom Out</button>
                            <button onclick="addMediaSlot()" class="text-xs bg-blue-600 hover:bg-blue-500 px-2 py-1 rounded">+ Aggiungi</button>
                        </div>
                    </div>
                    <div id="time-axis" class="ml-28 mb-2 h-6 relative text-[10px] text-slate-500"></div>
                    <div id="timeline" class="timeline-track rounded-lg overflow-auto space-y-2 p-2 h-[calc(100vh-170px)]">
                        <!-- Timeline rows loaded via JS -->
                    </div>
                </div>
            </div>

            <aside id="media-inspector" class="inspector-panel fixed right-0 top-0 z-30 h-full w-[360px] glass border-l border-slate-700/70 p-5 overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-purple-400">Proprietà Media</h3>
                    <button onclick="closeInspector()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700" aria-label="Chiudi pannello">&times;</button>
                </div>

                <div id="prop-preview" class="preview-frame mb-5 h-44 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center text-xs text-slate-500 overflow-hidden">
                    Seleziona un media
                </div>

                <div id="media-properties" class="space-y-4 text-sm">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-slate-500">Friendly name</label>
                        <input type="text" id="prop-name" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2" value="">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-slate-500">Tipo</label>
                        <select id="prop-type" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
                            <option value="VIDEO">Video</option>
                            <option value="AUDIO">Audio</option>
                            <option value="FOTO">Foto</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Start sec</label>
                            <input type="number" id="prop-start" min="0" step="1" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2" value="0">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Durata sec</label>
                            <input type="number" id="prop-duration" min="1" step="1" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2" value="10">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-slate-500">Screen</label>
                        <select id="prop-screen" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2"></select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-slate-500">Durata Transizione (sec)</label>
                        <input type="number" id="prop-fade-duration" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2" value="0.5" step="0.1">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button onclick="applyProperties()" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg font-semibold">Applica</button>
                        <button onclick="deleteSelected()" class="px-4 py-2 bg-red-600 hover:bg-red-500 rounded-lg font-semibold">Elimina</button>
                    </div>
                </div>
            </aside>
        </main>
    </div>

    <script>
        let currentSlotId = null;
        let selectedMediaId = null;
        let zoomLevel = 1;
        let timelineMedia = [];
        let screens = [];
        const pxPerSecondBase = 10;
        const snapSeconds = 10;
        let pointerAction = null;
        let suppressClick = false;

        const urlParams = new URLSearchParams(window.location.search);
        currentSlotId = urlParams.get('slot_id');

        function secondsToTime(seconds) {
            const safe = Math.max(0, Number(seconds) || 0);
            const min = Math.floor(safe / 60);
            const sec = Math.floor(safe % 60);
            return `${min}:${String(sec).padStart(2, '0')}`;
        }

        function timeToSeconds(value) {
            if (!value) return 0;
            if (/^\d+$/.test(String(value))) return Number(value);
            const parts = String(value).split(':').map(Number);
            if (parts.length === 3) return parts[0] * 3600 + parts[1] * 60 + parts[2];
            if (parts.length === 2) return parts[0] * 60 + parts[1];
            return 0;
        }

        function mediaStart(media) {
            return timeToSeconds(media.timestamp_inizio);
        }

        function mediaDuration(media) {
            if (media.durata_totale_sec) return Number(media.durata_totale_sec);
            const start = timeToSeconds(media.timestamp_inizio);
            const end = timeToSeconds(media.timestamp_fine);
            return Math.max(10, end - start || 10);
        }

        function secondsToSqlTime(seconds) {
            const safe = Math.max(0, Math.floor(Number(seconds) || 0));
            const h = Math.floor(safe / 3600);
            const m = Math.floor((safe % 3600) / 60);
            const s = safe % 60;
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }

        async function fetchSlotInfo() {
            if (!currentSlotId) return;
            const response = await fetch('/api/talenti');
            const result = await response.json();
            if (result.status === 'ok') {
                const slot = result.data.find(t => t.id == currentSlotId);
                if (slot) document.getElementById('slot-name').textContent = `Slot: ${slot.nome}`;
            }
        }

        async function fetchScreens() {
            const response = await fetch('/api/screens');
            const result = await response.json();
            screens = result.status === 'ok' ? result.data : [];
            const select = document.getElementById('prop-screen');
            select.innerHTML = '';
            screens.forEach(screen => {
                select.innerHTML += `<option value="${screen.id}">${screen.nome}</option>`;
            });
        }

        async function fetchTimeline() {
            if (!currentSlotId) return;
            const response = await fetch(`/api/media/talento?talento_id=${currentSlotId}`);
            timelineMedia = await response.json();
            await hydrateVideoDurations();
            renderTimeline();
        }

        async function hydrateVideoDurations() {
            const updates = timelineMedia
                .filter(media => (media.tipo_media || '').toUpperCase() === 'VIDEO' && !Number(media.durata_totale_sec))
                .map(media => loadVideoDuration(media).then(duration => {
                    if (!duration) return null;
                    media.durata_totale_sec = duration;
                    media.timestamp_fine = secondsToSqlTime(mediaStart(media) + duration);
                    return fetch(`/api/media/timeline/update?id=${media.id}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            durata_totale_sec: duration,
                            timestamp_fine: media.timestamp_fine
                        })
                    });
                }).catch(() => null));
            await Promise.all(updates);
        }

        function loadVideoDuration(media) {
            return new Promise(resolve => {
                const probe = document.createElement('video');
                probe.preload = 'metadata';
                probe.src = media.file_path;
                probe.onloadedmetadata = () => resolve(Math.max(1, Math.round(probe.duration || 0)));
                probe.onerror = () => resolve(null);
            });
        }

        function renderAxis(width) {
            const axis = document.getElementById('time-axis');
            axis.style.width = `${width}px`;
            axis.innerHTML = '';
            const maxSeconds = Math.max(30, Math.ceil(width / (pxPerSecondBase * zoomLevel)));
            for (let sec = 0; sec <= maxSeconds; sec += 10) {
                const marker = document.createElement('div');
                marker.className = 'absolute top-0 border-l border-slate-600 pl-1';
                marker.style.left = `${sec * pxPerSecondBase * zoomLevel}px`;
                marker.textContent = secondsToTime(sec);
                axis.appendChild(marker);
            }
        }

        function renderTimeline() {
            const container = document.getElementById('timeline');
            const pxPerSecond = pxPerSecondBase * zoomLevel;
            const maxEnd = Math.max(30, ...timelineMedia.map(m => mediaStart(m) + mediaDuration(m)));
            const width = Math.max(900, maxEnd * pxPerSecond + 160);
            renderAxis(width);
            container.innerHTML = '';

            screens.forEach(screen => {
                const row = document.createElement('div');
                row.className = 'flex min-h-[72px]';
                row.innerHTML = `
                    <div class="w-24 shrink-0 pr-3 text-xs font-bold text-yellow-400 flex items-center justify-end">${screen.nome}</div>
                    <div class="timeline-row relative h-16 rounded border border-slate-800" data-screen-id="${screen.id}" style="width:${width}px; --snap-width:${snapSeconds * pxPerSecond}px"></div>
                `;
                const track = row.querySelector('.timeline-row');
                timelineMedia.filter(m => String(m.screen_id) === String(screen.id)).forEach(media => {
                    track.appendChild(createMediaBlock(media, pxPerSecond));
                });
                container.appendChild(row);
            });
        }

        function createMediaBlock(media, pxPerSecond) {
            const start = mediaStart(media);
            const duration = mediaDuration(media);
            const block = document.createElement('div');
            block.className = `media-slot absolute top-2 h-12 rounded border px-2 py-1 text-xs overflow-hidden ${selectedMediaId == media.id ? 'selected' : ''} ${media.tipo_media === 'AUDIO' ? 'bg-green-500/20 border-green-500/40' : media.tipo_media === 'FOTO' ? 'bg-yellow-500/20 border-yellow-500/40' : 'bg-blue-500/20 border-blue-500/40'}`;
            block.style.left = `${start * pxPerSecond}px`;
            block.style.width = `${Math.max(12, duration * pxPerSecond)}px`;
            block.dataset.id = media.id;
            block.innerHTML = `
                <div class="resize-handle left" data-resize="left"></div>
                <div class="resize-handle right" data-resize="right"></div>
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="shrink-0">${mediaTypeIcon(media.tipo_media)}</span>
                    <span class="font-bold truncate">${media.friendly_name || media.file_path.split('/').pop()}</span>
                </div>
                <div class="text-[10px] text-slate-400 pl-5">${secondsToTime(start)} - ${secondsToTime(start + duration)}</div>
            `;
            block.addEventListener('click', () => {
                if (suppressClick) return;
                selectMedia(media.id);
            });
            block.addEventListener('pointerdown', event => startPointerAction(event, media, block));
            return block;
        }

        function mediaTypeIcon(type) {
            if (type === 'AUDIO') return iconSvg('M3 18v-6a9 9 0 0 1 18 0v6 M21 19a2 2 0 0 1-2 2h-1v-8h3v6z M3 19a2 2 0 0 0 2 2h1v-8H3v6z');
            if (type === 'FOTO') return iconSvg('M4 5h16v14H4z M8 13l2.5-3 3 4 2-2.5L20 17 M8 9h.01');
            return iconSvg('M4 6h11v12H4z M15 10l5-3v10l-5-3z');
        }

        function iconSvg(path) {
            return `<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="${path}"/></svg>`;
        }

        function snap(value) {
            return Math.max(0, Math.round(value / snapSeconds) * snapSeconds);
        }

        function snapDelta(value) {
            return Math.round(value / snapSeconds) * snapSeconds;
        }

        function rowAtPoint(x, y) {
            return document.elementsFromPoint(x, y).find(el => el.classList && el.classList.contains('timeline-row'));
        }

        function clearSnapTargets() {
            document.querySelectorAll('.timeline-row.snap-target').forEach(row => row.classList.remove('snap-target'));
        }

        function startPointerAction(event, media, block) {
            if (event.button !== 0) return;
            event.preventDefault();
            selectMedia(media.id, false);
            const pxPerSecond = pxPerSecondBase * zoomLevel;
            const mode = event.target.dataset.resize || 'move';
            pointerAction = {
                mode,
                media,
                block,
                pxPerSecond,
                startX: event.clientX,
                originalStart: mediaStart(media),
                originalDuration: mediaDuration(media),
                targetScreenId: media.screen_id,
                didMove: false
            };
            block.classList.add('dragging');
            block.setPointerCapture(event.pointerId);
            block.addEventListener('pointermove', movePointerAction);
            block.addEventListener('pointerup', endPointerAction);
            block.addEventListener('pointercancel', endPointerAction);
        }

        function movePointerAction(event) {
            if (!pointerAction) return;
            const action = pointerAction;
            const deltaSeconds = snapDelta((event.clientX - action.startX) / action.pxPerSecond);
            const minDuration = snapSeconds;
            let nextStart = action.originalStart;
            let nextDuration = action.originalDuration;

            if (action.mode === 'move') {
                nextStart = snap(action.originalStart + deltaSeconds);
                const targetRow = rowAtPoint(event.clientX, event.clientY);
                clearSnapTargets();
                if (targetRow) {
                    targetRow.classList.add('snap-target');
                    action.targetScreenId = targetRow.dataset.screenId;
                }
            } else if (action.mode === 'left') {
                nextStart = Math.min(snap(action.originalStart + deltaSeconds), action.originalStart + action.originalDuration - minDuration);
                nextDuration = action.originalDuration + action.originalStart - nextStart;
            } else if (action.mode === 'right') {
                nextDuration = Math.max(minDuration, snap(action.originalDuration + deltaSeconds));
            }

            action.didMove = true;
            action.nextStart = nextStart;
            action.nextDuration = nextDuration;
            action.block.style.left = `${nextStart * action.pxPerSecond}px`;
            action.block.style.width = `${Math.max(12, nextDuration * action.pxPerSecond)}px`;
        }

        async function endPointerAction(event) {
            if (!pointerAction) return;
            const action = pointerAction;
            action.block.classList.remove('dragging');
            action.block.releasePointerCapture(event.pointerId);
            action.block.removeEventListener('pointermove', movePointerAction);
            action.block.removeEventListener('pointerup', endPointerAction);
            action.block.removeEventListener('pointercancel', endPointerAction);
            clearSnapTargets();

            if (action.didMove) {
                const start = action.nextStart ?? action.originalStart;
                const duration = action.nextDuration ?? action.originalDuration;
                action.media.screen_id = action.targetScreenId || action.media.screen_id;
                action.media.timestamp_inizio = secondsToSqlTime(start);
                action.media.timestamp_fine = secondsToSqlTime(start + duration);
                action.media.durata_totale_sec = duration;
                suppressClick = true;
                renderTimeline();
                updateInspector(action.media);
                await saveTimeline(false);
                setTimeout(() => { suppressClick = false; }, 0);
            }

            pointerAction = null;
        }

        function selectMedia(mediaId, rerender = true) {
            selectedMediaId = mediaId;
            const media = timelineMedia.find(m => String(m.id) === String(mediaId));
            if (!media) return;
            updateInspector(media);
            if (rerender) renderTimeline();
        }

        function updateInspector(media) {
            document.getElementById('media-inspector').classList.add('open');
            document.getElementById('prop-name').value = media.friendly_name || media.file_path.split('/').pop();
            document.getElementById('prop-type').value = media.tipo_media || 'VIDEO';
            document.getElementById('prop-start').value = mediaStart(media);
            document.getElementById('prop-duration').value = mediaDuration(media);
            document.getElementById('prop-screen').value = media.screen_id || '';
            document.getElementById('prop-fade-duration').value = media.fade_in_sec || 0;
            renderPreview(media);
        }

        function renderPreview(media) {
            const preview = document.getElementById('prop-preview');
            const path = media.file_path || '';
            if ((media.tipo_media || '').toUpperCase() === 'FOTO') {
                preview.innerHTML = `<img src="${path}" alt="">`;
                return;
            }
            if ((media.tipo_media || '').toUpperCase() === 'AUDIO') {
                preview.innerHTML = `<audio src="${path}" controls class="w-full"></audio>`;
                return;
            }
            preview.innerHTML = `<video src="${path}" controls muted></video>`;
            const previewVideo = preview.querySelector('video');
            previewVideo.addEventListener('loadedmetadata', async () => {
                if (Number(media.durata_totale_sec) || !previewVideo.duration) return;
                const duration = Math.max(1, Math.round(previewVideo.duration));
                media.durata_totale_sec = duration;
                media.timestamp_fine = secondsToSqlTime(mediaStart(media) + duration);
                document.getElementById('prop-duration').value = duration;
                renderTimeline();
                await fetch(`/api/media/timeline/update?id=${media.id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        durata_totale_sec: duration,
                        timestamp_fine: media.timestamp_fine
                    })
                });
            }, { once: true });
        }

        function closeInspector() {
            selectedMediaId = null;
            document.getElementById('media-inspector').classList.remove('open');
            renderTimeline();
        }

        async function applyProperties() {
            const media = timelineMedia.find(m => String(m.id) === String(selectedMediaId));
            if (!media) return;
            const start = Number(document.getElementById('prop-start').value) || 0;
            const duration = Math.max(1, Number(document.getElementById('prop-duration').value) || 10);
            const payload = {
                friendly_name: document.getElementById('prop-name').value,
                tipo_media: document.getElementById('prop-type').value,
                screen_id: document.getElementById('prop-screen').value,
                timestamp_inizio: secondsToSqlTime(start),
                timestamp_fine: secondsToSqlTime(start + duration),
                durata_totale_sec: duration,
                fade_in_sec: Number(document.getElementById('prop-fade-duration').value) || 0,
                fade_out_sec: 0
            };
            const response = await fetch(`/api/media/timeline/update?id=${selectedMediaId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            if (result.status === 'ok') {
                Object.assign(media, payload);
                renderTimeline();
            }
        }

        async function deleteSelected() {
            if (!selectedMediaId) return;
            await fetch(`/api/media?id=${selectedMediaId}`, { method: 'DELETE' });
            selectedMediaId = null;
            await fetchTimeline();
        }

        function addMediaSlot() {
            window.alert('Aggiungi media dallo wizard Admin, poi posizionalo qui con drag and drop.');
        }

        function zoomIn() {
            zoomLevel = Math.min(zoomLevel + 0.2, 3);
            renderTimeline();
        }

        function zoomOut() {
            zoomLevel = Math.max(zoomLevel - 0.2, 0.5);
            renderTimeline();
        }

        async function saveTimeline(showMessage = true) {
            const items = timelineMedia.map((media, index) => ({
                id: media.id,
                screen_id: media.screen_id,
                timestamp_inizio: media.timestamp_inizio,
                timestamp_fine: media.timestamp_fine,
                durata_totale_sec: mediaDuration(media),
                ordine_esecuzione: index + 1
            }));
            const response = await fetch('/api/media/timeline/reorder', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ items })
            });
            const result = await response.json();
            if (showMessage && result.status === 'ok') {
                document.getElementById('slot-name').textContent += ' - salvata';
                setTimeout(fetchSlotInfo, 1200);
            }
        }

        async function init() {
            await fetchSlotInfo();
            await fetchScreens();
            await fetchTimeline();
        }

        init();
    </script>
</body>
</html>
