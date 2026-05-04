window.initMemozor = function() {
    const canvasEl = document.getElementById('memozor-canvas');
    if (!canvasEl || canvasEl.dataset.initialized) return;
    canvasEl.dataset.initialized = "true";

    const canvasContainer = document.getElementById('memozor-canvas-container');
    const canvas = new fabric.Canvas('memozor-canvas', {
        preserveObjectStacking: true,
        enableRetinaScaling: true,
        selection: true
    });
    canvas.defaultCursor = 'default';
    canvas.hoverCursor = 'move';
    canvas.moveCursor = 'move';

    const DEFAULT_FONT = "'Anton', Impact, sans-serif";
    const DEFAULT_CANVAS_WIDTH = 600;
    const DEFAULT_CANVAS_HEIGHT = 400;
    const MAX_CANVAS_WIDTH = 800;
    const MOBILE_CANVAS_MARGIN = 28;

    const uploadInput = document.getElementById('memozor-upload');
    const undoBtn = document.getElementById('memozor-undo');
    const redoBtn = document.getElementById('memozor-redo');
    const addTextBtn = document.getElementById('memozor-add-text');
    const fontFamilySelect = document.getElementById('memozor-font-family');
    const textColorInput = document.getElementById('memozor-text-color');
    const strokeColorInput = document.getElementById('memozor-stroke-color');
    const textSizeInput = document.getElementById('memozor-text-size');
    const saveBtn = document.getElementById('memozor-save');
    const messageDiv = document.getElementById('memozor-message');

    let history = [];
    let historyIndex = -1;
    let isStateLoading = false;
    let pinchState = null;

    function getEditorWidth() {
        const containerWidth = canvasContainer ? canvasContainer.clientWidth : window.innerWidth;
        return Math.max(280, Math.min(MAX_CANVAS_WIDTH, containerWidth - MOBILE_CANVAS_MARGIN));
    }

    function setCanvasSize(width, height) {
        canvas.setWidth(Math.round(width));
        canvas.setHeight(Math.round(height));
        canvas.calcOffset();
        canvas.renderAll();
    }

    setCanvasSize(Math.min(DEFAULT_CANVAS_WIDTH, getEditorWidth()), Math.round(Math.min(DEFAULT_CANVAS_WIDTH, getEditorWidth()) * DEFAULT_CANVAS_HEIGHT / DEFAULT_CANVAS_WIDTH));

    function loadFont(fontFamily) {
        if (!document.fonts || !fontFamily) return Promise.resolve();
        return document.fonts.load(`700 48px ${fontFamily}`).catch(() => undefined);
    }

    function keepTextReadable(text) {
        text.set({
            lockUniScaling: true,
            centeredScaling: true,
            fontWeight: '900',
            paintFirst: 'stroke',
            strokeLineJoin: 'round',
            charSpacing: 20,
            lineHeight: 0.9,
            shadow: new fabric.Shadow({ color: 'rgba(0,0,0,0.55)', blur: 2, offsetX: 1, offsetY: 1 })
        });
    }

    function saveState() {
        if (isStateLoading) return;
        if (historyIndex < history.length - 1) history = history.slice(0, historyIndex + 1);
        history.push(canvas.toJSON(['memozorRole']));
        historyIndex++;
        updateUndoRedoButtons();
    }

    function updateUndoRedoButtons() {
        if (undoBtn) undoBtn.disabled = historyIndex <= 0;
        if (redoBtn) redoBtn.disabled = historyIndex >= history.length - 1;
    }

    function hasImageObject() {
        return canvas.getObjects().some(obj => obj.type === 'image' || obj.memozorRole === 'base-image');
    }

    function getDistance(touches) {
        const dx = touches[0].clientX - touches[1].clientX;
        const dy = touches[0].clientY - touches[1].clientY;
        return Math.hypot(dx, dy);
    }

    function installPinchToScale() {
        const el = canvas.upperCanvasEl;
        el.style.touchAction = 'none';

        el.addEventListener('touchstart', (event) => {
            if (event.touches.length !== 2) return;
            const target = getEditableTarget(event);
            if (!target) return;
            event.preventDefault();
            canvas.setActiveObject(target);
            pinchState = {
                target,
                distance: getDistance(event.touches),
                scaleX: target.scaleX || 1,
                scaleY: target.scaleY || 1
            };
        }, { passive: false });

        el.addEventListener('touchmove', (event) => {
            if (!pinchState || event.touches.length !== 2) return;
            event.preventDefault();
            const distance = getDistance(event.touches);
            if (!distance || !pinchState.distance) return;
            const zoom = distance / pinchState.distance;
            pinchState.target.scaleX = Math.max(0.05, pinchState.scaleX * zoom);
            pinchState.target.scaleY = Math.max(0.05, pinchState.scaleY * zoom);
            pinchState.target.setCoords();
            canvas.requestRenderAll();
        }, { passive: false });

        el.addEventListener('touchend', () => {
            if (pinchState) {
                pinchState.target.setCoords();
                saveState();
            }
            pinchState = null;
        });
    }

    installPinchToScale();

    if (undoBtn) undoBtn.addEventListener('click', () => {
        if (historyIndex > 0) {
            isStateLoading = true;
            historyIndex--;
            canvas.loadFromJSON(history[historyIndex], function() {
                canvas.renderAll();
                updateUndoRedoButtons();
                isStateLoading = false;
            });
        }
    });

    if (redoBtn) redoBtn.addEventListener('click', () => {
        if (historyIndex < history.length - 1) {
            isStateLoading = true;
            historyIndex++;
            canvas.loadFromJSON(history[historyIndex], function() {
                canvas.renderAll();
                updateUndoRedoButtons();
                isStateLoading = false;
            });
        }
    });

    saveState();
    canvas.on('object:added', saveState);
    canvas.on('object:modified', saveState);
    canvas.on('object:removed', saveState);

    uploadInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            if (fileNameLabel) fileNameLabel.textContent = 'Nie wybrano pliku';
            return;
        }
        if (fileNameLabel) fileNameLabel.textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(f) {
            fabric.Image.fromURL(f.target.result, function(img) {
                canvas.clear();
                const canvasWidth = getEditorWidth();
                const canvasHeight = Math.max(220, Math.round(canvasWidth * (img.height / img.width)));
                setCanvasSize(canvasWidth, canvasHeight);

                const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
                img.set({
                    left: canvas.width / 2,
                    top: canvas.height / 2,
                    originX: 'center',
                    originY: 'center',
                    scaleX: scale,
                    scaleY: scale,
                    selectable: false,
                    evented: false,
                    hasControls: false,
                    hoverCursor: 'default',
                    moveCursor: 'default',
                    lockMovementX: true,
                    lockMovementY: true,
                    lockScalingX: true,
                    lockScalingY: true,
                    lockRotation: true,
                    memozorRole: 'base-image'
                });
                canvas.add(img);
                canvas.sendToBack(img);
                canvas.discardActiveObject();
                canvas.renderAll();
                saveState();
            });
        };
        reader.readAsDataURL(file);
    });

    addTextBtn.addEventListener('click', async () => {
        const selectedFont = fontFamilySelect ? fontFamilySelect.value : DEFAULT_FONT;
        await loadFont(selectedFont);
        const text = new fabric.IText('TWÓJ TEKST', {
            left: canvas.width / 2,
            top: canvas.height / 2,
            fontFamily: selectedFont,
            fill: textColorInput.value,
            stroke: strokeColorInput.value,
            strokeWidth: Math.max(2, Math.round(parseInt(textSizeInput.value, 10) / 16)),
            fontSize: parseInt(textSizeInput.value, 10),
            originX: 'center',
            originY: 'center',
            textAlign: 'center'
        });
        keepTextReadable(text);
        canvas.add(text);
        canvas.setActiveObject(text);
        canvas.renderAll();
    });

    canvas.on('selection:created', updateToolbar);
    canvas.on('selection:updated', updateToolbar);

    function updateToolbar() {
        const activeObj = canvas.getActiveObject();
        if (activeObj && activeObj.type === 'i-text') {
            if (fontFamilySelect) {
                const matchingOption = Array.from(fontFamilySelect.options).find(opt => opt.value === activeObj.fontFamily);
                if (matchingOption) fontFamilySelect.value = activeObj.fontFamily;
            }
            textColorInput.value = activeObj.fill;
            strokeColorInput.value = activeObj.stroke;
            textSizeInput.value = activeObj.fontSize;
        }
    }

    if (fontFamilySelect) fontFamilySelect.addEventListener('change', async function() {
        const activeObj = canvas.getActiveObject();
        await loadFont(this.value);
        if (activeObj && activeObj.type === 'i-text') {
            activeObj.set('fontFamily', this.value);
            keepTextReadable(activeObj);
            canvas.requestRenderAll();
            saveState();
        }
    });

    textColorInput.addEventListener('input', function() {
        const activeObj = canvas.getActiveObject();
        if (activeObj && activeObj.type === 'i-text') {
            activeObj.set('fill', this.value);
            canvas.renderAll();
        }
    });
    textColorInput.addEventListener('change', function() { saveState(); });

    strokeColorInput.addEventListener('input', function() {
        const activeObj = canvas.getActiveObject();
        if (activeObj && activeObj.type === 'i-text') {
            activeObj.set('stroke', this.value);
            canvas.renderAll();
        }
    });
    strokeColorInput.addEventListener('change', function() { saveState(); });

    textSizeInput.addEventListener('input', function() {
        const activeObj = canvas.getActiveObject();
        if (activeObj && activeObj.type === 'i-text') {
            const fontSize = parseInt(this.value, 10);
            activeObj.set({ fontSize, strokeWidth: Math.max(2, Math.round(fontSize / 16)) });
            canvas.renderAll();
        }
    });
    textSizeInput.addEventListener('change', function() { saveState(); });

    saveBtn.addEventListener('click', async () => {
        if (!hasImageObject()) {
            alert('Najpierw wybierz obrazek.');
            return;
        }
        canvas.discardActiveObject();
        canvas.renderAll();
        const dataURL = canvas.toDataURL({ format: 'png', quality: 1, multiplier: 2 });
        messageDiv.textContent = 'Zapisywanie...';

        try {
            const response = await fetch(memozorSettings.restUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': memozorSettings.nonce },
                body: JSON.stringify({
                    image_data: dataURL,
                    website_url: document.getElementById('memozor-website-url') ? document.getElementById('memozor-website-url').value : ''
                })
            });
            let result;
            const textResponse = await response.text();
            try { result = JSON.parse(textResponse); }
            catch (e) {
                console.error('Non-JSON response received: ', textResponse);
                messageDiv.innerHTML = `<span style="color:red">Błąd serwera (${response.status}): serwer zwrócił nieprawidłową odpowiedź.</span>`;
                return;
            }
            if (response.ok && result.success) window.location.href = result.url;
            else messageDiv.innerHTML = `<span style="color:red">Błąd zapisywania mema: ${result.message || result.code || 'Unknown error'}</span>`;
        } catch (err) {
            messageDiv.innerHTML = `<span style="color:red">Błąd sieci podczas zapisywania mema: ${err.message}</span>`;
            console.error(err);
        }
    });
};
