@extends('layouts.student')

@section('content')
<div class="row h-100">
    <div class="col-md-12 h-100 d-flex flex-column">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <span class="text-primary me-2"><i class="bi bi-robot"></i></span>Biology AI Tutor
                </h2>
                <p class="text-muted mb-0">Ask any question related to A/L Biology.</p>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="card border-0 shadow-sm flex-grow-1 rounded-4 overflow-hidden" style="background: #f8f9fa;">
            <div class="card-body p-0 d-flex flex-column h-100">
                
                <!-- Messages Area -->
                <div id="chatMessages" class="flex-grow-1 p-4 overflow-auto" style="scroll-behavior: smooth;">
                    <!-- Intro Message -->
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-robot"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="bg-white p-3 rounded-4 shadow-sm" style="border-top-left-radius: 0 !important; max-width: 80%;">
                                <p class="mb-0">Hello !! චාමර ලක්මාල් සර්ගේ biology පන්තියට සාදරයෙන් පිලිගමු ! 
මම ඔබගේ ජීව විද්‍යා විශයේ සගයා වන අතර මාගේ දැනුමෙන් ඔබට උදව් කල හැක</p>
                            </div>
                            <small class="text-muted ms-1">Just now</small>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-3 bg-white border-top">
                    <form id="chatForm" class="d-flex gap-2">
                        <input type="text" id="userQuestion" class="form-control form-control-lg border-0 bg-light rounded-pill px-4" placeholder="Type your biology question here..." autocomplete="off">
                        <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; min-width: 50px;">
                            <i class="bi bi-send-fill fs-5"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatForm = document.getElementById('chatForm');
        const chatMessages = document.getElementById('chatMessages');
        const userInput = document.getElementById('userQuestion');

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const question = userInput.value.trim();
            if (!question) return;

            // 1. Append User Message
            appendMessage('user', question);
            userInput.value = '';
            
            // 2. Show Loading Bubble
            const loadingId = appendLoading();
            scrollToBottom();

            try {
                // 3. API Call
                const response = await fetch("{{ route('student.biology-ai.ask') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ question: question })
                });

                const data = await response.json();

                // 4. Remove Loading & Show Answer
                removeMessage(loadingId);

                if (response.ok) {
                    appendMessage('ai', data.answer);
                } else {
                    appendMessage('ai', "Sorry, I encountered an error: " + (data.error || 'Unknown error'));
                }

            } catch (err) {
                removeMessage(loadingId);
                appendMessage('ai', "Network error. Please try again.");
                console.error(err);
            }

            scrollToBottom();
        });

        function appendMessage(role, text) {
            const isUser = role === 'user';
            const wrapper = document.createElement('div');
            wrapper.className = `d-flex mb-3 ${isUser ? 'justify-content-end' : ''}`;

            let avatar = '';
            if (!isUser) {
                avatar = `
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-robot"></i>
                        </div>
                    </div>`;
            } else {
                avatar = `
                     <div class="flex-shrink-0">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </div>`;
            }

            const content = `
                <div class="${isUser ? 'flex-grow-1 me-3 text-end' : 'flex-grow-1 ms-3'}">
                    <div class="${isUser ? 'bg-primary text-white' : 'bg-white text-dark'} p-3 rounded-4 shadow-sm" 
                         style="${isUser ? 'border-top-right-radius: 0 !important;' : 'border-top-left-radius: 0 !important;'} max-width: ${isUser ? '100%' : '80%'}; d-inline-block; text-align: left;">
                        <p class="mb-0" style="white-space: pre-wrap;">${text}</p>
                    </div>
                </div>`;

            wrapper.innerHTML = isUser ? content + avatar : avatar + content;
            chatMessages.appendChild(wrapper);
        }

        function appendLoading() {
            const id = 'loading-' + Date.now();
            const wrapper = document.createElement('div');
            wrapper.className = 'd-flex mb-3';
            wrapper.id = id;
            wrapper.innerHTML = `
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-robot"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="bg-white p-3 rounded-4 shadow-sm" style="border-top-left-radius: 0 !important; width: fit-content;">
                        <div class="typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>`;
            chatMessages.appendChild(wrapper);
            return id;
        }

        function removeMessage(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>

<style>
    /* Typing Indicator Animation */
    .typing-indicator span {
        display: inline-block;
        width: 6px;
        height: 6px;
        background-color: #aaa;
        border-radius: 50%;
        margin: 0 2px;
        animation: typing 1s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
</style>
@endsection
