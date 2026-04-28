import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.appLayout = function () {
    return {
        sidebarOpen: false,
        isDarkMode: false,

        init() {
            const storedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            this.isDarkMode = storedTheme
                ? storedTheme === 'dark'
                : systemPrefersDark;

            this.applyTheme();
        },

        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
            this.applyTheme();
        },

        applyTheme() {
            document.documentElement.classList.toggle('dark', this.isDarkMode);
        }
    };
};

window.registerPage = function () {
    return {
        isDarkMode: false,
        password: '',
        passwordConfirmation: '',
        strengthText: '弱',
        strengthTextClass: 'text-red-600 dark:text-red-400',
        strengthBarClass: 'bg-red-500',
        strengthWidth: '20%',

        init() {
            const storedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.isDarkMode = storedTheme ? storedTheme === 'dark' : systemPrefersDark;
            document.documentElement.classList.toggle('dark', this.isDarkMode);
            this.updateStrength();
        },

        get passwordsMatch() {
            return this.password === this.passwordConfirmation;
        },

        updateStrength() {
            let score = 0;

            if (this.password.length >= 8) score++;
            if (/[A-Z]/.test(this.password)) score++;
            if (/[a-z]/.test(this.password)) score++;
            if (/[0-9]/.test(this.password)) score++;
            if (/[^A-Za-z0-9]/.test(this.password)) score++;

            if (score <= 2) {
                this.strengthText = '弱';
                this.strengthTextClass = 'text-red-600 dark:text-red-400';
                this.strengthBarClass = 'bg-red-500';
                this.strengthWidth = '25%';
            } else if (score === 3 || score === 4) {
                this.strengthText = '中';
                this.strengthTextClass = 'text-amber-600 dark:text-amber-400';
                this.strengthBarClass = 'bg-amber-500';
                this.strengthWidth = '65%';
            } else {
                this.strengthText = '强';
                this.strengthTextClass = 'text-emerald-600 dark:text-emerald-400';
                this.strengthBarClass = 'bg-emerald-500';
                this.strengthWidth = '100%';
            }
        }
    };
};

window.toastCenter = function () {
    return {
        toasts: [],

        init() {
            if (window.__toastCenterBound) {
                return;
            }

            window.__toastCenterBound = true;

            window.addEventListener('app-toast', (event) => {
                const detail = event.detail || {};
                this.push(detail.message || '操作完成', detail.type || 'success');
            });
        },

        push(message, type = 'success') {
            const id = Date.now() + Math.random();

            const toast = {
                id,
                message,
                type,
                visible: true,
            };

            this.toasts.push(toast);

            setTimeout(() => {
                const target = this.toasts.find(item => item.id === id);
                if (target) {
                    target.visible = false;
                }

                setTimeout(() => {
                    this.toasts = this.toasts.filter(item => item.id !== id);
                }, 180);
            }, 2200);
        }
    };
};

window.pushToast = function (message, type = 'success') {
    window.dispatchEvent(new CustomEvent('app-toast', {
        detail: { message, type }
    }));
};

window.copyTextWithToast = async function (text, successMessage = '复制成功。') {
    try {
        await navigator.clipboard.writeText(text);
        window.pushToast(successMessage, 'success');
    } catch (error) {
        window.pushToast('复制失败，请稍后重试。', 'error');
    }
};

