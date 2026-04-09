window.initMemozor = function() {
    const canvasEl = document.getElementById('memozor-canvas');
    if (!canvasEl || canvasEl.dataset.initialized) return;
    canvasEl.dataset.initialized = "true";

    // Initialize Fabric.js Canvas
    const canvas = new fabric.Canvas('memozor-canvas');
    
    // UI Elements
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

    // State Management
    let history = [];
    let historyIndex = -1;
    let isStateLoading = false;

    function saveState() {
        if (isStateLoading) return;
        
        // If we are saving a new state after undoing, truncate the future history
        if (historyIndex < history.length - 1) {
            history = history.slice(0, historyIndex + 1);
        }
        
        // Save current canvas JSON to history
        history.push(canvas.toJSON());
        historyIndex++;
        
        updateUndoRedoButtons();
    }

    function updateUndoRedoButtons() {
        if (undoBtn) undoBtn.disabled = historyIndex <= 0;
        if (redoBtn) redoBtn.disabled = historyIndex >= history.length - 1;
    }

    if (undoBtn) {
        undoBtn.addEventListener('click', () => {
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
    }

    if (redoBtn) {
        redoBtn.addEventListener('click', () => {
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
    }

    // Save initial blank state
    saveState();

    // Bind state saving to canvas events
    canvas.on('object:added', saveState);
    canvas.on('object:modified', saveState);
    canvas.on('object:removed', saveState);

    // 1. Upload Background Image
    uploadInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(f) {
            const data = f.target.result;
            fabric.Image.fromURL(data, function(img) {
                // Resize canvas to match image dimensions or scale image
                const maxWidth = 800;
                let scale = 1;
                if (img.width > maxWidth) {
                    scale = maxWidth / img.width;
                }
                
                canvas.setWidth(img.width * scale);
                canvas.setHeight(img.height * scale);
                
                canvas.setBackgroundImage(img, function() {
                    canvas.renderAll();
                    saveState();
                }, {
                    scaleX: scale,
                    scaleY: scale
                });
            });
        };
        reader.readAsDataURL(file);
    });

    // 2. Add Text
    addTextBtn.addEventListener('click', () => {
        const text = new fabric.IText('YOUR TEXT HERE', {
            left: canvas.width / 2,
            top: canvas.height / 2,
            fontFamily: fontFamilySelect ? fontFamilySelect.value : 'Impact, sans-serif',
            fill: textColorInput.value,
            stroke: strokeColorInput.value,
            strokeWidth: 2,
            fontSize: parseInt(textSizeInput.value, 10),
            originX: 'center',
            originY: 'center',
            fontWeight: 'bold',
            textAlign: 'center'
        });
        canvas.add(text);
        canvas.setActiveObject(text);
    });

    // 3. Update Text Properties on Selection
    canvas.on('selection:created', updateToolbar);
    canvas.on('selection:updated', updateToolbar);

    function updateToolbar(e) {
        const activeObj = canvas.getActiveObject();
        if (activeObj && activeObj.type === 'i-text') {
            if (fontFamilySelect) {
                // Try to find the matching option in the select
                const matchingOption = Array.from(fontFamilySelect.options).find(opt => opt.value === activeObj.fontFamily);
                if (matchingOption) {
                    fontFamilySelect.value = activeObj.fontFamily;
                } else if (activeObj.fontFamily === 'Impact' || activeObj.fontFamily === 'Arial') {
                   // backwards compat handling
                   const compatOption = Array.from(fontFamilySelect.options).find(opt => opt.value.includes(activeObj.fontFamily));
                   if (compatOption) fontFamilySelect.value = compatOption.value;
                }
            }
            textColorInput.value = activeObj.fill;
            strokeColorInput.value = activeObj.stroke;
            textSizeInput.value = activeObj.fontSize;
        }
    }

    // 4. Live Update Active Text
    if (fontFamilySelect) {
        fontFamilySelect.addEventListener('change', function() {
            const activeObj = canvas.getActiveObject();
            if (activeObj && activeObj.type === 'i-text') {
                activeObj.set('fontFamily', this.value);
                canvas.renderAll();
                saveState();
            }
        });
    }

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
            activeObj.set('fontSize', parseInt(this.value, 10));
            canvas.renderAll();
        }
    });
    textSizeInput.addEventListener('change', function() { saveState(); });

    // 5. Save Meme
    saveBtn.addEventListener('click', async () => {
        if (!canvas.backgroundImage) {
            alert('Please upload an image first.');
            return;
        }

        // Deselect so handles don't appear in final image
        canvas.discardActiveObject();
        canvas.renderAll();

        const dataURL = canvas.toDataURL({
            format: 'png',
            quality: 1
        });

        messageDiv.textContent = 'Saving...';

        try {
            const response = await fetch(memozorSettings.restUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': memozorSettings.nonce
                },
                body: JSON.stringify({
                    image_data: dataURL,
                    website_url: document.getElementById('memozor-website-url') ? document.getElementById('memozor-website-url').value : ''
                })
            });

            let result;
            const textResponse = await response.text();
            try {
                result = JSON.parse(textResponse);
            } catch (e) {
                console.error("Non-JSON response received: ", textResponse);
                messageDiv.innerHTML = `<span style="color:red">Server error (${response.status}): The server returned an invalid response.</span>`;
                return;
            }
            
            if (response.ok && result.success) {
                window.location.href = result.url;
            } else {
                messageDiv.innerHTML = `<span style="color:red">Error saving meme: ${result.message || result.code || 'Unknown error'}</span>`;
            }

        } catch (err) {
            messageDiv.innerHTML = `<span style="color:red">Network error saving meme: ${err.message}</span>`;
            console.error(err);
        }
    });
};
