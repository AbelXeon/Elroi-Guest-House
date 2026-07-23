<!DOCTYPE html>
<html>
<head>
    <title>Register Guest - Elroi Guest House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📷 Register Guest & Scan ID</h2>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('guests.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Guest Info -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" required placeholder="e.g. John Doe">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ID Type</label>
                                <select name="id_type" class="form-select" required>
                                    <option value="National ID">National ID</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Driver License">Driver License</option>
                                    <option value="Other">Other ID</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ID Number</label>
                                <input type="text" name="id_number" class="form-control" required placeholder="e.g. A12345678">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_no" class="form-control" required placeholder="+123456789">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- Webcam ID Photo Capture -->
                        <div class="col-md-6 border-start">
                            <h5 class="text-center mb-3">Capture ID Document Photo</h5>
                            
                            <div class="d-flex flex-column align-items-center">
                                <video id="webcam" autoplay playsinline width="320" height="240" class="border rounded bg-dark mb-2"></video>
                                <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                                
                                <button type="button" class="btn btn-primary my-2" onclick="takeSnap()">📸 Take Snap of ID</button>
                                
                                <div id="preview-container" style="display:none;" class="text-center mt-2">
                                    <h6>Captured Preview:</h6>
                                    <img id="photo-preview" width="240" height="180" class="border rounded border-success">
                                </div>

                                <!-- Hidden input storing the Base64 photo string -->
                                <input type="hidden" name="id_photo" id="id_photo_input">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-success btn-lg w-100 mt-2">Save Guest Information</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Webcam JavaScript -->
    <script>
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('canvas');
        const photoInput = document.getElementById('id_photo_input');
        const preview = document.getElementById('photo-preview');
        const previewContainer = document.getElementById('preview-container');

        // Request access to system camera
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                alert('Unable to access webcam: ' + err.message);
            });

        function takeSnap() {
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, 320, 240);
            
            // Convert captured frame to Base64 Image
            const imageData = canvas.toDataURL('image/png');
            
            photoInput.value = imageData;
            preview.src = imageData;
            previewContainer.style.display = 'block';
        }
    </script>
</body>
</html>