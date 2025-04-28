// main.js - Hauptskript für die Sprachapp

document.addEventListener('DOMContentLoaded', function() {
    // Karteikarten-Funktionalität
    initFlashcards();
    
    // Audio-Funktionalität
    initAudio();
    
    // Mini-Test-Funktionalität
    initMiniTest();
    
    // Quiz-Funktionalität (Kahoot-Style)
    initQuiz();
    
    // Sprach-Switch
    initLanguageSwitch();
    
    // Favoriten-Funktionalität
    initFavorites();
});

// Karteikarten umdrehen
function initFlashcards() {
    const flashcards = document.querySelectorAll('.flashcard');
    
    flashcards.forEach(card => {
        card.addEventListener('click', function() {
            this.classList.toggle('flipped');
        });
    });
    
    // Richtig/Falsch Buttons
    const answerButtons = document.querySelectorAll('.answer-btn');
    answerButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Verhindert das Umdrehen der Karte
            
            const cardId = this.closest('.flashcard').dataset.cardId;
            const isCorrect = this.dataset.answer === 'correct';
            
            // AJAX-Anfrage zum Speichern des Ergebnisses
            updateCardProgress(cardId, isCorrect);
            
            // UI-Feedback
            const flashcard = this.closest('.flashcard');
            
            if (isCorrect) {
                flashcard.classList.add('answer-correct');
                setTimeout(() => {
                    flashcard.classList.remove('answer-correct');
                    // Karte entfernen oder zum nächsten gehen
                    nextCard(flashcard);
                }, 1000);
            } else {
                flashcard.classList.add('answer-wrong');
                setTimeout(() => {
                    flashcard.classList.remove('answer-wrong');
                    flashcard.classList.remove('flipped');
                }, 1000);
            }
        });
    });
}

// Zum nächsten Karteikarte gehen
function nextCard(currentCard) {
    const cardContainer = currentCard.parentElement;
    const nextCard = currentCard.nextElementSibling;
    
    currentCard.style.display = 'none';
    
    if (nextCard && nextCard.classList.contains('flashcard')) {
        nextCard.scrollIntoView({ behavior: 'smooth' });
    } else {
        // Keine weiteren Karten
        const completionMessage = document.createElement('div');
        completionMessage.className = 'alert alert-success mt-4';
        completionMessage.textContent = 'Gratulation! Du hast alle Karteikarten in diesem Set bearbeitet.';
        
        const reloadButton = document.createElement('button');
        reloadButton.className = 'btn btn-primary mt-3';
        reloadButton.textContent = 'Nochmal üben';
        reloadButton.addEventListener('click', () => window.location.reload());
        
        completionMessage.appendChild(document.createElement('br'));
        completionMessage.appendChild(reloadButton);
        
        cardContainer.appendChild(completionMessage);
        completionMessage.scrollIntoView({ behavior: 'smooth' });
    }
}

// AJAX-Anfrage zum Aktualisieren des Kartenfortschritts
function updateCardProgress(cardId, isCorrect) {
    $.ajax({
        url: 'php/user/update_progress.php',
        type: 'POST',
        data: {
            card_id: cardId,
            correct: isCorrect ? 1 : 0
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                // Aktualisieren des Fortschrittsbalkens, falls vorhanden
                updateProgressBar(data.total_correct, data.total_cards);
            }
        },
        error: function() {
            console.error('Fehler beim Aktualisieren des Fortschritts');
        }
    });
}

// Fortschrittsbalken aktualisieren
function updateProgressBar(correct, total) {
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        const percentage = Math.round((correct / total) * 100);
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
        progressBar.textContent = percentage + '%';
    }
}

// Audio-Funktionalität
function initAudio() {
    const audioButtons = document.querySelectorAll('.audio-btn');
    
    audioButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Verhindert das Umdrehen der Karte
            
            const audioUrl = this.dataset.audio;
            if (audioUrl) {
                const audio = new Audio(audioUrl);
                audio.play();
            }
        });
    });
}

