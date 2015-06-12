// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation({
    abide: {
        patterns: {
            surnames: /^([a-zA-ZáÁéÉíÍóÓúÚ+\s+])+$/,
            tlf: /^(\d{9})$/
        }
    }
});