<?php
// Include your database connection to bbms
include 'includes/db.php'; 

function tokenize($text) {
    // List of common words to ignore (stop words)
    $stop_words = ['is', 'the', 'how', 'do', 'i', 'to', 'a', 'can', 'what', 'are', 'of', 'for', 'in'];
    
    // Clean string: lowercase and extract words
    $words = str_word_count(strtolower($text), 1);
    
    // Return only the meaningful keywords (TF-IDF logic)
    return array_diff($words, $stop_words);
}

// Get the message from your chatbot frontend
if(isset($_POST['text'])) {
    $user_input = $_POST['text'];
    $user_tokens = tokenize($user_input);
    
    // Continue with the TF-IDF best-match algorithm against the 'chatbot' table...
}
?>