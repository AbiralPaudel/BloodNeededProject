<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAGAT CHAIYO</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="images/blood-drop.svg" type="image/x-icon">
    <style>
    html {
        min-height: 100%;
        position: relative;
    }
    /* Navbar styles */
    .navbar-nav .nav-item a {
        position: relative;
        color: #777;
        margin-right:10px;
        text-decoration: none;
        overflow: hidden;
    }
    .navbar-nav li a:hover {
        color: #1abc9c !important;
    }

    /* --- CHATBOT CUSTOM STYLES --- */
    #chatbot-bubble {
        position: fixed;
        bottom: 80px;
        right: 30px;
        width: 60px;
        height: 60px;
        background-color: #1abc9c;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 28px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 1000;
        transition: transform 0.3s ease;
    }
    #chatbot-bubble:hover { transform: scale(1.1); }

    #chat-card {
        position: fixed;
        bottom: 150px;
        right: 30px;
        width: 320px;
        height: 400px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        display: none; /* Hidden by default */
        flex-direction: column;
        z-index: 1000;
        overflow: hidden;
    }
    .chat-header {
        background: #1abc9c;
        color: white;
        padding: 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
    }
    .chat-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
    }
    .message {
        margin-bottom: 10px;
        padding: 8px 12px;
        border-radius: 10px;
        max-width: 80%;
        font-size: 14px;
    }
    .bot-msg { background: #e9ecef; align-self: flex-start; color: #333; }
    .user-msg { background: #1abc9c; align-self: flex-end; color: white; }
    .chat-footer {
        padding: 10px;
        border-top: 1px solid #eee;
        display: flex;
    }
    .chat-footer input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 5px 15px;
        outline: none;
    }
    </style>
</head>
<body style="background-color: #f5f5dc;">
    <div class="container" style="margin-bottom: 50px;">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color:#f8f88f;">
            <a class="navbar-brand" href="index.php" style="color: #777;font-size:22px;letter-spacing:2px;">RC</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="patient/register.php">REGISTER</a></li>
                    <li class="nav-item"><a class="nav-link" href="patient/login.php">LOGIN</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class='container text-center' style="color:#000;padding-top: 100px;padding-bottom:50px;">
        <h1 class="display-6">Ragat Chaiyo</h1>
        <div class="row align-items-center">
            <div class="col-lg-6">
                <p class="lead mt-3">
                    This system is designed to efficiently manage blood donations, donors, and recipients, ensuring the availability of safe and life-saving blood for those in need.
                </p>
                <p class="lead mt-3 mb-5">
                    Join us in the mission to save lives. Register as a donor or recipient and help make a difference!
                </p>
            </div>
            <div class="col-lg-6">
                <img id="animated-image" src="images/home.svg" alt="" class="img-fluid d-none d-lg-block">
            </div>
        </div>
    </div>

    <div id="chatbot-bubble" onclick="toggleChat()">
        <i class="fa fa-commenting"></i>
    </div>

    <div id="chat-card" class="flex-column">
        <div class="chat-header">
            <span>RC Assistant</span>
            <span style="cursor:pointer" onclick="toggleChat()">&times;</span>
        </div>
        <div class="chat-body" id="chatMessages">
            <div class="message bot-msg">Hello! I'm the Ragat Chaiyo assistant. How can I help you today?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Ask me something..." onkeypress="checkEnter(event)">
            <button onclick="sendMessage()" class="btn btn-link text-success p-0 ml-2"><i class="fa fa-paper-plane fa-lg"></i></button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function toggleChat() {
            const chatCard = document.getElementById('chat-card');
            chatCard.style.display = (chatCard.style.display === 'flex') ? 'none' : 'flex';
        }

        function checkEnter(event) {
            if (event.key === 'Enter') sendMessage();
        }

        function sendMessage() {
            const input = document.getElementById('userInput');
            const messages = document.getElementById('chatMessages');
            const text = input.value.trim();

            if (text === "") return;

            // User Message
            messages.innerHTML += `<div class="message user-msg">${text}</div>`;
            input.value = "";

            // Bot Response logic
            let botReply = "I'm sorry, I'm still learning. Please register or login to get more help!";
            const lowerText = text.toLowerCase();

            if(lowerText.includes("hello") || lowerText.includes("hi")) {
                botReply = "Namaste! How can I help you with blood donation today?";
            } else if(lowerText.includes("register")) {
                botReply = "You can register as a donor or recipient by clicking the REGISTER button in the top menu.";
            } else if(lowerText.includes("blood") || lowerText.includes("need")) {
                botReply = "If you need blood urgently, please Register/Login and post a request in the system.";
            }

            // Simulate typing delay
            setTimeout(() => {
                messages.innerHTML += `<div class="message bot-msg">${botReply}</div>`;
                messages.scrollTop = messages.scrollHeight;
            }, 600);

            messages.scrollTop = messages.scrollHeight;
        }
    </script>

    <footer class="footer" style="background-color:#1abc9c; color: #FFF; padding: 15px; text-align: center; position: absolute; bottom: 0; width: 100%;">
        &copy; 2025 Ragat Chaiyo - Blood Bank Management System
    </footer>
</body>
</html>