(function($) {
/* elRTE 'custom' toolbar */
elRTE.prototype.options.panels.style_custom = [
        'bold', 'italic', 'underline'
];
elRTE.prototype.options.panels.format_custom = [
        'formatblock', 'fontsize'
];
elRTE.prototype.options.panels.links_custom = [
    'link', 'unlink'
];
elRTE.prototype.options.panels.elements_custom = [
    'blockquote', 'div', 'stopfloat', 'css', 'nbsp', 'pagebreak'
];

elRTE.prototype.options.toolbars.custom = [
		'save', 'copypaste', 'style_custom', 'alignment',  'lists', 'indent', 'links_custom', 'eol', 
		'format_custom', 'colors', 'media', 'elements_custom', 'elfinder'];

})(jQuery);