window.eventRecordForm = function () {
    return {
        inlineFiles: {
            process: [],
            result: [],
        },
        savedRanges: {
            process: null,
            result: null,
        },

        init() {
            this.$nextTick(() => {
                ['process', 'result'].forEach((context) => {
                    const editor = this.editorFor(context);

                    if (editor) {
                        this.refreshContent(context);
                    }
                });
            });
        },

        editorFor(context) {
            return this.$refs[`${context}Editor`];
        },

        hiddenInputFor(context) {
            return this.$refs[`${context}Input`];
        },

        keysInputFor(context) {
            return this.$refs[`${context}ImageKeys`];
        },

        inputFor(context) {
            return this.$refs[`${context}Images`];
        },

        prepareSubmit() {
            ['process', 'result'].forEach((context) => this.refreshContent(context));
        },

        rememberSelection(context) {
            const editor = this.editorFor(context);
            const selection = window.getSelection();

            if (!editor || !selection || selection.rangeCount === 0) {
                return;
            }

            const range = selection.getRangeAt(0);
            if (editor.contains(range.commonAncestorContainer)) {
                this.savedRanges[context] = range.cloneRange();
            }
        },

        handlePaste(event, context) {
            const clipboard = event.clipboardData;
            const clipboardFiles = Array.from(clipboard?.files || [])
                .filter(file => file.type?.startsWith('image/'));
            const itemFiles = Array.from(clipboard?.items || [])
                .filter(item => item.kind === 'file' && item.type?.startsWith('image/'))
                .map(item => item.getAsFile())
                .filter(Boolean);
            const files = clipboardFiles.length > 0 ? clipboardFiles : itemFiles;

            if (files.length > 0) {
                event.preventDefault();
                this.addInlineFiles(context, files);
                return;
            }

            const text = this.plainTextFromClipboard(clipboard);

            if (text === '') {
                if (Array.from(clipboard?.types || []).includes('text/html')) {
                    event.preventDefault();
                }

                return;
            }

            event.preventDefault();
            this.insertPlainTextAtSelection(context, text);
            this.refreshContent(context);
        },

        plainTextFromClipboard(clipboard) {
            if (!clipboard) {
                return '';
            }

            const plainText = clipboard.getData('text/plain');
            if (plainText !== '') {
                return plainText;
            }

            const html = clipboard.getData('text/html');
            if (html === '') {
                return '';
            }

            const documentFromHtml = new DOMParser().parseFromString(html, 'text/html');

            return documentFromHtml.body?.innerText || documentFromHtml.body?.textContent || '';
        },

        insertPlainTextAtSelection(context, text) {
            const editor = this.editorFor(context);

            if (!editor) {
                return;
            }

            editor.focus();
            this.restoreSelection(context);

            try {
                if (typeof document.execCommand === 'function' && document.execCommand('insertText', false, text)) {
                    this.rememberSelection(context);
                    return;
                }
            } catch (error) {
                // Fall through to the Range-based insertion for browsers that disable execCommand.
            }

            this.insertTextWithRange(editor, text);
            this.rememberSelection(context);
        },

        restoreSelection(context) {
            const editor = this.editorFor(context);
            const selection = window.getSelection();
            const savedRange = this.savedRanges[context];

            if (!editor || !selection || !savedRange || !editor.contains(savedRange.commonAncestorContainer)) {
                return;
            }

            selection.removeAllRanges();
            selection.addRange(savedRange);
        },

        insertTextWithRange(editor, text) {
            const selection = window.getSelection();

            if (!selection || selection.rangeCount === 0) {
                editor.append(document.createTextNode(text));
                return;
            }

            const range = selection.getRangeAt(0);
            if (!editor.contains(range.commonAncestorContainer)) {
                editor.append(document.createTextNode(text));
                return;
            }

            const fragment = document.createDocumentFragment();
            const lines = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n').split('\n');

            lines.forEach((line, index) => {
                if (index > 0) {
                    fragment.append(document.createElement('br'));
                }

                if (line !== '') {
                    fragment.append(document.createTextNode(line));
                }
            });

            const lastNode = fragment.lastChild;

            range.deleteContents();
            range.insertNode(fragment);

            if (!lastNode) {
                return;
            }

            const caretRange = document.createRange();
            caretRange.setStartAfter(lastNode);
            caretRange.collapse(true);
            selection.removeAllRanges();
            selection.addRange(caretRange);
        },

        insertSelectedImages(context) {
            const input = this.inputFor(context);
            const files = Array.from(input?.files || [])
                .filter(file => file.type?.startsWith('image/'));

            if (files.length === 0) {
                return;
            }

            this.addInlineFiles(context, files);
        },

        addInlineFiles(context, files) {
            const input = this.inputFor(context);

            if (!input || typeof DataTransfer === 'undefined') {
                window.pushToast('当前浏览器不支持插入图片。', 'error');
                return;
            }

            Array.from(files)
                .filter(file => file.type?.startsWith('image/'))
                .forEach((file, index) => {
                    const item = this.prepareInlineFile(context, file, index);
                    this.inlineFiles[context].push(item);
                    this.insertInlineImage(context, item);
                });

            this.syncFileInput(context);
            this.refreshContent(context);
        },

        prepareInlineFile(context, file, index) {
            const extension = file.type?.split('/')[1] || 'png';
            const key = `inline-${context}-${Date.now()}-${Math.random().toString(36).slice(2, 10)}-${index}`;
            const filename = file.name && file.name !== 'image.png'
                ? file.name
                : `${key}.${extension}`;

            return {
                key,
                file: new File([file], filename, {
                    type: file.type || 'image/png',
                    lastModified: file.lastModified || Date.now(),
                }),
                url: URL.createObjectURL(file),
            };
        },

        insertInlineImage(context, item) {
            const editor = this.editorFor(context);

            if (!editor) {
                return;
            }

            const figure = document.createElement('figure');
            figure.dataset.inlineImage = '1';
            figure.contentEditable = 'false';
            figure.className = 'my-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950';

            const link = document.createElement('a');
            link.href = item.url;
            link.target = '_blank';
            link.className = 'inline-block max-w-full';

            const image = document.createElement('img');
            image.src = item.url;
            image.alt = '正文图片';
            image.dataset.uploadKey = item.key;
            image.className = 'rounded-xl object-contain';
            image.style.maxWidth = 'min(100%, 560px)';
            image.style.maxHeight = '360px';
            image.style.width = 'auto';
            image.style.height = 'auto';

            const button = document.createElement('button');
            button.type = 'button';
            button.dataset.removeInlineImage = '1';
            button.className = 'mt-2 rounded-xl border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/40';
            button.textContent = '删除图片';

            link.append(image);
            figure.append(link, button);
            this.insertNodeAtSelection(editor, context, figure);

            const paragraph = document.createElement('p');
            paragraph.innerHTML = '<br>';
            figure.after(paragraph);
            this.placeCaret(paragraph);
            this.rememberSelection(context);
        },

        insertNodeAtSelection(editor, context, node) {
            const selection = window.getSelection();
            const savedRange = this.savedRanges[context];
            let range = null;

            if (savedRange && editor.contains(savedRange.commonAncestorContainer)) {
                range = savedRange;
            } else if (selection && selection.rangeCount > 0) {
                const currentRange = selection.getRangeAt(0);
                if (editor.contains(currentRange.commonAncestorContainer)) {
                    range = currentRange;
                }
            }

            if (!range) {
                editor.append(node);
                return;
            }

            const topLevel = this.topLevelElementFor(range.startContainer, editor);
            if (topLevel) {
                topLevel.after(node);
                return;
            }

            selection.removeAllRanges();
            selection.addRange(range);
            range.deleteContents();
            range.insertNode(node);
        },

        topLevelElementFor(node, editor) {
            let current = node.nodeType === Node.ELEMENT_NODE
                ? node
                : node.parentElement;

            while (current && current.parentElement !== editor) {
                current = current.parentElement;
            }

            return current && current !== editor ? current : null;
        },

        placeCaret(element) {
            const range = document.createRange();
            const selection = window.getSelection();

            range.setStart(element, 0);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
            element.closest('[contenteditable="true"]')?.focus();
        },

        handleEditorClick(event, context) {
            const button = event.target.closest('[data-remove-inline-image]');

            if (!button) {
                this.rememberSelection(context);
                return;
            }

            const figure = button.closest('[data-inline-image]');
            const image = figure?.querySelector('img[data-upload-key], img[data-file-id]');
            const key = image?.dataset.uploadKey;

            if (key) {
                const removed = this.inlineFiles[context].find(file => file.key === key);
                if (removed?.url) {
                    URL.revokeObjectURL(removed.url);
                }

                this.inlineFiles[context] = this.inlineFiles[context].filter(file => file.key !== key);
                this.syncFileInput(context);
            }

            figure?.remove();
            this.refreshContent(context);
        },

        syncFileInput(context) {
            const input = this.inputFor(context);
            const keysInput = this.keysInputFor(context);

            if (!input || typeof DataTransfer === 'undefined') {
                return;
            }

            const transfer = new DataTransfer();
            this.inlineFiles[context].forEach(item => transfer.items.add(item.file));
            input.files = transfer.files;

            if (keysInput) {
                keysInput.value = JSON.stringify(this.inlineFiles[context].map(item => item.key));
            }
        },

        refreshContent(context) {
            const editor = this.editorFor(context);
            const hiddenInput = this.hiddenInputFor(context);

            if (!editor || !hiddenInput) {
                return;
            }

            const blocks = this.serializeNodes(Array.from(editor.childNodes));
            hiddenInput.value = blocks.length > 0
                ? JSON.stringify({
                    type: 'event_record_content',
                    version: 1,
                    blocks,
                })
                : '';
        },

        serializeNodes(nodes) {
            const blocks = [];
            let textParts = [];

            const flushText = () => {
                const text = this.normalizeEditorText(textParts.join(''));
                textParts = [];

                if (text !== '') {
                    blocks.push({ type: 'text', text });
                }
            };

            const walk = (node) => {
                if (node.nodeType === Node.TEXT_NODE) {
                    textParts.push(node.textContent || '');
                    return;
                }

                if (node.nodeType !== Node.ELEMENT_NODE) {
                    return;
                }

                const element = node;
                const imageBlock = this.imageBlockFrom(element);

                if (imageBlock) {
                    flushText();
                    blocks.push(imageBlock);
                    return;
                }

                if (element.tagName === 'BR') {
                    textParts.push('\n');
                    return;
                }

                Array.from(element.childNodes).forEach(walk);

                if (this.isTextBlockElement(element)) {
                    textParts.push('\n');
                    flushText();
                }
            };

            nodes.forEach(walk);
            flushText();

            return blocks;
        },

        imageBlockFrom(element) {
            if (element.matches('[data-inline-image]')) {
                const image = element.querySelector('img[data-upload-key], img[data-file-id]');
                return this.imageBlockFromImage(image);
            }

            if (element.matches('img[data-upload-key], img[data-file-id]')) {
                return this.imageBlockFromImage(element);
            }

            return null;
        },

        imageBlockFromImage(image) {
            const key = image?.dataset.uploadKey;
            const fileId = image?.dataset.fileId;

            if (key) {
                return { type: 'image', key };
            }

            if (fileId && Number.isFinite(Number(fileId))) {
                return { type: 'image', file_id: Number(fileId) };
            }

            return null;
        },

        isTextBlockElement(element) {
            return ['P', 'DIV', 'LI', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'PRE'].includes(element.tagName);
        },

        normalizeEditorText(text) {
            return text
                .replace(/\u00a0/g, ' ')
                .replace(/[ \t]+\n/g, '\n')
                .replace(/\n[ \t]+/g, '\n')
                .replace(/\n{3,}/g, '\n\n')
                .trim();
        },

        async deleteEventTag(tagId, url) {
            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('事件标签删除失败。');
                }

                document.querySelector(`[data-event-tag-id="${tagId}"]`)?.remove();
                window.pushToast('事件标签已删除。', 'success');
            } catch (error) {
                window.pushToast(error.message || '事件标签删除失败。', 'error');
            }
        },

        async deleteAttachment(fileId, url) {
            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('附件删除失败。');
                }

                document.querySelector(`[data-event-attachment-id="${fileId}"]`)?.remove();
                window.pushToast('附件已删除。', 'success');
            } catch (error) {
                window.pushToast(error.message || '附件删除失败。', 'error');
            }
        },
    };
};

