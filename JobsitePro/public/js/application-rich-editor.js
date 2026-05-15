/**
 * Application Rich Text Editor
 * Provides formatting tools for application messages
 */

class ApplicationRichEditor {
    constructor(textareaId, toolbarId) {
        this.textarea = document.getElementById(textareaId);
        this.toolbar = document.getElementById(toolbarId);
        this.init();
    }

    init() {
        if (!this.textarea) {
            console.error('Textarea not found');
            return;
        }

        this.createToolbar();
        this.attachListeners();
    }

    createToolbar() {
        const toolbar = document.createElement('div');
        toolbar.id = 'message_editor';
        toolbar.style.cssText = `
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
            border: 1px solid #ddd;
            flex-wrap: wrap;
        `;

        const buttons = [
            { id: 'bold', title: 'Bold (Ctrl+B)', html: '<strong>B</strong>', action: () => this.wrapText('**', '**') },
            { id: 'italic', title: 'Italic (Ctrl+I)', html: '<em>I</em>', action: () => this.wrapText('*', '*') },
            { id: 'underline', title: 'Underline', html: '<u>U</u>', action: () => this.wrapText('<u>', '</u>') },
            { id: 'code', title: 'Code', html: '{ }', action: () => this.wrapText('`', '`') },
            { id: 'hr', title: 'Separator', html: '---', action: () => this.insertText('\n---\n') },
            { id: 'bullet_list', title: 'Bullet List', html: '• List', action: () => this.insertList('bullet') },
            { id: 'number_list', title: 'Number List', html: '1. List', action: () => this.insertList('number') },
            { id: 'clear', title: 'Clear Formatting', html: 'Clear', action: () => this.clearFormatting() }
        ];

        buttons.forEach(btn => {
            const button = document.createElement('button');
            button.id = btn.id;
            button.type = 'button';
            button.title = btn.title;
            button.innerHTML = btn.html;
            button.style.cssText = `
                padding: 8px 12px;
                background: white;
                border: 1px solid #ddd;
                border-radius: 4px;
                cursor: pointer;
                font-weight: bold;
                transition: all 0.2s;
            `;

            button.addEventListener('click', (e) => {
                e.preventDefault();
                btn.action();
                this.textarea.focus();
            });

            button.addEventListener('mouseover', () => {
                button.style.background = '#007bff';
                button.style.color = 'white';
                button.style.borderColor = '#0056b3';
            });

            button.addEventListener('mouseout', () => {
                button.style.background = 'white';
                button.style.color = 'black';
                button.style.borderColor = '#ddd';
            });

            toolbar.appendChild(button);
        });

        this.textarea.parentNode.insertBefore(toolbar, this.textarea);
    }

    wrapText(before, after) {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        const text = this.textarea.value;
        const selected = text.substring(start, end);
        const replacement = before + selected + after;

        this.textarea.value = text.substring(0, start) + replacement + text.substring(end);
        this.textarea.selectionStart = start + before.length;
        this.textarea.selectionEnd = start + before.length + selected.length;
    }

    insertText(text) {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        const value = this.textarea.value;

        this.textarea.value = value.substring(0, start) + text + value.substring(end);
        this.textarea.selectionStart = this.textarea.selectionEnd = start + text.length;
    }

    insertList(type) {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        const value = this.textarea.value;
        const before = value.substring(0, start);
        const selected = value.substring(start, end);
        const after = value.substring(end);

        let list = '';
        if (type === 'bullet') {
            list = '• Item 1\n• Item 2\n• Item 3';
        } else {
            list = '1. Item 1\n2. Item 2\n3. Item 3';
        }

        this.textarea.value = before + list + after;
        this.textarea.selectionStart = start;
        this.textarea.selectionEnd = start + list.length;
    }

    clearFormatting() {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        const text = this.textarea.value;
        const selected = text.substring(start, end);

        // Remove markdown formatting
        let cleaned = selected
            .replace(/\*\*(.*?)\*\*/g, '$1')
            .replace(/\*(.*?)\*/g, '$1')
            .replace(/<u>(.*?)<\/u>/g, '$1')
            .replace(/`(.*?)`/g, '$1')
            .replace(/^[\*\-\+]\s/gm, '')
            .replace(/^\d+\.\s/gm, '');

        this.textarea.value = text.substring(0, start) + cleaned + text.substring(end);
    }

    getFormattedText() {
        return this.textarea.value;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('application_message')) {
        new ApplicationRichEditor('application_message', 'message_editor');
    }
});
