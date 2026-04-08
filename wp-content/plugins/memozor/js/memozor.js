document.addEventListener('DOMContentLoaded', () => {
    const canvasEl = document.getElementById('memozor-canvas');
    if (!canvasEl) return;

    // Initialize Fabric.js Canvas
    const canvas = new fabric.Canvas('memozor-canvas');
    
    // UI Elements
    const uploadInput = document.getElementById('memozor-upload');
    const addTextBtn = document.getElementById('memozor-add-text');
    const textColorInput = document.getElementById('memozor-text-color');
    const strokeColorInput = document.getElementById('memozor-stroke-color');
    const textSizeInput = document.getElementById('memozor-text-size');
    const saveBtn = document.getElementById('memozor-save');
    const messageDiv = document.getElementById('memozor-message');

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
                
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
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
            fontFamily: 'Impact, sans-serif',
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
            textColorInput.value = activeObj.fill;
            strokeColorInput.value = activeObj.stroke;
            textSizeInput.value = activeObj.fontSize;
        }
    }

    // 4. Live Update Active Text
    textColorInput.addEventListener('input', function() {
        const activeObj = canvas.getActiveObject();
        if (activeObj && activeObj.type === 'i-text') {
            activeObj.set('fill', this.value);
            canvas.renderAll();
        }
    });

    strokeColorInput.addEventListener('input', function() {
        const activeObj = canvas.getActiveObject();
        if (activeObj && activeObj.type === 'i-text') {
            activeObj.set('stroke', this.value);
            canvas.renderAll();
        }
    });

    textSizeInput.addEventListener('input', function() {
        const activeObj = canvas.getActiveObject();
        if (activeObj && activeObj.type === 'i-text') {
            activeObj.set('fontSize', parseInt(this.value, 10));
            canvas.renderAll();
        }
    });

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
                    image_data: dataURL
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
                messageDiv.innerHTML = `<span style="color:green">Success! Meme saved. <a href="${result.url}" target="_blank">View Image</a></span>`;
            } else {
                messageDiv.innerHTML = `<span style="color:red">Error saving meme: ${result.message || result.code || 'Unknown error'}</span>`;
            }

        } catch (err) {
            messageDiv.innerHTML = `<span style="color:red">Network error saving meme: ${err.message}</span>`;
            console.error(err);
        }
    });
});
