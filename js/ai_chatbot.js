/**
 * Nepal Ride Hub - AI Intelligent Assistant
 * A fully standalone AI chatbot for vehicle rental assistance.
 */

(function () {
    // Knowledge Base for the AI
    const KNOWLEDGE_BASE = {
        greetings: {
            patterns: ['hello', 'hi', 'hey', 'greetings', 'namaste'],
            response: "Namaste! I'm your Nepal Ride Hub Assistant. How can I help you explore the beauty of Nepal today?"
        },
        pricing: {
            patterns: ['price', 'cost', 'expensive', 'cheap', 'rate', 'how much', 'rent price'],
            response: "Our rental rates vary by vehicle type: Bikes start from Rs. 1,200/day, Cars from Rs. 5,000/day, and premium SUVs like the Mahindra Thar are Rs. 10,000/day. Check our 'Rent a Car' page for full details!"
        },
        location: {
            patterns: ['location', 'where', 'office', 'kathmandu', 'address'],
            response: "Our main hub is located in the heart of Kathmandu. We offer delivery to major hotels and the airport for your convenience. Where would you like to start your journey?"
        },
        documents: {
            patterns: ['document', 'license', 'permit', 'id', 'citizenship', 'passport', 'need to bring'],
            response: "To rent a vehicle, you'll need: 1) A valid Driving License, 2) Citizenship or Passport (original), and 3) A copy of your ID. You can upload these in your dashboard for pre-verification!"
        },
        booking: {
            patterns: ['book', 'reserve', 'rent', 'how to'],
            response: "Booking is easy! Just browse our fleet, select your dates, and click 'Book Now'. Once confirmed by our admin, your ride will be ready for pickup."
        },
        driver: {
            patterns: ['driver', 'with driver', 'chauffeur', 'self drive'],
            response: "We offer both self-drive options and professional drivers. If you're heading to difficult terrain like Manang or Mustang, we highly recommend taking one of our expert mountain drivers."
        },
        emergency: {
            patterns: ['emergency', 'accident', 'breakdown', 'help', 'stuck', 'police'],
            response: "Stay calm! If you're in an emergency, please use the red 'Emergency' button on our menu or call our 24/7 support line at +977 1-4000000 immediately."
        },
        cancel: {
            patterns: ['cancel', 'refund', 'change date'],
            response: "Cancellations made 24 hours before the pickup attract no charges. For later cancellations, a 1-day rental fee applies. Please check your dashboard to manage bookings."
        },
        travel: {
            patterns: ['visit', 'places', 'tour', 'destination', 'recommend', 'travel', 'trip', 'pokhara', 'mustang', 'chitwan'],
            response: "Nepal offers amazing variety! For adventure (Mustang/Manang), we recommend our Bikes or Mahindra Thar. For scenic beauty (Pokhara/Nagarkot), a comfortable Car is perfect. For wildlife (Chitwan), any of our rental cars will get you there safely!"
        },
        thanks: {
            patterns: ['thank', 'thanks', 'ok', 'okay', 'nice', 'good'],
            response: "You're very welcome! Is there anything else I can help you with today?"
        },
        default: "I'm not quite sure I understand that. Feel free to ask about our vehicle prices, necessary documents, or our office location in Kathmandu!"
    };

    const createChatbotUI = () => {
        // --- STYLES ---
        const style = document.createElement('style');
        style.textContent = `
            #ai-chatbot-container {
                position: fixed; bottom: 30px; right: 30px; z-index: 10000;
                font-family: 'Inter', sans-serif;
            }
            #chatbot-window {
                width: 380px; height: 500px; background: #fff;
                border-radius: 20px; box-shadow: 0 15px 50px rgba(0,0,0,0.15);
                display: none; flex-direction: column; overflow: hidden;
                border: 1px solid rgba(0,0,0,0.05); transform: translateY(20px);
                transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            }
            #chatbot-window.active { display: flex; transform: translateY(0); }
            #chatbot-header {
                background: linear-gradient(135deg, #3561ff, #1a45e4); color: #fff;
                padding: 20px; display: flex; align-items: center; gap: 12px;
            }
            #chatbot-header img { width: 40px; height: 40px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2); }
            #chatbot-messages {
                flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px;
                background: #f8f9fa; scroll-behavior: smooth;
            }
            .chat-msg { max-width: 80%; padding: 12px 16px; border-radius: 15px; font-size: 0.9rem; line-height: 1.4; }
            .msg-bot { background: #fff; color: #333; align-self: flex-start; border-bottom-left-radius: 2px; box-shadow: 0 2px 5px rgba(0,0,0,0.03); }
            .msg-user { background: #3561ff; color: #fff; align-self: flex-end; border-bottom-right-radius: 2px; }
            #chatbot-input-area { padding: 15px; background: #fff; border-top: 1px solid #eee; display: flex; gap: 10px; }
            #chatbot-input {
                flex: 1; border: 1px solid #ddd; border-radius: 50px; padding: 10px 18px;
                outline: none; font-size: 0.9rem; transition: border-color 0.2s;
            }
            #chatbot-input:focus { border-color: #3561ff; }
            #chatbot-send {
                width: 40px; height: 40px; border-radius: 50%; background: #3561ff; color: #fff;
                border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
                transition: transform 0.2s;
            }
            #chatbot-send:hover { transform: scale(1.1); }
            #chatbot-toggle {
                width: 60px; height: 60px; border-radius: 50%; background: #3561ff;
                box-shadow: 0 8px 25px rgba(53, 97, 255, 0.4); border: none;
                cursor: pointer; color: #fff; font-size: 1.5rem; display: flex;
                align-items: center; justify-content: center; transition: all 0.3s;
                margin-left: auto;
            }
            #chatbot-toggle:hover { transform: scale(1.05); }
            .typing-dot { width: 6px; height: 6px; background: #888; border-radius: 50%; display: inline-block; animation: typing 1.4s infinite; }
            .typing-dot:nth-child(2) { animation-delay: 0.2s; }
            .typing-dot:nth-child(3) { animation-delay: 0.4s; }
            @keyframes typing { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
            
            #chatbot-suggestions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
            .suggestion-chip {
                background: #fff; border: 1px solid #3561ff; color: #3561ff;
                padding: 6px 12px; border-radius: 20px; font-size: 0.8rem;
                cursor: pointer; transition: all 0.2s; white-space: nowrap;
            }
            .suggestion-chip:hover { background: #3561ff; color: #fff; }
        `;
        document.head.appendChild(style);

        // --- DOM ELEMENTS ---
        const container = document.createElement('div');
        container.id = 'ai-chatbot-container';

        container.innerHTML = `
            <div id="chatbot-window">
                <div id="chatbot-header">
                    <img src="https://ui-avatars.com/api/?name=Assistant&background=fff&color=3561ff" alt="AI">
                    <div>
                        <div style="font-weight: 700; font-size: 1rem;">Nepal Ride Hub AI</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Online | Ready to Help</div>
                    </div>
                </div>
                <div id="chatbot-messages"></div>
                <div id="chatbot-input-area">
                    <input type="text" id="chatbot-input" placeholder="Ask about car rentals...">
                    <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
            <button id="chatbot-toggle"><i class="fas fa-comment-dots"></i></button>
        `;

        document.body.appendChild(container);

        // --- LOGIC ---
        const windowEl = document.getElementById('chatbot-window');
        const toggleBtn = document.getElementById('chatbot-toggle');
        const inputEl = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send');
        const messagesEl = document.getElementById('chatbot-messages');

        const addMessage = (text, isUser = false) => {
            const msg = document.createElement('div');
            msg.className = `chat-msg ${isUser ? 'msg-user' : 'msg-bot'}`;
            msg.textContent = text;
            messagesEl.appendChild(msg);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        };

        const showSuggestions = () => {
            const suggestions = [
                "What are your rental prices?",
                "Where is your office?",
                "What documents are required?",
                "How do I book a vehicle?",
                "Do you provide drivers?",
                "Emergency help!",
                "Best places to visit in Nepal?"
            ];
            const container = document.createElement('div');
            container.id = 'chatbot-suggestions';
            suggestions.forEach(text => {
                const chip = document.createElement('div');
                chip.className = 'suggestion-chip';
                chip.textContent = text;
                chip.onclick = () => {
                    inputEl.value = text;
                    handleSend();
                    container.remove(); // Remove suggestions after selection
                };
                container.appendChild(chip);
            });
            messagesEl.appendChild(container);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        };

        const showTyping = () => {
            const typing = document.createElement('div');
            typing.id = 'chatbot-typing';
            typing.className = 'chat-msg msg-bot';
            typing.innerHTML = '<span class="typing-dot"></span> <span class="typing-dot"></span> <span class="typing-dot"></span>';
            messagesEl.appendChild(typing);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            return typing;
        };

        const getFallbackResponse = (input) => {
            const lowerInput = input.toLowerCase();
            for (const key in KNOWLEDGE_BASE) {
                if (KNOWLEDGE_BASE[key].patterns.some(p => lowerInput.includes(p))) {
                    return KNOWLEDGE_BASE[key].response;
                }
            }
            return KNOWLEDGE_BASE.default;
        };

        let discoveredModel = null;

        const getAIResponse = async (input) => {
            const API_KEY = 'AIzaSyA5zIYyS3v0aeEHiofr-fdXIgZ8hy90xV4';

            try {
                // 1. Discover a valid model across v1 and v1beta
                if (!discoveredModel) {
                    for (const ver of ['v1', 'v1beta']) {
                        try {
                            const listResponse = await fetch(`https://generativelanguage.googleapis.com/${ver}/models?key=${API_KEY.trim()}`);
                            if (listResponse.ok) {
                                const listData = await listResponse.json();
                                const found = listData.models?.find(m => m.supportedGenerationMethods.includes('generateContent'));
                                if (found) {
                                    discoveredModel = { ver, path: found.name };
                                    break;
                                }
                            }
                        } catch (e) { }
                    }
                }

                // fallback to a standard guess if discovery fails
                const apiVer = discoveredModel?.ver || 'v1beta';
                const modelPath = discoveredModel?.path || 'models/gemini-1.5-flash';

                // 2. Make the actual request
                const response = await fetch(`https://generativelanguage.googleapis.com/${apiVer}/${modelPath}:generateContent?key=${API_KEY.trim()}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contents: [{
                            parts: [{
                                text: `You are the expert ChatGPT-style AI Assistant for Nepal Ride Hub. 
                                Answer ANY question helpfully and smartly. 
                                For travel advice in Nepal:
                                - Recommend Mustang/Manang for off-road adventure (suggest our Bikes or Mahindra Thar).
                                - Recommend Pokhara, Chitwan, or Nagarkot for comfort/scenery (suggest our Cars).
                                Provide long, detailed, and professional responses.
                                Current user question: ${input}`
                            }]
                        }]
                    })
                });

                if (!response.ok) {
                    const err = await response.json().catch(() => ({}));
                    console.warn(`AI API Warning: ${response.status} - ${err.error?.message || 'Quota/Limit issue'}`);
                    return getFallbackResponse(input);
                }

                const data = await response.json();
                return data.candidates?.[0]?.content?.parts?.[0]?.text || getFallbackResponse(input);
            } catch (error) {
                console.error("AI Connection Error:", error.message);
                return getFallbackResponse(input);
            }
        };

        const handleSend = async () => {
            const text = inputEl.value.trim();
            if (!text) return;

            addMessage(text, true);
            inputEl.value = '';

            const typingEl = showTyping();

            try {
                const aiResponse = await getAIResponse(text);
                addMessage(aiResponse);
            } catch (err) {
                addMessage(getFallbackResponse(text));
            } finally {
                if (typingEl && typingEl.parentNode) typingEl.remove();
            }
        };

        toggleBtn.onclick = () => {
            windowEl.classList.toggle('active');
            if (windowEl.classList.contains('active')) {
                if (messagesEl.children.length === 0) {
                    addMessage("Hello! I'm your Nepal Ride Hub Assistant. How can I help you today?");
                    setTimeout(showSuggestions, 500);
                }
                inputEl.focus();
            }
        };

        sendBtn.onclick = handleSend;
        inputEl.onkeypress = (e) => { if (e.key === 'Enter') handleSend(); };
    };

    // Init when document is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createChatbotUI);
    } else {
        createChatbotUI();
    }
})();