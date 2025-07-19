
    // Enhanced JavaScript with WhatsApp-style functionality
    const recordBtn = document.getElementById("recordBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const recordingControls = document.getElementById("recordingControls");
    const recordingStatus = document.getElementById("recordingStatus");
    const timer = document.getElementById("timer");
    const audioPreview = document.getElementById("audioPreview");
    const audioRecorder = document.getElementById("audioRecorder");
    const canvas = document.getElementById("waveform");
    const canvasCtx = canvas.getContext("2d");

    let mediaRecorder, audioChunks = [], audioBlob;
    let isRecording = false;
    let timerInterval, seconds = 0;
    const maxDuration = 5 * 60; // 5 minutes

    let animationId, audioContext, analyser, dataArray, sourceNode;

    // Enhanced waveform drawing with WhatsApp-style bars
    function drawWaveform() {
      const WIDTH = canvas.width;
      const HEIGHT = canvas.height;
      const barWidth = 4;
      const barGap = 2;
      const numBars = Math.floor(WIDTH / (barWidth + barGap));

      function draw() {
        animationId = requestAnimationFrame(draw);
        
        if (analyser) {
          analyser.getByteFrequencyData(dataArray);
        }

        // Clear canvas with gradient background
        const gradient = canvasCtx.createLinearGradient(0, 0, 0, HEIGHT);
        gradient.addColorStop(0, '#f8f9fa');
        gradient.addColorStop(1, '#ffffff');
        canvasCtx.fillStyle = gradient;
        canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

        // Draw bars
        for (let i = 0; i < numBars; i++) {
          const x = i * (barWidth + barGap);
          let barHeight;
          
          if (isRecording && dataArray) {
            const audioIndex = Math.floor(i * dataArray.length / numBars);
            barHeight = (dataArray[audioIndex] / 255) * HEIGHT * 0.8;
          } else {
            // Idle state - small random bars
            barHeight = Math.random() * 10 + 5;
          }

          // Create bar gradient
          const barGradient = canvasCtx.createLinearGradient(x, HEIGHT - barHeight, x, HEIGHT);
          barGradient.addColorStop(0, '#25d366');
          barGradient.addColorStop(1, '#128c7e');
          
          canvasCtx.fillStyle = barGradient;
          canvasCtx.fillRect(x, HEIGHT - barHeight, barWidth, barHeight);
          
          // Add glow effect
          canvasCtx.shadowColor = '#25d366';
          canvasCtx.shadowBlur = 10;
          canvasCtx.fillRect(x, HEIGHT - barHeight, barWidth, barHeight);
          canvasCtx.shadowBlur = 0;
        }
      }

      draw();
    }

    // Button Events
    recordBtn.addEventListener("click", () => {
      isRecording ? stopRecording() : startRecording();
    });

    cancelBtn.addEventListener("click", cancelRecording);

    // Start Recording
    function startRecording() {
      navigator.mediaDevices.getUserMedia({ 
        audio: {
          echoCancellation: true,
          noiseSuppression: true,
          sampleRate: 44100
        }
      }).then(stream => {
        // Setup audio context for visualization
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        sourceNode = audioContext.createMediaStreamSource(stream);
        analyser = audioContext.createAnalyser();
        analyser.fftSize = 256;
        analyser.smoothingTimeConstant = 0.8;
        sourceNode.connect(analyser);
        dataArray = new Uint8Array(analyser.frequencyBinCount);

        // Start waveform animation
        drawWaveform();

        // Setup media recorder with optimized settings
        const options = {
          audioBitsPerSecond: 96000, // Optimized for WhatsApp-like quality
          mimeType: MediaRecorder.isTypeSupported('audio/webm;codecs=opus') 
            ? 'audio/webm;codecs=opus' 
            : 'audio/webm'
        };

        mediaRecorder = new MediaRecorder(stream, options);
        mediaRecorder.ondataavailable = e => {
          if (e.data.size > 0) {
            audioChunks.push(e.data);
          }
        };

        mediaRecorder.onstop = () => {
          if (audioChunks.length === 0) return;

          audioBlob = new Blob(audioChunks, { 
            type: options.mimeType 
          });
          
          const audioUrl = URL.createObjectURL(audioBlob);
          audioPreview.src = audioUrl;
          audioPreview.classList.remove("d-none");
          
          updateRecordingStatus("Recording complete! ✅", false);
          stopWaveform();
          
          // Stop all tracks
          stream.getTracks().forEach(track => track.stop());
        };

        mediaRecorder.start(1000); // Collect data every second
        audioChunks = [];
        isRecording = true;
        seconds = 0;
        
        // Start timer
        timerInterval = setInterval(updateTimer, 1000);
        
        // Update UI
        updateRecordingUI(true);
        updateRecordingStatus("Recording... Tap again to stop", true);
        
      }).catch(err => {
        console.error('Microphone access failed:', err);
        updateRecordingStatus("❌ Microphone access denied", false);
      });
    }

    // Stop Recording
    function stopRecording() {
      if (!isRecording) return;
      
      clearInterval(timerInterval);
      isRecording = false;
      
      updateRecordingUI(false);
      
      if (mediaRecorder && mediaRecorder.state !== "inactive") {
        mediaRecorder.stop();
      }
      
      if (audioContext) {
        audioContext.close();
      }
    }

    // Cancel Recording
    function cancelRecording() {
      if (!isRecording) return;
      
      clearInterval(timerInterval);
      isRecording = false;
      
      updateRecordingUI(false);
      updateRecordingStatus("Recording cancelled ❌", false);
      
      audioPreview.classList.add("d-none");
      audioChunks = [];
      
      if (mediaRecorder && mediaRecorder.state !== "inactive") {
        mediaRecorder.stop();
      }
      
      if (audioContext) {
        audioContext.close();
      }
      
      stopWaveform();
    }

    // Update Recording UI
    function updateRecordingUI(recording) {
      const icon = recordBtn.querySelector('i');
      
      if (recording) {
        recordBtn.classList.add("recording");
        audioRecorder.classList.add("recording");
        recordingControls.classList.remove("d-none");
        recordingStatus.classList.add("recording");
        icon.className = "fas fa-stop";
      } else {
        recordBtn.classList.remove("recording");
        audioRecorder.classList.remove("recording");
        recordingControls.classList.add("d-none");
        recordingStatus.classList.remove("recording");
        icon.className = "fas fa-microphone";
        timer.textContent = "";
      }
    }

    // Update Recording Status
    function updateRecordingStatus(message, isRecording) {
      recordingStatus.innerHTML = `<i class="fas fa-${isRecording ? 'circle' : 'info-circle'}"></i> ${message}`;
    }

    // Timer Functions
    function updateTimer() {
      seconds++;
      if (seconds >= maxDuration) {
        stopRecording();
        return;
      }
      timer.textContent = formatTime(seconds);
    }

    function formatTime(sec) {
      const min = String(Math.floor(sec / 60)).padStart(2, '0');
      const rem = String(sec % 60).padStart(2, '0');
      return `${min}:${rem}`;
    }

    // Waveform Functions
    function stopWaveform() {
      if (animationId) {
        cancelAnimationFrame(animationId);
      }
      
      // Clear canvas and show idle state
      canvasCtx.clearRect(0, 0, canvas.width, canvas.height);
      drawIdleWaveform();
    }

    function drawIdleWaveform() {
      const WIDTH = canvas.width;
      const HEIGHT = canvas.height;
      const barWidth = 4;
      const barGap = 2;
      const numBars = Math.floor(WIDTH / (barWidth + barGap));

      // Clear with gradient
      const gradient = canvasCtx.createLinearGradient(0, 0, 0, HEIGHT);
      gradient.addColorStop(0, '#f8f9fa');
      gradient.addColorStop(1, '#ffffff');
      canvasCtx.fillStyle = gradient;
      canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

      // Draw small idle bars
      for (let i = 0; i < numBars; i++) {
        const x = i * (barWidth + barGap);
        const barHeight = 8;
        
        canvasCtx.fillStyle = '#e9ecef';
        canvasCtx.fillRect(x, HEIGHT - barHeight, barWidth, barHeight);
      }
    }

document.getElementById("appointmentForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const submitBtn = this.querySelector('.submit-btn');
  const originalText = submitBtn.innerHTML;

  submitBtn.innerHTML = '<span class="loading"></span> Submitting...';
  submitBtn.disabled = true;

  const formData = new FormData(this);
  formData.append("action", "book");

  if (audioBlob) {
    formData.append("audio", audioBlob, "voice.webm");
  }

  fetch("./php/submit_appointment.php", {
    method: "POST",
    body: formData
  })
    .then(response => response.json())  // << Fix: Expect JSON
    .then(data => {
      if (data.success) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
        this.insertBefore(successDiv, this.firstChild);
        this.reset();
        audioPreview.classList.add("d-none");
        updateRecordingStatus("Tap microphone to start recording", false);
        recordingControls.classList.add("d-none");
        audioBlob = null;
        drawIdleWaveform();

        setTimeout(() => successDiv.remove(), 5000);
      } else {
        throw new Error(data.message || "Something went wrong");
      }
    })
    .catch(error => {
      console.error('Submission error:', error);
      updateRecordingStatus("❌ " + error.message, false);
    })
    .finally(() => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
});

    // Initialize idle waveform on page load
    window.addEventListener('load', () => {
      drawIdleWaveform();
    });

    // Handle canvas resize
    window.addEventListener('resize', () => {
      const rect = canvas.getBoundingClientRect();
      canvas.width = rect.width * window.devicePixelRatio;
      canvas.height = rect.height * window.devicePixelRatio;
      canvasCtx.scale(window.devicePixelRatio, window.devicePixelRatio);
      
      if (!isRecording) {
        drawIdleWaveform();
      }
    });