// Mini-Test-Funktionalität
function initMiniTest() {
    const miniTestForm = document.getElementById('mini-test-form');
    
    if (miniTestForm) {
        miniTestForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: 'php/user/check_mini_test.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const data = JSON.parse(response);
                    
                    // Ergebnisse anzeigen
                    const resultContainer = document.getElementById('test-results');
                    resultContainer.innerHTML = '';
                    
                    data.results.forEach(result => {
                        const resultElement = document.createElement('div');
                        resultElement.className = 'alert ' + (result.correct ? 'alert-success' : 'alert-danger');
                        
                        const wordElement = document.createElement('div');
                        wordElement.className = 'fw-bold';
                        wordElement.textContent = result.question;
                        
                        const answerElement = document.createElement('div');
                        if (result.correct) {
                            answerElement.className = 'result-correct';
                            answerElement.textContent = 'Richtig: ' + result.correct_answer;
                        } else {
                            answerElement.className = 'result-wrong';
                            answerElement.textContent = 'Falsch: Deine Antwort war "' + result.user_answer + 
                                                       '", richtig wäre "' + result.correct_answer + '"';
                        }
                        
                        resultElement.appendChild(wordElement);
                        resultElement.appendChild(answerElement);
                        resultContainer.appendChild(resultElement);
                    });
                    
                    // Gesamtergebnis anzeigen
                    const summaryElement = document.createElement('div');
                    summaryElement.className = 'alert alert-info';
                    summaryElement.innerHTML = `<strong>Ergebnis:</strong> ${data.correct_count} von ${data.total} richtig (${Math.round((data.correct_count / data.total) * 100)}%)`;
                    
                    resultContainer.appendChild(summaryElement);
                    
                    // Zum Ergebnis scrollen
                    resultContainer.scrollIntoView({ behavior: 'smooth' });
                },
                error: function() {
                    console.error('Fehler beim Überprüfen des Tests');
                }
            });
        });
    }
}

// Quiz-Funktionalität (Kahoot-Style)
function initQuiz() {
    const quizOptions = document.querySelectorAll('.quiz-option');
    
    quizOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Alle anderen Optionen deselektieren
            quizOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Diese Option selektieren
            this.classList.add('selected');
            
            // Antwort speichern
            const quizForm = this.closest('form');
            const hiddenInput = quizForm.querySelector('input[type="hidden"]');
            hiddenInput.value = this.dataset.value;
            
            // Automatisch nach kurzer Verzögerung absenden
            setTimeout(() => {
                const submitButton = quizForm.querySelector('button[type="submit"]');
                submitButton.click();
            }, 500);
        });
    });
}

// Sprach-Switch-Funktionalität
function initLanguageSwitch() {
    const languageSwitch = document.getElementById('language-switch');
    
    if (languageSwitch) {
        languageSwitch.addEventListener('change', function() {
            // Formular erstellen und absenden, um die Sprachrichtung zu ändern
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href;
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'direction';
            input.value = this.checked ? 'en_de' : 'de_en';
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        });
    }
}

// Favoriten-Funktionalität
function initFavorites() {
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Verhindert das Umdrehen der Karte bei Karteikarten
            
            const itemId = this.dataset.id;
            const itemType = this.dataset.type; // 'card' oder 'unit'
            const isFavorite = this.classList.contains('active');
            
            // AJAX-Anfrage zum Aktualisieren des Favoriten-Status
            $.ajax({
                url: 'php/user/update_favorite.php',
                type: 'POST',
                data: {
                    id: itemId,
                    type: itemType,
                    favorite: isFavorite ? 0 : 1
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        // UI aktualisieren
                        button.classList.toggle('active');
                        const icon = button.querySelector('i');
                        icon.className = button.classList.contains('active') ? 
                            'bi bi-star-fill' : 'bi bi-star';
                    }
                },
                error: function() {
                    console.error('Fehler beim Aktualisieren des Favoriten-Status');
                }
            });
        });
    });
}