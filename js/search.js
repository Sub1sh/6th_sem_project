// Fuzzy search function (tolerant to spelling mistakes)
function fuzzySearch(term, text) {
    term = term.toLowerCase();
    text = text.toLowerCase();
    
    // Exact match bonus
    if (text.includes(term)) return 100;
    
    // Check for close matches with Levenshtein-like scoring
    let score = 0;
    let termIndex = 0;
    
    for (let i = 0; i < text.length && termIndex < term.length; i++) {
        if (text[i] === term[termIndex]) {
            score++;
            termIndex++;
        }
    }
    
    // Return percentage match
    return (score / term.length) * 100;
}

// Get suggestions for search
function getSuggestions(searchTerm) {
    if (searchTerm.length < 2) return [];
    
    const suggestions = [];
    const searchLower = searchTerm.toLowerCase();
    
    // Common misspellings and corrections
    const corrections = {
        'toyota': ['toyota', 'toyotta', 'toyata', 'toyoto'],
        'honda': ['honda', 'hnda', 'hond', 'hnda'],
        'suv': ['suv', 's.u.v', 's u v', 'esuv'],
        'sedan': ['sedan', 'seden', 'sedon', 'seddan'],
        'truck': ['truck', 'truk', 'truk', 'truckk'],
        'bus': ['bus', 'buss', 'bas', 'bhus'],
        'van': ['van', 'vann', 'vn', 'vhan'],
        'bmw': ['bmw', 'b.m.w', 'b m w', 'bmv'],
        'mercedes': ['mercedes', 'mercedez', 'mercades', 'mersedes'],
        'electric': ['electric', 'electrik', 'elektric', 'elecric'],
        'automatic': ['automatic', 'automatik', 'otomatic', 'automatc'],
        'manual': ['manual', 'manul', 'manuall', 'manua']
    };
    
    // Check each correction
    for (const [correct, variants] of Object.entries(corrections)) {
        if (variants.includes(searchLower)) {
            suggestions.push(correct);
        }
    }
    
    return suggestions;
}

// Improved search function with fuzzy matching
function performSearch() {
    const searchTerm = $('#searchInput').val().trim();
    const filter = $('.filter-tag.active').data('filter') || 'all';
    
    if (searchTerm.length === 0 && filter === 'all') {
        loadAllVehicles();
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'search_backend.php',
        method: 'GET',
        data: { q: searchTerm, filter: filter },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            
            if (response.success && response.vehicles.length > 0) {
                displayResults(response.vehicles, searchTerm);
            } else {
                // Try fuzzy search if no direct results
                if (searchTerm.length > 0) {
                    tryFuzzySearch(searchTerm, filter);
                } else {
                    displayNoResults(searchTerm);
                }
            }
        },
        error: function() {
            hideLoading();
            displayNoResults(searchTerm);
        }
    });
}

// Try fuzzy search if direct search fails
function tryFuzzySearch(searchTerm, filter) {
    $.ajax({
        url: 'search_backend.php',
        method: 'GET',
        data: { q: '', filter: filter },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const fuzzyResults = [];
                const searchLower = searchTerm.toLowerCase();
                
                response.vehicles.forEach(vehicle => {
                    // Calculate match score
                    const searchFields = [
                        vehicle.vehicle_name,
                        vehicle.brand,
                        vehicle.type,
                        vehicle.description,
                        vehicle.tags,
                        vehicle.fuel_type,
                        vehicle.transmission
                    ].join(' ').toLowerCase();
                    
                    // Check for partial matches
                    let score = 0;
                    
                    // Check each word in search term
                    const searchWords = searchLower.split(' ');
                    searchWords.forEach(word => {
                        if (word.length > 2) {
                            if (searchFields.includes(word)) {
                                score += 100;
                            } else {
                                // Check for similar words
                                for (const fieldWord of searchFields.split(' ')) {
                                    if (fieldWord.length > 2 && 
                                        (fieldWord.includes(word) || word.includes(fieldWord) || 
                                         levenshteinDistance(fieldWord, word) <= 2)) {
                                        score += 70;
                                        break;
                                    }
                                }
                            }
                        }
                    });
                    
                    if (score > 0) {
                        vehicle.matchScore = score;
                        fuzzyResults.push(vehicle);
                    }
                });
                
                // Sort by match score
                fuzzyResults.sort((a, b) => b.matchScore - a.matchScore);
                
                if (fuzzyResults.length > 0) {
                    displayResults(fuzzyResults.slice(0, 10), searchTerm);
                    $('#resultsTitle').html(`Showing similar results for "<span class="text-primary">${searchTerm}</span>"`);
                } else {
                    displayNoResults(searchTerm);
                }
            }
        }
    });
}

// Levenshtein distance for fuzzy matching
function levenshteinDistance(a, b) {
    const matrix = [];
    
    for (let i = 0; i <= b.length; i++) {
        matrix[i] = [i];
    }
    
    for (let j = 0; j <= a.length; j++) {
        matrix[0][j] = j;
    }
    
    for (let i = 1; i <= b.length; i++) {
        for (let j = 1; j <= a.length; j++) {
            if (b.charAt(i - 1) === a.charAt(j - 1)) {
                matrix[i][j] = matrix[i - 1][j - 1];
            } else {
                matrix[i][j] = Math.min(
                    matrix[i - 1][j - 1] + 1,
                    matrix[i][j - 1] + 1,
                    matrix[i - 1][j] + 1
                );
            }
        }
    }
    
    return matrix[b.length][a.length];
}