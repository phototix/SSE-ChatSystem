<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebRTC Room</title>
    <style>
        #video-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        video {
            width: 100%;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <h1>WebRTC Room</h1>
    <div id="video-container">
        <video id="localVideo" autoplay playsinline muted></video>
    </div>

    <script src="script.js"></script>
</body>
</html>
