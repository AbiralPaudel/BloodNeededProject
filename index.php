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
        .message{
            margin:8px;
            padding:8px 12px;
            border-radius:10px;
            max-width:75%;
            font-size:14px;
        }
        .user-msg{
            background:#dcf8c6;
            margin-left:auto;
        }
        .bot-msg{
            background:#f1f0f0;
        }
        .message small{
            display:block;
            font-size:10px;
            opacity:0.6;
        }
        .chat-modal{
            position:fixed;
            top:0;left:0;
            width:100%;height:100%;
            background:rgba(0,0,0,0.6);
            display:flex;
            justify-content:center;
            align-items:center;
            z-index:9999;
        }
        .modal-content{
            background:#fff;
            padding:20px;
            width:90%;
            max-width:400px;
            border-radius:8px;
        }
        .modal-content input,
        .modal-content select{
            width:100%;
            padding:8px;
            margin-bottom:10px;
        }
        .modal-content button{
            padding:8px 12px;
            margin-right:5px;
        }
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
            <div class="message bot-msg">Hello! Welcome to our Ragat Chahiyo system.</div>
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
      

            /* ==========================================
                BLOOD DONATION NLP CHATBOT
                TF-IDF + COSINE SIMILARITY ENGINE
                ========================================== 
            */

            const SIMILARITY_THRESHOLD = 0.30;
            const REGISTER_THRESHOLD = 0.40;

            let conversationContext = {};
            let idfCache = {};
            let vocabulary = [];
            let tfidfMatrix = [];

            /* ================================
            STOPWORDS
            ================================= */
            const stopWords = new Set([
            "is","am","are","the","a","an","and","or","of","to","in","on",
            "for","with","what","how","can","i","you","me","my","about",
            "please","tell","do","does","did","be","been","this","that","have"
            ]);

            // quick commands
            const EXIT_REGEX = /\b(exit|bye|goodbye|quit|close|see you|see ya|sayonara)\b/i;
            const CLEAR_REGEX = /\b(clear chat|clear conversation|clear|reset chat|reset conversation|new chat|start over|erase conversation)\b/i;

            /* ================================
            FAQ KNOWLEDGE BASE (20+)
            ================================= */
            let faqs = [

                {question:"What is Ragat Chahiyo?", answer:"Ragat Chahiyo is a blood donor and recipient connection platform based in Kathmandu, Nepal that helps patients find blood donors quickly."},

                {question:"How does Ragat Chahiyo work?", answer:"Ragat Chahiyo connects blood donors and patients in Kathmandu. You can register as donor or post urgent blood requests."},

                {question:"Is Ragat Chahiyo free?", answer:"Yes, Ragat Chahiyo is completely free to use for donors and patients in Nepal."},

                {question:"Who can donate blood in Nepal?", answer:"Anyone aged 18–60 years, weighing more than 50kg, and medically fit can donate blood in Nepal."},

                {question:"What is eligibility for blood donation in Kathmandu?", answer:"You must be 18–60 years old, over 50kg weight, healthy, and at least 3 months since last donation."},

                {question:"How often can I donate blood in Nepal?", answer:"You can donate whole blood every 3 months as per Nepal Red Cross guidelines."},

                {question:"Where can I donate blood in Kathmandu?", answer:"You can donate at Nepal Red Cross Society, Teaching Hospital Maharajgunj, Bir Hospital, and Patan Hospital."},

                {question:"How to register as donor in Ragat Chahiyo?", answer:"Click register and create a donor profile with your blood group and contact details."},

                {question:"How to request blood in Kathmandu?", answer:"Login to Ragat Chahiyo and post a blood request with hospital name and urgency level."},

                {question:"What blood groups are most needed in Nepal?", answer:"O negative is highly needed. O+ and A+ are also frequently required in Kathmandu hospitals."},

                {question:"Is blood donation safe in Nepal?", answer:"Yes, blood donation in authorized blood banks in Kathmandu is completely safe and sterile."},

                {question:"How much blood is taken during donation?", answer:"Approximately 350ml to 450ml of blood is collected per donation."},

                {question:"How long does blood donation take?", answer:"The entire process takes about 30 to 45 minutes."},

                {question:"Does blood donation hurt?", answer:"You may feel a small needle prick but it is generally painless."},

                {question:"Can women donate blood in Nepal?", answer:"Yes, healthy women can donate blood if hemoglobin levels are normal."},

                {question:"Can I donate blood during menstruation?", answer:"Yes, if you feel healthy and your hemoglobin is normal."},

                {question:"Can diabetics donate blood?", answer:"Controlled diabetics on oral medication may donate after medical screening."},

                {question:"Can I donate after COVID recovery?", answer:"Yes, after 28 days of full recovery."},

                {question:"Can I donate after vaccination?", answer:"Yes, after 14 days if no symptoms are present."},

                {question:"What documents are required to donate blood?", answer:"You need a valid citizenship card or any government ID."},

                {question:"What is universal donor?", answer:"O negative blood group is called universal donor."},

                {question:"What is universal recipient?", answer:"AB positive is universal recipient."},

                {question:"Can I donate blood if I have tattoos?", answer:"Yes, after 6 months of getting a tattoo."},

                {question:"Can smokers donate blood?", answer:"Yes, but avoid smoking at least 2 hours before donation."},

                {question:"Can I donate blood if I have high blood pressure?", answer:"Controlled blood pressure patients may donate after medical approval."},

                {question:"Is there any payment for blood donation?", answer:"No, blood donation in Nepal is voluntary and unpaid."},

                {question:"How does Ragat Chahiyo verify donors?", answer:"Donors are verified via phone number and blood group details."},

                {question:"How to update my blood group in Ragat Chahiyo?", answer:"Login and edit your profile settings."},

                {question:"Can I delete my Ragat Chahiyo account?", answer:"Yes, contact support or use account settings to delete profile."},

                {question:"How to find O negative donor in Kathmandu?", answer:"Search O- donors or post urgent request in Ragat Chahiyo."},

                {question:"Emergency blood needed in Kathmandu", answer:"Post urgent request on Ragat Chahiyo with hospital details immediately."},

                {question:"Blood needed at Teaching Hospital Kathmandu", answer:"Post request specifying Teaching Hospital Maharajgunj."},

                {question:"Blood needed at Bir Hospital", answer:"Post urgent request with Bir Hospital details in Ragat Chahiyo."},

                {question:"Blood needed at Patan Hospital", answer:"Login and post urgent request mentioning Patan Hospital."},

                {question:"Can I donate blood if I am underweight?", answer:"You must weigh at least 50kg to donate blood."},

                {question:"Can I donate blood if I have fever?", answer:"No, you must wait until fully recovered."},

                {question:"What to eat before blood donation?", answer:"Eat light healthy food and drink plenty of water."},

                {question:"What to avoid before blood donation?", answer:"Avoid alcohol and heavy fatty meals before donation."},

                {question:"What happens after blood donation?", answer:"You will rest for 10-15 minutes and receive light refreshments."},

                {question:"Can I exercise after donating blood?", answer:"Avoid heavy exercise for 24 hours."},

                {question:"How long does donated blood last?", answer:"Whole blood can be stored up to 35-42 days."},

                {question:"Is blood tested after donation?", answer:"Yes, all donated blood is screened for infections."},

                {question:"Can foreigners donate blood in Nepal?", answer:"Yes, if they meet eligibility criteria and have valid ID."},

                {question:"Can students donate blood?", answer:"Yes, if 18+ and healthy."},

                {question:"How to contact Nepal Red Cross Kathmandu?", answer:"You can visit Nepal Red Cross Society Central Blood Bank Kathmandu."},

                {question:"Is Ragat Chahiyo available outside Kathmandu?", answer:"Currently focused in Kathmandu Valley but expanding soon."},

                {question:"Does Ragat Chahiyo support platelet donation?", answer:"Yes, you can specify platelet donation in your profile."},

                {question:"What is platelet donation?", answer:"Platelets help cancer and dengue patients and can be donated separately."},

                {question:"How often can I donate platelets?", answer:"Platelets can be donated every 2 weeks."},

                {question:"Can I donate if I had surgery?", answer:"You must wait 6 months after major surgery."},

                {question:"Can I donate if pregnant?", answer:"No, pregnant women cannot donate blood."},

                {question:"How to search donors by blood group?", answer:"Use filter option in Ragat Chahiyo dashboard."},

                {question:"What is rare blood group in Nepal?", answer:"AB negative is considered rare in Nepal."},

                {question:"How to share blood request quickly?", answer:"Share your Ragat Chahiyo request link on Facebook and Viber."},

                {question:"Is my data safe in Ragat Chahiyo?", answer:"Yes, your personal information is securely stored."},

                {question:"Can I donate blood at home?", answer:"No, blood donation must be done at authorized centers."},

                {question:"What is hemoglobin requirement?", answer:"Minimum hemoglobin should be 12.5 g/dL."},

                {question:"Can I donate if I have cold?", answer:"Wait until fully recovered."},

                {question:"Is fasting required before donation?", answer:"No fasting is not required."},

                {question:"Can elderly donate blood?", answer:"Up to age 60 if medically fit."},

                {question:"How many donors are available in Kathmandu?", answer:"Ragat Chahiyo is growing with active registered donors."},

                {question:"Can I donate blood if I drink alcohol?", answer:"Avoid alcohol 24 hours before donation."},

                {question:"How to get emergency donor within 1 hour?", answer:"Post urgent request and call nearby donors."},

                {question:"Does Ragat Chahiyo send notifications?", answer:"Yes, registered donors receive notifications for nearby requests."},

                {question:"Can I register as both donor and patient?", answer:"Yes, you can select your role during registration."},

                {question:"How to reset password?", answer:"Use forgot password option on login page."},

                {question:"Does Ragat Chahiyo work on mobile?", answer:"Yes, it is mobile-friendly and accessible across Nepal."},

                {question:"Can I donate if I have hepatitis history?", answer:"No, hepatitis patients cannot donate blood."},

                {question:"Can I donate if I have HIV?", answer:"No, HIV positive individuals cannot donate blood."},

                {question:"How to become regular donor?", answer:"Donate every 3 months and stay active on Ragat Chahiyo."},

                {question:"Is there blood shortage in Kathmandu?", answer:"Yes, shortages happen during festivals and emergencies."},

                {question:"Can I donate during Dashain or Tihar?", answer:"Yes, donation is encouraged during festivals."},

                {question:"How to organize blood donation camp?", answer:"Contact Nepal Red Cross and collaborate with Ragat Chahiyo."},

                {question:"Can I donate after dental treatment?", answer:"Wait 3 days after minor dental procedures."},

                {question:"How to contact Ragat Chahiyo support?", answer:"Use contact form on website."},

                {question:"Can I cancel blood request?", answer:"Yes, update your request status after fulfilled."},

                {question:"How to confirm donor arrival?", answer:"Contact donor directly via phone."},

                {question:"Can I donate if I have asthma?", answer:"Mild controlled asthma patients may donate."},

                {question:"Is blood donation confidential?", answer:"Yes, donor information is kept confidential."},

                {question:"Can I donate twice in one month?", answer:"No, minimum 3 months gap required."},

                {question:"What is cross matching?", answer:"Lab test to ensure donor and recipient compatibility."},

                {question:"Does Ragat Chahiyo verify hospitals?", answer:"Yes, major Kathmandu hospitals are recognized."},

                {question:"How to find AB negative donor?", answer:"Search AB- in donor filter or post urgent request."},

                {question:"Can army personnel donate blood?", answer:"Yes, if medically fit."},

                {question:"How to volunteer with Ragat Chahiyo?", answer:"Register and select volunteer option."},

                {question:"Can I donate if I have anemia?", answer:"No, hemoglobin must be normal."},

                {question:"Is there age limit for donors?", answer:"Yes, 18 to 60 years."},

                {question:"How to update contact number?", answer:"Edit profile in dashboard."},

                {question:"Can I donate if I had malaria?", answer:"Wait 3 months after recovery."},

                {question:"Can I donate if I take antibiotics?", answer:"Wait until medication course is completed."},

                {question:"How to check my blood group?", answer:"You can test at nearby hospital lab."},

                {question:"Why donate blood?", answer:"Blood donation saves lives of accident victims, surgery patients and mothers."},

                {question:"What is plasma donation?", answer:"Plasma is liquid part of blood used for treatment."},

                {question:"Does Ragat Chahiyo support plasma requests?", answer:"Yes, you can mention plasma in request."},

                {question:"How many lives can one donation save?", answer:"One donation can save up to three lives."}

        ];

            /* ================================
            ADVANCED PREPROCESSING & TF-IDF
            - Stemming (lightweight)
            - Synonym mapping
            - Nepali-English mapping
            - Faster sparse TF-IDF representation
            ================================= */

            const synonyms = new Map([
                // donation synonyms
                ['donate','donate'], ['give','donate'], ['donation','donate'], ['donor','donate'],
                ['register','register'], ['signup','register'], ['sign-up','register'], ['sign up','register'],
                ['urgent','urgent'], ['emergency','urgent'], ['asap','urgent'], ['immediately','urgent'], ['jaldi','urgent'], ['chito','urgent'],
                ['need','need'], ['needed','need'], ['want','need'], ['wants','need'], ['chahiyo','need'], ['ragat','blood'], ['blood','blood']
            ]);

            // Additional synonym sets for query expansion
            const synonymSets = {
                'blood_needed': ['urgent','emergency','asap','jaldi','chito']
            };

            function simpleStem(word){
                // very lightweight stemming: remove common suffixes
                return word.replace(/(ing|ed|ly|es|s)$/,'');
            }

            function preprocess(text){
                text = text.toLowerCase();
                // Nepali-English mapping
                text = text.replace(/\bragat\b/g,'blood');
                text = text.replace(/\bchahiyo\b/g,'need');

                // remove punctuation except + and - (for blood types)
                text = text.replace(/[^a-z0-9\+\-\s]/g,' ');

                // tokenization
                let tokens = text.split(/\s+/).filter(Boolean);

                let out = [];
                for(let t of tokens){
                    // map synonyms
                    if(synonyms.has(t)){
                        out.push(synonyms.get(t));
                        continue;
                    }
                    // normalize numbers/hospitals as-is
                    t = simpleStem(t);
                    out.push(t);
                }

                // remove stopwords quickly
                return out.filter(w => w && !stopWords.has(w));
            }

            // Build sparse TF-IDF: for each document, store {term:weight} and norm
            function buildTFIDF(){
                vocabulary = [];
                let documents = faqs.map(f => preprocess(f.question));
                // build vocabulary (index)
                const termIndex = new Map();
                documents.forEach(doc=>{
                    doc.forEach(term=>{
                        if(!termIndex.has(term)){
                            termIndex.set(term, termIndex.size);
                        }
                    });
                });

                vocabulary = Array.from(termIndex.keys());
                const docCount = documents.length;

                // compute idf
                idfCache = {};
                vocabulary.forEach(term=>{
                    let df = documents.filter(doc=>doc.includes(term)).length;
                    idfCache[term] = Math.log((docCount + 1) / (df + 1));
                });

                // build sparse tf-idf for each doc
                tfidfMatrix = [];
                const docNorms = [];
                documents.forEach(doc=>{
                    const freqs = {};
                    doc.forEach(t=> freqs[t] = (freqs[t]||0) + 1);
                    const vec = {};
                    let sumSq = 0;
                    for(const term in freqs){
                        const w = freqs[term] * (idfCache[term] || 0);
                        vec[term] = w;
                        sumSq += w*w;
                    }
                    const norm = Math.sqrt(sumSq) || 1;
                    tfidfMatrix.push({vec, norm});
                    docNorms.push(norm);
                });

                // cache idf
                localStorage.setItem('idfCache', JSON.stringify(idfCache));
            }

            function sparseCosine(queryVec, queryNorm, doc){
                // doc.vec is map term->weight
                let dot = 0;
                for(const term in queryVec){
                    if(doc.vec[term]) dot += queryVec[term] * doc.vec[term];
                }
                return dot / (queryNorm * doc.norm);
            }

            function matchQuery(query){
                const tokens = preprocess(query);
                const freqs = {};
                tokens.forEach(t => freqs[t] = (freqs[t]||0) + 1);
                const queryVec = {};
                let sumSq = 0;
                for(const t in freqs){
                    const w = freqs[t] * (idfCache[t] || 0);
                    queryVec[t] = w;
                    sumSq += w*w;
                }
                const queryNorm = Math.sqrt(sumSq) || 1;

                let best = {similarity:0, index:-1};
                for(let i=0;i<tfidfMatrix.length;i++){
                    const sim = sparseCosine(queryVec, queryNorm, tfidfMatrix[i]);
                    if(sim > best.similarity){ best.similarity = sim; best.index = i; }
                }
                return {similarity: best.similarity, index: best.index};
            }

            /* ================================
            INTENT CLASSIFICATION (rule-based lightweight)
            Returns {intent, score}
            ================================= */
            function detectUrgency(text){
                const urgents = ['urgent','emergency','asap','immediately','jaldi','chito','now','quick'];
                const low = text.toLowerCase();
                for(const u of urgents) if(low.includes(u)) return true;
                return false;
            }

            function detectBloodGroupInText(text){
                // Normalize then test
                const normalized = normalizeBloodInput(text);
                if(/^(A\+|A\-|B\+|B\-|AB\+|AB\-|O\+|O\-)$/.test(normalized)) return normalized;
                return null;
            }

            function intentClassifier(text){
                const low = text.toLowerCase();
                const intentScores = {};

                function add(intent, score){ intentScores[intent] = (intentScores[intent]||0) + score; }

                // Greetings
                if(/\b(hi|hello|namaste|hey|good morning|good evening)\b/.test(low)) add('greeting',1);

                // Registration
                if(/\b(register|signup|sign up|create account)\b/.test(low)) add('registration',1);

                // Donation process
                if(/\b(donate|donation|how to donate|where to donate)\b/.test(low)) add('donation process',1);

                // Eligibility
                if(/\b(can i donate|who can donate|eligib|age|weight|hemoglobin)\b/.test(low)) add('eligibility',1);

                // Medical restriction
                if(/\b(diabetes|hiv|hepatitis|pregnant|anemia|fever|surgery|vaccin|vaccine)\b/.test(low)) add('medical restriction',1);

                // Hospital inquiry
                if(/\b(hospital|aiims|bir hospital|patan|teaching hospital|where to donate)\b/.test(low)) add('hospital inquiry',1);

                // Account help
                if(/\b(login|password|reset|account|profile)\b/.test(low)) add('account help',1);

                // Urgent request / blood group search
                if(/\b(need blood|need .*blood|want blood|want .*blood|blood needed|looking for blood|searching for blood|urgent|emergency|asap|jaldi|chito)\b/.test(low)) add('urgent request',2);

                // Blood group explicit
                const bg = detectBloodGroupInText(text);
                if(bg) add('blood group search',2);

                // Fallback small credit to TF-IDF
                add('fallback', 0.1);

                // Normalize scores to [0,1]
                const entries = Object.entries(intentScores);
                let max = 0; for(const [,s] of entries) if(s>max) max=s;
                if(max===0) return {intent:'fallback', score:0};
                // pick highest
                let bestIntent = 'fallback', bestScore = 0;
                for(const [k,s] of entries){
                    const sc = s / max;
                    if(sc > bestScore){ bestScore = sc; bestIntent = k; }
                }
                return {intent: bestIntent, score: bestScore};
            }

            /* ================================
            Combined matching: intent + TF-IDF weighted scoring
            score = alpha * intentScore + beta * tfidfScore
            Where alpha=0.6, beta=0.4 by default. Intent high-priority intents can override.
            ================================= */
            const ALPHA = 0.6, BETA = 0.4;

            function combinedMatch(text){
                const intent = intentClassifier(text);
                const tf = matchQuery(text);
                const tfidfScore = tf.similarity || 0;
                const combined = ALPHA * intent.score + BETA * tfidfScore;
                return {intent, tfidf: tf, score: combined};
            }

            /* ================================
            MESSAGE HANDLING
            ================================= */
            function toggleChat() {
                        const chatCard = document.getElementById('chat-card');
                        chatCard.style.display = (chatCard.style.display === 'flex') ? 'none' : 'flex';
                    }

                    function checkEnter(event) {
                        if (event.key === 'Enter') sendMessage();
                    }
            function sendMessage(){
            const input = document.getElementById('userInput');
            const messages = document.getElementById('chatMessages');
            let text = input.value.trim();
            if(!text) return;

            // Immediate short-circuit commands
            if(EXIT_REGEX.test(text)){
                // close chat immediately
                input.value = '';
                const chatCard = document.getElementById('chat-card');
                chatCard.style.display = 'none';
                conversationContext = {}; // clear context
                return;
            }

            if(CLEAR_REGEX.test(text)){
                // clear conversation history and reset context
                messages.innerHTML = `<div class="message bot-msg">Hello! Welcome to our Ragat Chahiyo system.</div>`;
                conversationContext = {};
                input.value = '';
                return;
            }

            addMessage(text,"user-msg");
            input.value="";

            setTimeout(()=>{
                // If we are expecting a blood type, handle that specially
                if (conversationContext.expecting_blood_type) {
                    handleBloodTypeResponse(text);
                } else {
                    handleBotResponse(text);
                }
            },600);
            }

            function normalizeBloodInput(raw) {
                raw = raw.toLowerCase().trim();
                // common words removal
                raw = raw.replace(/\b(blood|group|type|need|want|urgent|please|any|have|lookingfor|looking for|searching for)\b/g, '');
                // collapse spaces
                raw = raw.replace(/\s+/g, ' ');
                // normalize words
                raw = raw.replace(/positive|plus|pos/g, '+');
                raw = raw.replace(/negative|minus|neg/g, '-');
                // remove extraneous characters except + and -
                raw = raw.replace(/[^a-z0-9\+\-]/g, '');
                // handle short forms like op/on/ap/an etc
                raw = raw.replace(/^op$/,'o+').replace(/^on$/,'o-');
                raw = raw.replace(/^ap$/,'a+').replace(/^an$/,'a-');
                raw = raw.replace(/^bp$/,'b+').replace(/^bn$/,'b-');
                raw = raw.replace(/^abp$/,'ab+').replace(/^abn$/,'ab-');
                // remove trailing/leading spaces and return upper
                raw = raw.trim();
                // if it's like 'a+' or 'ab+' return upper
                if (/^[abop]{1,2}[\+\-]$/.test(raw) ) return raw.toUpperCase();
                // try to extract a pattern like A, B, AB, O with sign
                let m = raw.match(/(ab|a|b|o)(\+|\-)?/);
                if (m) {
                    let g = m[1] + (m[2] || '');
                    return g.toUpperCase();
                }
                return raw.toUpperCase();
            }

            function mapToColumn(group){
                const m = {
                    'A+':'AP','A-':'AN','B+':'BP','B-':'BN','AB+':'ABP','AB-':'ABN','O+':'OP','O-':'ON'
                };
                return m[group] || null;
            }

            function handleBloodTypeResponse(text){
                const group = normalizeBloodInput(text);

                // helper: levenshtein distance for fuzzy matching
                function levenshtein(a, b){
                    if (a === b) return 0;
                    const al = a.length, bl = b.length;
                    if (al === 0) return bl;
                    if (bl === 0) return al;
                    let matrix = Array.from({length: al + 1}, () => Array(bl + 1).fill(0));
                    for (let i = 0; i <= al; i++) matrix[i][0] = i;
                    for (let j = 0; j <= bl; j++) matrix[0][j] = j;
                    for (let i = 1; i <= al; i++){
                        for (let j = 1; j <= bl; j++){
                            const cost = a[i-1] === b[j-1] ? 0 : 1;
                            matrix[i][j] = Math.min(matrix[i-1][j] + 1, matrix[i][j-1] + 1, matrix[i-1][j-1] + cost);
                        }
                    }
                    return matrix[al][bl];
                }

                const canonical = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                let col = mapToColumn(group);

                // Fallback: try fuzzy match to canonical list
                if(!col){
                    let best = null, bestScore = Infinity;
                    const cleaned = group.toLowerCase().replace(/[^a-z0-9\+\-]/g,'');
                    canonical.forEach(c => {
                        const score = levenshtein(cleaned, c.toLowerCase());
                        if(score < bestScore){ bestScore = score; best = c; }
                    });
                    // allow small typos (threshold 1)
                    if(best && bestScore <= 1){
                        col = mapToColumn(best);
                        // use best as group label
                        groupLabel = best;
                    }
                }

                if(!col){
                    addMessage('I could not understand that blood group. Please reply with A+, A-, B+, B-, O+, O-, AB+, or AB-.', 'bot-msg');
                    return;
                }

                // clear expectation
                conversationContext.expecting_blood_type = false;

                const finalGroup = (typeof groupLabel !== 'undefined') ? groupLabel : group;

                // Query server API
                let fd = new FormData();
                fd.append('blood', finalGroup);
                fetch('api/check_blood.php', {method:'POST', body: fd})
                .then(r=>r.json())
                .then(data=>{
                    if(data.status==='success'){
                        const count = data.count || 0;
                        if(count > 0){
                            addMessage(`We have ${count} unit(s) of ${finalGroup} available. You can post a request or contact nearby donors.`, 'bot-msg');
                        } else {
                            addMessage(`Sorry, we don't currently have ${finalGroup} in stock. Please post an urgent request and share hospital details.`, 'bot-msg');
                        }
                    } else {
                        addMessage('Sorry, could not check stock right now. Try again later.', 'bot-msg');
                    }
                })
                .catch(()=> addMessage('Server error while checking stock.', 'bot-msg'));
            }

            function handleBotResponse(text){
            const lower = text.toLowerCase();

            if(lower.startsWith("addfaq:")){
                let parts = text.replace("addfaq:","").split("|");
                if(parts.length===2){
                faqs.push({question:parts[0].trim(), answer:parts[1].trim()});
                buildTFIDF();
                addMessage("FAQ added successfully.","bot-msg");
                }else{
                addMessage("Format: addfaq: question | answer","bot-msg");
                }
                return;
            }

            // Explicit intent rules (take precedence over TF-IDF matching)
            if(/\b(register|signup|sign up|sign\-up)\b/.test(lower) || lower.includes('i want to register')){
                showRegisterModal();
                return;
            }

            // donation intent should be explicit (not eligibility questions like "can I donate")
            if(/\b(donate blood|want to donate|i want to donate|how to donate|where to donate)\b/.test(lower)){
                addMessage("If you'd like to donate blood, please register as a donor. Opening the registration form now.","bot-msg");
                showRegisterModal();
                return;
            }

            if(lower.includes('blood groups') || lower.includes('blood group') || lower.includes('blood types') || lower.includes('blood type') || /what .*blood/.test(lower)){
                addMessage('Common blood groups: A+, A-, B+, B-, O+, O-, AB+, AB-.', 'bot-msg');
                return;
            }

            // If user asks for blood (need/need blood) or asks if we have blood, ask which blood group they need
            if(/\b(need blood|need a blood|i need blood|need blood for|need .*blood|want blood|i want blood|want .*blood|looking for blood|looking for a donor|searching for blood|need urgent|need asap|need help finding blood)\b/.test(lower)
               || /blood needed/.test(lower)
               || ((/\b(do you have|have you|is there|do you have any|any available|any in stock|available|in stock|have any|have any in)\b/.test(lower)) && lower.includes('blood'))){
                conversationContext.expecting_blood_type = true;
                addMessage('Which blood group do you need? (e.g. A+, O-, B+)', 'bot-msg');
                return;
            }

            // don't answer for very short/single-word queries
            const preTokens = preprocess(text);
            if(preTokens.length <= 1){
                addMessage("I'm sorry, I didn't quite understand that. Could you give me a bit more detail?","bot-msg");
                return;
            }

            let match = matchQuery(text);

            if(match.similarity > REGISTER_THRESHOLD && lower.includes("register")){
                showRegisterModal();
                return;
            }

            if(match.similarity > SIMILARITY_THRESHOLD){
                addMessage(faqs[match.index].answer,"bot-msg");
            }else{
                addMessage("I'm sorry, I didn't quite understand that. You can ask about eligibility, registration, blood groups or similar topics.","bot-msg");
            }
            }

            // Intercept next user message when expecting a blood type
            // (we handle this earlier in the flow via conversationContext)

            /* ================================
            MESSAGE UI
            ================================= */
            function addMessage(text,className){
            const messages=document.getElementById("chatMessages");
            let time=new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
            messages.innerHTML += `<div class="message ${className}">
            <span>${text}</span>
            <small>${time}</small>
            </div>`;
            messages.scrollTop = messages.scrollHeight;
            }

            /* ================================
            REGISTRATION MODAL
            ================================= */
            function showRegisterModal(){
            const modal = document.createElement("div");
            modal.className="chat-modal";
            modal.innerHTML=`
            <div class="modal-content">
            <h3>Register</h3>
            <input id="regName" placeholder="Full Name">
            <input id="regEmail" placeholder="Email">
            <input id="regUsername" placeholder="Username">
            <input id="regPassword" type="password" placeholder="Password">
            <select id="regBlood">
                <option>A+</option><option>A-</option>
                <option>B+</option><option>B-</option>
                <option>O+</option><option>O-</option>
                <option>AB+</option><option>AB-</option>
            </select>
            <select id="regRole">
                <option value="donor">Donor</option>
                <option value="patient">Patient</option>
            </select>
            <button onclick="submitRegistration()">Submit</button>
            <button onclick="this.closest('.chat-modal').remove()">Cancel</button>
            </div>
            `;
            document.body.appendChild(modal);
            }

            function submitRegistration(){
            let name=document.getElementById("regName").value.trim();
            let email=document.getElementById("regEmail").value.trim();
            let username=document.getElementById("regUsername").value.trim();
            let pass=document.getElementById("regPassword").value;
            let blood=document.getElementById("regBlood").value;
            let role=document.getElementById("regRole").value;

            if(!name||!email||!username||!pass){
                alert("All fields required");
                return;
            }

            let formData=new FormData();
            formData.append("name",name);
            formData.append("email",email);
            formData.append("username",username);
            formData.append("pwd",pass);
            formData.append("blood",blood);
            formData.append("role",role);
            formData.append("ajax","1");

            // choose endpoint based on selected role
            const endpoint = role === 'donor' ? 'donor/register.inc.php' : 'patient/register.inc.php';

            fetch(endpoint,{
                method:"POST",
                body:formData
            })
            .then(res=>res.json())
            .then(data=>{
                alert(data.message || 'Server response');
                if(data.status==="success"){
                    document.querySelector(".chat-modal").remove();
                }
            })
            .catch(()=>alert("Server error"));
            }

            /* ================================
            INIT
            ================================= */
            window.onload=function(){
            buildTFIDF();
            addMessage("Namaste 🙏 I am your Ragat Chahiyo Assistant. How can I help?","bot-msg");
            };
    </script>

    <footer class="footer" style="background-color:#1abc9c; color: #FFF; padding: 15px; text-align: center; position: absolute; bottom: 0; width: 100%;">
        &copy; 2025 Ragat Chaiyo - Blood Bank Management System
    </footer>
</body>
</html>