window.passwordRow = function (id, revealUrl) {
    return {
        id,
        revealUrl,
        revealed: false,
        plainPassword: '',
        maskedText: '••••••••',
        loading: false,

        async fetchPlainPassword() {
            const response = await fetch(this.revealUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || '读取密码失败，请稍后重试。');
            }

            return data.password || '';
        },

        async handleReveal() {
            if (this.loading || this.revealed) {
                return;
            }

            this.loading = true;

            try {
                const password = await this.fetchPlainPassword();
                this.plainPassword = password;
                this.revealed = true;
                window.pushToast('密码已显示。', 'success');
            } catch (error) {
                window.pushToast(error.message || '读取密码失败，请稍后重试。', 'error');
            } finally {
                this.loading = false;
            }
        },

        async handleCopy() {
            if (this.loading) {
                return;
            }

            this.loading = true;

            try {
                const password = this.revealed && this.plainPassword
                    ? this.plainPassword
                    : await this.fetchPlainPassword();

                await navigator.clipboard.writeText(password);
                window.pushToast('密码已复制到剪贴板。', 'success');
            } catch (error) {
                window.pushToast(error.message || '复制失败，请稍后重试。', 'error');
            } finally {
                this.loading = false;
            }
        }
    };
};

window.passwordEditorField = function (revealUrl) {
    return {
        revealUrl,
        loading: false,
        inputVisible: false,
        countdown: 0,
        timer: null,

        toggleVisibility() {
            this.inputVisible = !this.inputVisible;
            this.$refs.passwordInput.type = this.inputVisible ? 'text' : 'password';
        },

        clearRevealState() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }

            this.countdown = 0;
        },

        startCountdown() {
            this.clearRevealState();
            this.countdown = 15;

            this.timer = setInterval(() => {
                this.countdown -= 1;

                if (this.countdown <= 0) {
                    this.clearRevealState();
                    this.$refs.passwordInput.value = '';
                    this.inputVisible = false;
                    this.$refs.passwordInput.type = 'password';
                }
            }, 1000);
        },

        async revealCurrentPassword() {
            if (!this.revealUrl || this.loading || this.countdown > 0) {
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(this.revealUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({})
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || '读取密码失败，请稍后重试。');
                }

                this.$refs.passwordInput.value = data.password || '';
                this.inputVisible = true;
                this.$refs.passwordInput.type = 'text';
                this.startCountdown();
                window.pushToast('当前密码已短时显示。', 'success');
            } catch (error) {
                window.pushToast(error.message || '读取密码失败，请稍后重试。', 'error');
            } finally {
                this.loading = false;
            }
        }
    };
};

Alpine.start();
